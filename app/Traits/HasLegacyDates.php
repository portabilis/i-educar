<?php

namespace App\Traits;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasLegacyDates
{
    /**
     * Get the name of the "created at" column.
     *
     * @return string|null
     */
    public function getCreatedAtColumn(): string|null
    {
        return 'data_cadastro';
    }


    /**
     * Get the name of the "updated at" column.
     *
     * @return string|null
     */
    public function getUpdatedAtColumn(): string|null
    {
        return null;
    }
}
