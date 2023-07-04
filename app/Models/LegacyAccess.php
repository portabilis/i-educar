<?php

namespace App\Models;

use App\Models\Builders\LegacyAccessBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAccess extends LegacyModel
{
    public const CREATED_AT = 'data_hora';

    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'portal.acesso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_acesso';

    protected string $builder = LegacyAccessBuilder::class;

    public array $legacy = [
        'id' => 'cod_acesso',
        'access' => 'data_hora',
        'internal_ip' => 'ip_interno',
        'external_ip' => 'ip_externo',
        'people_id' => 'cod_pessoa',
        'success' => 'sucesso',
    ];

    public function getLastAccess()
    {
        return $this->query()
            ->orderBy('data_hora', 'DESC')
            ->first();
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_pessoa');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'cod_pessoa');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_pessoa');
    }
}
