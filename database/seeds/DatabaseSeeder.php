<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(GenresTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(BandsTableSeeder::class);
        $this->call(BandGenresTableSeeder::class);
        $this->call(SpacesTableSeeder::class);
        $this->call(ConcertsTableSeeder::class);
        $this->call(TicketsTableSeeder::class);
    }
}
