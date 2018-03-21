<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','price','qty'
    ];

    public function order() {
        return $this->belongsTo('App\Models\OrderDetail');
    }
}
