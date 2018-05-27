<?php

namespace App\Http\Resources;

use App\Band;
use App\Space;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed band_id
 * @property mixed space_id
 * @property mixed user_id
 */
class ConcertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return ["data" => ["id" => $this->id,
            "band_id" => $this->band_id,
            "space_id" => $this->space_id,
            "available_tickets" => $this->available_tickets,
            "total_tickets" => $this->total_tickets,
            "concert_start" => $this->concert_start,
            "name" => $this->name,
            "user_id" => $this->user_id,
            "poster_url" => $this->poster_url,
            "short_description" => $this->short_description,
            "long_description" => $this->long_description
        ],
            "related_objects" => [
                'band' => new BandConcertResource(Band::find($this->band_id)),
                'space' => new SpaceResource(Space::find($this->space_id)),
                'user' => User::find($this->user_id)
            ]];
    }
}
