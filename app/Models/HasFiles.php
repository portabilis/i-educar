<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFiles
{
    /**
     * @return BelongsToMany<File, $this>
     */
    public function files(): BelongsToMany
    {
        return $this->morphToMany(File::class, 'relation', 'files_relations');
    }
}
