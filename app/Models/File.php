<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.files';

    protected $fillable = [
        'url',
        'size',
        'original_name',
        'extension',
    ];

    public function relations()
    {
        return $this->hasMany(FileRelation::class);
    }
}
