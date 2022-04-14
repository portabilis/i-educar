<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'url',
        'type',
        'size',
        'original_name',
        'extension',
        'created_at',
        'updated_at',
    ];

    public function relations()
    {
        return $this->hasMany(FileRelation::class);
    }
}
