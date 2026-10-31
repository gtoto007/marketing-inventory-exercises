<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The catalog: categories, items, suppliers and the prices of each supplier.
 */
class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the 5 categories.
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Materiale stampato'],
            ['id' => 2, 'name' => 'Segnaletica'],
            ['id' => 3, 'name' => 'Gadget promozionali'],
            ['id' => 4, 'name' => 'Arredamento'],
            ['id' => 5, 'name' => 'Attrezzatura audio e video'],
        ]);

        // Create 3 items for each category.
        // We only rent furniture and audio/video, so they have no minimum stock.
        DB::table('items')->insert([
            ['id' => 1, 'category_id' => 1, 'title' => 'Volantino promozionale A5', 'description' => 'Volantino A5 fronte-retro con logo aziendale.', 'photo_url' => null, 'ean' => '8001234500016', 'min_stock_quantity' => 1000, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 2, 'category_id' => 1, 'title' => 'Brochure aziendale a tre ante', 'description' => 'Brochure A4 a tre ante di presentazione dell\'azienda.', 'photo_url' => null, 'ean' => '8001234500023', 'min_stock_quantity' => 500, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 3, 'category_id' => 1, 'title' => 'Catalogo prodotti 2026', 'description' => 'Catalogo prodotti di 48 pagine, formato A4 in brossura.', 'photo_url' => null, 'ean' => '8001234500030', 'min_stock_quantity' => 200, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],

            ['id' => 4, 'category_id' => 2, 'title' => 'Roll-up con logo 85x200', 'description' => 'Roll-up avvolgibile stampato con il logo aziendale.', 'photo_url' => null, 'ean' => '8001234500047', 'min_stock_quantity' => 2, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 5, 'category_id' => 2, 'title' => 'Striscione in PVC con logo 300x100', 'description' => 'Striscione in PVC da esterno con logo aziendale e occhielli.', 'photo_url' => null, 'ean' => '8001234500054', 'min_stock_quantity' => 1, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 6, 'category_id' => 2, 'title' => 'Adesivo da pavimento con logo 60cm', 'description' => 'Adesivo antiscivolo da pavimento con logo aziendale.', 'photo_url' => null, 'ean' => '8001234500061', 'min_stock_quantity' => 10, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],

            ['id' => 7, 'category_id' => 3, 'title' => 'Penna a sfera con logo', 'description' => 'Penna a sfera con logo aziendale stampato.', 'photo_url' => null, 'ean' => '8001234500078', 'min_stock_quantity' => 300, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 8, 'category_id' => 3, 'title' => 'Shopper in cotone con logo', 'description' => 'Shopper in cotone con logo aziendale stampato.', 'photo_url' => null, 'ean' => '8001234500085', 'min_stock_quantity' => 100, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 9, 'category_id' => 3, 'title' => 'Laccetto portabadge con logo', 'description' => 'Laccetto in poliestere con logo aziendale stampato.', 'photo_url' => null, 'ean' => '8001234500092', 'min_stock_quantity' => 200, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],

            ['id' => 10, 'category_id' => 4, 'title' => 'Sgabello da bar', 'description' => 'Sgabello da bar bianco, altezza 75cm.', 'photo_url' => null, 'ean' => '8001234500108', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 11, 'category_id' => 4, 'title' => 'Tavolo pieghevole 180x80', 'description' => 'Tavolo pieghevole con copertura elastica bianca.', 'photo_url' => null, 'ean' => '8001234500115', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 12, 'category_id' => 4, 'title' => 'Divano lounge', 'description' => 'Divano lounge a due posti per l\'area meeting.', 'photo_url' => null, 'ean' => '8001234500122', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],

            ['id' => 13, 'category_id' => 5, 'title' => 'Monitor LED 55"', 'description' => 'Display professionale 4K da 55 pollici.', 'photo_url' => null, 'ean' => '8001234500139', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 14, 'category_id' => 5, 'title' => 'Supporto da terra per monitor', 'description' => 'Supporto da terra per display da 40 a 65 pollici.', 'photo_url' => null, 'ean' => '8001234500146', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 15, 'category_id' => 5, 'title' => 'Microfono wireless', 'description' => 'Microfono wireless a mano con ricevitore.', 'photo_url' => null, 'ean' => '8001234500153', 'min_stock_quantity' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
        ]);

        // Create 2 suppliers. The companies are made up and the contacts are fake.
        DB::table('suppliers')->insert([
            ['id' => 1, 'name' => 'Tipografia Lombarda Srl', 'phone' => '+39 02 3926 1184', 'email' => 'orders@tipografialombarda.example.com', 'vat_number' => 'IT05218470963', 'address_line' => 'Via Gallarate 212', 'city' => 'Milano', 'postal_code' => '20151', 'country' => 'IT', 'website' => 'https://www.tipografialombarda.example.com', 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
            ['id' => 2, 'name' => 'Arredo Eventi Noleggi Srl', 'phone' => '+39 02 9348 7710', 'email' => 'rentals@arredoeventi.example.com', 'vat_number' => 'IT07731450968', 'address_line' => 'Via Risorgimento 9', 'city' => 'Rho', 'postal_code' => '20017', 'country' => 'IT', 'website' => null, 'created_at' => '2026-01-05 09:00:00', 'updated_at' => '2026-01-05 09:00:00'],
        ]);

        // Set what each supplier offers and at what price.
        // Supplier 1 sells print, signage and gadgets. Supplier 2 rents furniture and audio/video.
        DB::table('item_supplier')->insert([
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>1, 'sku' => 'TL-FLY-A5', 'purchase_price' => 0.08, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>2, 'sku' => 'TL-BRO-3F', 'purchase_price' => 0.35, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>3, 'sku' => 'TL-CAT-26', 'purchase_price' => 3.20, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>4, 'sku' => 'TL-RU-85200', 'purchase_price' => 79.00, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>5, 'sku' => 'TL-PVC-31', 'purchase_price' => 45.00, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>6, 'sku' => 'TL-FS-60', 'purchase_price' => 6.50, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>7, 'sku' => 'TL-PEN-01', 'purchase_price' => 0.45, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>8, 'sku' => 'TL-TOTE-01', 'purchase_price' => 1.80, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],
            ['supplier_id' => 1, 'type' => 'PURCHASABLE', 'item_id' =>9, 'sku' => 'TL-LAN-20', 'purchase_price' => 0.55, 'rental_daily_rate' => null, 'daily_late_fee_per_unit' => 0, 'non_return_fee' => 0],

            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>10, 'sku' => 'AEN-STL-75', 'purchase_price' => null, 'rental_daily_rate' => 6.50, 'daily_late_fee_per_unit' => 3.00, 'non_return_fee' => 85.00],
            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>11, 'sku' => 'AEN-TBL-18', 'purchase_price' => null, 'rental_daily_rate' => 9.00, 'daily_late_fee_per_unit' => 4.00, 'non_return_fee' => 120.00],
            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>12, 'sku' => 'AEN-SOF-02', 'purchase_price' => null, 'rental_daily_rate' => 45.00, 'daily_late_fee_per_unit' => 20.00, 'non_return_fee' => 900.00],
            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>13, 'sku' => 'AEN-MON-55', 'purchase_price' => null, 'rental_daily_rate' => 60.00, 'daily_late_fee_per_unit' => 30.00, 'non_return_fee' => 700.00],
            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>14, 'sku' => 'AEN-STD-01', 'purchase_price' => null, 'rental_daily_rate' => 15.00, 'daily_late_fee_per_unit' => 7.00, 'non_return_fee' => 180.00],
            ['supplier_id' => 2, 'type' => 'RENTABLE', 'item_id' =>15, 'sku' => 'AEN-MIC-WL', 'purchase_price' => null, 'rental_daily_rate' => 18.00, 'daily_late_fee_per_unit' => 9.00, 'non_return_fee' => 250.00],
        ]);
    }
}