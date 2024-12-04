<?php

declare(strict_types=1);

namespace App\Http\Controllers;

    use App\Http\Requests\GetNearbyStoreRequest;
    use App\Services\StoreService;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;

class GetNearbyStoreControllerAction
{
    protected $storeService;

    /**
     * Constructor for injecting the StoreService.
     *
     * @param StoreService $storeService
     */
    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }


    /**
     * @OA\Get(
     *     path="/api/stores",
     *     summary="Get stores near a given postcode",
     *     description="Retrieve a list of stores near a given postcode with optional delivery information.",
     *     operationId="getStoresNearby",
     *     tags={"Store"},
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         required=true,
     *         description="The postcode of the location to find nearby stores",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="delivery",
     *         in="query",
     *         required=false,
     *         description="Whether to filter stores that offer delivery (true/false)",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stores retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Stores retrieved successfully."),
     *             @OA\Property(
     *                 property="stores",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", description="Store ID"),
     *                     @OA\Property(property="name", type="string", description="Store name"),
     *                     @OA\Property(
     *                         property="location",
     *                         type="object",
     *                         @OA\Property(property="type", type="string", example="Point"),
     *                         @OA\Property(
     *                             property="coordinates",
     *                             type="array",
     *                             @OA\Items(type="number", format="float", example=-4.006499)
     *                         )
     *                     ),
     *                     @OA\Property(property="status", type="string", description="Store status (e.g., open/closed)"),
     *                     @OA\Property(property="type", type="string", description="Store type (e.g., shop)"),
     *                     @OA\Property(property="max_delivery_distance", type="integer", description="Maximum delivery distance in meters"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Store creation timestamp"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Store update timestamp"),
     *                     @OA\Property(property="distance", type="integer", description="Distance from the given location in meters")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="next_cursor", type="string", nullable=true, description="Next page cursor, if available"),
     *                 @OA\Property(property="previous_cursor", type="string", nullable=true, description="Previous page cursor, if available")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid postcode or query parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid postcode or delivery filter.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No stores found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No stores found near the given location")
     *         )
     *     ),
     * )
     */
    public function __invoke(GetNearbyStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $responseData = $this->storeService->getNearbyStores(
                $validatedData['postcode'],
                $validatedData['radius'] ?? StoreService::ASSUMED_CLOSENESS_RADIUS,
                $validatedData['delivery'] ?? 'false',
                $validatedData['per_page'] ?? 10
            );

            return response()->json($responseData);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
