<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\OrganizationController;
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

Route::post('/auth/register', [RegistrationController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/api/users/{id}', [UserController::class, 'show']);
    Route::get('/api/organisations', [OrganizationController::class, 'index']);
    Route::get('/api/organisations/{orgId}', [OrganizationController::class, 'show']);
    Route::post('/api/organisations', [OrganizationController::class, 'store']);
    Route::post('/api/organisations/{orgId}/users', [OrganizationController::class, 'addUserToOrganisation']);
});
