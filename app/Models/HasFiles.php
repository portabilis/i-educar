<?php

namespace App\Models;

trait HasFiles
{
    /**
     * @return BelongsToMany
     */
    public function files()
    {
        return $this->morphToMany(File::class, 'relation', 'files_relations');
    }
}
