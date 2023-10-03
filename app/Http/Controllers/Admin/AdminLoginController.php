<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function adminLogin()
    {
        return view('admin/login');
    }

    public function adminAuthenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){

            $admin = Auth::guard('admin')->user();

            if($admin->role == 1)
            {
                return redirect()->route('admin/dashboard');
            }
            else{
                return redirect()->route('admin/login')->with('error', 'You are not Authorized to access Admin Panel.');
            }
        }
        else{
            return redirect()->route('admin/login')->with('error', 'Either Email/Password is incorrect');
        }
    }
}
