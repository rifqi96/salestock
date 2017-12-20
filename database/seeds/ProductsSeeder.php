<?php

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
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
            'name' => 'Product 1',
            'price' => 100000,
            'qty' => 50
        ));

        array_push($data, array(
            'id' => 2,
            'name' => 'Product 2',
            'price' => 150000,
            'qty' => 60
        ));

        array_push($data, array(
            'id' => 3,
            'name' => 'Product 3',
            'price' => 200000,
            'qty' => 70
        ));

        foreach($data as $key=>$val){
            Product::create($data[$key]);
        }   
    }
}
