<?php

namespace Tests\Feature;

use App\Actions\AssignOrderToBatch;
use App\Models\Hmo;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubmitOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        // Create a test HMO
        Hmo::factory()->create(['code' => 'TEST001']);
    }

    public function test_api_successfully_receives_data()
    {
        $orderData = [
            'hmo_code' => 'TEST001',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-07-20',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2],
                ['name' => 'Item 2', 'unit_price' => 12, 'quantity' => 4],
            ],
        ];

        $response = $this->postJson(route('order.store'), $orderData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Order submitted successfully']);

        AssignOrderToBatch::assertPushedOn('order_batching');
    }

    public function test_api_response_format_and_status_code_for_successful_request()
    {
        $orderData = [
            'hmo_code' => 'TEST001',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-07-20',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2],
                ['name' => 'Item 2', 'unit_price' => 16, 'quantity' => 5],
                ['name' => 'Item 3', 'unit_price' => 6, 'quantity' => 12],
            ],
        ];

        $response = $this->postJson(route('order.store'), $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'hmo_code',
                    'provider_name',
                    'encounter_date',
                    'total_price',
                    'items' => [
                        '*' => ['name', 'unit_price', 'quantity']
                    ]
                ]
            ]);

        $response->assertJsonCount(3, 'data.items');
        AssignOrderToBatch::assertPushedOn('order_batching');
    }

    public function test_api_error_responses_for_invalid_requests()
    {
        // Test missing required field
        $response = $this->postJson(route('order.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hmo_code', 'provider_name', 'encounter_date', 'items']);

        AssignOrderToBatch::assertNotPushed();

        // Test invalid HMO code
        $response = $this->postJson(route('order.store'), [
            'hmo_code' => 'INVALID',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-07-20',
            'items' => [['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2]],
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hmo_code']);

        AssignOrderToBatch::assertNotPushed();

        // Test future encounter date
        $futureDate = now()->addDays(1)->format('Y-m-d');
        $response = $this->postJson(route('order.store'), [
            'hmo_code' => 'TEST001',
            'provider_name' => 'Test Provider',
            'encounter_date' => $futureDate,
            'items' => [['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2]],
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['encounter_date']);

        AssignOrderToBatch::assertNotPushed();
    }
}
