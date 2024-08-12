<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class FileRelation extends Model
{
    protected $table = 'public.files_relations';

    protected $fillable = [
        'relation_type',
        'relation_id',
        'file_id',
    ];

    /**
     * @return BelongsTo<File, $this>
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
