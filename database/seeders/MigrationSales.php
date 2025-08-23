<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MigrationSales extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sales')->truncate();

        echo "Starting migration of Items...\n";

        DB::table('sales_old')->orderBy('id')->chunkById(500, function ($oldSales) {
            foreach ($oldSales as $old) {
                DB::table('sales')->insert([
                    'id'             => $old->id,
                    'invoice_number' => "KBM-" . $old->id,
                    'customer_id'    => $old->customer_id ?? null,
                    'sale_date'      => $old->date,
                    'table_no'       => null,
                    'sales_type'     => 'regular',
                    'order_type'     => 'dine_in',
                    'subtotal'       => $old->inv_total,
                    'tax_amount'     => $old->total_tax ?? 0,
                    'discount_amount' => $old->total_discount ?? 0,
                    'total_amount'   => $old->total ?? 0,
                    'payment_method' => 'room_charge',
                    'total_items'    => $old->total_items ?? 0,
                    'activity'       => $old->activity,
                    'user_id'        => 1,
                    'notes'          => null,
                    'status'         => 'completed',
                    'slug'           => Str::random(10),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
            echo "Migrated " . count($oldSales) . " Sales.\n";
            sleep(1);
        });


        echo "Migration Sales completed.\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
