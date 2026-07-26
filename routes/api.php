<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Hr\DashboardController as HrDashboardController;
use App\Http\Controllers\Api\Hr\DivisionController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use App\Http\Controllers\Api\Hr\QuizCategoryController;
use App\Http\Controllers\Api\Hr\QuizController;
use App\Http\Controllers\Api\Hr\QuestionController;
use App\Http\Controllers\Api\Hr\ReportController;
use App\Http\Controllers\Api\Hr\ProfileController as HrProfileController;

use App\Http\Controllers\Api\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Api\Employee\QuizController as EmployeeQuizController;
use App\Http\Controllers\Api\Employee\VideoChallengeController as EmployeeVideoChallengeController;
use App\Http\Controllers\Api\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\HR\VideoChallengeController as HRVideoChallengeController;

/*
|--------------------------------------------------------------------------
| API Routes untuk HR
|--------------------------------------------------------------------------
*/
Route::prefix('hr')->group(function () {
    Route::get('/dashboard', [HrDashboardController::class, 'index']);

    Route::apiResource('divisions', DivisionController::class);

    Route::get('/employees/bulk/division', [EmployeeController::class, 'bulkDivision']);
    Route::post('/employees/bulk/division', [EmployeeController::class, 'bulkDivisionUpdate']);
    Route::apiResource('employees', EmployeeController::class);

    Route::get('/profile', [HrProfileController::class, 'edit']);
    Route::put('/profile', [HrProfileController::class, 'update']);
    Route::put('/profile/password', [HrProfileController::class, 'updatePassword']);

    Route::apiResource('categories', QuizCategoryController::class);

    Route::apiResource('quizzes', QuizController::class);

    Route::prefix('quizzes/{quiz}/questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::post('/import', [QuestionController::class, 'importFromPdf']);
        Route::post('/import/confirm', [QuestionController::class, 'confirmImport']);
        Route::get('/import/cancel', [QuestionController::class, 'cancelImport']);
        Route::get('/{question}', [QuestionController::class, 'show']);
        Route::put('/{question}', [QuestionController::class, 'update']);
        Route::delete('/{question}', [QuestionController::class, 'destroy']);
    });

    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{quiz}', [ReportController::class, 'show']);
    Route::get('/reports/{quiz}/export', [ReportController::class, 'export']);

    Route::apiResource('video-challenges', HRVideoChallengeController::class);
});

/*
|--------------------------------------------------------------------------
| API Routes untuk Employee
|--------------------------------------------------------------------------
*/
Route::prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index']);

    Route::get('/quizzes/join', [EmployeeQuizController::class, 'join']);
    Route::post('/quizzes/enroll', [EmployeeQuizController::class, 'enroll']);
    Route::get('/quizzes/{quiz}/preview', [EmployeeQuizController::class, 'preview']);
    Route::post('/quizzes/{quiz}/start', [EmployeeQuizController::class, 'start']);
    Route::get('/quizzes/take/{attempt}', [EmployeeQuizController::class, 'take']);
    Route::post('/quizzes/take/{attempt}/save', [EmployeeQuizController::class, 'saveAnswer']);
    Route::post('/quizzes/take/{attempt}/submit', [EmployeeQuizController::class, 'submit']);
    Route::get('/quizzes/take/{attempt}/unanswered', [EmployeeQuizController::class, 'unansweredCount']);
    Route::get('/quizzes/result/{attempt}', [EmployeeQuizController::class, 'result']);
    Route::get('/quizzes/history', [EmployeeQuizController::class, 'history']);

    Route::get('/video-challenges', [EmployeeVideoChallengeController::class, 'index']);
    Route::post('/video-challenges/{videoChallenge}/submit', [EmployeeVideoChallengeController::class, 'submit']);

    Route::get('/profile', [EmployeeProfileController::class, 'edit']);
    Route::put('/profile', [EmployeeProfileController::class, 'update']);
    Route::put('/profile/password', [EmployeeProfileController::class, 'updatePassword']);
});
