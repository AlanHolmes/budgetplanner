<?php

namespace Tests\Feature;

use App\Budget;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddTransactionTest extends TestCase
{
    use DatabaseMigrations;

    protected $valid_params = [
        'budget_id' => 1,
        'description' => 'Weekly Food Shopping',
        'amount' => '50.00',
        'date' => '2017-12-01',
    ];

    /** @test */
    public function an_authenticated_user_can_view_the_create_transaction_form_with_only_their_budgets()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $budgetA = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);
        $otherBudget = factory(Budget::class)->create([
            'user_id' => $otherUser->id,
        ]);
        $budgetB = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/transactions/create");

        $response->assertStatus(200);
        $response->assertViewIs('transactions.create');

        $response->data('budgets')->assertContains($budgetA);
        $response->data('budgets')->assertContains($budgetB);
        $response->data('budgets')->assertNotContains($otherBudget);
    }

    /** @test */
    public function guests_cannot_view_the_create_transaction_form()
    {
        $response = $this->get('/transactions/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_add_a_transaction()
    {
        $this->disableExceptionHandling();

        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/transactions', [
            'budget_id' => $budget->id,
            'description' => 'Weekly Food Shopping',
            'amount' => '50.00',
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response->assertRedirect("/budgets/{$budget->id}");

        tap(Transaction::first(), function ($transaction) use ($budget) {
            $this->assertTrue($transaction->budget->is($budget));

            $this->assertEquals('Weekly Food Shopping', $transaction->description);
            $this->assertEquals('5000', $transaction->amount);
            $this->assertTrue(Carbon::now()->startOfDay()->eq($transaction->date));
        });
    }

    /** @test */
    public function an_authenticated_user_cannot_add_a_transaction_to_a_budget_that_doesnt_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/transactions', $this->validParams([
            'budget_id' => '9999',
        ]));

        $response->assertStatus(404);
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function an_authenticated_user_cannot_add_a_transaction_to_others_budgets()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
        ]));

        $response->assertStatus(404);
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function guests_cannot_add_a_transaction()
    {
        $budget = factory(Budget::class)->create();

        $response = $this->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function description_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from('/transactions/create')->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
            'description' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/transactions/create');
        $response->assertSessionHasErrors('description');
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function amount_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from('/transactions/create')->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
            'amount' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/transactions/create');
        $response->assertSessionHasErrors('amount');
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function amount_is_numeric()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from('/transactions/create')->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
            'amount' => 'not a number',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/transactions/create');
        $response->assertSessionHasErrors('amount');
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function date_is_required()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from('/transactions/create')->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
            'date' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/transactions/create');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Transaction::count());
    }

    /** @test */
    public function date_is_a_valid_date()
    {
        $user = factory(User::class)->create();
        $budget = factory(Budget::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from('/transactions/create')->post('/transactions', $this->validParams([
            'budget_id' => $budget->id,
            'date' => 'not a date',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/transactions/create');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Transaction::count());
    }
}
