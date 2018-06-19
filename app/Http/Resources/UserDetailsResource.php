<?php

namespace App\Http\Resources;

use App\Country;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed country_id
 * @property mixed user_id
 */
class UserDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
//        print_r($this);
//        die();
        return ["data" =>
            [
                "id" => $this->id,
                "user_id" => $this->user_id,
                "dob" => $this->dob,
                "description" => $this->description,
                "country_id" => $this->country_id,
            ],
            "related_objects" => [
                'user' => new UserResource(User::find($this->user_id)),
                'country' => Country::find($this->country_id)
            ]];
    }
}
