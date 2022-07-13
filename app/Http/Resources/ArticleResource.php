<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type'          => 'articles',
            'id'            => (string) $this->id,
            'attributes'    => [
                'title' => $this->title,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'rating' => $this->rating,
                'content' => $this->content,
                'image' => $this->image ? url(Storage::url($this->image)) : null,
            ],
            'links'         => [
                'self' => route('articles.show', ['article' => $this->id]),
            ],
        ];
    }
}
