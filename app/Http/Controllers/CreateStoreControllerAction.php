<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use MatanYadaev\EloquentSpatial\Objects\Point;



/**
 * @OA\Info(
 *     title="Snappy App",
 *     version="1.0.0",
 *     description="Store management API "
 * )
 */



class CreateStoreControllerAction
{
    /**
     * @OA\Post(
     *     path="/api/stores",
     *     summary="Create a new store",
     *     tags={"Store"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "location", "status", "type", "max_delivery_distance"},
     *             @OA\Property(property="name", type="string", description="Name of the store"),
     *             @OA\Property(property="location", type="array", @OA\Items(type="number"), description="Geographical location of the store [longitude, latitude]"),
     *             @OA\Property(property="status", type="string", description="Status of the store"),
     *             @OA\Property(property="type", type="string", description="Type of the store"),
     *             @OA\Property(property="max_delivery_distance", type="integer", description="Maximum delivery distance in meters")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Store created successfully."),
     *             @OA\Property(property="store", type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Store Name"),
     *                 @OA\Property(property="location", type="array", @OA\Items(type="number"), example={-0.1276, 51.5074}),
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="type", type="string", example="grocery"),
     *                 @OA\Property(property="max_delivery_distance", type="integer", example=5000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid input")
     *         )
     *     )
     * )
     */
    public function __invoke(StoreRequest $request): JsonResponse
    {
        $store = new Store();
        $store->name = $request->name;
        $store->location = new Point($request->location[0], $request->location[1]);
        $store->status = $request->status;
        $store->type = $request->type;
        $store->max_delivery_distance = $request->max_delivery_distance;
        $store->save();

        return response()->json([
            'message' => 'Store created successfully.',
            'store' => $store
        ], 201);
    }
}
