<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducacensoImport extends Model
{
    protected $table = 'educacenso_imports';

    protected $fillable = ['year', 'school', 'user_id', 'finished', 'registration_date'];

    protected $dates = [
        'registration_date',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Individual::class, 'user_id', 'id');
    }

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }
}
