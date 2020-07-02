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
        'type',
        'relation_id',
        'file_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * @return MorphTo
     */
    public function relation()
    {
        return $this->morphTo(null, 'type', 'relation_id');
    }
}
