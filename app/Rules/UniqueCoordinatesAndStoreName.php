<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Store;
use Illuminate\Contracts\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UniqueCoordinatesAndStoreName implements Rule
{
    public function passes($attribute, $value)
    {

        $location = request('location');
        $name = request('name');

        $location = request('location');
        if (!is_numeric($location[0]) || !is_numeric($location[1])) {
            return false;
        }


        $location = new Point($location[0], $location[1]);

        $store = Store::query()
            ->withDistanceSphere('location', $location)
            ->where('name', $name )
            ->orderBy('distance');

        return !$store->exists();
    }

    public function message()
    {
        return 'A store with the same name and coordinates already exists.';
    }
}
