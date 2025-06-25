<?php

namespace Tests\Feature\ProviderClaims;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class ProviderClaimsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'test@example.com')->first();
    }
    public function test_unauthenticated_user_cannot_access(): void
    {
        $claims_listing_response = $this->get('/provider-claims');
        $claims_creation_response = $this->get('/provider-claims/create');

        $claims_listing_response->assertRedirect('/login');
        $claims_creation_response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_claims_listing(): void
    {
        $this->actingAs($this->user);
        $claims_listing_response = $this->get('/provider-claims');
        $claims_listing_response->assertInertia(fn (Assert $page) => $page->component('Claims/ListClaims'));
    }

    public function test_user_can_see_claims_stats_on_dashboard(): void
    {
        $this->actingAs($this->user);

        $items = ClaimItem::factory(10)->make();
        $total = round($items->sum('sub_total'), 2);

        Claim::factory(10)->hasItems($items)->create([
            'provider_id' => $this->user->id,
            'total_amount' => $total
        ]);
        Claim::factory(2)->hasItems($items)->create([
            'provider_id' => $this->user->id,
            'total_amount' => $total,
            'processed_at' => now()
        ]);

        $claims_listing_response = $this->get('/dashboard');
        $claims_listing_response->assertInertia(function (Assert $page) use ($total) {
            $page->component('Claims/Dashboard')
                ->has('claimsSummary', function (Assert $page) use ($total) {
                    $page->where('pending_batches_count', 10)
                        ->has('total_amount')
                        ->where('total_claims_count', 12)
                        ->where('processed_batches_count', 2);
                });
        });
    }

    public function test_user_can_access_claims_creation_page(): void
    {
        $this->actingAs($this->user);
        $claims_creation_response = $this->get('/provider-claims/create');
        $claims_creation_response->assertInertia(function (Assert $page) {
             $page->component('Claims/SubmitClaim')
                ->has('insurers')
                ->has('specialties');
        });
    }

    public function test_user_can_submit_claim(): void
    {
        $claim = Claim::factory()->make([
            'encounter_date' => now()->subDay(2)->format('Y-m-d'),
            'insurer_code' => Insurer::first()->code
        ])->toArray();
        $claim['items'] = ClaimItem::factory(3)->make()->toArray();
        $this->actingAs($this->user);
        $this->post('/provider-claims/submit', $claim)
            ->assertRedirect('/provider-claims');
    }
}
