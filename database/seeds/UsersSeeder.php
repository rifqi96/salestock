<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();

        array_push($data, array(
            'id' => 1,
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@salestock.id',
            'password' => bcrypt('admin'),
            'name' => 'Salestock Admin',
            'address' => 'Indonesia',
            'phone' => '081278209381'
        ));

        array_push($data, array(
            'id' => 2,
            'role' => 'customer',
            'name' => 'customer',
            'email' => 'customer@salestock.id',
            'password' => bcrypt('customer'),
            'name' => 'Salestock Customer',
            'address' => 'Indonesia',
            'phone' => '085678291381'
        ));

        foreach($data as $key=>$val){
            User::create($data[$key]);
        }   
    }
}
