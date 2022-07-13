<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticlesResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        return [
            'data' => ArticleResource::collection($this->collection),
        ];
    }
    public function with($request)
    {
        return [
            'links'    => [
                'self' => route('articles.index'),
            ],
        ];
    }
}
