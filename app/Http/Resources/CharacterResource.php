<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            // Add other character attributes as needed
        ];
    }
}
