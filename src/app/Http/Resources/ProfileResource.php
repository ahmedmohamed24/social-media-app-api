<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'bio' => $this->bio,
            'phone_number' => $this->phone_number,
            'country' => $this->country,
            'city' => $this->city,
            'postal-code' => $this->{'postal-code'},
            'address-line-1' => $this->{'address-line-1'},
            'address-line-2' => $this->{'address-line-2'},
            'photo_path' => $this->{'photo_path'},
            'cover_path' => $this->cover_path,
            'education' => $this->education,
        ];
    }
}
