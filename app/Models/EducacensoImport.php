<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducacensoImport extends Model
{
    protected $table = 'educacenso_import';

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }
}
