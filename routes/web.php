<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UtmController;

Route::get('/utm',[UtmController::class,'showForm'])->name('utm.form');
Route::post('/utm/single',[UtmController::class,'generateSingle'])->name('utm.single');
Route::post('/utm/paragraph',[UtmController::class,'generateParagraph'])->name('utm.paragraph');


Route::get('/', function () {
    return view('welcome');
});
