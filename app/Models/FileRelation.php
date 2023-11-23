<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileRelation extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.files_relations';

    protected $fillable = [
        'relation_type',
        'relation_id',
        'file_id',
    ];

    /**
     * @return BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
