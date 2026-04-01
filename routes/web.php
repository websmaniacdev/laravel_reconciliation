<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OutsourceReceiptController;
use App\Http\Controllers\HostingerInvoiceController;
use App\Http\Controllers\BankStatementController;
use App\Http\Controllers\GodaddyReceiptController;
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
Route::get('/run-invoices-command', [InvoiceController::class, 'runCommand'])->name('invoices.run');


// ── Outsource Receipts ────────────────────────────────────────────────
Route::get('/outsource',            [OutsourceReceiptController::class, 'index'])->name('outsource.index');
Route::post('/outsource/upload',    [OutsourceReceiptController::class, 'upload'])->name('outsource.upload');
Route::get('/outsource/export',     [OutsourceReceiptController::class, 'export'])->name('outsource.export');
Route::post('/outsource/merge', [OutsourceReceiptController::class, 'merge'])->name('outsource.merge');
Route::post('/outsource/merge-by-month', [OutsourceReceiptController::class, 'mergeByMonth'])->name('outsource.mergeByMonth');
Route::delete('/outsource/{receipt}', [OutsourceReceiptController::class, 'destroy'])->name('outsource.destroy');
Route::delete('/outsource/pending/{pending}',        [OutsourceReceiptController::class, 'destroyPending'])->name('outsource.pending.destroy');
Route::post('/outsource/pending/{pending}/retry',    [OutsourceReceiptController::class, 'retryPending'])->name('outsource.pending.retry');
Route::get('/run-outsource-command', [OutsourceReceiptController::class, 'runCommand'])->name('outsource.run');



Route::prefix('hostinger-invoices')->name('hostinger.invoices.')->group(function () {

    Route::get('/',                                    [HostingerInvoiceController::class, 'index'])->name('index');
    Route::post('/upload',                             [HostingerInvoiceController::class, 'upload'])->name('upload');
    Route::get('/export',                              [HostingerInvoiceController::class, 'export'])->name('export');
    Route::delete('/{record}',                         [HostingerInvoiceController::class, 'destroy'])->name('destroy');
    Route::delete('/pending/{pending}',                [HostingerInvoiceController::class, 'destroyPending'])->name('pending.destroy');
    Route::post('/pending/{pending}/retry',            [HostingerInvoiceController::class, 'retryPending'])->name('pending.retry');
    Route::patch('/{id}/client-name', [HostingerInvoiceController::class, 'updateClientName'])->name('update-client-name');
    Route::get('/run-hostinger-invoices-command', [HostingerInvoiceController::class, 'runCommand'])->name('run');
});

Route::prefix('bankstatements')->name('bankstatements.')->group(function () {
    Route::get('/',                         [BankStatementController::class, 'index'])->name('index');
    Route::post('/upload',                  [BankStatementController::class, 'upload'])->name('upload');
    Route::get('/export',                   [BankStatementController::class, 'export'])->name('export');
    Route::post('/merge',                   [BankStatementController::class, 'merge'])->name('merge');
    Route::post('/merge-by-month',          [BankStatementController::class, 'mergeByMonth'])->name('mergeByMonth');
    Route::delete('/{transaction}',         [BankStatementController::class, 'destroy'])->name('destroy');
    Route::delete('/pending/{pending}',     [BankStatementController::class, 'destroyPending'])->name('destroyPending');
    Route::post('/pending/{pending}/retry', [BankStatementController::class, 'retryPending'])->name('retryPending');
    Route::get('/run-bankstatements-command', [BankStatementController::class, 'runCommand'])->name('run');
});


Route::get('/godaddy',            [GodaddyReceiptController::class, 'index'])->name('godaddy.index');
Route::post('/godaddy/upload',    [GodaddyReceiptController::class, 'upload'])->name('godaddy.upload');
Route::get('/godaddy/export',     [GodaddyReceiptController::class, 'export'])->name('godaddy.export');
Route::delete('/godaddy/{receipt}', [GodaddyReceiptController::class, 'destroy'])->name('godaddy.destroy');
Route::delete('/godaddy/pending/{pending}',       [GodaddyReceiptController::class, 'destroyPending'])->name('godaddy.pending.destroy');
Route::post('/godaddy/pending/{pending}/retry',   [GodaddyReceiptController::class, 'retryPending'])->name('godaddy.pending.retry');
Route::get('/run-godaddy-command', [GodaddyReceiptController::class, 'runCommand'])->name('godaddy.run');
