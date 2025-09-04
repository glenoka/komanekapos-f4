<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MigrationSalesItems extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sales_details')->truncate();

        echo "Starting migration of Sales Items...\n";

        DB::table('sale_items_old')->orderBy('id')->chunkById(1000, function ($oldSalesItem) {
            foreach ($oldSalesItem as $old) {
                DB::table('sales_details')->insert([
                    'sale_id'        => $old->sale_id,
                    'product_id' => $old->product_id ,
                    'product_name'    => $old->product_name,
                    'quantity'      => $old->quantity,
                    'unit'       => 'porsi',
                    'unit_price'     => $this->checkPrice($old->product_id),
                    'original_price'     =>$this->checkPrice($old->product_id)* $old->quantity,
                    'discount'       => 0,
                    'discount_amount' => 0,
                    'total_price'   => $old->gross_total,
                    'is_complimentary' => false,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
            echo "Migrated " . count($oldSalesItem) . " Sales.\n";
            sleep(1);
        });


        echo "Migration Sales completed.\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function checkPrice($id)
    {
        $price = DB::table('products')->where('id', $id)->first();
        if ($price) {
            return $price->price ?? 0;
        }
        return 0;
    }
}
