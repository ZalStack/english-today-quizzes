<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\DivisionController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\QuizCategoryController;
use App\Http\Controllers\HR\QuizController as HRQuizController;
use App\Http\Controllers\HR\QuestionController;
use App\Http\Controllers\HR\ReportController;
use App\Http\Controllers\HR\VideoChallengeController as HRVideoChallengeController;
use App\Http\Controllers\HR\MateriController as HRMateriController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\QuizController as EmployeeQuizController;
use App\Http\Controllers\Employee\VideoChallengeController as EmployeeVideoChallengeController;
use App\Http\Controllers\Employee\MateriController as EmployeeMateriController;
use App\Http\Controllers\Employee\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome Page
// Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
// Welcome Page - Redirect ke login
Route::get('/', function () {
    return redirect('/login');
})->name('welcome');

// Authentication Routes (Breeze)
require __DIR__.'/auth.php';

// HR Routes
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
   Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');

    // Division Management
    Route::resource('divisions', DivisionController::class);

    // Employee Management
    Route::get('/employees/bulk/division', [EmployeeController::class, 'bulkDivision'])->name('employees.bulk.division');
    Route::post('/employees/bulk/division', [EmployeeController::class, 'bulkDivisionUpdate'])->name('employees.bulk.division.update');
    Route::resource('employees', EmployeeController::class);

    // Profile Management untuk HR
    Route::get('/profile', [App\Http\Controllers\HR\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\HR\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\HR\ProfileController::class, 'updatePassword'])->name('profile.password');

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

    // Video Challenges
    Route::resource('video-challenges', HRVideoChallengeController::class);
    Route::get('/video-challenges/{videoChallenge}/export-submissions', [HRVideoChallengeController::class, 'exportSubmissions'])->name('video-challenges.export-submissions');

    // Materi Management
    Route::get('/materi/{materi}/download', [HRMateriController::class, 'download'])->name('materi.download');
    Route::resource('materi', HRMateriController::class);
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
    Route::get('/quizzes/take/{attempt}/unanswered', [EmployeeQuizController::class, 'unansweredCount'])->name('quizzes.unanswered');

    // Results & History
    Route::get('/quizzes/result/{attempt}', [EmployeeQuizController::class, 'result'])->name('quizzes.result');
    Route::get('/quizzes/history', [EmployeeQuizController::class, 'history'])->name('quizzes.history');

    // Video Challenges
    Route::get('/video-challenges', [EmployeeVideoChallengeController::class, 'index'])->name('video-challenges.index');
    Route::post('/video-challenges/{videoChallenge}/submit', [EmployeeVideoChallengeController::class, 'submit'])->name('video-challenges.submit');

    // Materi
    Route::get('/materi', [EmployeeMateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{materi}', [EmployeeMateriController::class, 'show'])->name('materi.show');
    Route::get('/materi/{materi}/download', [EmployeeMateriController::class, 'download'])->name('materi.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/quizzes/{quiz}/start', function (\App\Models\Quiz $quiz) {
        return redirect()->route('employee.quizzes.preview', $quiz);
    })->name('quizzes.start.redirect');
});

// Redirect to appropriate dashboard after login
Route::get('/dashboard', function () {
    if (auth()->user()->isHR()) {
        return redirect()->route('hr.dashboard');
    }
    return redirect()->route('employee.dashboard');
})->middleware(['auth'])->name('dashboard');
