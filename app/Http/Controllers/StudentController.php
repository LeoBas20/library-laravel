<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use App\Models\Book;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function books()
    {
        $userId = Auth::id(); 

        $books = DB::table('books_db as b')
            ->leftJoin('transactions as t', function($join) use ($userId) {
                $join->on('b.book_id', '=', 't.book_id')
                     ->where('t.user_id', '=', $userId)
                     ->whereIn('t.status', ['pending', 'borrowed', 'overdue']);
            })
            ->select(
                'b.book_id', 
                'b.title', 
                'b.author', 
                'b.isbn', 
                'b.quantity', 
                't.status as borrow_status'
            )
            ->orderBy('b.title')
            ->get();

        return view('student.books', compact('books'));
    }

    public function borrowBook(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer|exists:books_db,book_id'
        ]);

        $userId = Auth::id();

        $exists = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('book_id', $request->book_id)
            ->whereIn('status', ['pending', 'borrowed', 'overdue'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'You already have an active request for this book.']);
        }

        DB::table('transactions')->insert([
            'user_id'      => $userId,
            'book_id'      => $request->book_id,
            'qty'          => 1, 
            'request_date' => Carbon::now(),
            'status'       => 'pending'
        ]);

        return redirect()->route('student.books')->with('msg', 'pending');
    }

    public function borrowed()
    {
        $userId = Auth::id();

        DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today()->toDateString())
            ->whereNull('return_date')
            ->update(['status' => 'overdue']);

        $borrowedBooks = DB::table('transactions as t')
            ->join('books_db as b', 't.book_id', '=', 'b.book_id')
            ->where('t.user_id', $userId)
            ->select(
                't.id as transaction_id',
                't.book_id',
                'b.title',
                'b.author',
                'b.isbn',
                't.request_date',
                't.issue_date',
                't.due_date',
                't.return_date',
                't.status',
                DB::raw('DATEDIFF(t.due_date, NOW()) as days_left')
            )
            ->orderByDesc('t.id')
            ->get();

        return view('student.borrowed', compact('borrowedBooks'));
    }

    public function returnBook(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'book_id'        => 'required|integer'
        ]);

        DB::transaction(function () use ($request) {
            DB::table('transactions')
                ->where('id', $request->transaction_id)
                ->where('user_id', Auth::id())
                ->update([
                    'status'      => 'returned',
                    'return_date' => Carbon::now()
                ]);

            DB::table('books_db')
                ->where('book_id', $request->book_id)
                ->increment('quantity');
        });

        return redirect()->route('student.borrowed')->with('msg', 'returned');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id() . ',user_id'
        ]);

        DB::table('users')
            ->where('user_id', Auth::id())
            ->update(['email' => $request->email]);

        return redirect()->route('student.profile')->with('message', 'Email updated successfully!');
    }

    public function changePassword()
    {
        $user = Auth::user();
        $student_display = "{$user->name} ({$user->user_id})";
        return view('student.changepass', compact('student_display'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('msg', 'incorrect');
        }

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update(['password' => Hash::make($request->new_password)]);

        return back()->with('msg', 'updated');
    }
}