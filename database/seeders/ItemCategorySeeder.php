<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the cities table
        DB::table('item_categories')->truncate();

        $categories = [
            'Construction Materials',
            'Tools and Equipment',
            'Electrical Supplies',
            'Plumbing Supplies',
            'Fixing and Fastening Items',
            'Finishing Materials',
            'Miscellaneous Items',
        ];

        foreach ($categories as $category) {
            ItemCategory::factory()->create(['name' => $category]);
        }
    }
}
