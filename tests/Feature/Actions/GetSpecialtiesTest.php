<?php

namespace Tests\Feature\Actions;

use App\Enums\Specialty;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetSpecialtiesTest extends TestCase
{
    #[Test]
    public function it_returns_the_list_of_specialties()
    {
        $this->getJson(route('specialties.index'))
            ->assertSuccessful()
            ->assertJsonCount(count(Specialty::cases()), 'data');
    }
}
