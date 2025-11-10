<?php

namespace Tests\Feature\Actions;

use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetInsurerByCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_insurer_by_code()
    {
        // Create an insurer with a specific code
        $insurer = Insurer::factory()->create(['code' => 'ABC']);

        // Call the endpoint with a mixed case code
        $response = $this->getJson(route('insurers.by-code', ['code' => 'aBc']));

        $this->assertEquals($insurer->id, $response->json('data.id'));
        $this->assertEquals($insurer->code, $response->json('data.code'));
        $this->assertEquals($insurer->name, $response->json('data.name'));
    }

    #[Test]
    public function it_returns_404_for_non_existent_code()
    {
        $response = $this->getJson(route('insurers.by-code', ['code' => 'NONEXISTENT']));

        $response->assertNotFound();
    }
}
