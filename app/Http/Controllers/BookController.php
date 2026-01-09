<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    public function index()
    {
        $admin_name = Auth::user()->name; 
        $books = Book::orderBy('title')->get();
        return view('admin.books', compact('books', 'admin_name'));
    }

    public function store(Request $request)
    {
        
        $exists = DB::table('books_db')->where('isbn', $request->isbn)->exists();

        if ($exists) {
            return redirect()->route('admin.books')->with('msg', 'duplicate');
        }

        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'isbn' => 'required',
            'copies' => 'required|integer|min:0'
        ]);

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'quantity' => $request->copies
        ]);

        return redirect()->route('admin.books')->with('msg', 'added');
    }

    public function update(Request $request)
    {
        
        $duplicate = DB::table('books_db')
            ->where('isbn', $request->isbn)
            ->where('book_id', '!=', $request->id)
            ->exists();

        if ($duplicate) {
            return redirect()->route('admin.books')->with('msg', 'duplicate');
        }

        $request->validate([
            'id' => 'required|exists:books_db,book_id',
            'title' => 'required',
            'author' => 'required',
            'copies' => 'required|integer|min:0'
        ]);

        $book = Book::findOrFail($request->id);
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'quantity' => $request->copies
        ]);

        return redirect()->route('admin.books')->with('msg', 'updated');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect()->route('admin.books')->with('msg', 'deleted');
    }

    public function studentIndex()
    {
        $uid = Auth::user()->user_id;

        $books = Book::orderBy('title')
            ->get()
            ->map(function ($book) use ($uid) {
                $transaction = Transaction::where('book_id', $book->book_id)
                    ->where('user_id', $uid)
                    ->whereIn('status', ['pending', 'borrowed', 'overdue'])
                    ->first();

                $book->borrow_status = $transaction ? $transaction->status : null;
                return $book;
            });

        return view('student.books', compact('books'));
    }

    public function borrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books_db,book_id',
        ]);

        $uid = Auth::user()->user_id;

        $exists = Transaction::where('book_id', $request->book_id)
            ->where('user_id', $uid)
            ->whereIn('status', ['pending', 'borrowed', 'overdue'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'You already have an active request for this book.']);
        }

        Transaction::create([
            'user_id' => $uid,
            'book_id' => $request->book_id,
            'qty' => 1,
            'status' => 'pending',
            'request_date' => now(),
        ]);

        return redirect()->route('student.books')->with('msg', 'pending');
    }
}