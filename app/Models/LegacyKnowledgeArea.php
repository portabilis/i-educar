<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyKnowledgeArea extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.area_conhecimento';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
