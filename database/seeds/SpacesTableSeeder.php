<?php

use Illuminate\Database\Seeder;
use \App\Space as Space;

class SpacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            Space::create([
                'name' => $faker->safeColorName,
                'description' => $faker->realText(),
                'lng' => $faker->longitude,
                'lat' => $faker->latitude
            ]);
        }
    }
}
