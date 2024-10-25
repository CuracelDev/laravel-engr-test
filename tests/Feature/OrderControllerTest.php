<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Hmo;
use App\Models\Provider;

class OrderControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;
   

    /** @test */
    public function it_can_submit_order()
    {
      
        $hmo = Hmo::factory()->create(['code' => 'HMO-A']);
        $provider = Provider::factory()->create(['id' => 1, 'name' => 'ProviderA']);

        $orderData = [
            'encounter_date' => '2024-10-20',
            'provider_id' => $provider->id,
            'hmo_code' => $hmo->code,
            'total' => 150.00,
            'items' => [
                [
                    'item' => 'Item A',
                    'unit_price' => 50.00,
                    'qty' => 2,
                    'subtotal' => 100.00,
                ],
                [
                    'item' => 'Item B',
                    'unit_price' => 50.00,
                    'qty' => 1,
                    'subtotal' => 50.00,
                ]
            ]
        ];

        
        $response = $this->post('/submitOrder', $orderData); 

       
        $response->assertStatus(302); 
        $response->assertSessionHas('success', 'Order submitted successfully!');

       
    }

    /** @test */
    /* public function it_fails_to_submit_order_with_invalid_data()
    {
       
        $hmo = Hmo::factory()->create(['code' => 'HMO-A']);
        $provider = Provider::factory()->create(['id' => 1, 'name' => 'Provider A']);

       
        $invalidOrderData = [
            'encounter_date' => '', 
            'provider_id' => $provider->id,
            'hmo_code' => $hmo->code,
            'total' => 100,
            'items' => [
                [
                    'item' => 'Item A',
                    'unit_price' => 50.00,
                    'qty' => 2,
                    'subtotal' => 100.00,
                ],
            ],
        ];

       
        $response = $this->withoutMiddleware()->post('/submitOrder', $invalidOrderData);

       
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['encounter_date', 'total', 'items']);
    }
  */
}
