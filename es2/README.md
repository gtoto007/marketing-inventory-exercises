# Exercise 2: Event cost and item availability

A Laravel 11 implementation of the logistics logic designed in [Exercise 1](../es1/README.md):
the cost of an event (for managers) and the quantity of an item ready to be dispatched to an event (for the administration team).

## What was done

- **[MarketingInventoryService](app/Services/MarketingInventoryService.php)**, the service asked by the exercise:
  - `getEventCost`: the total cost of an event;
  - `getAvailableItemQuantity`: the units of an item ready to be dispatched to an event.
- **[Tests](tests/Feature/MarketingInventoryServiceTest.php)**: separate feature tests for purchased item cost, rented item cost and item availability.
- **Database**: all the tables defined in [Exercise 1](../es1/schema.dbml) are implemented as [migrations](database/migrations), with their [models](app/Models), [enums](app/Enums) and [factories](database/factories).
- **[EventPolicy](app/Policies/EventPolicy.php)**: who can see the event cost and the item availability.
- **[StockService](app/Services/StockService.php)**: not asked by the exercise, but useful to record the movements of the stock lots (receipt of orders and rentals, shipment to the event, consumed, lost, return...): every movement updates `stock_movements` and `stock_balances` together, so the two tables always stay consistent. It also keeps the tests readable, as each step of the flow is a single call (e.g. `transitToEvent`, `consume`, `returnToSupplier`).


---

## What was not done

I did not add views or controllers for these features because they were not explicitly requested by the exercise. To check that [MarketingInventoryService](app/Services/MarketingInventoryService.php) works, I wrote a [feature test](tests/Feature/MarketingInventoryServiceTest.php) in Pest and added [factories](database/factories) to create users, orders, rental agreements, items, suppliers, carriers and events for the test.


---

## Notes and assumptions

- Event cost
  - The cost of an event is the sum of:
    - Owned stock lots consumed (e.g. given away) or lost at the event × their purchase price.
    - Rental agreements linked to the event: rent + penalties (e.g. units lost or given back late) + shipping of the agreement.
    - Shipments to and from the event (`event_shipments`).
  - The cost is calculated from `stock_balances` (the current situation), not from `stock_movements` (the history): the same units pass through several states, so summing the movements would count them more than once.
  - Units still at the event, in transit or back in the warehouse cost nothing: they are not spent.
  - A cancelled rental agreement costs nothing (no cancellation fee).
  - Index considerations:
    - Since I expect up to about 100 items per event and few movements per lot, I added single-column indexes on `stock_balances.event_id` and `stock_movements.stock_lot_id`, without extra composite indexes such as `(event_id, state)` or `(stock_lot_id, to_state)`.
- Item availability
  - Item availability is the sum of the units from owned lots of that item in `in_warehouse`, plus, if an event is passed to the method, the units from rented lots of the same item in `in_warehouse` whose rental agreement belongs to that event.
  - "Ready to be dispatched", as stated in the exercise, means units in the `in_warehouse` state in `stock_balances` in our system.

---

## Time spent and use of AI

**Time spent:** two days.

**Use of AI:** I used AI for mechanical tasks and as a reminder, while the design and the decisions were mine:

- **Mechanical tasks**: converting the schema defined in [Exercise 1](../es1/schema.dbml) into migrations, and creating the factories, under my supervision.
- **Framework reminders**: how to use some Laravel features I did not remember, e.g. converting a SQL query into Eloquent with `whereHas` and `whereRelation`.
- **README**: preparing this README, guided by me on what to write.


---

## Quick start

The project uses SQLite: no database server is needed. PHP 8.4.1 or newer and Composer are required.

```bash
cd es2
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Run the [feature test](tests/Feature/MarketingInventoryServiceTest.php) (it uses an in-memory SQLite database):

```bash
php artisan test tests/Feature/MarketingInventoryServiceTest.php
```

To fill the database with example users, items, carriers, events, orders, rentals and stock movements in Italian:

```bash
php artisan db:seed
```
