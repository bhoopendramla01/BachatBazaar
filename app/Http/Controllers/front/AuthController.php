<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view("front.account.login");
    }

    public function register(Request $request)
    {
        return view("front.account.register");
    }

    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'gender' => 'required|in:M,F',
            'password'=> 'required|min:6|confirmed',
        ]);

        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have been registered Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'You have been registered Successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function profile()
    {
        return view('front.account.profile');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if(Auth::attempt(['email'=> $request->email,'password'=> $request->password], $request->get('remember'))){
                if(session()->has('url.intended')){
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account/profile')->with('success','You are now Login.');
            }else{
                session()->flash('error','Either email/password is incorrect.');
                return redirect()->route('account/login')->withInput($request->only('email'));
            }
        } else {
            return redirect()->route('account/login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account/login')->with('success','You are Now successfully Logout.');
    }
}
