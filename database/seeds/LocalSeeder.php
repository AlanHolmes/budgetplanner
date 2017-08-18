<?php

use App\User;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds. (Local only)
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return;
        }


        factory(User::class)->create([
            'name' => 'Alan',
            'email' => 'alan@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
