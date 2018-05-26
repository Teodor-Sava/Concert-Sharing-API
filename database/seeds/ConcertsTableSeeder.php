<?php

use Illuminate\Database\Seeder;
use \App\Band as Band;
use \App\Space as Space;
use \App\Concert as Concert;
use \App\User as User;

class ConcertsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $bands = Band::all()->pluck('id')->toArray();
        $spaces = Space::all()->pluck('id')->toArray();
        $users = User::all()->pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            $number = $faker->randomNumber(3);
            Concert::create([
                'name' => $faker->name(),
                'band_id' => $faker->randomElement($bands),
                'space_id' => $faker->randomElement($spaces),
                'user_id' => $faker->randomElement($users),
                'short_description' => $faker->realText(100),
                'long_description' => $faker->realText(),
                'available_tickets' => $number,
                'total_tickets' => $number,
                'concert_start' => $faker->dateTimeThisYear(),
                'poster_url' => 'http://www.realclearlife.com/wp-content/uploads/2016/10/Concert-Poster-4_1200.jpg'
            ]);
        }
    }
}
