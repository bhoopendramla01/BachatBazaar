<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy("name","asc")->with('sub_category')->where('status',1)->get();
        $brands = Brands::orderBy("name","asc")->where('status',1)->get();
        $products = Product::orderBy('title','asc')->where('status',1)->get();

        return view("front/shop", compact("categories","brands","products"));
    }
}
