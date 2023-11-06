<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TempImage;
use File;
use Image;

class SubCategoryController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view("admin.sub_category.create", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories'
        ]);

        if ($validator->passes()) {
            $sub_category = new SubCategory;
            $sub_category->name = $request->name;
            $sub_category->category_id = $request->category;
            $sub_category->slug = $request->slug;
            $sub_category->status = $request->status;
            $sub_category->save();

            session()->flash('success', 'Sub Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category added Successfully'
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
        $sub_categories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')->latest('id')->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $sub_categories = $sub_categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $subCategories = $sub_categories->paginate(6);

        return view('admin/sub_category/list', compact('subCategories'));
    }

    public function edit($id, Request $request)
    {
        $sub_category = SubCategory::find($id);
        $categories = Category::orderBy('name','ASC')->get();
        // $data['categories'] = $categories;

        if (empty($sub_category)) {
            session()->flash('error', 'Sub Category Not Found');
            return redirect('admin/sub-category/index');
        }
        return view('admin/sub_category/edit', compact('sub_category','categories'));
    }

    public function update($id, Request $request)
    {
        $sub_category = SubCategory::find($id);

        if (empty($sub_category)) {
            return response()->json([
                'status' => 'error',
                'notFound' => true,
                'message' => 'Sub Category not Found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,' . $sub_category->id . ',id'
        ]);

        if ($validator->passes()) {
            $sub_category->name = $request->name;
            $sub_category->category_id = $request->category;
            $sub_category->slug = $request->slug;
            $sub_category->status = $request->status;
            $sub_category->save();

            session()->flash('success', 'Sub Category updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id)
    {
        $sub_category = SubCategory::find($id);

        if (empty($sub_category)) {
            session()->flash('error', 'Sub Category Not Found');
            return redirect('admin/sub-category/index');
        }

        // File::delete(public_path() . '/uploads/category/Thumb/' . $category->image);
        // File::delete(public_path() . '/uploads/category/' . $category->image);

        $sub_category->delete();

        session()->flash('success', 'Sub Category deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted Successfully'
        ]);
    }
}
