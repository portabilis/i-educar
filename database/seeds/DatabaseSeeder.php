<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultAcessoSistemaTableSeeder::class);

        $this->call(DefaultCadastroDeficienciaTableSeeder::class);
        $this->call(DefaultCadastroEscolaridadeTableSeeder::class);
        $this->call(DefaultCadastroEstadoCivilTableSeeder::class);
        $this->call(DefaultCadastroOrgaoEmissorRgTableSeeder::class);
        $this->call(DefaultCadastroRacaTableSeeder::class);
        $this->call(DefaultCadastroPessoaTableSeeder::class);
        $this->call(DefaultCadastroFisicaTableSeeder::class);

        $this->call(DefaultPublicPaisTableSeeder::class);
        $this->call(DefaultPublicUfTableSeeder::class);
        $this->call(DefaultPublicMunicipioTableSeeder::class);
        $this->call(DefaultPublicDistritoTableSeeder::class);

        $this->call(DefaultCadastroCodigoCartorioInepTableSeeder::class);

        $this->call(DefaultModulesEducacensoCursoSuperiorTableSeeder::class);
        $this->call(DefaultModulesEducacensoIesTableSeeder::class);
        $this->call(DefaultModulesEducacensoOrgaoRegionalTableSeeder::class);
        $this->call(DefaultModulesEtapasEducacensoTableSeeder::class);
        $this->call(DefaultModulesLinguaIndigenaEducacensoTableSeeder::class);
        $this->call(DefaultModulesFormulaMediaTableSeeder::class);
        $this->call(DefaultModulesTabelaArredondamentoTableSeeder::class);
        $this->call(DefaultModulesTabelaArredondamentoValorTableSeeder::class);
        $this->call(DefaultModulesRegraAvaliacaoTableSeeder::class);
        $this->call(DefaultModulesTipoVeiculoTableSeeder::class);

        $this->call(DefaultPortalAcessoTableSeeder::class);
        $this->call(DefaultPortalFuncionarioTableSeeder::class);
        $this->call(DefaultPortalFuncionarioVinculoTableSeeder::class);
        $this->call(DefaultPortalAgendaTableSeeder::class);
        $this->call(DefaultPortalImagemTipoTableSeeder::class);
        $this->call(DefaultPortalImagemTableSeeder::class);
        $this->call(DefaultPortalMenuMenuTableSeeder::class);
        $this->call(DefaultPortalMenuSubMenuTableSeeder::class);
        $this->call(DefaultPortalMenuFuncionarioTableSeeder::class);

        $this->call(DefaultPmicontrolesisTutormenuTableSeeder::class);
        $this->call(DefaultPmicontrolesisMenuTableSeeder::class);

        $this->call(DefaultUrbanoTipoLogradouroTableSeeder::class);

        $this->call(DefaultPmieducarTipoUsuarioTableSeeder::class);
        $this->call(DefaultPmieducarUsuarioTableSeeder::class);
        $this->call(DefaultPmieducarInstituicaoTableSeeder::class);
        $this->call(DefaultPmieducarAbandonoTipoTableSeeder::class);
        $this->call(DefaultPmieducarConfiguracoesGeraisTableSeeder::class);
        $this->call(DefaultPmieducarEscolaLocalizacaoTableSeeder::class);
        $this->call(DefaultPmieducarHistoricoEducarTableSeeder::class);
        $this->call(DefaultPmieducarHistoricoGradeCursoTableSeeder::class);
        $this->call(DefaultPmieducarMenuTipoUsuarioTableSeeder::class);
        $this->call(DefaultPmieducarTipoAutorTableSeeder::class);
        $this->call(DefaultPmieducarTurmaTurnoTableSeeder::class);

        $this->call(DefaultRelatorioSituacaoMatriculaTableSeeder::class);
    }
}
