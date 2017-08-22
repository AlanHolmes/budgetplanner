<?php

namespace Tests\Unit;

use App\Budgets;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    /** @test */
    public function can_get_the_budget_amount_as_float()
    {
        $budget = factory(Budgets::class)->make([
            'user_id' => 1,
            'budget' => '355050',
        ]);

        $this->assertEquals('3550.50', $budget->budget_as_float);
    }
}
