<?php

namespace App\Traits;

use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasLegacyUserAction
{
    public function initializeHasLegacyUserAction(): void
    {
        $this->fillable = array_unique(array_merge($this->fillable, ['ref_usuario_exc', 'ref_usuario_cad']));
        $this->legacy = array_unique(array_merge($this->legacy, [
            'deleted_by' => 'ref_usuario_exc',
            'created_by' => 'ref_usuario_cad',
        ]));
    }

    /**
     * Usuário que criou
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_cad');
    }

    /**
     * Usuário que deletou
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_exc');
    }
}
