<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OutsourceReceiptController;
use App\Http\Controllers\HostingerInvoiceController;
use App\Http\Controllers\BankStatementController;
use App\Http\Controllers\GodaddyReceiptController;
use Illuminate\Support\Facades\Route;

Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/',          [InvoiceController::class, 'index'])->name('index');
    Route::post('/upload',  [InvoiceController::class, 'upload'])->name('upload');
    Route::get('/export',   [InvoiceController::class, 'export'])->name('export');
    Route::post('/merge',   [InvoiceController::class, 'merge'])->name('merge');
    Route::post('/merge-by-month', [InvoiceController::class, 'mergeByMonth'])->name('mergeByMonth');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    Route::delete('/pending/{pending}', [InvoiceController::class, 'destroyPending'])->name('pending.destroy');
    Route::post('/pending/{pending}/retry', [InvoiceController::class, 'retryPending'])->name('pending.retry');
    Route::get('/run-invoices-command', [InvoiceController::class, 'runCommand'])->name('run');
});

Route::prefix('outsource')->name('outsource.')->group(function () {
    Route::get('/',            [OutsourceReceiptController::class, 'index'])->name('index');
    Route::post('/upload',    [OutsourceReceiptController::class, 'upload'])->name('upload');
    Route::get('/export',     [OutsourceReceiptController::class, 'export'])->name('export');
    Route::post('/merge', [OutsourceReceiptController::class, 'merge'])->name('merge');
    Route::post('/merge-by-month', [OutsourceReceiptController::class, 'mergeByMonth'])->name('mergeByMonth');
    Route::delete('/{receipt}', [OutsourceReceiptController::class, 'destroy'])->name('destroy');
    Route::delete('/pending/{pending}',        [OutsourceReceiptController::class, 'destroyPending'])->name('pending.destroy');
    Route::post('/pending/{pending}/retry',    [OutsourceReceiptController::class, 'retryPending'])->name('pending.retry');
    Route::get('/run-outsource-command', [OutsourceReceiptController::class, 'runCommand'])->name('run');
});

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

Route::prefix('godaddy')->name('godaddy.')->group(function () {
    Route::get('/',            [GodaddyReceiptController::class, 'index'])->name('index');
    Route::post('/upload',    [GodaddyReceiptController::class, 'upload'])->name('upload');
    Route::get('/export',     [GodaddyReceiptController::class, 'export'])->name('export');
    Route::delete('/{receipt}', [GodaddyReceiptController::class, 'destroy'])->name('destroy');
    Route::delete('/pending/{pending}',       [GodaddyReceiptController::class, 'destroyPending'])->name('pending.destroy');
    Route::post('/pending/{pending}/retry',   [GodaddyReceiptController::class, 'retryPending'])->name('pending.retry');
    Route::get('/run-godaddy-command', [GodaddyReceiptController::class, 'runCommand'])->name('run');
});