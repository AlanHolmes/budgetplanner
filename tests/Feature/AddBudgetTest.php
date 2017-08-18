<?php

namespace Tests\Feature;

use App\Budgets;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddBudgetTest extends TestCase
{
    use DatabaseMigrations;

    protected $valid_params = [
        'name' => 'My Monthly Budget',
        'description' => 'Monthly spending money',
        'budget' => '200',
        'frequency' => 'monthly',
        'start_on' => 1,
    ];


    /** @test */
    public function an_authenticated_user_can_view_the_create_budget_form()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/budgets/create');

        $response->assertStatus(200);
        $response->assertViewIs('budgets.create');
    }

    /** @test */
    public function guests_cannot_view_the_create_budget_form()
    {
        $response = $this->get('/budgets/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_setup_a_budget()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/budgets', [
            'name' => 'My Monthly Budget',
            'description' => 'Monthly spending money',
            'budget' => '200.00',
            'frequency' => 'monthly',
            'start_on' => '1',
        ]);

        $response->assertRedirect('/budgets');

        tap(Budgets::first(), function ($budget) use ($user) {
            $this->assertTrue($budget->user->is($user));

            $this->assertEquals('My Monthly Budget', $budget->name);
            $this->assertEquals('Monthly spending money', $budget->description);
            $this->assertEquals('20000', $budget->budget);
            $this->assertEquals('monthly', $budget->frequency);
            $this->assertEquals('1', $budget->start_on);
        });
    }

    /** @test */
    public function a_guest_cannot_setup_a_budget()
    {
        $response = $this->post('/budgets', $this->validParams());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function name_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function budget_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'budget' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('budget');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function budget_must_be_numeric()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'budget' => 'not a number',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('budget');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function budget_must_be_at_least_5()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'budget' => '4.99',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('budget');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function description_is_optional()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'description' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap(Budgets::first(), function ($budget) use ($user) {
            $this->assertTrue($budget->user->is($user));

            $this->assertNull($budget->description);
        });
    }

    /** @test */
    public function frequency_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'frequency' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('frequency');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function frequency_doesnt_allow_non_valid_values()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'frequency' => 'not a valid value',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('frequency');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function frequency_can_be_monthly()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'frequency' => 'monthly',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap(Budgets::first(), function ($budget) use ($user) {
            $this->assertTrue($budget->user->is($user));

            $this->assertEquals('monthly', $budget->frequency);
        });
    }

    /** @test */
    public function frequency_can_be_weekly()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'frequency' => 'weekly',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets');

        tap(Budgets::first(), function ($budget) use ($user) {
            $this->assertTrue($budget->user->is($user));

            $this->assertEquals('weekly', $budget->frequency);
        });
    }

    /** @test */
    public function start_on_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'start_on' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('start_on');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function start_on_must_be_an_integer()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'start_on' => '3.5',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('start_on');
        $this->assertEquals(0, Budgets::count());
    }

    /** @test */
    public function start_on_must_be_positive()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/budgets/create')->post('/budgets', $this->validParams([
            'start_on' => '-1',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/budgets/create');
        $response->assertSessionHasErrors('start_on');
        $this->assertEquals(0, Budgets::count());
    }
}
