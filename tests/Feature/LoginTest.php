<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function logging_in_using_valid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('my-secret-password'),
        ]);

        $response = $this->post('login', [
            'email' => 'john@example.com',
            'password' => 'my-secret-password',
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function logging_in_using_invalid_credentials()
    {
         factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('my-secret-password'),
        ]);

        $response = $this->from('/login')->post('login', [
            'email' => 'john@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_in_with_an_account_that_doesnt_exist()
    {
        $response = $this->from('/login')->post('login', [
            'email' => 'unknown@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());
        $this->assertTrue(Auth::check());

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}
