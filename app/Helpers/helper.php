<?php

namespace App\Helpers;
use DB;


class Helper{
    public static function getCategories()
    {
        return DB::table('categories')->where('status',1)->where('showHome','Yes')->get();
    }
    
}