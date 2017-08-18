<?php

namespace Tests\Feature;

use App\Budgets;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UpdateBudgetTest extends TestCase
{
    use DatabaseMigrations;

    protected $old_attributes = [
        'name' => 'Old Budget',
        'description' => 'Old description',
        'budget' => '20000',
        'frequency' => 'monthly',
        'start_on' => 1,
    ];
    protected $valid_params = [
        'name' => 'New Budget',
        'description' => 'New Description',
        'budget' => '400',
        'frequency' => 'weekly',
        'start_on' => 3,
    ];

    /** @test */
    public function an_authenticated_user_can_view_the_edit_form_for_their_own_budgets()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/budgets/{$budget->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('budgets.edit');
        $this->assertTrue($response->data('budget')->is($budget));
    }

    /** @test */
    public function an_authenticate_user_sees_404_when_attempting_to_view_edit_form_for_a_non_existant_budget()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get("/budgets/9999/edit");

        $response->assertStatus(404);
    }

    /** @test */
    public function an_authenticated_user_cannot_view_the_edit_form_for_others_budgets()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $budget = factory(Budgets::class)->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get("/budgets/{$budget->id}/edit");

        $response->assertStatus(404);
    }

    /** @test */
    public function guests_are_asked_to_login_when_trying_to_view_the_edit_form_for_an_existing_budget()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get("/budgets/{$budget->id}/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function guests_are_asked_to_login_when_trying_to_view_the_edit_form_for_a_non_existing_budget()
    {
        $response = $this->get("/budgets/9999/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticate_user_can_update_their_own_budget()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create([
            'user_id' => $user->id,
            'name' => 'Old Budget',
            'description' => 'Old description',
            'budget' => '200',
            'frequency' => 'monthly',
            'start_on' => 1,
        ]);

        $response = $this->actingAs($user)->patch("/budgets/{$budget->id}", [
            'name' => 'New Budget',
            'description' => 'New Description',
            'budget' => '400',
            'frequency' => 'weekly',
            'start_on' => 3,
        ]);


        $response->assertRedirect('/budgets');

        tap($budget->fresh(), function ($budget) {
            $this->assertEquals('New Budget', $budget->name);
            $this->assertEquals('New Description', $budget->description);
            $this->assertEquals('40000', $budget->budget);
            $this->assertEquals('weekly', $budget->frequency);
            $this->assertEquals('3', $budget->start_on);
        });
    }

    /** @test */
    public function an_authenticate_user_sees_404_when_attempting_to_update_concert_that_doesnt_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->patch("/budgets/9999", $this->validParams());

        $response->assertStatus(404);
    }

    /** @test */
    public function an_authenticate_user_cannot_update_others_budgets()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $otherUser->id,
        ]));

        $response = $this->actingAs($user)->patch("/budgets/{$budget->id}", $this->validParams());

        $response->assertStatus(404);

        tap($budget->fresh(), function ($budget) {
            $this->assertEquals('Old Budget', $budget->name);
            $this->assertEquals('Old description', $budget->description);
            $this->assertEquals('20000', $budget->budget);
        });
    }

    /** @test */
    public function guests_cannot_edit_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $otherUser->id,
        ]));

        $response = $this->patch("/budgets/{$budget->id}", $this->validParams());

        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $this->assertModelMatchesData($budget, [
            'user_id' => $otherUser->id,
        ]);
    }

    /** @test */
    public function name_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'name' => 'Existing Budget',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('name');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'name' => 'Existing Budget',
        ]);
    }

    /** @test */
    public function budget_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'budget' => '30000',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'budget' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('budget');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'budget' => '30000',
        ]);
    }

    /** @test */
    public function budget_must_be_numeric()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'budget' => '30000',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'budget' => 'not a number',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('budget');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'budget' => '30000',
        ]);
    }

    /** @test */
    public function budget_must_be_at_least_5()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'budget' => '30000',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'budget' => '4.99',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('budget');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'budget' => '30000',
        ]);
    }

    /** @test */
    public function description_is_optional()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'description' => 'Old description',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'description' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap($budget->fresh(), function ($budget)  {
            $this->assertNull($budget->description);
        });
    }

    /** @test */
    public function frequency_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'frequency' => 'monthly',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'frequency' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('frequency');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'frequency' => 'monthly',
        ]);
    }

    /** @test */
    public function frequency_doesnt_allow_non_valid_values()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'frequency' => 'monthly',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'frequency' => 'not a valid value',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('frequency');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'frequency' => 'monthly',
        ]);
    }

    /** @test */
    public function frequency_can_be_monthly()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'frequency' => 'weekly',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'frequency' => 'monthly',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap($budget->fresh(), function ($budget)  {
            $this->assertEquals('monthly', $budget->frequency);
        });
    }

    /** @test */
    public function frequency_can_be_weekly()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'frequency' => 'monthly',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'frequency' => 'weekly',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap($budget->fresh(), function ($budget)  {
            $this->assertEquals('weekly', $budget->frequency);
        });
    }

    /** @test */
    public function start_on_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'start_on' => '1',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'start_on' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('start_on');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'start_on' => '1',
        ]);
    }

    /** @test */
    public function start_on_must_be_an_integer()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'start_on' => '1',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'start_on' => '3.5',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('start_on');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'start_on' => '1',
        ]);
    }

    /** @test */
    public function start_on_must_be_positive()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budgets::class)->create($this->oldAttributes([
            'user_id' => $user->id,
            'start_on' => '1',
        ]));

        $response = $this->actingAs($user)->from("/budgets/{$budget->id}/edit")->patch("/budgets/{$budget->id}", $this->validParams([
            'start_on' => '-1',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/budgets/{$budget->id}/edit");
        $response->assertSessionHasErrors('start_on');

        $this->assertModelMatchesData($budget, [
            'user_id' => $user->id,
            'start_on' => '1',
        ]);
    }
}
