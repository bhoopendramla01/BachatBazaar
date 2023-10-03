<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $admin = Auth::guard('admin')->user();
        echo 'Welcome'.$admin->name.'<a href="/admin/logout">Logout</a>';
    }

    public function adminLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin/login');
    }
}
