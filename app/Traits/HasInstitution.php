<?php

namespace App\Traits;

use App\Models\LegacyInstitution;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasInstitution
{
    public function initializeHasInstitution(): void
    {
        $this->fillable = array_unique(array_merge($this->fillable, ['ref_cod_instituicao']));
        $this->legacy = array_unique(array_merge($this->legacy, [
            'institution_id' => 'ref_cod_instituicao',
        ]));
    }

    /**
     * Instituição
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }
}
