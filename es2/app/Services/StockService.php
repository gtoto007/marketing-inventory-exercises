<?php

namespace App\Services;

use App\Enums\OwnershipType;
use App\Enums\PurchaseOrderStatus;
use App\Enums\RentalAgreementStatus;
use App\Enums\StockState;
use App\Models\Event;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RentalAgreement;
use App\Models\RentalAgreementItem;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

/**
 * Moves the goods of a stock lot between states: every move writes the history (stock_movements)
 * and updates the current situation (stock_balances).
 */
class StockService
{
    /**
     * The goods of a received purchase order are stocked: every item becomes an OWNED lot in the warehouse.
     *
     * @return Collection<int, StockLot> the new lots, keyed by item id
     */
    public function stockOrderItems(PurchaseOrder $order, User $user): Collection
    {
        // Goods already received, or never coming.
        if (in_array($order->status, [PurchaseOrderStatus::RECEIVED, PurchaseOrderStatus::CANCELLED], true)) {
            throw new LogicException("Purchase order {$order->id} cannot be stocked ({$order->status->value}).");
        }

        return DB::transaction(function () use ($order, $user) {
            $lots = new Collection();

            foreach ($order->items as $item) {
                $lot = $this->addToWarehouse($item, $user, "Purchase order {$order->supplier_order_number}");
                $lots->put($item->item_id, $lot);
            }

            $order->update(['status' => PurchaseOrderStatus::RECEIVED]);

            return $lots;
        });
    }

    /**
     * The goods of a received rental agreement are stocked: every item becomes a RENTED lot in the warehouse.
     *
     * @return Collection<int, StockLot> the new lots, keyed by item id
     */
    public function stockRentalItems(RentalAgreement $agreement, User $user): Collection
    {
        if (in_array($agreement->status, [RentalAgreementStatus::RECEIVED, RentalAgreementStatus::RETURNED, RentalAgreementStatus::CANCELLED], true)) {
            throw new LogicException("Rental agreement {$agreement->id} cannot be stocked ({$agreement->status->value}).");
        }

        return DB::transaction(function () use ($agreement, $user) {
            $lots = new Collection();

            foreach ($agreement->items as $item) {
                $lot = $this->addToWarehouse($item, $user, "Rental agreement {$agreement->supplier_agreement_number}");
                $lots->put($item->item_id, $lot);
            }

            $agreement->update(['status' => RentalAgreementStatus::RECEIVED]);

            return $lots;
        });
    }

    /**
     * Move a quantity of a lot from one state to another.
     *
     * @param  CarbonInterface|null  $movedAt  when the move happened (default: now)
     */
    public function move(StockLot $lot, StockState $from, StockState $to, int $qta, User $user, ?Event $event = null, ?string $reason = null, ?CarbonInterface $movedAt = null): StockMovement
    {
        $this->validateQuantity($qta);
        $this->validateMove($lot, $from, $to, $event);
        $this->validateMovedAt($movedAt);

        return DB::transaction(function () use ($lot, $from, $to, $qta, $user, $event, $reason, $movedAt) {
            $this->removeFromBalance($lot, $from, $event, $qta);
            $this->addToBalance($lot, $to, $event, $qta);

            return $this->recordMovement($lot, $from, $to, $qta, $user, $event, $reason, $movedAt);
        });
    }

