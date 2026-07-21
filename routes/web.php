<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortariaController;

Route::get('/', [PortariaController::class,'index'])->name('portarias.index');
Route::get('/create',  [PortariaController::class, 'create'])->name('portarias.create');
Route::post('/store', [PortariaController::class, 'store'])->name('portarias.store');
Route::get('/portarias/{portaria}', [PortariaController::class, 'show'])->name('portarias.show');
Route::get('/portarias/{portaria}/edit', [PortariaController::class, 'edit'])->name('portarias.edit');
Route::delete('/portaria/{portaria}', [PortariaController::class, 'destroy'])->name('portarias.destroy');
