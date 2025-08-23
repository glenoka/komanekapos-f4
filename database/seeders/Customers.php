<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Customers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers_old')->orderBy('id')->chunk(1000, function ($oldCustomers) {
            $insertData = [];

            foreach ($oldCustomers as $old) {
                $insertData[] = [
                    'id' => $old->id,
                    'name' => $old->name,
                    'status' => 'regular',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert batch
            DB::table('customers')->insert($insertData);

            // Jeda 1 detik biar ringan
            sleep(1);
        });
    }
}
