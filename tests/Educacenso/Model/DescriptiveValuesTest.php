<?php

use iEducar\Modules\Educacenso\Model\AbastecimentoAgua;
use iEducar\Modules\Educacenso\Model\CategoriaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\DestinacaoLixo;
use iEducar\Modules\Educacenso\Model\EsgotamentoSanitario;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\FonteEnergia;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\FormasContratacaoPoderPublico;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoEnsino;
use iEducar\Modules\Educacenso\Model\PoderPublicoTransporte;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\TipoCursoItinerario;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use iEducar\Modules\Educacenso\Model\VeiculoTransporteEscolar;

// As telas montam as opções dos campos do Censo a partir destas descrições.
test('cada opção do Censo tem uma descrição', function (array $values) {
    expect($values)->not->toBeEmpty()
        ->each->toBeString()->not->toBeEmpty();
})->with([
    'abastecimento de água' => [fn () => AbastecimentoAgua::getDescriptiveValues()],
    'categoria da escola privada' => [fn () => CategoriaEscolaPrivada::getDescriptiveValues()],
    'destinação do lixo' => [fn () => DestinacaoLixo::getDescriptiveValues()],
    'esgotamento sanitário' => [fn () => EsgotamentoSanitario::getDescriptiveValues()],
    'estrutura curricular' => [fn () => EstruturaCurricular::getDescriptiveValues()],
    'fonte de energia' => [fn () => FonteEnergia::getDescriptiveValues()],
    'forma de organização da turma' => [fn () => FormaOrganizacaoTurma::getDescriptiveValues()],
    'formas de contratação do poder público' => [fn () => FormasContratacaoPoderPublico::getDescriptiveValues()],
    'modalidade do curso' => [fn () => ModalidadeCurso::getDescriptiveValues()],
    'organização do ensino' => [fn () => OrganizacaoEnsino::getDescriptiveValues()],
    'poder público do transporte' => [fn () => PoderPublicoTransporte::getDescriptiveValues()],
    'recursos para as provas' => [fn () => RecursosRealizacaoProvas::getDescriptiveValues()],
    'cargo do gestor' => [fn () => SchoolManagerRole::getDescriptiveValues()],
    'tipo de curso do itinerário' => [fn () => TipoCursoItinerario::getDescriptiveValues()],
    'composição do itinerário formativo' => [fn () => TipoItinerarioFormativo::getDescriptiveValuesOfItineraryComposition()],
    'unidades curriculares' => [fn () => UnidadesCurriculares::getDescriptiveValues()],
    'veículo do transporte escolar' => [fn () => VeiculoTransporteEscolar::getDescriptiveValues()],
]);
