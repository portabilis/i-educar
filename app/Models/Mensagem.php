<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

}
