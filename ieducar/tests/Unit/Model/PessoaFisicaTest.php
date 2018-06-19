<?php

namespace Tests\Unit\Model;

require_once __DIR__ . '/../../../intranet/include/pessoa/clsPessoaFisica.inc.php';

use Tests\SuiteTestCase\IeducarTestCase;

class PessoaFisicaTest extends IeducarTestCase
{
    public function getYamlDataSet()
    {
        return 'pessoafisica.yaml';
    }

    public function testGetById()
    {
        $pessoaFisica = new \clsPessoaFisica();
        $pessoaFisica->idpes = 1;
        $pessoa = $pessoaFisica->detalhe();
    }

    public function testGetByCpf()
    {

    }
}