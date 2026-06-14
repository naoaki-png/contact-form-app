<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'index'])->name('home.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/contacts/thanks', function () {
    return view('contact.thanks');
})->name('contact.thanks');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/contacts/export', [AdminController::class, 'downloadCsv'])->name('admin.contacts.csv');
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('tag.edit');
    Route::post('/admin/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy'])->name('tag.destroy');
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])->name('tag.update');

});
