<?php

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\Registro60;
use iEducar\Modules\Educacenso\ExportRule\CargaHorariaIntegralizada;
use iEducar\Modules\Educacenso\ExportRule\CargoGestor;
use iEducar\Modules\Educacenso\ExportRule\CriterioAcessoGestor;
use iEducar\Modules\Educacenso\ExportRule\EsferaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\RegrasGeraisRegistro30;
use iEducar\Modules\Educacenso\ExportRule\TipoVinculoGestor;
use iEducar\Modules\Educacenso\ExportRule\TipoVinculoServidor;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa as Esfera;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use Tests\TestCase;

uses(TestCase::class);

test('carga horaria integralizada vazia vira zero quando a etapa permite', function () {
    $aluno = new Registro60;
    $aluno->etapaTurma = 39;
    $aluno->cargaHorariaIntegralizada = null;

    $this->assertSame(0, CargaHorariaIntegralizada::handle($aluno)->cargaHorariaIntegralizada);

    $aluno->cargaHorariaIntegralizada = 800;

    $this->assertSame(800, CargaHorariaIntegralizada::handle($aluno)->cargaHorariaIntegralizada);
});

test('gestor sem cargo de diretor em escola privada paralisada perde criterio e vinculo', function () {
    $gestor = new Registro40;
    $gestor->cargo = SchoolManagerRole::OUTRO;
    $gestor->dependenciaAdministrativa = DependenciaAdministrativaEscola::PRIVADA;
    $gestor->situacaoFuncionamento = SituacaoFuncionamento::PARALISADA;
    $gestor->criterioAcesso = 1;
    $gestor->tipoVinculo = 1;

    $gestor = CargoGestor::handle($gestor);

    $this->assertNull($gestor->criterioAcesso);
    $this->assertNull($gestor->tipoVinculo);

    $gestor->criterioAcesso = 1;
    $gestor->tipoVinculo = 1;

    $this->assertNull(CriterioAcessoGestor::handle($gestor)->criterioAcesso);
    $this->assertNull(TipoVinculoGestor::handle($gestor)->tipoVinculo);
});

test('docente de escola privada nao exporta tipo de vinculo', function () {
    $docente = new Registro50;
    $docente->dependenciaAdministrativaEscola = DependenciaAdministrativaEscola::PRIVADA;
    $docente->funcaoDocente = FuncaoExercida::DOCENTE;
    $docente->tipoVinculo = 1;

    $this->assertNull(TipoVinculoServidor::handle($docente)->tipoVinculo);
});

test('esfera administrativa incompativel com a dependencia nao e exportada', function () {
    $escola = new Registro00;
    $escola->esferaAdministrativa = Esfera::MUNICIPAL;
    $escola->dependenciaAdministrativa = DependenciaAdministrativaEscola::FEDERAL;

    $this->assertNull(EsferaAdministrativa::handle($escola)->esferaAdministrativa);
});

test('estrangeiro que mora fora nao exporta naturalidade nem endereco', function () {
    $pessoa = new Registro30;
    $pessoa->nacionalidade = Nacionalidade::ESTRANGEIRA;
    $pessoa->municipioNascimento = 4205407;
    $pessoa->paisResidencia = PaisResidencia::ARGENTINA;
    $pessoa->cep = '88000000';
    $pessoa->cpf = '12345678909';
    $pessoa->justificativaFaltaDocumentacao = 1;

    $pessoa = RegrasGeraisRegistro30::handle($pessoa);

    $this->assertNull($pessoa->municipioNascimento);
    $this->assertNull($pessoa->paisResidencia);
    $this->assertNull($pessoa->cep);
    $this->assertNull($pessoa->justificativaFaltaDocumentacao);
});
