<?php

namespace App\Models;

use App\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyMenuUserType extends Model
{
    use HasFactory;

    protected $table = 'pmieducar.menu_tipo_usuario';

    public $timestamps = false;

    public $primaryKey = 'ref_cod_tipo_usuario';

    public $incrementing = false;

    protected $fillable = [
        'ref_cod_tipo_usuario',
        'menu_id',
        'cadastra',
        'visualiza',
        'exclui',
    ];

    public function menus(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario');
    }
}
