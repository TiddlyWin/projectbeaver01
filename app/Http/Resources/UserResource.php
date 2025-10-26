<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => (int) $this->resource->getKey(),
            'name'  => (string) $this->resource->name,
            'email' => $this->resource->email,
            'main_character' => CharacterResource::make(
                $this->whenLoaded('mainCharacter')
            ),
            'characters' => CharacterResource::collection(
                $this->whenLoaded('characters')
            ),
        ];
    }
}
