<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNearbyRequest;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;

class FindStoreByPostcodeControllerAction
{
    /**
     * Handle the incoming request to return stores near a given postcode.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $postcode = $request->get('postcode');

        $cacheKey = 'stores_near_' . $postcode;

        $cachedStores = Cache::get($cacheKey);

//        if ($cachedStores) {
//            return response()->json($cachedStores);
//        }

        $postcodeRecord = Postcode::where('postcode', $postcode)->first();

        if (!$postcodeRecord) {
            return response()->json([
                'message' => 'Postcode not found.',
            ], 404);
        }

        $latitude = $postcodeRecord->latitude;
        $longitude = $postcodeRecord->longitude;
        $location = new Point($latitude, $longitude);

        //Default to 5km or 5000m radius if radius is not provided by user.
        $radius = $request->get('radius', 5000);

        $stores = Store::query()
            ->withDistanceSphere('location', $location)
            ->whereDistanceSphere('location', $location, '<=', $radius)
            ->orderBy('distance')
            ->cursorPaginate(10);

        $responseData = [
            'message' => 'Stores retrieved successfully.',
            'stores' => $stores->items(),
            'pagination' => [
                'next_cursor' => $stores->nextCursor(),
                'previous_cursor' => $stores->previousCursor(),
            ]
        ];

        Cache::put($cacheKey, $responseData, now()->addMinutes(10));

        return response()->json($responseData);
    }
}
