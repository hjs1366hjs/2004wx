<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WxUserModel extends Model
{
    //
    protected $table='p_wx_user';
    protected $primaryKey='id';
    public $timestamps=false;
}
