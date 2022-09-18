<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.mensagens';

    protected $fillable = [
        'registro_id',
        'emissor_user_id',
        'receptor_user_id',
        'texto',
    ];

}
