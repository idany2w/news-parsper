<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::delete('articles/{article}', [ArticleController::class, 'delete'])->name('articles.delete');
Route::put('articles/{article}/rating', [ArticleController::class, 'updateRating'])->name('articles.rating.update');
