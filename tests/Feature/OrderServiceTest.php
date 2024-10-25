<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use App\Jobs\BatchedOrdersJob;
use Mockery;

class OrderServiceTest extends TestCase
{
    use WithFaker;

    protected $orderRepositoryMock;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
      
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderService = new OrderService($this->orderRepositoryMock);
    }

    /** @test */
    public function it_can_add_order_and_dispatch_job()
    {
       
        $orderData = [
            'hmo_code' => 'HMO-A',
            'provider_id' => 1,
            'encounter_date' => '2024-10-20',
            'total' => 150.00,
            'items' => [
                [
                    'item' => 'Item 1',
                    'unit_price' => 10.00,
                    'qty' => 2,
                    'subtotal' => 20.00,
                ],
                [
                    'item' => 'Item 2',
                    'unit_price' => 15.00,
                    'qty' => 1,
                    'subtotal' => 15.00,
                ]
            ]
        ];

       
        $this->orderRepositoryMock->shouldReceive('addOrder')
            ->once()
            ->with($orderData)
            ->andReturn([
                'order' => (object)['id' => 1, 'batch_id' => 1],
                'hmo_email' => 'test@example.com',
                'batch_name' => 'ProviderName102024', 
            ]);

       
        Queue::fake();

      
        $this->orderService->addOrder($orderData);

      
        Queue::assertPushed(BatchedOrdersJob::class, function ($job) {
            return $job->orders->id === 1 &&
                   $job->batchName === 'ProviderName102024' && 
                   $job->hmoEmail === 'test@example.com';
        });
    }

    /** @test */
    public function it_can_get_orders_by_batch()
    {
       
        $hmoCode = 'HMO-A';
        $providerId = 1;
        $searchDate = '2024-10-20';
        $orders = collect([
            (object)['id' => 1, 'total' => 150.00, 'items' => []],
            (object)['id' => 2, 'total' => 100.00, 'items' => []],
        ]);

       
        $this->orderRepositoryMock->shouldReceive('getOrdersByBatch')
            ->once()
            ->with($hmoCode, $providerId, $searchDate)
            ->andReturn($orders);

        
        $result = $this->orderService->getOrdersByBatch($hmoCode, $providerId, $searchDate);

       
        $this->assertEquals($orders, $result);
    }
}
