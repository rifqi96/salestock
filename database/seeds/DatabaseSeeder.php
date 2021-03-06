<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->command->info('Users have been seeded');

        $this->call(ProductsSeeder::class);
        $this->command->info('Products have been seeded');

        $this->call(CouponsSeeder::class);
        $this->command->info('Coupons have been seeded');
    }
}
