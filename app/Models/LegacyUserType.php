<?php

namespace App\Models;

use App\Menu;
use App\Traits\HasLegacyDates;
use App\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
class LegacyUserType extends LegacyModel
{
    use HasLegacyDates;

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
    ];

    public function users(): HasMany
    {
        return $this->hasMany(LegacyUser::class, 'ref_cod_tipo_usuario');
    }

    public function menus(): BelongsToMany
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
     */
    public function getProcesses(): SupportCollection
    {
        if ($this->level === self::LEVEL_ADMIN) {
            return collect(
                Menu::all()
                    ->pluck('id')
                    ->mapWithKeys(static fn ($id) => [$id => self::CAN_REMOVE])
            );
        }

        return $this->menus()
            ->get()
            ->mapWithKeys(static function ($menu) {
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

    public function getLevelDescriptions(): SupportCollection
    {
        $levels = [
            self::LEVEL_ADMIN => 'Poli-institucional',
            self::LEVEL_INSTITUTIONAL => 'Institucional',
            self::LEVEL_SCHOOLING => 'Escola',
            self::LEVEL_LIBRARY => 'Biblioteca',
        ];

        return collect($levels)->filter(fn ($value, $key) => $this->level <= $key);
    }

    protected function level(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nivel
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nm_tipo
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->descricao
        );
    }

    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool) $this->ativo
        );
    }
}
