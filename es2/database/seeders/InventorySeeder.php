<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Where the goods are: stock movements, stock balances and event shipments.
 *
 * Bought and rented goods follow the same path:
 * supplier -> warehouse -> on the way to the event -> at the event -> on the way back -> warehouse.
 * At the end, rented goods go back to the supplier.
 *
 * Story of each lot:
 * Lot 1 (2000 flyers):  we send 800 to event 1, give away 700, lose 10, bring back 90.
 * Lot 2 (2 roll-ups):   we send both to event 2, they are still there.
 * Lot 3 (500 pens):     they never leave the warehouse.
 * Lot 4 (6 bar stools): rented for event 1, 1 is lost, we give 5 back to the supplier on time.
 * Lot 5 (2 monitors):   rented for event 1, we give both back to the supplier 2 days late.
 * Lot 6 (1 sofa):       rented for event 2, it is still there.
 * Lot 7 (4 tables):     rented for event 2, they are still in the warehouse (available only for event 2).
 */
class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchaseOrderCreators = DB::table('purchase_orders')->pluck('created_by', 'id');
        $rentalAgreementCreators = DB::table('rental_agreements')->pluck('created_by', 'id');

        DB::table('stock_movements')->insert([
            // Items arrive from the supplier: from_state is NULL and performed_by is the order or rental creator.
            ['id' => 1, 'stock_lot_id' => 1, 'event_id' => null, 'qta' => 2000, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (ordine TL-2026-0118)', 'performed_by' => $purchaseOrderCreators[1], 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 2, 'stock_lot_id' => 2, 'event_id' => null, 'qta' => 2, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (ordine TL-2026-0118)', 'performed_by' => $purchaseOrderCreators[1], 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 3, 'stock_lot_id' => 3, 'event_id' => null, 'qta' => 500, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (ordine TL-2026-0118)', 'performed_by' => $purchaseOrderCreators[1], 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 4, 'stock_lot_id' => 4, 'event_id' => null, 'qta' => 6, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (noleggio AEN-NL-26-152)', 'performed_by' => $rentalAgreementCreators[1], 'created_at' => '2026-09-18 10:00:00', 'updated_at' => '2026-09-18 10:00:00'],
            ['id' => 5, 'stock_lot_id' => 5, 'event_id' => null, 'qta' => 2, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (noleggio AEN-NL-26-152)', 'performed_by' => $rentalAgreementCreators[1], 'created_at' => '2026-09-18 10:00:00', 'updated_at' => '2026-09-18 10:00:00'],

            // Event 1: flyers, bar stools and monitors leave the warehouse.
            ['id' => 6, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 800, 'from_state' => 'in_warehouse', 'to_state' => 'in_transit_to_event', 'reason' => 'Spedito all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-20 09:00:00', 'updated_at' => '2026-09-20 09:00:00'],
            ['id' => 7, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 6, 'from_state' => 'in_warehouse', 'to_state' => 'in_transit_to_event', 'reason' => 'Spedito all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-20 09:00:00', 'updated_at' => '2026-09-20 09:00:00'],
            ['id' => 8, 'stock_lot_id' => 5, 'event_id' => 1, 'qta' => 2, 'from_state' => 'in_warehouse', 'to_state' => 'in_transit_to_event', 'reason' => 'Spedito all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-20 09:00:00', 'updated_at' => '2026-09-20 09:00:00'],

            // Event 1: everything arrives at the event.
            ['id' => 9, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 800, 'from_state' => 'in_transit_to_event', 'to_state' => 'at_event', 'reason' => 'Consegnato all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-21 10:00:00', 'updated_at' => '2026-09-21 10:00:00'],
            ['id' => 10, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 6, 'from_state' => 'in_transit_to_event', 'to_state' => 'at_event', 'reason' => 'Consegnato all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-21 10:00:00', 'updated_at' => '2026-09-21 10:00:00'],
            ['id' => 11, 'stock_lot_id' => 5, 'event_id' => 1, 'qta' => 2, 'from_state' => 'in_transit_to_event', 'to_state' => 'at_event', 'reason' => 'Consegnato all\'evento', 'performed_by' => 3, 'created_at' => '2026-09-21 10:00:00', 'updated_at' => '2026-09-21 10:00:00'],

            // Event 1: we give away 700 flyers, lose 10 flyers and 1 bar stool.
            ['id' => 12, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 700, 'from_state' => 'at_event', 'to_state' => 'consumed', 'reason' => 'Regalato ai visitatori', 'performed_by' => 3, 'created_at' => '2026-09-24 18:00:00', 'updated_at' => '2026-09-24 18:00:00'],
            ['id' => 13, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 10, 'from_state' => 'at_event', 'to_state' => 'lost', 'reason' => 'Danneggiato allo stand', 'performed_by' => 3, 'created_at' => '2026-09-24 19:00:00', 'updated_at' => '2026-09-24 19:00:00'],
            ['id' => 14, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 1, 'from_state' => 'at_event', 'to_state' => 'lost', 'reason' => 'Non trovato durante l\'imballaggio', 'performed_by' => 3, 'created_at' => '2026-09-25 09:00:00', 'updated_at' => '2026-09-25 09:00:00'],

            // Event 1: what is left goes back to the warehouse.
            ['id' => 15, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 90, 'from_state' => 'at_event', 'to_state' => 'in_transit_from_event', 'reason' => 'Rispedito al magazzino', 'performed_by' => 3, 'created_at' => '2026-09-25 10:00:00', 'updated_at' => '2026-09-25 10:00:00'],
            ['id' => 16, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 5, 'from_state' => 'at_event', 'to_state' => 'in_transit_from_event', 'reason' => 'Rispedito al magazzino', 'performed_by' => 3, 'created_at' => '2026-09-25 10:00:00', 'updated_at' => '2026-09-25 10:00:00'],
            ['id' => 17, 'stock_lot_id' => 5, 'event_id' => 1, 'qta' => 2, 'from_state' => 'at_event', 'to_state' => 'in_transit_from_event', 'reason' => 'Rispedito al magazzino', 'performed_by' => 3, 'created_at' => '2026-09-25 10:00:00', 'updated_at' => '2026-09-25 10:00:00'],
            ['id' => 18, 'stock_lot_id' => 1, 'event_id' => 1, 'qta' => 90, 'from_state' => 'in_transit_from_event', 'to_state' => 'in_warehouse', 'reason' => 'Rientrato in magazzino', 'performed_by' => 3, 'created_at' => '2026-09-27 15:00:00', 'updated_at' => '2026-09-27 15:00:00'],
            ['id' => 19, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 5, 'from_state' => 'in_transit_from_event', 'to_state' => 'in_warehouse', 'reason' => 'Rientrato in magazzino', 'performed_by' => 3, 'created_at' => '2026-09-27 15:00:00', 'updated_at' => '2026-09-27 15:00:00'],
            ['id' => 20, 'stock_lot_id' => 5, 'event_id' => 1, 'qta' => 2, 'from_state' => 'in_transit_from_event', 'to_state' => 'in_warehouse', 'reason' => 'Rientrato in magazzino', 'performed_by' => 3, 'created_at' => '2026-09-27 15:00:00', 'updated_at' => '2026-09-27 15:00:00'],

            // Event 1: we give the rented bar stools back on the last day of the agreement (28/09), the monitors 2 days late.
            ['id' => 21, 'stock_lot_id' => 4, 'event_id' => 1, 'qta' => 5, 'from_state' => 'in_warehouse', 'to_state' => 'returned_to_supplier', 'reason' => 'Restituito al fornitore (noleggio AEN-NL-26-152)', 'performed_by' => 3, 'created_at' => '2026-09-28 11:00:00', 'updated_at' => '2026-09-28 11:00:00'],
            ['id' => 22, 'stock_lot_id' => 5, 'event_id' => 1, 'qta' => 2, 'from_state' => 'in_warehouse', 'to_state' => 'returned_to_supplier', 'reason' => 'Restituito al fornitore in ritardo (noleggio AEN-NL-26-152)', 'performed_by' => 3, 'created_at' => '2026-09-30 11:00:00', 'updated_at' => '2026-09-30 11:00:00'],

            // Event 2: the rented sofa and tables arrive in the warehouse.
            ['id' => 23, 'stock_lot_id' => 6, 'event_id' => null, 'qta' => 1, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (noleggio AEN-NL-26-188)', 'performed_by' => $rentalAgreementCreators[2], 'created_at' => '2026-10-24 10:00:00', 'updated_at' => '2026-10-24 10:00:00'],
            ['id' => 24, 'stock_lot_id' => 7, 'event_id' => null, 'qta' => 4, 'from_state' => null, 'to_state' => 'in_warehouse', 'reason' => 'Ricevuto dal fornitore (noleggio AEN-NL-26-188)', 'performed_by' => $rentalAgreementCreators[2], 'created_at' => '2026-10-24 10:00:00', 'updated_at' => '2026-10-24 10:00:00'],

            // Event 2: roll-ups and sofa go to the event, the tables stay in the warehouse.
            // The event is still going on, so nothing has come back yet.
            ['id' => 25, 'stock_lot_id' => 2, 'event_id' => 2, 'qta' => 2, 'from_state' => 'in_warehouse', 'to_state' => 'in_transit_to_event', 'reason' => 'Spedito all\'evento', 'performed_by' => 3, 'created_at' => '2026-10-26 09:00:00', 'updated_at' => '2026-10-26 09:00:00'],
            ['id' => 26, 'stock_lot_id' => 6, 'event_id' => 2, 'qta' => 1, 'from_state' => 'in_warehouse', 'to_state' => 'in_transit_to_event', 'reason' => 'Spedito all\'evento', 'performed_by' => 3, 'created_at' => '2026-10-26 09:00:00', 'updated_at' => '2026-10-26 09:00:00'],
            ['id' => 27, 'stock_lot_id' => 2, 'event_id' => 2, 'qta' => 2, 'from_state' => 'in_transit_to_event', 'to_state' => 'at_event', 'reason' => 'Consegnato all\'evento', 'performed_by' => 3, 'created_at' => '2026-10-27 10:00:00', 'updated_at' => '2026-10-27 10:00:00'],
            ['id' => 28, 'stock_lot_id' => 6, 'event_id' => 2, 'qta' => 1, 'from_state' => 'in_transit_to_event', 'to_state' => 'at_event', 'reason' => 'Consegnato all\'evento', 'performed_by' => 3, 'created_at' => '2026-10-27 10:00:00', 'updated_at' => '2026-10-27 10:00:00'],
        ]);

        // How many pieces are in each state today, after all the movements above.
        // We only write the states that still have pieces (no rows with qta 0).
        // In the warehouse event_id is NULL. In all other states event_id is the event the goods moved for.
        DB::table('stock_balances')->insert([
            ['id' => 1, 'stock_lot_id' => 1, 'state' => 'in_warehouse', 'event_id' => null, 'qta' => 1290, 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-27 15:00:00'], // 2000 - 800 + 90
            ['id' => 2, 'stock_lot_id' => 1, 'state' => 'consumed', 'event_id' => 1, 'qta' => 700, 'created_at' => '2026-09-24 18:00:00', 'updated_at' => '2026-09-24 18:00:00'],
            ['id' => 3, 'stock_lot_id' => 1, 'state' => 'lost', 'event_id' => 1, 'qta' => 10, 'created_at' => '2026-09-24 19:00:00', 'updated_at' => '2026-09-24 19:00:00'],
            ['id' => 4, 'stock_lot_id' => 2, 'state' => 'at_event', 'event_id' => 2, 'qta' => 2, 'created_at' => '2026-10-27 10:00:00', 'updated_at' => '2026-10-27 10:00:00'],
            ['id' => 5, 'stock_lot_id' => 3, 'state' => 'in_warehouse', 'event_id' => null, 'qta' => 500, 'created_at' => '2026-09-10 11:00:00', 'updated_at' => '2026-09-10 11:00:00'],
            ['id' => 6, 'stock_lot_id' => 4, 'state' => 'lost', 'event_id' => 1, 'qta' => 1, 'created_at' => '2026-09-25 09:00:00', 'updated_at' => '2026-09-25 09:00:00'],
            ['id' => 7, 'stock_lot_id' => 4, 'state' => 'returned_to_supplier', 'event_id' => 1, 'qta' => 5, 'created_at' => '2026-09-28 11:00:00', 'updated_at' => '2026-09-28 11:00:00'],
            ['id' => 8, 'stock_lot_id' => 5, 'state' => 'returned_to_supplier', 'event_id' => 1, 'qta' => 2, 'created_at' => '2026-09-30 11:00:00', 'updated_at' => '2026-09-30 11:00:00'],
            ['id' => 9, 'stock_lot_id' => 6, 'state' => 'at_event', 'event_id' => 2, 'qta' => 1, 'created_at' => '2026-10-27 10:00:00', 'updated_at' => '2026-10-27 10:00:00'],
            ['id' => 10, 'stock_lot_id' => 7, 'state' => 'in_warehouse', 'event_id' => null, 'qta' => 4, 'created_at' => '2026-10-24 10:00:00', 'updated_at' => '2026-10-24 10:00:00'],
        ]);

        // Shipments between the warehouse and the events.
        // Event 1 has a shipment there and one back. Event 2 only has the one there, because it is not finished.
        DB::table('event_shipments')->insert([
            ['id' => 1, 'event_id' => 1, 'carrier_id' => 1, 'tracking_number' => '061202918845', 'shipping_cost' => 185.00],
            ['id' => 2, 'event_id' => 1, 'carrier_id' => 1, 'tracking_number' => '061202937710', 'shipping_cost' => 160.00],
            ['id' => 3, 'event_id' => 2, 'carrier_id' => 2, 'tracking_number' => 'MI26A1026873', 'shipping_cost' => 150.00],
        ]);
    }
}
