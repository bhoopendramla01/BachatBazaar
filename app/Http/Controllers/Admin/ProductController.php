<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brands;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create()
    {
        // $data = [];
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brands::orderBy('name','ASC')->get();
        // $data['$categories'] = $categories;
        // $data['$brands'] = $brands;
        return view("admin.products.create", compact("categories","brands"));
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required',
        ];

        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->save();

            session()->flash('success', 'Product added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product added Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function index(Request $request)
    {
        // $products = Product::latest()->orderBy("id", "ASC");
        $products = Product::select('products.*', 'categories.name as categoryName', 'sub_categories.name as subCategoryName', 'brands.name as brandName')->latest('id')->leftJoin('categories', 'categories.id', 'products.category_id')->leftJoin('sub_categories', 'sub_categories.id', 'products.sub_category_id')->leftJoin('brands', 'brands.id', 'products.brand_id');

        if (!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
        }

        $products = $products->paginate(6);

        return view('admin/products/list', compact('products'));
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (empty($product)) {
            session()->flash('error', 'Product Not Found');
            return redirect('admin/sub-category/index');
        }

        // File::delete(public_path() . '/uploads/category/Thumb/' . $category->image);
        // File::delete(public_path() . '/uploads/category/' . $category->image);

        $product->delete();

        session()->flash('success', 'Product deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Product deleted Successfully'
        ]);
    }
}
