<?php

use Illuminate\Database\Seeder;
use \App\Band as Band;
use \App\Country as Country;

class BandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $countries = Country::all()->pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            Band::create([
                'name' => $faker->name,
                'country_id' => $faker->randomElement($countries),
                'no_members' => $faker->randomNumber(1),
                'founded_at' => $faker->date(),
                'band_requests' => $faker->realText()
            ]);
        }
    }
}
