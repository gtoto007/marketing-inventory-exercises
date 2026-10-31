<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * What we buy (purchase orders) and what we rent (rental agreements).
 * The prices are the same as in item_supplier.
 */
class ProcurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 purchase orders, both delivered to our warehouse.
        // Order 1 arrived before event 1. Order 2 is new and has not arrived yet.
        DB::table('purchase_orders')->insert([
            ['id' => 1, 'supplier_id' => 1, 'created_by' => 3, 'supplier_order_number' => 'TL-2026-0118', 'status' => 'RECEIVED', 'shipping_cost' => 35.00, 'shipping_address' => 'Via Mecenate 76', 'shipping_city' => 'Milano', 'shipping_postal_code' => '20138', 'shipping_country' => 'IT', 'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 2, 'supplier_id' => 1, 'created_by' => 3, 'supplier_order_number' => 'TL-2026-0251', 'status' => 'PENDING', 'shipping_cost' => 35.00, 'shipping_address' => 'Via Mecenate 76', 'shipping_city' => 'Milano', 'shipping_postal_code' => '20138', 'shipping_country' => 'IT', 'created_at' => '2026-10-27 17:00:00', 'updated_at' => '2026-10-27 17:00:00'],
        ]);

        // Add the items to the purchase orders: 3 items in order 1, 1 item in order 2.
        DB::table('purchase_order_items')->insert([
            ['id' => 1, 'purchase_order_id' => 1, 'item_id' => 1, 'price' => 0.08, 'qta' => 2000],  // Volantino promozionale A5
            ['id' => 2, 'purchase_order_id' => 1, 'item_id' => 4, 'price' => 79.00, 'qta' => 2],    // Roll-up con logo
            ['id' => 3, 'purchase_order_id' => 1, 'item_id' => 7, 'price' => 0.45, 'qta' => 500],   // Penna a sfera con logo
            ['id' => 4, 'purchase_order_id' => 2, 'item_id' => 2, 'price' => 0.35, 'qta' => 1000],  // Brochure aziendale a tre ante
        ]);

        // Create 2 rental agreements, one for each event.
        // Rented goods come to our warehouse a few days before the event
        // and go back to the supplier a few days after it.
        // Rental 1 is finished. Rental 2 has been received and has not been returned yet.
        DB::table('rental_agreements')->insert([
            ['id' => 1, 'supplier_id' => 2, 'event_id' => 1, 'created_by' => 2, 'supplier_agreement_number' => 'AEN-NL-26-152', 'status' => 'RETURNED', 'start_date' => '2026-09-18', 'end_date' => '2026-09-28', 'shipping_cost' => 150.00, 'created_at' => '2026-08-28 09:45:00', 'updated_at' => '2026-09-30 11:00:00'],
            ['id' => 2, 'supplier_id' => 2, 'event_id' => 2, 'created_by' => 2, 'supplier_agreement_number' => 'AEN-NL-26-188', 'status' => 'RECEIVED', 'start_date' => '2026-10-24', 'end_date' => '2026-11-04', 'shipping_cost' => 120.00, 'created_at' => '2026-10-05 14:20:00', 'updated_at' => '2026-10-24 10:00:00'],
        ]);

        // Add the items to the rental agreements: 2 items in rental 1, 2 items in rental 2.
        DB::table('rental_agreement_items')->insert([
            ['id' => 1, 'rental_agreement_id' => 1, 'item_id' => 10, 'daily_rate_per_unit' => 6.50, 'daily_late_fee_per_unit' => 3.00, 'non_return_fee' => 85.00, 'qta' => 6],    // Sgabello da bar
            ['id' => 2, 'rental_agreement_id' => 1, 'item_id' => 13, 'daily_rate_per_unit' => 60.00, 'daily_late_fee_per_unit' => 30.00, 'non_return_fee' => 700.00, 'qta' => 2], // Monitor LED 55"
            ['id' => 3, 'rental_agreement_id' => 2, 'item_id' => 12, 'daily_rate_per_unit' => 45.00, 'daily_late_fee_per_unit' => 20.00, 'non_return_fee' => 900.00, 'qta' => 1], // Divano lounge
            ['id' => 4, 'rental_agreement_id' => 2, 'item_id' => 11, 'daily_rate_per_unit' => 9.00, 'daily_late_fee_per_unit' => 4.00, 'non_return_fee' => 120.00, 'qta' => 4],   // Tavolo pieghevole 180x80
        ]);

        // Create one stock lot for each item that has arrived in the warehouse.
        // Lots 1-3 are bought (OWNED), lots 4-7 are rented (RENTED).
        // Order 2 has no lot because it has not arrived yet.
        DB::table('stock_lots')->insert([
            ['id' => 1, 'item_id' => 1, 'ownership_type' => 'OWNED', 'purchase_order_item_id' => 1, 'rental_agreement_item_id' => null, 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 2, 'item_id' => 4, 'ownership_type' => 'OWNED', 'purchase_order_item_id' => 2, 'rental_agreement_item_id' => null, 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 3, 'item_id' => 7, 'ownership_type' => 'OWNED', 'purchase_order_item_id' => 3, 'rental_agreement_item_id' => null, 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 4, 'item_id' => 10, 'ownership_type' => 'RENTED', 'purchase_order_item_id' => null, 'rental_agreement_item_id' => 1, 'created_at' => '2026-09-18 10:00:00', 'updated_at' => '2026-09-18 10:00:00'],
            ['id' => 5, 'item_id' => 13, 'ownership_type' => 'RENTED', 'purchase_order_item_id' => null, 'rental_agreement_item_id' => 2, 'created_at' => '2026-09-18 10:00:00', 'updated_at' => '2026-09-18 10:00:00'],
            ['id' => 6, 'item_id' => 12, 'ownership_type' => 'RENTED', 'purchase_order_item_id' => null, 'rental_agreement_item_id' => 3, 'created_at' => '2026-10-24 10:00:00', 'updated_at' => '2026-10-24 10:00:00'],
            ['id' => 7, 'item_id' => 11, 'ownership_type' => 'RENTED', 'purchase_order_item_id' => null, 'rental_agreement_item_id' => 4, 'created_at' => '2026-10-24 10:00:00', 'updated_at' => '2026-10-24 10:00:00'],
        ]);
    }
}
