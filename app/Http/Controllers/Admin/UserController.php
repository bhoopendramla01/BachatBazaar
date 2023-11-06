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
}
