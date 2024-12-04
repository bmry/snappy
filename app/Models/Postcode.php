<?php

declare(strict_types=1);


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = ['postcode', 'latitude', 'longitude', 'country_id'];

}
