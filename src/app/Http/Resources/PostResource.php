<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
        // return parent::toArray($request);

        return [
            'owner' => UserResource::make($this->owner()->first()),
            'id' => $this->id,
            'content' => $this->content,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'likes' => LikeResource::collection($this->likes),
            'comments' => CommentResource::collection($this->comments),
            'media' => $this->media,
        ];
    }
}
