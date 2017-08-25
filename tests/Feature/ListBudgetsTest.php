<?php

namespace Tests\Feature;

use App\Budget;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ListBudgetsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_authenicated_user_can_view_a_list_of_their_own_budgets()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $budgetA = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);
        $otherUserBudget = factory(Budget::class)->create([
            'user_id' => $otherUser->id,
        ]);
        $budgetB = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/budgets');

        $response->assertStatus(200);
        $response->assertViewIs('budgets.index');

        $response->data('budgets')->assertContains($budgetA);
        $response->data('budgets')->assertContains($budgetB);
        $response->data('budgets')->assertNotContains($otherUserBudget);
    }


    /** @test */
    public function guests_are_asked_to_login_when_trying_to_view_the_budgets_list()
    {
        $response = $this->get("/budgets");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

}
