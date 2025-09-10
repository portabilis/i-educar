<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducacensoIndigenousPeople extends Model
{
    protected $table = 'modules.povo_indigena_educacenso';

    protected $fillable = [
        'id',
        'name',
    ];
}
