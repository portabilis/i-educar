<?php

namespace App\Models\Traits;

use App\Models\Message;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMessages
{
    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'messageable');
    }
}
