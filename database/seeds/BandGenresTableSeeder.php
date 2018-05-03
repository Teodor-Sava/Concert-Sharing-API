<?php

use Illuminate\Database\Seeder;
use \App\Band as Band;
use \App\Genre as Genre;
use \App\BandGenre as BandGenre;

class BandGenresTableSeeder extends Seeder
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
        $genres = Genre::all()->pluck('id')->toArray();

        foreach ($bands as $band) {
            BandGenre::create([
                'band_id' => $band,
                'genre_id' => $faker->randomElement($genres)
            ]);
        }
    }
}
