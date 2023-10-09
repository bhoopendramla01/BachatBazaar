<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use File;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(6);

        return view('admin/category/list', compact('categories'));
    }

    public function create()
    {
        return view('admin/category/create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {
            $category = new Category;
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/tempImage/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                $dPath = public_path() . '/uploads/category/Thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();
            }

            session()->flash('success', 'Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category added Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $category = Category::find($id);

        if (empty($category)) {
            session()->flash('error', 'Category Not Found');
            return redirect('admin/category/index');
        }
        return view('admin/category/edit', compact('category'));
    }

    public function update($id, Request $request)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return response()->json([
                'status' => 'error',
                'notFound' => true,
                'message' => 'Category not Found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id'
        ]);

        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $oldImage = $category->image;

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/tempImage/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                $dPath = public_path() . '/uploads/category/Thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();

                //Delete old Category Image and Thumb Image

                File::delete(public_path() . '/uploads/category/Thumb/' . $oldImage);
                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }

            session()->flash('success', 'Category updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category updated Successfully'
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
        $category = Category::find($id);

        if (empty($category)) {
            session()->flash('error', 'Category Not Found');
            return redirect('admin/category/index');
        }

        File::delete(public_path() . '/uploads/category/Thumb/' . $category->image);
        File::delete(public_path() . '/uploads/category/' . $category->image);

        $category->delete();

        session()->flash('success', 'Category deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category deleted Successfully'
        ]);
    }
}
