<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brands::latest()->orderBy("id", "ASC")->paginate(6);

        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // $brands = $brands->paginate(6);

        return view('admin/brands/list', compact('brands'));
    }

    public function create()
    {
        return view("admin.brands.create");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories'
        ]);

        if ($validator->passes()) {
            $sub_category = new Brands;
            $sub_category->name = $request->name;
            $sub_category->slug = $request->slug;
            $sub_category->status = $request->status;
            $sub_category->save();

            session()->flash('success', 'Brands added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brands added Successfully'
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
        $brands = Brands::find($id);

        if (empty($brands)) {
            session()->flash('error', 'Brands Not Found');
            return redirect('admin/brands/index');
        }

        $brands->delete();

        session()->flash('success', 'Brands deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Brands deleted Successfully'
        ]);
    }


    public function edit($id, Request $request)
    {
        $brands = Brands::find($id);

        if (empty($brands)) {
            session()->flash('error', 'Brands Not Found');
            return redirect('admin/brands/index');
        }
        return view('admin/brands/edit', compact('brands'));
    }

    public function update($id, Request $request)
    {
        $brands = Brands::find($id);

        if (empty($brands)) {
            return response()->json([
                'status' => 'error',
                'notFound' => true,
                'message' => 'Brands not Found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brands->id . ',id'
        ]);

        if ($validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            session()->flash('success', 'Brands updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brands updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
