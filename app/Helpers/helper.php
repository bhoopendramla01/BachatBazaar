<?php

namespace App\Helpers;
use app\Models\Category;

class Helper
{
    public static function getCategories()
    {
        return Category::orderBy('name', 'ASC')->get();
    }
}