<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTaughtContent extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.conteudo_ministrado_conteudo';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    use HasFactory;
}
