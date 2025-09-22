<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'slug'      => $this->slug,
            'excerpt'   => $this->excerpt,
            'content'   => $this->content,
            'cover_url' => $this->cover_url ? url($this->cover_url) : null,
            'category'  => new CategoryResource($this->whenLoaded('category')),
            'author'    => [
                'id'   => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
            ],
            'published_at' => $this->published_at,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
