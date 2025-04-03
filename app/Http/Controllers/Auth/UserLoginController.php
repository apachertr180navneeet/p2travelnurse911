<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{
   
    public function user() {

        if (Auth::check()) {
            return redirect()->route("classified.index");
        }
        return view("auth.userlogin");
    }

    public function userLogin(Request $request) {
       
        if (Auth::check()) {
            return redirect()->route("classified.index");
        }
        $validated = $request->validate([
            "email" => ["required"],
            "password" => ["required"]
        ]);
        $user = User::where("email", $validated["email"])->where('role_id','9')->first();       
        if ($user && !$user->status) {
            return back()->withErrors("Your account is currently inactive!");
        }
        if ($user && Hash::check($validated["password"], $user->password)) {
            Auth::login($user, $request->has("remember"));
            return redirect()->route("classified.index");
        }
        return back()->withErrors("Your login credentials don't match!");
    }
}
