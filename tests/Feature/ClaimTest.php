<?php

namespace Tests\Feature;

use App\Models\Insurer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method of ClaimController.
     */
    public function test_claim_index_page_is_accessible(): void
    {
        $response = $this->get(route('claim.index'));

        $response->assertStatus(200);
        $response->assertSee('Claims');
    }

    /**
     * Test the create method of ClaimController.
     */
    public function test_claim_create_page_is_accessible(): void
    {
        Insurer::factory()->create();

        $response = $this->get(route('claim.create'));

        $response->assertStatus(200);
        $response->assertSee('SubmitClaim');
    }

    /**
     * Test storing a new claim.
     */
    public function test_store_new_claim(): void
    {
        $insurer = Insurer::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'insurer_id' => $insurer->id,
            'priority_level' => 1,
            'speciality' => 'cardiology',
            'name' => 'Test Claim',
            'date' => now()->format('Y-m-d'),
            'items' => [
                ['name' => 'Item 1', 'quantity' => 2, 'unit_price' => 50],
                ['name' => 'Item 2', 'quantity' => 1, 'unit_price' => 100],
            ],
        ];

        $response = $this->post(route('claim.store'), $data);

        $response->assertRedirect(route('claim.index'));
        $this->assertDatabaseHas('claims', ['name' => 'Test Claim']);
        $this->assertDatabaseHas('items', ['name' => 'Item 1']);
        $this->assertDatabaseHas('items', ['name' => 'Item 2']);
    }

    /**
     * Test validation errors when storing a new claim.
     */
    public function test_store_new_claim_validation_errors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'insurer_id' => null,
            'priority_level' => null,
            'speciality' => null,
            'name' => '',
            'date' => '',
            'items' => [],
        ];

        $response = $this->post(route('claim.store'), $data);

        $response->assertSessionHasErrors([
            'insurer_id',
            'priority_level',
            'speciality',
            'name',
            'date',
            'items',
        ]);
    }
}
