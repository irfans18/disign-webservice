<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\CertificateRevokationController;
use App\Http\Controllers\API\CertificateSigningRequestController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
   return $request->user();
});

// Route::get('/', function () {
//    return view('welcome');
// });

Route::get('test/', function () {
    return response()->json(['message' => 'Hello brader']);
});
Route::get('/mfs', function () {
   Artisan::call('migrate:fresh', ['--seed' => true]);

   return response()->json(['message' => 'Migration and seeding completed.']);
});
Route::post('/upload', [StorageController::class, 'upload']);
Route::get('/show/{filename}', [StorageController::class, 'show']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
   Route::post('/device', [DeviceController::class, 'register']);

   Route::post('/isvalid', [CertificateRevokationController::class, 'checkLicenceValidation']);
   Route::post('/revoke', [CertificateRevokationController::class, 'requestRevocation']);
   Route::post('/csr', [CertificateSigningRequestController::class, 'signCsr']);

   Route::get('/user/devices', [UserController::class, 'getUserDevices']);
   Route::get('/user/{hwid}', [UserController::class, 'userInfo']);
   Route::post('/auth', [UserController::class, 'pinAuth']);
   Route::post('/logout', [UserController::class, 'logout']);
});
