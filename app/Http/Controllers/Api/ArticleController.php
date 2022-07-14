<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticlesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'perPage' => 'integer',
        ]);

        $perPage = $request->perPage ?? 15;

        $result = Article::paginate($perPage)->transform(function($item){
            $item->content = Str::limit(strip_tags($item->content), 200);
            return $item;
        });

        return new ArticlesResource($result);
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
