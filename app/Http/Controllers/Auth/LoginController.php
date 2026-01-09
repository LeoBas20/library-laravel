<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{

    public function showStudentLogin() 
    {
        return view('auth.login', ['role' => 'student']);
    }

    public function showAdminLogin() 
    {
        return view('auth.login', ['role' => 'admin']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_id'  => ['required', 'string'],
            'password' => ['required'],
            'role'     => ['required', 'string'], 
        ]);

        if (Auth::attempt(['user_id' => $credentials['user_id'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if (strtolower($user->role) !== strtolower($credentials['role'])) {
                Auth::logout();
                return back()->withErrors(['user_id' => 'Unauthorized role portal.']);
            }

            $request->session()->regenerate();

            if (strtolower($user->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['user_id' => 'Invalid credentials.'])->onlyInput('user_id');
    }

    public function logout(Request $request) 
    {
        Auth::logout();

        // Completely clear the session data and CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}