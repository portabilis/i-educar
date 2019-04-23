<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;

class Registro40 implements RegistroEducacenso
{
    public $registro;

    public $inepEscola;

    public $codigoPessoa;

    public $inepGestor;

    public $cargo;

    public $criterioAcesso;

    public $especificacaoCriterioAcesso;

    public $tipoVinculo;

    public $dependenciaAdministrativa;
}
