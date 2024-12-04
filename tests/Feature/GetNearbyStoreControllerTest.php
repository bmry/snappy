<?php

namespace Tests\Feature;

use Database\Seeders\PostcodeSeeder;
use Database\Seeders\StoreSeeder;
use Tests\TestCase;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetNearbyStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the database before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PostcodeSeeder::class);
        $this->seed(StoreSeeder::class);
    }

    /**
     * Test retrieving stores near a given postcode.
     *
     * @return void
     */
    public function testGetStoresNearAPostcode()
    {
        $response = $this->getJson('/api/stores?postcode=SA34NS');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'stores' => [
                '*' => [
                    'id',
                    'name',
                    'location',
                    'status',
                    'type',
                    'max_delivery_distance',
                    'created_at',
                    'updated_at',
                    'distance',
                ]
            ],
        ]);

        $response->assertJsonCount(2, 'stores');
    }

    /**
     * Test retrieving stores near a given postcode.
     *
     * @return void
     */
    public function testGetStoresNearAPostcodeWithSpace()
    {
        $response = $this->getJson('/api/stores?postcode=SA3 4NS');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'stores' => [
                '*' => [
                    'id',
                    'name',
                    'location',
                    'status',
                    'type',
                    'max_delivery_distance',
                    'created_at',
                    'updated_at',
                    'distance',
                ]
            ],
        ]);

    }



    /**
     * Test retrieving stores near a given postcode.
     *
     * @return void
     */
    public function testGetStoresThatCanDeliverToAPostcode()
    {
        $response = $this->getJson('/api/stores?postcode=SA34NS&delivery=true');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'stores' => [
                '*' => [
                    'id',
                    'name',
                    'location',
                    'status',
                    'type',
                    'max_delivery_distance',
                    'created_at',
                    'updated_at',
                    'distance',
                ]
            ],
        ]);

        $response->assertJsonCount(1, 'stores');
    }

    /**
     * Test retrieving stores with an invalid postcode.
     *
     * @return void
     */
    public function testGetStoresWithInvalidPostcode()
    {
        $response = $this->getJson('/api/stores?postcode=INVALID');

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The selected postcode is invalid.',
            'errors' => [
                'postcode' => [
                    'The selected postcode is invalid.',
                ],
            ],
        ]);
    }

    /**
     * Test retrieving stores with a postcode that has no nearby stores.
     *
     * @return void
     */
    public function testGetStoresWithNoNearbyStores()
    {
        $response = $this->getJson('/api/stores?postcode=SA106AA');
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Stores retrieved successfully.',
            'stores' => [],
            'pagination' => [
                'next_cursor' => null,
                'previous_cursor' => null,
            ],
        ]);
    }
}
