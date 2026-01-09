<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the Student Login Form
     */
    public function showStudentLogin() 
    {
        return view('auth.login', ['role' => 'student']);
    }

    /**
     * Show the Admin Login Form
     */
    public function showAdminLogin() 
    {
        return view('auth.login', ['role' => 'admin']);
    }

    /**
     * Handle authentication attempts
     */
    public function login(Request $request)
    {
        // 1. Validate the incoming request
        $credentials = $request->validate([
            'user_id'  => ['required', 'string'],
            'password' => ['required'],
            'role'     => ['required', 'string'], 
        ]);

        // 2. Attempt to log the user in using custom 'user_id'
        if (Auth::attempt(['user_id' => $credentials['user_id'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            /**
             * 3. Portal Authorization Check
             * We convert both to lowercase to avoid case-sensitivity issues
             * which often cause the "page refresh" effect.
             */
            if (strtolower($user->role) !== strtolower($credentials['role'])) {
                Auth::logout();
                return back()->withErrors(['user_id' => 'Unauthorized role portal.']);
            }

            // 4. Regenerate session to prevent session fixation
            $request->session()->regenerate();

            /**
             * 5. Role-Based Redirection
             * Directs users to their specific dashboard based on their role.
             */
            if (strtolower($user->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('student.dashboard');
        }

        // 6. Failed login attempt
        return back()->withErrors(['user_id' => 'Invalid credentials.'])->onlyInput('user_id');
    }

    /**
     * Log the user out and clean up the session
     */
    public function logout(Request $request) 
    {
        Auth::logout();

        // Completely clear the session data and CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}