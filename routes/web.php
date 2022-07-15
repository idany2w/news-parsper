<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Articles', []);
})->name('home');


Route::get('/articles', function () {
    return Inertia::render('Articles', []);
})->name('articles.index');

Route::get('/articles/{article}', function (App\Models\Article $article) {
    App\Http\Resources\ArticleResource::withoutWrapping();
    
    return Inertia::render('Article', [
        'article' => new App\Http\Resources\ArticleResource($article)
    ]);
})->name('articles.show');