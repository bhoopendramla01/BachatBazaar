<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        // $admin = Auth::guard('admin')->user();s
        // echo 'Welcome'.$admin->name.'<a href="/admin/logout">Logout</a>';
        $user = User::all();
        return view('admin/dashboard', compact('user'));
    }

    public function adminLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin/login');
    }
}
