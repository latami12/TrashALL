<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'from_user'     => new UserResource($this->whenLoaded('from')),
            'message'       => $this->message,
            'deleted_at,'   => $this->deleted_at,        
            'created_at,'   => $this->created_at,
            'updated_at,'   => $this->updated_at        
        ];
    }
}