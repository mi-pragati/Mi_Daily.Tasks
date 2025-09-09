<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'post_id' => $this->post_id,
            'body'    => $this->body,
            'author'  => [
                'id'   => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