    /**
     * Send a quantity of a lot from the warehouse to the event: it leaves in transit.
     */
    public function transitToEvent(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::IN_WAREHOUSE, StockState::IN_TRANSIT_TO_EVENT, $qta, $user, $event);
    }

    /**
     * A quantity of a lot in transit arrives at the event.
     */
    public function deliverToEvent(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::IN_TRANSIT_TO_EVENT, StockState::AT_EVENT, $qta, $user, $event);
    }

    /**
     * A quantity of a lot is consumed at the event: it is a cost of the event.
     */
    public function consume(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::AT_EVENT, StockState::CONSUMED, $qta, $user, $event);
    }

    /**
     * A quantity of a lot is lost at the event: it is a cost of the event.
     */
    public function lose(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::AT_EVENT, StockState::LOST, $qta, $user, $event);
    }

    /**
     * A quantity of a lot leaves the event: it is in transit back to the warehouse.
     */
    public function transitToWarehouse(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::AT_EVENT, StockState::IN_TRANSIT_FROM_EVENT, $qta, $user, $event);
    }

    /**
     * A quantity of a lot coming back from the event is in the warehouse again.
     */
    public function deliverToWarehouse(StockLot $lot, int $qta, Event $event, User $user): StockMovement
    {
        return $this->move($lot, StockState::IN_TRANSIT_FROM_EVENT, StockState::IN_WAREHOUSE, $qta, $user, $event);
    }

    /**
     * Give a quantity of a rented lot back to the supplier, from the warehouse.
     * The event is the one the goods were rented for.
     *
     * @param  CarbonInterface|null  $returnedAt  when the units were given back (default: now): it sets the delay penalty
     */
    public function returnToSupplier(StockLot $lot, int $qta, User $user, ?CarbonInterface $returnedAt = null): StockMovement
    {
        $event = $lot->rentalAgreementItem?->rentalAgreement->event;

        return $this->move($lot, StockState::IN_WAREHOUSE, StockState::RETURNED_TO_SUPPLIER, $qta, $user, $event, movedAt: $returnedAt);
    }

    /**
     * The goods of an order or rental item arrive into the warehouse: the item becomes a new lot,
     * and the goods enter with the only movement that has no starting state.
     */
    private function addToWarehouse(PurchaseOrderItem|RentalAgreementItem $item, User $user, string $reason): StockLot
    {
        $this->validateQuantity($item->qta);

        $lot = $this->convertToStockLot($item);
        $this->addToBalance($lot, StockState::IN_WAREHOUSE, null, $item->qta);
        $this->recordMovement($lot, null, StockState::IN_WAREHOUSE, $item->qta, $user, null, $reason);

        return $lot;
    }

    /**
     * An item of a purchase order becomes a lot of OWNED goods, an item of a rental agreement a lot of RENTED goods.
     * Only called by addToWarehouse, so a lot is never created without entering the warehouse.
     */
    private function convertToStockLot(PurchaseOrderItem|RentalAgreementItem $item): StockLot
    {
        return $item->stockLots()->create([
            'item_id' => $item->item_id,
            'ownership_type' => $item instanceof PurchaseOrderItem ? OwnershipType::OWNED : OwnershipType::RENTED,
        ]);
    }

    private function validateMove(StockLot $lot, StockState $from, StockState $to, ?Event $event): void
    {
        if ($from === $to) {
            throw new InvalidArgumentException("The starting and the final state are the same ({$to->value}).");
        }

        // Only rented goods are given back to a supplier.
        if ($to === StockState::RETURNED_TO_SUPPLIER && $lot->ownership_type !== OwnershipType::RENTED) {
            throw new LogicException("Lot {$lot->id} is owned, it cannot be returned to a supplier.");
        }

        // Rented goods can only be used for the event they were rented for.
        if ($event !== null && $lot->ownership_type === OwnershipType::RENTED
            && $lot->rentalAgreementItem->rentalAgreement->event_id !== $event->id) {
            throw new LogicException("Lot {$lot->id} was rented for another event.");
        }
    }

    private function removeFromBalance(StockLot $lot, StockState $state, ?Event $event, int $qta): void
    {
        $balance = $this->findBalance($lot, $state, $event);

        if (! $balance || $balance->qta < $qta) {
            $available = $balance?->qta ?? 0;

            throw new LogicException("Not enough goods in lot {$lot->id} ({$state->value}): {$available} available, {$qta} requested.");
        }

        // A balance with no goods left is deleted: only states that still have goods have a row.
        if ($balance->qta === $qta) {
            $balance->delete();
        } else {
            $balance->decrement('qta', $qta);
        }
    }

    private function addToBalance(StockLot $lot, StockState $state, ?Event $event, int $qta): void
    {
        $balance = $this->findBalance($lot, $state, $event);

        if ($balance) {
            $balance->increment('qta', $qta);
        } else {
            $lot->stockBalances()->create(['state' => $state, 'event_id' => $this->balanceEventId($state, $event), 'qta' => $qta]);
        }
    }

    private function findBalance(StockLot $lot, StockState $state, ?Event $event): ?StockBalance
    {
        return $lot->stockBalances()
            ->where('state', $state)
            ->where('event_id', $this->balanceEventId($state, $event))
            ->lockForUpdate()
            ->first();
    }

    /**
     * The event of the balance: goods in the warehouse are shared, in every other state they belong to the event of the move.
     */
    private function balanceEventId(StockState $state, ?Event $event): ?int
    {
        return $state === StockState::IN_WAREHOUSE ? null : $event?->id;
    }

    private function recordMovement(StockLot $lot, ?StockState $from, StockState $to, int $qta, User $user, ?Event $event, ?string $reason, ?CarbonInterface $movedAt = null): StockMovement
    {
        // The movement always keeps the event, so the history is complete.
        $movement = $lot->stockMovements()->make([
            'qta' => $qta,
            'from_state' => $from,
            'to_state' => $to,
            'reason' => $reason,
            'event_id' => $event?->id,
            'performed_by' => $user->id,
        ]);

        // created_at is when the move happened: it is not fillable, so it is set only here.
        $movement->forceFill(['created_at' => $movedAt ?? now()])->save();

        return $movement;
    }

    private function validateQuantity(int $qta): void
    {
        if ($qta < 1) {
            throw new InvalidArgumentException('The quantity must be at least 1.');
        }
    }

    private function validateMovedAt(?CarbonInterface $movedAt): void
    {
        // A move cannot happen in the future.
        if ($movedAt !== null && $movedAt->isFuture()) {
            throw new InvalidArgumentException('A move cannot happen in the future.');
        }
    }
}
