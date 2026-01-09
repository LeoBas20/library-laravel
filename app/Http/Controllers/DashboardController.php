<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //Student Section

    public function studentIndex()
    {
        $user = Auth::user(); //

        if (!$user) {
            return redirect()->route('login')->withErrors(['user_id' => 'Please login to access the dashboard.']); //
        }

        $uid = $user->user_id; //

        $stats = [
            'total_borrowed' => Transaction::where('user_id', $uid)->where('status', 'borrowed')->count(), //
            'total_returned' => Transaction::where('user_id', $uid)->where('status', 'returned')->count(), //
            'total_overdue'  => Transaction::where('user_id', $uid)->where('status', 'overdue')->count(), //
        ];

        $recentTransactions = Transaction::with('book')
            ->where('user_id', $uid)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get(); //

        return view('student.dashboard', compact('user', 'stats', 'recentTransactions')); //
    }

    //Admin Section

    public function adminIndex()
    {
        $admin = Auth::user(); //

        Transaction::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today()->toDateString())
            ->whereNull('return_date')
            ->update(['status' => 'overdue']); //

        $stats = [
            'total_books'    => DB::table('books_db')->count(), //
            'borrowed_books' => Transaction::where('status', 'borrowed')->count(), //
            'overdue_books'  => Transaction::where('status', 'overdue')->count(), //
            'total_students' => User::where('role', 'student')->count(), //
        ];

        $recentTransactions = Transaction::with(['book', 'user'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get(); //

        return view('admin.dashboard', compact('admin', 'stats', 'recentTransactions')); //
    }

    public function studentList()
    {
        $admin_name = Auth::user()->name; //
        $students = User::where('role', 'student')->orderBy('name')->get(); //
        return view('admin.students', compact('admin_name', 'students')); //
    }

    public function rejectedList()
    {
        $admin_name = Auth::user()->name; //
        $rejectedRequests = Transaction::with(['book', 'user'])
            ->where('status', 'rejected')
            ->orderByDesc('request_date')
            ->get(); //
        return view('admin.rejected', compact('admin_name', 'rejectedRequests')); //
    }

    // --- Profile & Password Management ---

    public function adminProfile()
    {
        $admin = Auth::user(); //
        $admin_name = $admin->name; //
        return view('admin.profile', compact('admin', 'admin_name')); //
    }

    public function updateAdminProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id() . ',user_id'
        ]); //

        DB::table('users')
            ->where('user_id', Auth::id())
            ->update(['email' => $request->email]); //

        return redirect()->route('admin.profile')->with('message', 'Profile updated successfully.'); //
    }

    public function adminChangePassword()
    {
        $user = Auth::user(); //
        $admin_name = $user->name; //
        $admin_display = "{$user->name} ({$user->user_id})"; //
        return view('admin.changepass', compact('admin_name', 'admin_display')); //
    }

    public function updateAdminPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]); //

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return back()->with('msg', 'incorrect');
        } //

        DB::table('users')
            ->where('user_id', Auth::id())
            ->update(['password' => Hash::make($request->new_password)]); //

        return back()->with('msg', 'updated'); //
    }
}