<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacySchool;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SituationController extends ResourceController
{

    public function index(LegacySchool $school, Request $request): JsonResource
    {
        return $this->newResource([
            9 => 'Exceto Transferidos/Abandono',
            0 => 'Todos',
            1 => 'Aprovado',
            2 => 'Reprovado',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            8 => 'Aprovado sem exame',
            10 => 'Aprovado após exame',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por falta'
        ]);
    }
}
