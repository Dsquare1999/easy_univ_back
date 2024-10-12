<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\V1\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\V1\Auth\NewPasswordController;
use App\Http\Controllers\V1\Auth\PasswordResetLinkController;
use App\Http\Controllers\V1\Auth\RegisteredUserController;
use App\Http\Controllers\V1\Auth\VerifyEmailController;

use App\Http\Controllers\V1\TagController;
use App\Http\Controllers\V1\CycleController;
use App\Http\Controllers\V1\FiliereController;
use App\Http\Controllers\V1\InvoiceController;
use App\Http\Controllers\V1\ClasseController;
use App\Http\Controllers\V1\MatiereController;
use App\Http\Controllers\V1\StudentController;
use App\Http\Controllers\V1\ProgramController;
use App\Http\Controllers\V1\ReleveController;


enum TokenAbility: string
{
    case ISSUE_ACCESS_TOKEN = 'issue-access-token';
    case ACCESS_API = 'access-api';
}

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/v1/auth')->middleware('auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value)->group(function () {
    Route::get('/refresh-token', [AuthenticatedSessionController::class, 'refreshToken']);
});


// guest routes
Route::prefix('/v1/auth')->middleware(['guest'])->group(function(){
    Route::post('/register', [RegisteredUserController::class, 'store'])
                ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->name('login');


    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

// auth routes
Route::prefix('/v1')->middleware(['auth'])->group(function(){

});

Route::prefix('/v1/users')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [AuthenticatedSessionController::class, "index"]);
    Route::post("/turnProfessor", [AuthenticatedSessionController::class, "turnProfessor"]);
    Route::post("/turnStudent", [AuthenticatedSessionController::class, "turnStudent"]);
    Route::delete("/deleteUser/{id}", [AuthenticatedSessionController::class, "deleteUser"]);
});



Route::prefix('/v1/students')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [StudentController::class, "index"]);
    Route::post("/store", [StudentController::class,"store"]);
    Route::post("/validate", [StudentController::class,"validate"]);
    Route::post("/refuse", [StudentController::class,"refuse"]);
    Route::delete("/destroy/{id}",  [StudentController::class,"destroy"]);
});


Route::prefix('/v1/tags')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [TagController::class, "index"]);
    Route::get("/show/{id}", [TagController::class,"show"]);
    Route::post("/store", [TagController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [TagController::class,"update"]);
    Route::delete("/destroy/{id}",  [TagController::class,"destroy"]);
});

Route::prefix('/v1/classes')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [ClasseController::class, "index"]);
    Route::get("/show/{id}", [ClasseController::class,"show"]);
    Route::post("/store", [ClasseController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [ClasseController::class,"update"]);
    Route::delete("/destroy/{id}",  [ClasseController::class,"destroy"]);
});
 
Route::prefix('/v1/matieres')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [MatiereController::class, "index"]);
    Route::get("/show/{id}", [MatiereController::class,"show"]);
    Route::post("/store", [MatiereController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [MatiereController::class,"update"]);
    Route::delete("/destroy/{id}",  [MatiereController::class,"destroy"]);
});
 
Route::prefix('/v1/programs')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [ProgramController::class, "index"]);
    Route::get("/show/{id}", [ProgramController::class,"show"]);
    Route::post("/store", [ProgramController::class,"store"]);
    Route::post("/report", [ProgramController::class,"report"]);
    Route::match(['put', 'patch'], '/update/{id}',  [ProgramController::class,"update"]);
    Route::delete("/destroy/{id}",  [ProgramController::class,"destroy"]);
});
 
Route::prefix('/v1/releves')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [ReleveController::class, "index"]);
    Route::get("/show/{id}", [ReleveController::class,"show"]);
    Route::post("/store", [ReleveController::class,"store"]);
    Route::post("/mark", [ReleveController::class,"mark"]);
    Route::post("/generate/{id}",  [ReleveController::class,"generate"]);
    Route::match(['put', 'patch'], '/update/{id}',  [ReleveController::class,"update"]);
    Route::delete("/destroy/{id}",  [ReleveController::class,"destroy"]);
});
 

Route::prefix('/v1/cycles')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [CycleController::class, "index"]);
    Route::get("/show/{id}", [CycleController::class,"show"]);
    Route::post("/store", [CycleController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [CycleController::class,"update"]);
    Route::delete("/destroy/{id}",  [CycleController::class,"destroy"]);
});
 
Route::prefix('/v1/filieres')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [FiliereController::class, "index"]);
    Route::get("/show/{id}", [FiliereController::class,"show"]);
    Route::post("/store", [FiliereController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [FiliereController::class,"update"]);
    Route::delete("/destroy/{id}",  [FiliereController::class,"destroy"]);
});

Route::prefix('/v1/invoices')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function(){
    Route::get("/", [InvoiceController::class, "index"]);
    Route::get("/show/{id}", [InvoiceController::class,"show"]);
    Route::post("/store", [InvoiceController::class,"store"]);
    Route::match(['put', 'patch'], '/update/{id}',  [InvoiceController::class,"update"]);
    Route::delete("/destroy/{id}",  [InvoiceController::class,"destroy"]);

    Route::get('/getInvoicesByUser/{user_id}', [InvoiceController::class, 'getInvoicesByUser']);
    Route::get('/getInvoicesByClasse/{classe_id}', [InvoiceController::class, 'getInvoicesByClasse']);
    Route::get('/getInvoicesByTag/{tag_id}', [InvoiceController::class, 'getInvoicesByTag']);
});


Route::prefix('/v1/auth')->middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->group(function () {
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    Route::post('/update', [AuthenticatedSessionController::class, 'update'])
        ->name('update');

    Route::post('/updateCover', [AuthenticatedSessionController::class, 'updateCover'])
        ->name('updateCover');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});