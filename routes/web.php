<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CommunicationTaskController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShipmentArrivalTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('contracts', ContractController::class);
    Route::resource('shipments', ShipmentController::class);
    Route::post('shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
    Route::post('shipments/{shipment}/arrival-tasks', [ShipmentArrivalTaskController::class, 'store'])->name('shipments.arrival-tasks.store');
    Route::post('shipments/{shipment}/arrival-tasks/defaults', [ShipmentArrivalTaskController::class, 'addDefaults'])->name('shipments.arrival-tasks.defaults');
    Route::patch('shipments/{shipment}/arrival-tasks/{task}/toggle', [ShipmentArrivalTaskController::class, 'toggle'])->name('shipments.arrival-tasks.toggle');
    Route::delete('shipments/{shipment}/arrival-tasks/{task}', [ShipmentArrivalTaskController::class, 'destroy'])->name('shipments.arrival-tasks.destroy');

    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.mark-paid');

    Route::resource('claims', ClaimController::class);

    Route::resource('communications', CommunicationTaskController::class);
    Route::post('communications/{communication}/mark-replied', [CommunicationTaskController::class, 'markReplied'])->name('communications.mark-replied');

    Route::resource('suppliers', SupplierController::class);

    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::get('search', [SearchController::class, 'global'])->name('search.global');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
