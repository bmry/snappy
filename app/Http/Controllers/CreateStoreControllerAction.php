<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;

class CreateStoreControllerAction
{
    /**
     * Handle the incoming request to add a new store.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function __invoke(StoreRequest $request): JsonResponse
    {
        // Validation is handled by StoreRequest
        // If the validation passes, the code below will execute

        // Create a new store instance with validated data
        $store = new Store();
        $store->name = $request->name;
        $store->location = new Point($request->location[0], $request->location[1]);
        $store->status = $request->status;
        $store->type = $request->type;
        $store->max_delivery_distance = $request->max_delivery_distance;
        $store->save();

        // Return a JSON response with the created store data
        return response()->json([
            'message' => 'Store created successfully.',
            'store' => $store
        ], 201);
    }
}
