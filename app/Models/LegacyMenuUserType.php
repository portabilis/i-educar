<?php

namespace App\Models;

use App\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyMenuUserType extends Model
{
    use HasFactory;

    protected $table = 'pmieducar.menu_tipo_usuario';

    public $timestamps = false;
    public $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'ref_cod_tipo_usuario',
        'menu_id',
        'cadastra',
        'visualiza',
        'exclui',
    ];

    public function menus()
    {
        return $this->belongsTo(Menu::class);
    }
}
