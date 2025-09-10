<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        $canEdit = false;

        if ($user) {
            if ($user->isAdmin()) {
                $canEdit = true;
            } elseif ($this->user_id === $user->id) {
                $canEdit = true;
            }
        }

        return [
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'can_edit' => $canEdit,
            'can_delete' => $canEdit,
            'user' => $this->when($this->user, function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
