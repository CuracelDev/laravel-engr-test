<?php

namespace Feature\Actions;

use App\Enums\PriorityLevel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetPrioritiesTest extends TestCase
{
    #[Test]
    public function it_returns_the_list_of_priorities()
    {
        $this->getJson(route('priorities.index'))
            ->assertSuccessful()
            ->assertJsonCount(count(PriorityLevel::cases()), 'data');
    }
}
