<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('articles', [ArticleController::class, 'index'])->name('api.articles.index');
Route::get('articles/{article}', [ArticleController::class, 'show'])->name('api.articles.show');
Route::delete('articles/{article}', [ArticleController::class, 'delete'])->name('api.articles.delete');
Route::put('articles/{article}/rating', [ArticleController::class, 'updateRating'])->name('api.articles.rating.update');
