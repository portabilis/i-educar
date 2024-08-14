<?php

namespace App\Models;

use App\Models\Builders\LegacyAccessBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacyAccess extends LegacyModel
{
    /** @use HasBuilder<LegacyAccessBuilder> */
    use HasBuilder;

    public const CREATED_AT = 'data_hora';

    public const UPDATED_AT = null;

    protected $table = 'portal.acesso';

    protected $primaryKey = 'cod_acesso';

    protected static string $builder = LegacyAccessBuilder::class;

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'id' => 'cod_acesso',
        'access' => 'data_hora',
        'internal_ip' => 'ip_interno',
        'external_ip' => 'ip_externo',
        'people_id' => 'cod_pessoa',
        'success' => 'sucesso',
    ];

    public function getLastAccess(): self
    {
        return $this->query()
            ->orderBy('data_hora', 'DESC')
            ->first();
    }

    /**
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_pessoa');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'cod_pessoa');
    }

    /**
     * @return BelongsTo<LegacyEmployee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_pessoa');
    }
}
