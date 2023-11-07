<?php 

namespace app\Helpers;
use app\Models\Category;
function getCategories()
    {
        $categories = Category::orderBy('name','ASC')->get();
        return $categories;
    }
