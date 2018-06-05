<?php

namespace App\Http\Resources;

use App\Band;
use App\BandGenre;
use App\Country;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Intervention\Image\Image;

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
            "id" => $this->id,
            "name" => $this->name,
            "country" => Country::find($this->country_id),
            "genre" => Band::find($this->id)->genre()->get(),
            "no_members" => $this->no_members,
            "founded_at" => $this->founded_at,
            "band_requests" => $this->band_requests,
            "price" => $this->price,
            "short_description" => $this->short_description,
            "long_description" => $this->long_description,
            "image_url" => $this->image_url,
            "user" => new UserResource(User::find($this->user_id))
        ];
    }
}
