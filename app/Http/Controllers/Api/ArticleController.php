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
            'from' => 'date',
        ]);

        $perPage = $request->perPage ?? 15;

        $result = Article::orderBy('created_at', 'desc')->paginate($perPage)->transform(function($item){
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
        $id = $article->id;
        $article->delete();
        return $id;
    }

    public function updateRating(Request $request, Article $article)
    {
        $request->validate([
            'rating' => 'integer|min:1|max:10',
        ]);

        $rating = round(($article->rating + $request->rating)/2, 2);
        if($rating > 10) $rating = 10;
        if($rating < 1) $rating = 1;
        
        $article->update( [ 'rating' => $rating] );
        
        ArticleResource::withoutWrapping();
        return new ArticleResource($article);
    }
}
