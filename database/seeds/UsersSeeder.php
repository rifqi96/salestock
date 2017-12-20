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

        for($i=1; $i<=2; $i++){
            if($i == 1){
                array_push($data, array(
                    'id' => $i,
                    'role' => 'admin',
                    'name' => 'admin',
                    'email' => 'admin@salestock.id',
                    'password' => bcrypt('admin'),
                    'name' => 'Salestock Admin'
                ));
            }
            else if($i == 2){
                array_push($data, array(
                    'id' => $i,
                    'role' => 'customer',
                    'name' => 'customer',
                    'email' => 'customer@salestock.id',
                    'password' => bcrypt('customer'),
                    'name' => 'Salestock Customer'
                ));
            }
        }

        foreach($data as $key=>$val){
            User::create($data[$key]);
        }   
    }
}
