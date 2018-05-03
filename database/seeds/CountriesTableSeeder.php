<?php

use Illuminate\Database\Seeder;
use \App\Country as Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 50; $i++) {
            Country::create([
                'name' => $faker->country
            ]);
        }
    }
}
