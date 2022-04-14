<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalReason extends Model
{
    protected $table = 'pmieducar.motivo_afastamento';

    protected $primaryKey = 'cod_motivo_afastamento';
}
