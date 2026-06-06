<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PksController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pks', [PksController::class, 'index'])->name('pks.index');
    Route::get('/pks/preview/cetak', [PksController::class, 'preview'])->name('pks.preview');
    Route::get('/pks/{id}/cetak', [PksController::class, 'cetak'])->whereNumber('id')->name('pks.cetak');

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->whereNumber('id')->name('invoice.show');
    Route::get('/invoice/{id}/cetak', [InvoiceController::class, 'cetak'])->whereNumber('id')->name('invoice.cetak');

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{id}/kwitansi', [PaymentController::class, 'cetakKwitansi'])->whereNumber('id')->name('payment.kwitansi');
});

Route::middleware(['auth', 'verified', 'role:lpu'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('pks', PksController::class)->except(['index', 'show']);
    Route::resource('katalog', KatalogController::class);
    Route::resource('tarif', TarifController::class);
});

Route::middleware(['auth', 'verified', 'role:penyetor'])->group(function () {
    Route::resource('invoice', InvoiceController::class)->except(['index', 'show']);
    Route::patch('/invoice/{id}/billing', [InvoiceController::class, 'updateBilling'])->name('invoice.updateBilling');

    Route::get('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/{id}/edit', [PaymentController::class, 'edit'])->whereNumber('id')->name('payment.edit');
    Route::put('/payment/{id}', [PaymentController::class, 'update'])->whereNumber('id')->name('payment.update');
});

require __DIR__.'/auth.php';
