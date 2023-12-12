<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::any('/task/create', [TaskController::class, 'create'])->name('task_create');
Route::any('/task/{id}/edit', [TaskController::class, 'edit'])->name('task_edit');
Route::get('/task/{id}/delete', [TaskController::class, 'delete'])->name('task_delete');
Route::any('/tasks', [TaskController::class, 'index'])->name('task_list');
