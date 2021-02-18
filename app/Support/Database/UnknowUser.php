<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait UnknowUser
{

    public function getUnknowUserId(): int
    {
        $result = DB::selectOne("
             SELECT idpes
             FROM cadastro.pessoa
             WHERE nome = 'Desconhecido'
        ");

        return $result->idpes;
    }

    public function checkUnknowUserExists(): bool
    {
        return (bool) $this->getUnknowUserId();
    }
}