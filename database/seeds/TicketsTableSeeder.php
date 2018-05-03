<?php

use Illuminate\Database\Seeder;
use \App\User as User;
use \App\Concert as Concert;
use \App\Ticket as Ticket;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all()->pluck('id')->toArray();
        $concerts = Concert::all()->take(20)->pluck('id')->toArray();
        $price = [100, 50, 200, 75, 25];

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            Ticket::create([
                'user_id' => $faker->randomElement($users),
                'concert_id' => $faker->randomElement($concerts),
                'price' => $faker->randomElement($price)
            ]);
        }
    }
}
