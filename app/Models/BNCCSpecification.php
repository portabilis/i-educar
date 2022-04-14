<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BNCCSpecification extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.bncc_especificacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
