<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PksController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'verified', 'role:lpu,kepala_stasiun'])->group(function () {
    Route::get('/pks', [PksController::class, 'index'])->name('pks.index');

    Route::get('/rekapitulasi/kerja-sama', [RekapitulasiController::class, 'kerjaSama'])->name('rekapitulasi.kerja-sama');
    Route::post('/rekapitulasi/kerja-sama/export', [RekapitulasiController::class, 'exportKerjaSama'])->name('rekapitulasi.kerja-sama.export');
});

Route::middleware(['auth', 'verified', 'role:lpu'])->group(function () {
    Route::get('/pks/preview/cetak', [PksController::class, 'preview'])->name('pks.preview');
    Route::get('/pks/{id}/cetak', [PksController::class, 'cetak'])->whereNumber('id')->name('pks.cetak');

    Route::resource('clients', ClientController::class)->except(['show']);
    Route::resource('pks', PksController::class)->except(['index', 'show']);
    Route::resource('katalog', KatalogController::class);
    Route::resource('tarif', TarifController::class);
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
});

Route::middleware(['auth', 'verified', 'role:penyetor,kepala_stasiun'])->group(function () {
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->whereNumber('id')->name('invoice.show');

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');

    Route::get('/rekapitulasi/penerimaan', [RekapitulasiController::class, 'penerimaan'])->name('rekapitulasi.penerimaan');
    Route::post('/rekapitulasi/penerimaan/export', [RekapitulasiController::class, 'exportPenerimaan'])->name('rekapitulasi.penerimaan.export');
});

Route::middleware(['auth', 'verified', 'role:penyetor'])->group(function () {
    Route::get('/invoice/{id}/cetak', [InvoiceController::class, 'cetak'])->whereNumber('id')->name('invoice.cetak');

    Route::get('/payment/{id}/kwitansi', [PaymentController::class, 'cetakKwitansi'])->whereNumber('id')->name('payment.kwitansi');

    Route::resource('invoice', InvoiceController::class)->except(['index', 'show']);
    Route::patch('/invoice/{id}/billing', [InvoiceController::class, 'updateBilling'])->name('invoice.updateBilling');

    Route::get('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/import-simponi', [PaymentController::class, 'importSimponi'])->name('payment.import-simponi');
    Route::post('/payment/import-simponi/preview', [PaymentController::class, 'previewImportSimponi'])->name('payment.import-simponi.preview');
    Route::post('/payment/import-simponi/reset', [PaymentController::class, 'resetImportSimponi'])->name('payment.import-simponi.reset');
    Route::post('/payment/import-simponi/store', [PaymentController::class, 'storeImportSimponi'])->name('payment.import-simponi.store');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/{id}/edit', [PaymentController::class, 'edit'])->whereNumber('id')->name('payment.edit');
    Route::put('/payment/{id}', [PaymentController::class, 'update'])->whereNumber('id')->name('payment.update');
});

require __DIR__.'/auth.php';
