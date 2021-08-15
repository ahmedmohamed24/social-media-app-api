<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'name' => $this->name,
            'file_name' => $this->file_name,
            'uuid' => $this->uuid,
            'extension' => $this->extension,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'url' => $this->getUrl(),
        ];
    }
}
