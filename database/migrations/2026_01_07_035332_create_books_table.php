<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename 'books' to 'books_db'
        Schema::create('books_db', function (Blueprint $table) {
            // 2. Add your specific columns
            $table->id('book_id'); // This creates an auto-incrementing primary key named book_id
            $table->string('title');
            $table->string('author', 50);
            $table->string('isbn', 50);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_db');
    }
};