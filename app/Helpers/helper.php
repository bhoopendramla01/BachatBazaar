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
        return Product::orderBy('title','ASC')->with('product_images')->where('status',1)->take(8)->where('is_featured','Yes')->get();
    } 
    
    public static function isLatest()
    {
        return Product::orderBy('id','DESC')->with('product_images')->where('status',1)->take(8)->get();
    }
}