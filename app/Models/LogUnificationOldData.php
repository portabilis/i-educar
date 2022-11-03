<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LogUnificationOldData extends Model
{
    protected $table = 'log_unification_old_data';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unification()
    {
        return $this->belongsTo(LogUnification::class, 'unification_id', 'id');
    }

    protected function keys(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => json_decode($value, true)
        );
    }

    protected function oldData(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => json_decode($value, true)
        );
    }
}
