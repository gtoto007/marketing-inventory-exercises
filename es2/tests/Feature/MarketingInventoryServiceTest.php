<?php

use App\Models\Event;
use App\Models\EventShipment;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\RentalAgreement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\MarketingInventoryService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates the event cost for purchased items', function () {
    $stockService = new StockService();
    $manager = User::factory()->manager()->create();
    $supplier = Supplier::factory()->create();

    $event = Event::factory()->create();

    // Given: 100 units bought; 50 go to the event, 10 are consumed, 10 lost and 30 come back
    $order = PurchaseOrder::factory()->for($supplier)
        ->withItem(Item::factory()->purchasable($supplier)->create(), 100)
        ->create();
    $purchasedLot = $stockService->stockOrderItems($order, $manager)->first();

    $stockService->transitToEvent($purchasedLot, 50, $event, $manager);
    $stockService->deliverToEvent($purchasedLot, 50, $event, $manager);
    $stockService->consume($purchasedLot, 10, $event, $manager);
    $stockService->lose($purchasedLot, 10, $event, $manager);
    $stockService->transitToWarehouse($purchasedLot, 30, $event, $manager);
    $stockService->deliverToWarehouse($purchasedLot, 30, $event, $manager);

    // Given: a shipment to the event of 30
    EventShipment::factory()->for($event)->create(['shipping_cost' => 30]);

    // When
    $eventCost = (new MarketingInventoryService())->getEventCost($event, $manager);

    // Then: only consumed and lost units are charged, plus shipping
    $purchasedItem = $order->items->first();
    $expectedCost = (10 + 10) * $purchasedItem->price + 30;

    expect($eventCost)->toEqualWithDelta($expectedCost, 0.001);
});

it('calculates the event cost for rented items', function () {
    $stockService = new StockService();
    $manager = User::factory()->manager()->create();
    $supplier = Supplier::factory()->create();

    // Given: an event of 3 days, ended 3 days ago
    $event = Event::factory()->create([
        'start_date' => today()->subDays(5),
        'end_date' => today()->subDays(3),
    ]);

    // Given: 50 units rented for the event
    $rental = RentalAgreement::factory()->for($supplier)->for($event)
        ->withItem(Item::factory()->rentable($supplier)->create(), 50)
        ->create();
    $rentedLot = $stockService->stockRentalItems($rental, $manager)->first();

    // The rented lot travels from the warehouse to its event and back
    $stockService->transitToEvent($rentedLot, 50, $event, $manager);
    $stockService->deliverToEvent($rentedLot, 50, $event, $manager);
    $stockService->transitToWarehouse($rentedLot, 50, $event, $manager);
    $stockService->deliverToWarehouse($rentedLot, 50, $event, $manager);

    // Then it is given back to the supplier 1 day after the end of the agreement
    $stockService->returnToSupplier($rentedLot, 50, $manager, $event->end_date->copy()->addDay());

    // Given: a shipment to the event of 30
    EventShipment::factory()->for($event)->create(['shipping_cost' => 30]);

    // When
    $eventCost = (new MarketingInventoryService())->getEventCost($event, $manager);

    // Then
    $rentedItem = $rental->items->first();

    $expectedCost = 50 * $rentedItem->daily_rate_per_unit * 3 // rent: 50 units for 3 days
        + 50 * 1 * $rentedItem->daily_late_fee_per_unit       // penalty for delay: 50 units, 1 day late
        + $rental->shipping_cost                              // shipping of the rental agreement
        + 30;                                                 // shipment to the event

    expect($eventCost)->toEqualWithDelta($expectedCost, 0.001);
});

it('calculates the item units available to be dispatched to the event', function () {
    $stockService = new StockService();
    $admin = User::factory()->admin()->create();
    $event = Event::factory()->create();

    // Given: an item sold by one supplier and rented by another
    $seller = Supplier::factory()->create();
    $renter = Supplier::factory()->create();
    $item = Item::factory()->purchasable($seller)->rentable($renter)->create();

    // Given: 20 units bought and received in the warehouse
    $order = PurchaseOrder::factory()->for($seller)->withItem($item, 20)->create();
    $purchasedLot = $stockService->stockOrderItems($order, $admin)->first();

    // Given: 20 units rented for the event
    $rental = RentalAgreement::factory()->for($renter)->for($event)->withItem($item, 20)->create();
    $rentedLot = $stockService->stockRentalItems($rental, $admin)->first();

    // Given: 10 purchased units and 10 rented units are then sent to the event
    $stockService->transitToEvent($purchasedLot, 10, $event, $admin);
    $stockService->transitToEvent($rentedLot, 10, $event, $admin);

    // When
    $availableQta = (new MarketingInventoryService())->getAvailableItemQuantity($item, $admin, $event);

    // Then: 10 bought units + 10 rented for the event are still in the warehouse
    expect($availableQta)->toBe(10 + 10);
});
