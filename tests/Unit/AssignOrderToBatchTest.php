<?php

namespace Tests\Unit;

use App\Models\Batch;
use App\Models\Hmo;
use App\Models\Order;
use App\Actions\AssignOrderToBatch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Lorisleiva\Actions\Decorators\JobDecorator;

class AssignOrderToBatchTest extends TestCase
{
    use RefreshDatabase;

    protected $batchingObj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->batchingObj = AssignOrderToBatch::make();
    }

    public function test_order_is_assigned_to_correct_batch_based_on_encounter_date()
    {
        $hmo = Hmo::factory()->create(['code' => 'TEST001', 'batching_criteria' => 'encounter_date']);

        $order = Order::factory()->create([
            'hmo_code' => $hmo->code,
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-05-20',
        ]);

        $this->batchingObj->handle($order);

        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo->id,
            'name' => 'Test Provider May 2023',
            'month' => '05',
            'year' => '2023',
        ]);
    }

    public function test_order_is_assigned_to_correct_batch_based_on_submission_date()
    {
        $hmo = Hmo::factory()->create(['code' => 'TEST001', 'batching_criteria' => 'submission_date']);

        $order = Order::factory()->create([
            'hmo_code' => $hmo->code,
            'provider_name' => 'Test Provider',
            'encounter_date' => '2023-06-15',
            'created_at' => '2023-10-20',
        ]);

        $this->batchingObj->handle($order);

        $this->assertDatabaseHas('batches', [
            'hmo_id' => $hmo->id,
            'name' => 'Test Provider Oct 2023',
            'month' => '10',
            'year' => '2023',
        ]);
    }
}
