<?php

namespace App\Models;

use iEducar\Modules\Unification\LogUnificationTypeInterface;
use iEducar\Modules\Unification\PersonLogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Database\Eloquent\Model;

class LogUnification extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function main()
    {
        return $this->morphTo(null, 'type', 'main_id');
    }

    public function oldData()
    {
        return $this->hasMany(LogUnificationOldData::class, 'unification_id', 'id');
    }

    public function getDuplicatesIdAttribute($value)
    {
        return json_decode($value, false);
    }

    /**
     * @return string
     */
    public function getMainName()
    {
        return $this->getAdapter()->getMainPersonName($this);
    }

    public function getDuplicatesName()
    {
        return $this->getAdapter()->getDuplicatedPeopleName($this);
    }

    /**
     * @return LogUnificationTypeInterface
     */
    public function getAdapter()
    {
        if ($this->type == Individual::class) {
            $adapter = new PersonLogUnification();
        }

        if ($this->type == Student::class) {
            $adapter = new StudentLogUnification();
        }

        return $adapter;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStudent($query)
    {
        return $query->where('type', Student::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePerson($query)
    {
        return $query->where('type', Individual::class);
    }
}
