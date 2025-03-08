<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\ReportController;
// ✅ استرجاع بيانات المستخدم بعد تسجيل الدخول
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ مثال على إضافة مسارات الـ API الخاصة بالمستخدمين
Route::controller(UserController::class)->group(function(){
    Route::post('register',  'register');   
    Route::post('login',  'login');       
    
});

// ✅ مثال على إضافة مسارات الملف الشخصي
Route::middleware('auth:sanctum')->prefix('profile')->controller(App\Http\Controllers\ProfileController::class)->group(function(){
    Route::get('/', 'index');     
    Route::post('/', 'store');    
    Route::put('/{id}', 'update');
    Route::get('/{id}', 'show');  
});

Route::middleware('auth:sanctum')->prefix('questions')->controller(QuestionController::class)->group(function () {
    Route::get('/', 'index');        
    Route::post('/', 'store');       
    Route::delete('/{id}', 'destroy'); 
    Route::put('/{id}', 'update');  
    Route::get('/{id}', 'show'); 
});
Route::middleware('auth:sanctum')->prefix('rounds')->controller(RoundController::class)->group(function () {
    Route::get('/', 'getAllRound');            
    Route::post('/', 'createRound');         
   // Route::delete('/{id}', 'destroy');   
    //Route::put('/{id}', 'update');        
    Route::get('/{id}', 'getRound');  
    Route::post('/{id}/calculatePoints',  'calculatePoints');       
    //Route::get('/{id}/calculate-points', 'calculatePoints'); 
Route::post('/{roundId}/submit-answers', 'submitAnswers');
Route::get('/{roundId}/questions',  'getRoundQuestions');

});
Route::middleware('auth:sanctum')->prefix('reports')->controller(ReportController::class)->group(function () {
    Route::get('/leaderboard', 'leaderboard');
    Route::get('/users', 'allUsersReport');
    Route::post('/submit-answers', 'submitAnswers'); 
    Route::get('/statistics', 'getStatisticsByCategory');
    Route::get('/questions',  'getQuestionById');
    Route::get('/statistics/{userId}', 'getUserStatistics');
    Route::get('/user-report/{user_id}', 'getUserReport');
    Route::post('/redeem',  'redeem');
    
});