<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MigrationCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories_old')->orderBy('id')->chunk(1000, function ($oldCategories) {
            $insertData = [];

            foreach ($oldCategories as $old) {
                $insertData[] = [
                    'name' => $old->name,
                    'slug' => Str::slug($old->name), // generate slug
                    'sort_order' => $old->numsort ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // insert batch sekali untuk 1000 row
            DB::table('categories')->insert($insertData);

            // jeda biar server ga berat
            sleep(1);
        });
    }
}
