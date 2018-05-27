<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "name" => $this->name,
            "founded_at" => $this->founded_at,
            "short_description" => $this->short_description,
            "long_description" => $this->long_description,
            "user_id" => $this->user_id
        ];
    }
}
