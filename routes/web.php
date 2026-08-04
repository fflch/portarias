<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortariaController;

Route::get('/', [PortariaController::class,'index'])->name('portarias.index');
Route::get('/portarias/create',  [PortariaController::class, 'create'])->name('portarias.create');
Route::post('/portarias', [PortariaController::class, 'store'])->name('portarias.store');
Route::get('/portarias/{portaria}', [PortariaController::class, 'show'])->name('portarias.show');
Route::get('/portarias/{portaria}/edit', [PortariaController::class, 'edit'])->name('portarias.edit');
Route::put('/portarias/{portaria}', [PortariaController::class, 'update'])->name('portarias.update');
Route::delete('/portarias/{portaria}', [PortariaController::class, 'destroy'])->name('portarias.destroy');
