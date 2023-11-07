<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->orderBy("id", "ASC")->paginate(6);
        return view("admin/users/list", compact("users"));
    }

    public function create()
    {
        return view("admin/users/create");
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            session()->flash('error', 'User Not Found');
            return redirect('admin/users/index');
        }

        // File::delete(public_path() . '/uploads/category/Thumb/' . $category->image);
        // File::delete(public_path() . '/uploads/category/' . $category->image);

        $user->delete();

        session()->flash('success', 'User deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'User deleted Successfully'
        ]);
    }
}
