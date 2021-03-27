<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Home\Navigation\NavigationController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [LoginController::class, 'show']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');






Route::get('/files/download/{starPath}', ['App\Http\Controllers\FileController', 'download'])
    ->where('starPath', '.*');

Route::get('/expertise/application/create', ['App\Http\Controllers\CreateApplication', 'create'])->name('application.create');

Route::middleware('auth')->group(function () {

    Route::get('/home/navigation', [NavigationController::class, 'show'])->name('navigation');
    Route::get('/test', ['App\Http\Controllers\Test', 'test']);
});

