<?php

namespace Tests\Feature;

use App\Events\CreateOrder;
use App\Models\Hmo;
use App\Notifications\OrderCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubmitOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test HMO
        Hmo::factory()->create(['code' => 'TEST001']);
    }

    public function test_api_successfully_receives_data()
    {
        $orderData = $this->getOrderData('2023-07-20');

        $response = $this->postJson(route('order.store'), $orderData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Order submitted successfully']);
    }

    public function test_api_response_format_and_status_code_for_successful_request()
    {
        $orderData = $this->getOrderData('2023-07-20');

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
    }

    public function test_api_error_responses_for_invalid_requests()
    {
        // Test missing required field
        $response = $this->postJson(route('order.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hmo_code', 'provider_name', 'encounter_date', 'items']);

        // Test invalid HMO code
        $response = $this->postJson(route('order.store'), [
            'hmo_code' => 'INVALID',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-07-20',
            'items' => [['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2]],
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hmo_code']);

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
    }

    public function test_order_is_assigned_to_correct_batch_based_on_encounter_date()
    {
        $hmo = Hmo::factory()->create(['code' => 'TEST002', 'batching_criteria' => 'encounter_date']);

        $orderData = $this->getOrderData('2023-05-20', $hmo->code);

        $this->postJson(route('order.store'), $orderData);

        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo->id,
            'name' => 'Test Provider May 2023',
            'month' => '05',
            'year' => '2023',
        ]);
    }

    public function test_order_is_assigned_to_correct_batch_based_on_submission_date()
    {
        $hmo = Hmo::factory()->create(['code' => 'TEST003', 'batching_criteria' => 'submission_date']);

        $orderData = $this->getOrderData('2023-06-15', $hmo->code);

        $this->postJson(route('order.store'), $orderData);

        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo->id,
            'name' => 'Test Provider ' . now()->format('M Y'),
        ]);
    }

    public function test_it_dispatches_order_created_event()
    {
        Notification::fake();

        Event::fake([
            CreateOrder::class,
        ]);

        $orderData = $this->getOrderData('2023-06-15');

        $response = $this->post(route('order.store'), $orderData);

        $response->assertStatus(201);

        Event::assertDispatched(CreateOrder::class, 1);
    }

    public function test_it_sends_notifications()
    {
        Notification::fake();

        $data = $this->getOrderData('2023-01-10');
        $response = $this->post(route('order.store'), $data);

        $response->assertStatus(201);

        Notification::assertSentTo(
            Hmo::where('code', $data['hmo_code'])->first(),
            OrderCreated::class
        );
    }

    private function getOrderData($encounterDate, $hmo_code = 'TEST001')
    {
        return [
            'hmo_code' => $hmo_code,
            'provider_name' => 'Test Provider',
            'encounter_date' => $encounterDate,
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10, 'quantity' => 2, 'sub_total' => 20],
                ['name' => 'Item 2', 'unit_price' => 16, 'quantity' => 5, 'sub_total' => 80],
                ['name' => 'Item 3', 'unit_price' => 6, 'quantity' => 12, 'sub_total' => 72],
            ],
        ];
    }
}
