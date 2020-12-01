<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    //
    protected $table='ecs_cart';
    protected $primaryKey='rec_id';
    public $timestamps=false;
}
