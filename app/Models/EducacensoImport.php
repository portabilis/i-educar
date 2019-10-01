<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducacensoImport extends Model
{
    protected $table = 'educacenso_import';

    protected $fillable = ['year', 'school', 'user_id', 'finished'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Individual::class, 'user_id', 'id');
    }
}
