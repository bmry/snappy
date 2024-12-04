<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Country extends Model
{
    protected $fillable = ['name', 'code'];

    public function postcodes()
    {
        return $this->hasMany(Postcode::class);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public static function getByCodeWithCache(string $code): ?self
    {
        return Cache::remember("country_{$code}", now()->addHours(1), function () use ($code) {
            return self::byCode($code)->first();
        });
    }
}
