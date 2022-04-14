<?php

namespace App\Models;

use App\Menu;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @property int               $id
 * @property string            $name
 * @property string            $description
 * @property int               $level
 * @property bool              $active
 * @property Collection|Menu[] $menus
 * @property Collection|User[] $users
 */
class LegacyUserType extends Model
{
    public const LEVEL_ADMIN = 1;
    public const LEVEL_INSTITUTIONAL = 2;
    public const LEVEL_SCHOOLING = 4;
    public const LEVEL_LIBRARY = 8;

    public const CAN_VIEW = 1;
    public const CAN_MODIFY = 2;
    public const CAN_REMOVE = 3;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nivel',
        'nm_tipo',
        'descricao',
        'ref_funcionario_cad',
        'data_cadastro',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getLevelAttribute()
    {
        return $this->nivel;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_tipo;
    }

    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->descricao;
    }

    /**
     * @return bool
     */
    public function getActiveAttribute()
    {
        return boolval($this->ativo);
    }

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }

    /**
     * @return BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'pmieducar.menu_tipo_usuario',
            'ref_cod_tipo_usuario',
            'menu_id',
            'cod_tipo_usuario',
            'id'
        )->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * Retorna os processos e níveis de permissão em uma coleção chave => valor.
     *
     * @return Collection
     */
    public function getProcesses()
    {
        if ($this->level === self::LEVEL_ADMIN) {
            return collect(Menu::all()->pluck('id')->mapWithKeys(function ($id) {
                return [$id => self::CAN_REMOVE];
            }));
        }

        return $this->menus()->get()->mapWithKeys(function ($menu) {
            $level = 0;

            if ($menu->pivot->visualiza ?? false) {
                $level = 1;
            }

            if ($menu->pivot->cadastra ?? false) {
                $level = 2;
            }

            if ($menu->pivot->exclui ?? false) {
                $level = 3;
            }

            return [$menu->id => $level];
        });
    }

    /**
     * @return SupportCollection
     */
    public function getLevelDescriptions()
    {
        $levels = [
            self::LEVEL_ADMIN => 'Poli-institucional',
            self::LEVEL_INSTITUTIONAL => 'Institucional',
            self::LEVEL_SCHOOLING => 'Escola',
            self::LEVEL_LIBRARY => 'Biblioteca',
        ];

        return collect($levels)->filter(function ($value, $key) {
            return $this->level <= $key;
        });
    }
}
