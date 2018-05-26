<?php

use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $users = \App\User::all()->pluck('id')->toArray();
        $concerts = \App\Concert::all()->pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            \App\Review::create([
                'title' => $faker->title(),
                'concert_id' => $faker->randomElement($concerts),
                'user_id' => $faker->randomElement($users),
                'message' => $faker->realText(150),
            ]);
        }
    }
}
