<?php

namespace Tests\Feature\Auth;

use App\Models\Hmo;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_order_screen_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_create_order_is_validates_input(): void
    {
        $response = $this->post('/provider/order', []);

        $this->assertEquals(302, $response->getStatusCode());
    }


    public function test_can_create_new_order(): void
    {
        $response = $this->post('/provider/order', [
            'hmo_code' => 'HMO-A',
            'provider_name' => "Provider First",
            'encounter_date' => "2024-09-09",
            'amount' => 2000,
            'items' => [
                'title' => "Item One",
                'unit_price' => 20,
                'quantity' => 2,
                'sub_total' => 40
            ]
        ]);

        $response->assertRedirect(route('home.submit.order', absolute: false));
    }

    public function test_can_list_hmos(): void
    {
        $user = User::factory()->create();
        Hmo::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertInertia(function ($page) {
            $page->has('hmos', 1);
        });
    }

    public function test_can_list_hmo_orders(): void
    {
        $user = User::factory()->create();
        $hmo = Hmo::factory()->create();
        Order::factory()
            ->state([
                'hmo_id' => $hmo->id,
            ])
            ->create();

        $response = $this->actingAs($user)->get('/dashboard/orders/'.$hmo->id);

        $response->assertInertia(function ($page) {
            $page->has('orders.data', 1);
        });
    }
}

