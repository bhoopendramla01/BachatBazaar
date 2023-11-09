<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\subCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = "";
        $subCategorySelected = "";
        $brandArray = [];

        $categories = Category::orderBy("name", "asc")->with('sub_category')->where('status', 1)->get();
        $brands = Brands::orderBy("name", "asc")->where('status', 1)->get();
        $products = Product::where('status', 1)->get();

        //Apply Filters here
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = subCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }

        if (!empty($request->get("brand"))) {
            $brandArray = explode(",", $request->get("brand"));
            $products = $products->whereIn("brand_id", $brandArray);
        }

        // dd($request->get("price_max"));
        // dd($request->get("price_min"));

        if ($request->get('price_min') != '' && $request->get('price_max') != '') {
            if ($request->get('price_max') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 10000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
            // dd($products);
        }

        // dd($products);

        // $priceMin = intval($request->get("price_min"));
        // $priceMax = intval($request->get("price_max"));

        // $products = $products->orderBy('id','desc')->paginate(6);
        // $products = $products->paginate(6);

        $data["categories"] = $categories;
        $data["brands"] = $brands;
        $data["products"] = $products;
        $data["subCategorySelected"] = $subCategorySelected;
        $data["categorySelected"] = $categorySelected;
        $data["brandArray"] = $brandArray;
        $data["priceMin"] = intval($request->get("price_min"));
        $data["priceMax"] = intval($request->get("price_max")) == 0 ? 1000 : $request->get("price_max");

        return view("front/shop", $data);
    }
}
