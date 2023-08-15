<?php

namespace App\Traits;

trait HasLegacyDates
{
    public function initializeHasLegacyDates(): void
    {
        $this->legacy = array_unique(array_merge($this->legacy, [
            'created_at' => 'data_cadastro',
        ]));
    }

    /**
     * Get the name of the "created at" column.
     */
    public function getCreatedAtColumn(): ?string
    {
        return 'data_cadastro';
    }

    /**
     * Get the name of the "updated at" column.
     */
    public function getUpdatedAtColumn(): ?string
    {
        return null;
    }
}
