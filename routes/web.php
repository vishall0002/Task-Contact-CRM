<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CustomFieldController;
 use App\Http\Controllers\ContactController;
 use App\Http\Controllers\ContactMergeController;

Route::get('/', function () {
    return view('auth');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);






Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

      Route::get('/custom-fields', [CustomFieldController::class, 'index'])
        ->name('custom.fields');

    Route::post('/custom-fields', [CustomFieldController::class, 'store'])
        ->name('custom.fields.store');

     Route::post('/custom-fields/status', [CustomFieldController::class, 'updateStatus'])
    ->name('custom.fields.status');

    Route::post('/custom-fields/delete', [CustomFieldController::class, 'bulkDelete'])
    ->name('custom.fields.delete');

    Route::post('/custom-fields/update', [CustomFieldController::class, 'update'])
    ->name('custom.fields.update');




    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

     // Add this for contacts CRUD
   Route::get('/contacts/custom-fields/{id}', [ContactController::class, 'getCustomFields']);

   Route::get('/contacts/custom-fields/{id}', function ($id) {
        return \App\Models\ContactCustomFieldValue::with('customField')
            ->where('contact_id', $id)
            ->get();
    });


    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/store', [ContactController::class, 'store'])->name('contacts.store');
    Route::post('/contacts/update', [ContactController::class, 'update'])->name('contacts.update');
    Route::post('/contacts/delete', [ContactController::class, 'delete'])->name('contacts.delete');
    Route::get('/contacts/{id}', [ContactController::class, 'show'])->name('contacts.show');


   

// Route::post('/contacts/merge/preview', [ContactMergeController::class, 'preview'])
//     ->name('contacts.merge.preview');

Route::match(['get', 'post'], '/contacts/merge/preview',
    [ContactMergeController::class, 'preview']
)->name('contacts.merge.preview');


Route::post('/contacts/merge/confirm', [ContactMergeController::class, 'confirm'])
    ->name('contacts.merge.confirm');



});
