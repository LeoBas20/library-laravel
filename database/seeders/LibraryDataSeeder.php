<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LibraryDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Move Users
        $oldUsers = DB::connection('legacy')->table('users')->get();
        foreach ($oldUsers as $user) {
            DB::table('users')->updateOrInsert(
                ['user_id' => $user->user_id],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password, // Keeps old passwords
                    'role' => $user->role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Move Books
        $oldBooks = DB::connection('legacy')->table('books_db')->get();
        foreach ($oldBooks as $book) {
            DB::table('books_db')->updateOrInsert(
                ['book_id' => $book->book_id],
                [
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'quantity' => $book->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Move Transactions
        $oldTransactions = DB::connection('legacy')->table('transactions')->get();
        foreach ($oldTransactions as $tx) {
            DB::table('transactions')->updateOrInsert(
                ['id' => $tx->id],
                [
                    'user_id' => $tx->user_id,
                    'book_id' => $tx->book_id,
                    'qty' => $tx->qty,
                    'request_date' => $tx->request_date,
                    'issue_date' => $tx->issue_date,
                    'due_date' => $tx->due_date,
                    'return_date' => $tx->return_date,
                    'status' => $tx->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}