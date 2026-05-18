<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PksController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::resource('katalog', KatalogController::class);


Route::resource('pks', PksController::class);
Route::resource('katalog', \App\Http\Controllers\KatalogController::class);
Route::resource('tarif', \App\Http\Controllers\TarifController::class);
Route::get('/pks/{id}/cetak', [PksController::class, 'cetak'])->name('pks.cetak');
Route::get('/pks', [PksController::class, 'index'])->name('pks.index');
require __DIR__.'/auth.php';
