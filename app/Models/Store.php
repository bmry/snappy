<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Store extends Model
{
    use HasSpatial;

    protected $fillable = [
        'name',
        'location',
        'status',
        'type',
        'max_delivery_distance',
    ];

    protected $casts = [
        'location' => Point::class,
        'max_delivery_distance' => 'float',
    ];
}
