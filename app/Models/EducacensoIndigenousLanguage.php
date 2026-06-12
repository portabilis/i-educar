<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducacensoIndigenousLanguage extends Model
{
    public $timestamps = false;

    protected $table = 'modules.lingua_indigena_educacenso';

    protected $fillable = [
        'id',
        'lingua',
    ];
}
