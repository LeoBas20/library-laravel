<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionController;


Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::middleware(['guest'])->group(function () {
    Route::get('/login/student', [LoginController::class, 'showStudentLogin'])->name('login');
    Route::get('/login/student-portal', [LoginController::class, 'showStudentLogin'])->name('login.student');
    Route::get('/login/admin', [LoginController::class, 'showAdminLogin'])->name('login.admin');
});

Route::middleware(['auth'])->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    //Admin Section
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {

        // Dashboard, Student List, and Profile
        Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
        Route::get('/students', [DashboardController::class, 'studentList'])->name('admin.students');
        Route::get('/profile', [DashboardController::class, 'adminProfile'])->name('admin.profile');
        Route::post('/profile', [DashboardController::class, 'updateAdminProfile'])->name('admin.profile.update');
        
        // Password Management
        Route::get('/change-password', [DashboardController::class, 'adminChangePassword'])->name('admin.changepass');
        Route::post('/change-password', [DashboardController::class, 'updateAdminPassword'])->name('admin.password.update');

        // Books Management
        Route::get('/books', [BookController::class, 'index'])->name('admin.books');
        Route::post('/books/store', [BookController::class, 'store'])->name('admin.books.store');
        Route::post('/books/update', [BookController::class, 'update'])->name('admin.books.update');
        Route::get('/books/delete/{id}', [BookController::class, 'destroy'])->name('admin.books.delete');
        
        // Transaction
        Route::get('/requests', [TransactionController::class, 'pendingRequests'])->name('admin.requests');
        Route::post('/requests/approve', [TransactionController::class, 'approve'])->name('admin.requests.approve');
        Route::get('/requests/reject/{id}', [TransactionController::class, 'reject'])->name('admin.requests.reject');
        Route::get('/borrowed', [TransactionController::class, 'borrowedList'])->name('admin.borrowed.list');
        Route::get('/rejected', [TransactionController::class, 'rejectedList'])->name('admin.rejected');

        Route::get('/transactions', [TransactionController::class, 'allTransactions'])->name('admin.transactions.all');
        
    });

    //Student Section
    Route::prefix('student')->middleware(['role:student'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'studentIndex'])->name('student.dashboard');
        
        Route::get('/books', [BookController::class, 'studentIndex'])->name('student.books');
        Route::post('/books/borrow', [BookController::class, 'borrow'])->name('student.books.borrow');
        
        Route::get('/borrowed', [StudentController::class, 'borrowed'])->name('student.borrowed');
        Route::post('/books/return', [StudentController::class, 'returnBook'])->name('student.books.return');

        Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
        Route::post('/profile', [StudentController::class, 'updateProfile'])->name('student.profile.update');
        Route::get('/change-password', [StudentController::class, 'changePassword'])->name('student.changepass');
        Route::post('/change-password', [StudentController::class, 'updatePassword'])->name('student.password.update');
    });
});

Route::get('/home', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return strtolower($user->role) === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('student.dashboard');
    }
    return redirect('/');
});