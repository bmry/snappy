<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of a store with valid data.
     *
     * @return void
     */
    public function testCreateStoreSuccess()
    {
        $data = [
            'name' => 'Test Store',
            'location' => [-0.1276, 51.5074],
            'status' => 'open',
            'type' => 'shop',
            'max_delivery_distance' => 5000,
        ];

        $response = $this->postJson('/api/stores', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Store created successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'store' => [
                    'id',
                    'name',
                    'location' => [
                        'type',
                        'coordinates',
                    ],
                    'status',
                    'type',
                    'max_delivery_distance',
                ],
            ]);

        $this->assertDatabaseHas('stores', [
            'name' => 'Test Store',
            'status' => 'open',
            'type' => 'shop',
            'max_delivery_distance' => 5000,
        ]);
    }

    /**
     * Test invalid data (missing required fields).
     *
     * @return void
     */
    /**
     * Test invalid data (missing required fields).
     *
     * @return void
     */
    public function testCreateStoreValidationFailure()
    {
        $data = [
            'location' => [-0.1276, 51.5074],
            'status' => 'dummy',
            'type' => 'dummy',
            'max_delivery_distance' => 5000,
        ];

        $response = $this->postJson('/api/stores', $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Store name is required. (and 2 more errors)',
                'errors' => [
                    'name' => [
                        'Store name is required.'
                    ],
                    'status' => [
                        'Store status must be either "open" or "closed".'
                    ],
                    'type' => [
                        'Store type must be either "takeaway", "shop", or "restaurant".'
                    ]
                ]
            ]);
    }


    /**
     * Test invalid location data (coordinates).
     *
     * @return void
     */
    public function testCreateStoreInvalidLocation()
    {
        $data = [
            "name" => "Tesco",
            "location" => ["da", "do"],
            "status" => "open",
            "type" => "shop",
            "max_delivery_distance" => 100
        ];

        $response = $this->postJson('/api/stores', $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Both latitude and longitude must be numeric values. (and 2 more errors)',
                'errors' => [
                    'location.0' => [
                        'Both latitude and longitude must be numeric values.'
                    ],
                    'location.1' => [
                        'Both latitude and longitude must be numeric values.'
                    ],
                ]
            ]);
    }
}
