<?php

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
     * Handle the incoming request to return stores near a given postcode.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(GetNearbyStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $responseData = $this->storeService->getNearbyStores(
                $validatedData['postcode'],
                $validatedData['radius'] ?? 5000,
                $validatedData['delivery'] ?? false,
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
