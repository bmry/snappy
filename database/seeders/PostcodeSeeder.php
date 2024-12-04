<?php

namespace Database\Seeders;

use App\Models\Postcode;
use Illuminate\Database\Seeder;

class PostcodeSeeder extends Seeder
{
    public function run(): void
    {

        Postcode::create([
            'postcode' => 'SA34NS',
            'longitude' => -4.0064990,
            'latitude' => 51.5706990,
            'country_id' => 1,
        ]);

        Postcode::create([
            'postcode' => 'SA34NA',
            'longitude' => -4.0056440,
            'latitude' => 51.5732220,
            'country_id' => 1,
        ]);

        Postcode::create([
            'postcode' => 'SA106AA',
            'longitude' => -3.8459750,
            'latitude' => 51.6557310,
            'country_id' => 1,
        ]);
    }
}
