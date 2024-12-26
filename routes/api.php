<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\CMSController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forget-password', [AuthController::class, 'submitForgetPasswordForm']);
    Route::post('reset-password', [AuthController::class, 'submitResetPasswordForm']);
});


Route::middleware(['optional'])->group(function () {
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('blog/{blog}', [BlogController::class, 'show']);
    Route::get('comments', [CommentController::class, 'index']);
    Route::get('projects/{project}', [ProjectController::class, 'show']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('incomes', IncomeController::class);


    Route::patch('projects/{project}/change-status', [ProjectController::class, 'changeStatus']);


    Route::apiResource('blog', BlogController::class)->except(['index', 'show']);


    Route::apiResource('comments', CommentController::class)->only(['store', 'update', 'destroy']);

    Route::match(['get', 'post'], 'media', [MediaController::class, 'store']);
    Route::delete('media/{media}', [MediaController::class, 'destroy']);
});


Route::middleware(['auth:sanctum'])->group(function (){
    Route::apiResource('logs', UserActivityController::class);
    Route::apiResource('roles',RoleController::class);
    Route::apiResource('managers', ManagerController::class);
    Route::apiResource('donors', DonorController::class);
    Route::apiResource('projects', ProjectController::class)->except('index','show');
    Route::get('my-projects', [ProjectController::class, 'myProjects']);
    Route::get('expenses-pdf', [PDFController::class, 'outcomesPDF']);
    Route::get('incomes-pdf', [PDFController::class, 'incomesPDF']);
    Route::post('/cms/home', [CMSController::class, 'home']);
    Route::post('/cms/about-us', [CMSController::class, 'aboutUs']);
    Route::post('/cms/links', [CMSController::class, 'links']);
    Route::post('/cms/titles', [CMSController::class, 'titles']);
    Route::post('/cms/logos', [CMSController::class, 'logos']);
});


Route::prefix('manager')->middleware(['auth:sanctum','manager'])->group(function (){
    Route::apiResource('roles',RoleController::class);
    Route::apiResource('managers', ManagerController::class);
    Route::apiResource('projects', ProjectController::class);
});


Route::prefix('visitor')->middleware(['auth:sanctum','visitor'])->group(function (){
    Route::apiResource('roles',RoleController::class);
    Route::apiResource('managers', ManagerController::class);
    Route::apiResource('projects', ProjectController::class);
});

    Route::get('/cms/home', [CMSController::class, 'showHome']);
    Route::get('/cms/about-us/', [CMSController::class, 'showAbout']);
    Route::get('/cms/links', [CMSController::class, 'showLinks']);
    Route::get('/cms/titles', [CMSController::class, 'showTitles']);
    Route::get('/cms/logos', [CMSController::class, 'showLogos']);
