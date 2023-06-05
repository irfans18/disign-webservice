<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
// Route::post('/csr', [CertificateSigningRequestController::class, 'signCsr']);
// Route::post('/revoke', [CertificateRevokationController::class, 'revoke']);
Route::get('/mfs', function () {
   Artisan::call('migrate:fresh', ['--seed' => true]);

   return response()->json(['message' => 'Migration and seeding completed.']);

});

Route::middleware('auth:sanctum')->group(function () {
   // Route::get('/devices', [DeviceController::class, 'showUserDevices']);
   Route::post('/device', [DeviceController::class, 'register']);
   // Route::post('/check-device', [DeviceController::class, 'checkDevice']);

   Route::post('/isvalid', [CertificateRevokationController::class, 'checkLicenceValidation']);
   Route::post('/revoke', [CertificateRevokationController::class, 'requestRevocation']);
   Route::post('/csr', [CertificateSigningRequestController::class, 'signCsr']);

   Route::get('/user/devices', [UserController::class, 'getUserDevices']);
   Route::get('/user/{hwid}', [UserController::class, 'userInfo']);
   Route::post('/auth', [UserController::class, 'pinAuth']);
   Route::post('/logout', [UserController::class, 'logout']);
});
