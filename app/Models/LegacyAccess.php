<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyAccess extends Model
{
    /**
     * @var string
     */
    protected $table = 'portal.acesso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_acesso';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $dates = ['data_hora'];

    public function getLastAccess()
    {
        return $this->query()
            ->orderBy('data_hora', 'DESC')
            ->first();
    }
}
