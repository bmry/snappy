<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Contracts\Pagination\CursorPaginator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\Cache;

class StoreService
{
    const ASSUMED_CLOSENESS_RADIUS = 5000;
    /**
     * Get nearby stores based on postcode and radius.
     *
     * @param string $postcode
     * @param int $radius
     * @param bool $delivery
     * @param int $perPage
     * @return array
     */
    public function getNearbyStores(string $postcode, int $radius, string $delivery = 'false', int $perPage = 10): array
    {
        $cacheKey = 'stores_near_' . $postcode;

        $cachedStores = Cache::get($cacheKey);
        if ($cachedStores) {
            return $cachedStores;
        }

        $postcodeRecord = Postcode::where('postcode', $postcode)->first();

        if (!$postcodeRecord) {
            throw new \Exception('Postcode not found.');
        }

        $location = new Point(floatval($postcodeRecord->latitude), floatval($postcodeRecord->longitude));

        $stores = $this->fetchStores($location, $radius, $delivery, $perPage);

        $responseData = [
            'message' => 'Stores retrieved successfully.',
            'stores' => $stores->items(),
            'pagination' => [
                'next_cursor' => $stores->nextCursor(),
                'previous_cursor' => $stores->previousCursor(),
            ]
        ];

        Cache::put($cacheKey, $responseData, now()->addMinutes(1));

        return $responseData;
    }

    /**
     * Fetch stores based on location, radius, and delivery options.
     *
     * @param Point $location
     * @param int $radius
     * @param bool $delivery
     * @param int $perPage
     * @return CursorPaginator
     */
    protected function fetchStores(Point $location, int $radius, string $delivery, int $perPage): CursorPaginator
    {
        if ('true' === $delivery) {

            return Store::query()
                ->withDistanceSphere('location', $location)
                ->whereRaw('ST_Distance_Sphere(location, ST_GeomFromText(?, 0)) <= max_delivery_distance', [
                    $location->toWkt()
                ])
                ->orderBy('distance')
                ->cursorPaginate($perPage);
        }

        return Store::query()
            ->withDistanceSphere('location', $location)
            ->whereDistanceSphere('location', $location, '<=', $radius)
            ->orderBy('distance')
            ->cursorPaginate($perPage);
    }
}
