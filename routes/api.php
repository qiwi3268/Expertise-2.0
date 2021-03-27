<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\ApiControllers\Miscs\DependencyController;

use App\Http\ApiControllers\Dadata\Banks\BankByBikController;
use App\Http\ApiControllers\Dadata\Organizations\OrganizationByOrgInnController;
use App\Http\ApiControllers\Dadata\Organizations\OrganizationByPersInnController;

use App\Http\ApiControllers\Files\UploadController;
use App\Http\ApiControllers\Files\CheckController;
use App\Http\ApiControllers\Files\HashController;
use App\Http\ApiControllers\Files\NeedsController;
use App\Http\ApiControllers\Files\InternalSignatureValidationController;
use App\Http\ApiControllers\Files\ExternalSignatureValidationController;

use App\Http\ApiControllers\Forms\Expertise\ApplicationSaveController;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::middleware('auth:sanctum')->group(function () {


    Route::get('/miscs/dependency', [DependencyController::class, 'get']);

    Route::get('/dadata/bankByBik', [BankByBikController::class, 'show']);
    Route::get('/dadata/organizationByOrgInn', [OrganizationByOrgInnController::class, 'show']);
    Route::get('/dadata/organizationByPersInn', [OrganizationByPersInnController::class, 'show']);

    Route::post('/files/upload', [UploadController::class, 'upload']);
    Route::post('/files/check', [CheckController::class, 'check']);
    Route::post('/files/hash', [HashController::class, 'hash']);
    Route::post('/files/needs', [NeedsController::class, 'needs']);
    Route::post('/files/internalSignatureValidation', [InternalSignatureValidationController::class, 'validateSignature']);
    Route::post('/files/externalSignatureValidation', [ExternalSignatureValidationController::class, 'validateSignature']);

    // todo rename
    Route::post('/formExpertiseApplicationSave', [ApplicationSaveController::class, 'save']);
//});



