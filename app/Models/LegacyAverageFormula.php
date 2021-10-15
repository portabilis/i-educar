<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyAverageFormula extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'modules.formula_media';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome', 'formula_media',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
