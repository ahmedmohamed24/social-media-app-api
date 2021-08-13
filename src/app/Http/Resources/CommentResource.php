<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'likes_count' => $this->likes_count,
            'replies_count' => $this->replies_count,
            'likes' => LikeResource::collection($this->likes),
            'replies' => ReplyResource::collection($this->replies),
        ];
    }
}
