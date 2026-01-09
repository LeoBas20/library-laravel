<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books_db'; 
    protected $primaryKey = 'book_id';
    
    protected $fillable = ['title', 'author', 'isbn', 'quantity'];
}