<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     * Explicitly defined for legacy database compatibility.
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     * These match your legacy database columns.
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'qty',
        'request_date',
        'issue_date',
        'due_date',
        'return_date',
        'status'
    ];

    /**
     * Indicates if the model should be timestamped.
     * Set to false because legacy tables usually don't have created_at/updated_at.
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     * Converting strings to Carbon objects for easier formatting in Blade.
     */
    protected $casts = [
        'request_date' => 'date',
        'issue_date'   => 'date',
        'due_date'     => 'date',
        'return_date'  => 'date',
    ];

    /**
     * Relationship: A transaction belongs to a User.
     * Maps user_id to the custom primary key in your users table.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: A transaction belongs to a Book.
     * Maps book_id to the custom primary key in your books_db table.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }
}