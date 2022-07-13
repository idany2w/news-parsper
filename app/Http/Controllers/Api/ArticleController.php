<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticlesResource;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'perPage' => 'integer',
        ]);

        $perPage = $request->perPage;

        return new ArticlesResource(Article::paginate($perPage));
    }

    public function show(Article $article)
    {
        ArticleResource::withoutWrapping();
        return new ArticleResource($article);
    }

    public function delete(Article $article)
    {
        return $article->delete();
    }

    public function updateRating(Request $request, Article $article)
    {
        $request->validate([
            'rating' => 'integer|in:-1,1',
        ]);

        $rating = round(($article->rating + $request->rating)/2, 2);
        
        $article->update( [ 'rating' => $rating] );
        
        ArticleResource::withoutWrapping();
        return new ArticleResource($article);
    }
}
