<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyInstitutionDocument extends Model
{
    public $timestamps = false;

    protected $table = 'pmieducar.instituicao_documentacao';

    protected $fillable = [
        'instituicao_id',
        'titulo_documento',
        'url_documento',
        'ref_usuario_cad',
        'ref_cod_escola',
    ];
}
