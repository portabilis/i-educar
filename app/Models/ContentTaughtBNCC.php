<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTaughtBNCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.conteudo_ministrado_bncc';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    use HasFactory;
}
