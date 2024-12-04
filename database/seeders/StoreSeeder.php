<?php

namespace Database\Seeders;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreSeeder extends Seeder
{
    public function run()
    {
       Store::create([
           'name' => "Tesco",
           'status' => 'open',
           'type' => 'shop',
           'location' => new Point(51.5706990, -4.0064990),
           'max_delivery_distance' => 500,
        ]);

        Store::create([
            'name' => "Walmart",
            'status' => 'open',
            'type' => 'shop',
            'location' => new Point(51.5732220, -4.0056440),
            'max_delivery_distance' => 100,
        ]);

    }
}
