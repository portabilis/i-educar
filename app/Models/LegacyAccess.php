<?php

namespace App\Models;

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
}
