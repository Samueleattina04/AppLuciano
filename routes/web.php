<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('suppliers', SupplierController::class);
Route::resource('contracts', ContractController::class);
Route::resource('containers', ContainerController::class);
Route::resource('payments', PaymentController::class);

Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.mark-paid');

Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
