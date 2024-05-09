<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice', 'index')->name('invoice');
        Route::post('/invoice/addInvoice', 'addInvoice')->name('invoice.addInvoice');
        Route::get('/invoice/{id}/editInvoice', 'editInvoice')->name('invoice.editInvoice');
        Route::get('/invoice/viewInvoice', 'viewInvoice')->name('invoice.viewInvoice');
        Route::put('/invoice/{id}/updateInvoice', 'updateInvoice')->name('invoice.updateInvoice');
        Route::delete('/invoice/{id}/deleteInvoice', 'deleteInvoice')->name('invoice.deleteInvoice');
        Route::get('/products/search', 'productsSearch')->name('products.search');
    });
});

require __DIR__.'/auth.php';
