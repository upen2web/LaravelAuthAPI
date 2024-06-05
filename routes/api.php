<?php

use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/password_reset_mail_send', [PasswordResetController::class, 'password_reset_mail_send']);
Route::post('/reset_password/{token}',[PasswordResetController::class, 'reset']);


Route::middleware('auth:sanctum')->group(function() {
Route::delete('/logout', [UserController::class, 'logout']);
Route::get('/loggeduser', [UserController::class, 'loggedUser']);
Route::post('/password-change', [UserController::class, 'password_change']);
});