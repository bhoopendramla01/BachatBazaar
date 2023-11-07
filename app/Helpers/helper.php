<?php 

use app\Models\Category;
    function getCategories()
    {
        $categories = Category::all();

        return $categories;
    }

?>