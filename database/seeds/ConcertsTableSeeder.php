<?php

use Illuminate\Database\Seeder;
use \App\Band as Band;
use \App\Space as Space;
use \App\Concert as Concert;

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

        for ($i = 0; $i < 50; $i++) {
            $number = $faker->randomNumber(3);
            Concert::create([
                'name' => $faker->name(),
                'band_id' => $faker->randomElement($bands),
                'space_id' => $faker->randomElement($spaces),
                'available_tickets' => $number,
                'total_tickets' => $number,
                'concert_start' => $faker->dateTimeThisYear()
            ]);
        }
    }
}
