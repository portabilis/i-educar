<?php

namespace App\Models;

use App\Menu;
use Database\Factories\LegacyMenuUserTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyMenuUserType extends Model
{
    /** @use HasFactory<LegacyMenuUserTypeFactory> */
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

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function menus(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /**
     * @return BelongsTo<LegacyUserType, $this>
     */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario');
    }
}
