<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar_url,
            'role' => $this->whenLoaded('role', fn () => $this->role?->name),
            'is_blocked' => $this->is_blocked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
