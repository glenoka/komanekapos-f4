<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MigrationProduct extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('products')->truncate();

    echo "Starting migration of products...\n";

    DB::table('products_old')->orderBy('id')->chunk(1000, function ($oldProducts) {
        $insertData = [];
        $usedSlugs = DB::table('products')->pluck('slug')->toArray(); // slug yg sudah ada di DB

        foreach ($oldProducts as $old) {
            $slug = $this->generateUniqueSlug($old->name, $usedSlugs);
            $usedSlugs[] = $slug; // simpan slug supaya batch ini juga ter-cover

            $insertData[] = [
                'id' => $old->id,
                'name' => $old->name,
                'slug' => $slug,
                'type' => $old->cat_id === '2' ? 'beverage' : 'food',
                'category_id' => $old->category_id ?? null,
                'price' => $old->price ?? 0,
                'status' => 'active',
            ];
        }

        DB::table('products')->insert($insertData);
        echo "Migrated " . count($insertData) . " products.\n";
        sleep(1);
    });

    echo "Migration products completed.\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

private function generateUniqueSlug($name, &$usedSlugs)
{
    $slug = Str::slug($name);
    $originalSlug = $slug;
    $counter = 1;

    // cek baik di DB (sudah dimasukkan sebelumnya) maupun array batch ini
    while (in_array($slug, $usedSlugs)) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

}
