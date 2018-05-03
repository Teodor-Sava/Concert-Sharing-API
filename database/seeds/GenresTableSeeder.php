<?php

use Illuminate\Database\Seeder;
use \App\Genre as Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $genres = ['Alternative Rock', 'College Rock', 'Crossover Thrash', 'Crust Punk', 'Folk Punk', 'Grunge', 'Hardcore Punk', 'Hard Rock', 'Indie Rock'];

        foreach ($genres as $genre)
            Genre::create([
                'type' => $genre
            ]);
    }
}
