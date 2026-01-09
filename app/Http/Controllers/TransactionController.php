<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function pendingRequests()
    {
        $admin_name = Auth::user()->name; 
        
        $requests = Transaction::with(['book', 'user'])
            ->where('status', 'pending')
            ->orderByDesc('request_date')
            ->get();

        return view('admin.requests', compact('admin_name', 'requests'));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        $transaction = Transaction::with('book')->findOrFail($request->transaction_id);
        
        if ($transaction->book->quantity <= 0) {
            return redirect()->route('admin.requests')->with('msg', 'failed');
        }

        $transaction->update([
            'status' => 'borrowed',
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
        ]);

        $transaction->book()->decrement('quantity');

        return redirect()->route('admin.requests')->with('msg', 'approved');
    }

    public function reject($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 'rejected']);

        return redirect()->route('admin.requests')->with('msg', 'rejected');
    }

    public function rejectedList()
    {
        $admin_name = Auth::user()->name;

        $rejectedRequests = Transaction::with(['book', 'user'])
            ->where('status', 'rejected')
            ->orderByDesc('request_date')
            ->get();

        return view('admin.rejected', compact('admin_name', 'rejectedRequests'));
    }

    public function borrowedList(Request $request)
    {
        $admin_name = Auth::user()->name;

        Transaction::where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->whereNull('return_date')
            ->update(['status' => 'overdue']);

        $status = $request->get('status', 'borrowed');
        if (!in_array($status, ['borrowed', 'overdue'])) {
            $status = 'borrowed';
        }

        $transactions = Transaction::with(['book', 'user'])
            ->where('status', $status)
            ->orderByDesc('issue_date')
            ->get();

        return view('admin.borrowed', compact('admin_name', 'transactions', 'status'));
    }

    public function allTransactions()
    {
        $admin_name = Auth::user()->name;

        $transactions = Transaction::with(['book', 'user'])
            ->orderByDesc('id')
            ->get();

        return view('admin.transactions', compact('admin_name', 'transactions'));
    }
}