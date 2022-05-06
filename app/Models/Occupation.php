<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Occupation extends Model
{
    protected $table = 'cadastro.profissao';
    protected $primaryKey = null;
    public $incrementing = false;
    
    use HasFactory;
}
