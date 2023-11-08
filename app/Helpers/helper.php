<?php

namespace App\Helpers;
use App\Models\Category;
use App\Models\Product;

class Helper{
    public static function getCategories()
    {
        return Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->where('showHome','Yes')->get();
    }

    public static function isFeatured()
    {
        return Product::orderBy('title','ASC')->where('status',1)->where('is_featured','Yes')->get();
    }    
}