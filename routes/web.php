<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\DivisionController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\QuizCategoryController;
use App\Http\Controllers\HR\QuizController as HRQuizController;
use App\Http\Controllers\HR\QuestionController;
use App\Http\Controllers\HR\ReportController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\QuizController as EmployeeQuizController;
use App\Http\Controllers\Employee\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome Page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authentication Routes (Breeze)
require __DIR__.'/auth.php';

// HR Routes
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');

    // Division Management
    Route::resource('divisions', DivisionController::class);

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');

    // Quiz Categories
    Route::resource('categories', QuizCategoryController::class);

    // Quiz Management
    Route::resource('quizzes', HRQuizController::class);

    // Question Management
    Route::prefix('quizzes/{quiz}/questions')->name('quizzes.questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        
        Route::post('/import', [QuestionController::class, 'importFromPdf'])->name('import');
        Route::post('/import/confirm', [QuestionController::class, 'confirmImport'])->name('import.confirm');
        Route::get('/import/cancel', [QuestionController::class, 'cancelImport'])->name('import.cancel');


        Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{question}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('destroy');
        
    });

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{quiz}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{quiz}/export', [ReportController::class, 'export'])->name('reports.export');
});

// Employee Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Join Quiz
    Route::get('/quizzes/join', [EmployeeQuizController::class, 'join'])->name('quizzes.join');
    Route::post('/quizzes/enroll', [EmployeeQuizController::class, 'enroll'])->name('quizzes.enroll');

    // Quiz Taking
    Route::get('/quizzes/{quiz}/preview', [EmployeeQuizController::class, 'preview'])->name('quizzes.preview');
    Route::post('/quizzes/{quiz}/start', [EmployeeQuizController::class, 'start'])->name('quizzes.start');
    Route::get('/quizzes/take/{attempt}', [EmployeeQuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quizzes/take/{attempt}/save', [EmployeeQuizController::class, 'saveAnswer'])->name('quizzes.save');
    Route::post('/quizzes/take/{attempt}/submit', [EmployeeQuizController::class, 'submit'])->name('quizzes.submit');

    // Results & History
    Route::get('/quizzes/result/{attempt}', [EmployeeQuizController::class, 'result'])->name('quizzes.result');
    Route::get('/quizzes/history', [EmployeeQuizController::class, 'history'])->name('quizzes.history');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Redirect to appropriate dashboard after login
Route::get('/dashboard', function () {
    if (auth()->user()->isHR()) {
        return redirect()->route('hr.dashboard');
    }
    return redirect()->route('employee.dashboard');
})->middleware(['auth'])->name('dashboard');
