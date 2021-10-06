<?php

namespace App\Models;

trait HasFiles
{
    /**
     * @return BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'files_relations', 'relation_id', 'file_id');
    }
}
