<?php

namespace Tests\Feature\Actions;

use App\Enums\PriorityLevel;
use App\Enums\Specialty;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmitClaimTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_submit_claim_successfully()
    {
        Mail::fake();

        $insurer = Insurer::factory()->create();

        $data = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Test Provider',
            'specialty' => Specialty::CARDIOLOGY,
            'priority_level' => PriorityLevel::LEVEL_1,
            'encounter_date' => '2024-01-01',
            'items' => [
                [
                    'name' => 'Test Item 1',
                    'unit_price' => 100.50,
                    'quantity' => 2,
                ],
                [
                    'name' => 'Test Item 2',
                    'unit_price' => 50.25,
                    'quantity' => 1,
                ],
            ],
        ];

        $this->postJson(route('claims.store'), $data)->assertSuccessful();

        $this->assertDatabaseHas(Claim::class, [
            'insurer_id' => $insurer->id,
            'provider_name' => 'Test Provider',
            'total_amount' => 251.25, // 100.50 * 2 + 50.25 * 1
        ]);

        $this->assertDatabaseCount(ClaimItem::class, 2);
    }

    #[Test]
    public function it_fails_with_invalid_insurer_code()
    {
        $data = [
            'insurer_code' => 'INVALID',
            'provider_name' => 'Test Provider',
            'specialty' => Specialty::CARDIOLOGY,
            'priority_level' => PriorityLevel::LEVEL_1,
            'encounter_date' => '2024-01-01',
            'items' => [
                [
                    'name' => 'Test Item 1',
                    'unit_price' => 100.50,
                    'quantity' => 2,
                ],
            ],
        ];

        $this->postJson(route('claims.store'), $data)->assertUnprocessable();
    }
}
