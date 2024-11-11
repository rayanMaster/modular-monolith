<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the cities table
        DB::table('cities')->truncate();

        $cities = [
            'Freetown',
            'Bo',
            'Kenema',
            'Makeni',
            'Koidu',
            'Lunsar',
            'Port Loko',
            'Kabala',
            'Magburaka',
            'Pujehun',
            'Kailahun',
            'Moyamba',
            'Bonthe',
            'Masiaka',
            'Segbwema',
            'Kambia',
            'Moyamba',
            'Pendembu',
            'Binkolo',
            'Rokupr',
        ];

        foreach ($cities as $city) {
            City::factory()->create([
                'name' => $city,
                'status' => StatusEnum::Active,
            ]);
        }
    }
}
