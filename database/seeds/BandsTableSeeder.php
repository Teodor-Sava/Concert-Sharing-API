<?php

use Illuminate\Database\Seeder;
use \App\Band as Band;
use \App\Country as Country;
use \App\User as User;

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
        $users = User::all()->pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            Band::create([
                'name' => $faker->name,
                'country_id' => $faker->randomElement($countries),
                'no_members' => $faker->randomNumber(1),
                'founded_at' => $faker->date(),
                'band_requests' => $faker->realText(),
                'long_description' => $faker->realText(),
                'short_description' => $faker->realText(100),
                'price' => $faker->randomNumber(4),
                'user_id' => $faker->randomElement($users),
                'image_url' => 'https://img.wennermedia.com/620-width/jimi-hendrix-265cfe18-2512-4cff-bc07-251d1acd7d21.jpg'
            ]);
        }
    }
}
