<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use app\Models\Category;

class FrontController extends Controller
{
    public function index()
    {
        // $categories = Category::orderBy('name','ASC')->get();
        return view("front/home");
    }
}
