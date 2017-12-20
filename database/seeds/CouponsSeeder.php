<?php

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponsSeeder extends Seeder
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
            'code' => 'PERCENTAGE',
            'type' => 'percentage',
            'discount' => 30,
            'qty' => 10,
            'start_date' => '2017-12-10',
            'end_date' => '2017-12-30'
        ));

        array_push($data, array(
            'id' => 2,
            'code' => 'NOMINAL',
            'type' => 'nominal',
            'discount' => 50000,
            'qty' => 10,
            'start_date' => '2017-12-15',
            'end_date' => '2017-12-30'
        ));

        foreach($data as $key=>$val){
            Coupon::create($data[$key]);
        }   
    }
}
