<?php

namespace iEducar\Modules\Educacenso;

use iEducar\Modules\Educacenso\Migrations\AjustaValoresAbastecimentoEnergia;
use iEducar\Modules\Educacenso\Migrations\AjustaValoresEsgotoSanitario;
use iEducar\Modules\Educacenso\Migrations\AtualizaValoresEscolaridadeServidorEducacenso;
use iEducar\Modules\Educacenso\Migrations\InsereDadosPredioCompartilhadoOutraEscola;
use iEducar\Modules\Educacenso\Migrations\InsertEmployeeGraduations;
use iEducar\Modules\Educacenso\Migrations\InsertEmployees;
use iEducar\Modules\Educacenso\Migrations\InsertSchoolManagers;
use iEducar\Modules\Educacenso\Migrations\MigraDadosAreasExternasEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosBanheirosEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosCartorio;
use iEducar\Modules\Educacenso\Migrations\MigraDadosDormitorioEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosEquipamentosEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosLaboratoriosEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosPossuiDependenciasEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasAtividadesEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasFuncionaisEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasGeraisEscola;
use iEducar\Modules\Educacenso\Migrations\MigraDadosTurmaUnificadaEtapaEducacenso;
use iEducar\Modules\Educacenso\Migrations\MigraDisciplinaEducacensoRemovidas;
use iEducar\Modules\Educacenso\Migrations\MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado;
use iEducar\Modules\Educacenso\Migrations\MigraTipoAtendimentoDaTurmaEducacenso;
use iEducar\Modules\Educacenso\Migrations\RemoveDadosInvalidosDestinacaoLixo;
use iEducar\Modules\Educacenso\Migrations\RemoveDadosInvalidosLocalFuncionamentoEscola;
use iEducar\Modules\Educacenso\Migrations\RemoveDeprecatedEtapaEducacensoFromTurmas;
use iEducar\Modules\Educacenso\Migrations\RemoveValoresInvalidosDaLocalizacaoDiferenciada;
use iEducar\Modules\Educacenso\Migrations\UpdateDeficienciaEducacensoValuesForLayout2019;
use iEducar\Modules\Educacenso\Migrations\UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;

class RunMigrations
{
    public function run()
    {
        foreach ($this->getMigrations() as $migration) {
            $migration::execute();
        }
    }

    /**
     * @return MigrationRepositoryInterface[]
     */
    private function getMigrations()
    {
        return [
            AjustaValoresAbastecimentoEnergia::class,
            AjustaValoresEsgotoSanitario::class,
            InsereDadosPredioCompartilhadoOutraEscola::class,
            InsertEmployeeGraduations::class,
            InsertEmployees::class,
            InsertSchoolManagers::class,
            MigraDadosAreasExternasEscola::class,
            MigraDadosBanheirosEscola::class,
            MigraDadosCartorio::class,
            MigraDadosDormitorioEscola::class,
            MigraDadosEquipamentosEscola::class,
            MigraDadosLaboratoriosEscola::class,
            MigraDadosPossuiDependenciasEscola::class,
            MigraDadosSalasAtividadesEscola::class,
            MigraDadosSalasFuncionaisEscola::class,
            MigraDadosSalasGeraisEscola::class,
            MigraDadosTurmaUnificadaEtapaEducacenso::class,
            MigraDisciplinaEducacensoRemovidas::class,
            MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado::class,
            MigraTipoAtendimentoDaTurmaEducacenso::class,
            RemoveDadosInvalidosDestinacaoLixo::class,
            RemoveDadosInvalidosLocalFuncionamentoEscola::class,
            RemoveDeprecatedEtapaEducacensoFromTurmas::class,
            RemoveValoresInvalidosDaLocalizacaoDiferenciada::class,
            UpdateDeficienciaEducacensoValuesForLayout2019::class,
            UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019::class,
            AtualizaValoresEscolaridadeServidorEducacenso::class,
        ];
    }
}