<?php

namespace App\Models;

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

    public function getKeysAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getOldDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
