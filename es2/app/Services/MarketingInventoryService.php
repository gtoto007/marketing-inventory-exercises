<?php

namespace App\Services;

use App\Enums\OwnershipType;
use App\Enums\StockState;
use App\Models\Event;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Event cost for managers and item availability for the administration team.
 */
class MarketingInventoryService
{
    /**
     * The total cost of an event. Only managers and admins can see it.
     *
     * The cost of an event is given by:
     * - the units of owned stock lots consumed (e.g. given away) or lost at the event, at their purchase price
     *   (units still at the event or back in the warehouse cost nothing);
     * - the rental agreements linked to the event: the rent for every day of the agreement,
     *   the penalties for units lost or given back late, and the shipping (a cancelled agreement costs nothing);
     * - the shipments to and from the event.
     *
     * @throws AuthorizationException when the user is not a manager or an admin
     */
    public function getEventCost(Event $event, User $user): float
    {
        Gate::forUser($user)->authorize('viewCost', $event);

        // Computed attributes of the Event model (accessors): each one calculates one part of the cost.
        return $event->cost_of_owned_stock_lots
            + $event->cost_of_rental_agreements
            + $event->cost_of_shipments;
    }

    /**
     * Units of the item ready to be dispatched: owned units in the warehouse,
     * plus units rented for the event when an event is provided.
     *
     * Only the administration team can see it.
     *
     * @throws AuthorizationException when the user is not an admin
     */
    public function getAvailableItemQuantity(Item $item, User $user, ?Event $event = null): int
    {
        Gate::forUser($user)->authorize('viewItemAvailability', [Event::class, $event]);

        $quantity = $this->getOwnedQuantityInWarehouse($item->id);

        if ($event !== null) {
            $quantity += $this->getRentedQuantityInWarehouse($item->id, $event->id);
        }

        return $quantity;
    }

    /**
     * Units of owned stock lots of the item in the warehouse: they can be sent to any event.
     */
    private function getOwnedQuantityInWarehouse(int $itemId): int
    {
        return (int) StockBalance::where('state', StockState::IN_WAREHOUSE)
            ->whereHas('stockLot', fn ($lot) => $lot
                ->where('item_id', $itemId)
                ->where('ownership_type', OwnershipType::OWNED))
            ->sum('qta');
    }

    /**
     * Units of rented stock lots of the item in the warehouse: they can only be sent to the event they were rented for.
     */
    private function getRentedQuantityInWarehouse(int $itemId, int $eventId): int
    {
        return (int) StockBalance::where('state', StockState::IN_WAREHOUSE)
            ->whereHas('stockLot', fn ($lot) => $lot
                ->where('item_id', $itemId)
                ->whereRelation('rentalAgreementItem.rentalAgreement', 'event_id', $eventId))
            ->sum('qta');
    }
}
