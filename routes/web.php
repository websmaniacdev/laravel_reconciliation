<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OutsourceReceiptController;
use App\Http\Controllers\HostingerInvoiceController;
use Illuminate\Support\Facades\Route;

// ── Invoice Records ───────────────────────────────────────────────────
Route::get('/invoices',          [InvoiceController::class, 'index'])->name('invoices.index');
Route::post('/invoices/upload',  [InvoiceController::class, 'upload'])->name('invoices.upload');
Route::get('/invoices/export',   [InvoiceController::class, 'export'])->name('invoices.export');
Route::post('/invoices/merge',   [InvoiceController::class, 'merge'])->name('invoices.merge');
Route::post('/invoices/merge-by-month', [InvoiceController::class, 'mergeByMonth'])->name('invoices.mergeByMonth');
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

Route::delete('/invoices/pending/{pending}', [InvoiceController::class, 'destroyPending'])->name('invoices.pending.destroy');
Route::post('/invoices/pending/{pending}/retry', [InvoiceController::class, 'retryPending'])->name('invoices.pending.retry');


// ── Outsource Receipts ────────────────────────────────────────────────
Route::get('/outsource',            [OutsourceReceiptController::class, 'index'])->name('outsource.index');
Route::post('/outsource/upload',    [OutsourceReceiptController::class, 'upload'])->name('outsource.upload');
Route::get('/outsource/export',     [OutsourceReceiptController::class, 'export'])->name('outsource.export');
Route::post('/outsource/merge', [OutsourceReceiptController::class, 'merge'])->name('outsource.merge');
Route::post('/outsource/merge-by-month', [OutsourceReceiptController::class, 'mergeByMonth'])->name('outsource.mergeByMonth');
Route::delete('/outsource/{receipt}', [OutsourceReceiptController::class, 'destroy'])->name('outsource.destroy');

Route::delete('/outsource/pending/{pending}',        [OutsourceReceiptController::class, 'destroyPending'])->name('outsource.pending.destroy');
Route::post('/outsource/pending/{pending}/retry',    [OutsourceReceiptController::class, 'retryPending'])->name('outsource.pending.retry');



Route::prefix('hostinger-invoices')->name('hostinger.invoices.')->group(function () {

    Route::get('/',                                    [HostingerInvoiceController::class, 'index'])->name('index');
    Route::post('/upload',                             [HostingerInvoiceController::class, 'upload'])->name('upload');
    Route::get('/export',                              [HostingerInvoiceController::class, 'export'])->name('export');

    // Record delete
    Route::delete('/{record}',                         [HostingerInvoiceController::class, 'destroy'])->name('destroy');

    // Pending PDF actions
    Route::delete('/pending/{pending}',                [HostingerInvoiceController::class, 'destroyPending'])->name('pending.destroy');
    Route::post('/pending/{pending}/retry',            [HostingerInvoiceController::class, 'retryPending'])->name('pending.retry');
});
