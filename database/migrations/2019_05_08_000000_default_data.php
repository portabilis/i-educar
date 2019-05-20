<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DefaultData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => DefaultAcessoSistemaTableSeeder::class,]);

        Artisan::call('db:seed', ['--class' => DefaultCadastroDeficienciaTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroEscolaridadeTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroEstadoCivilTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroOrgaoEmissorRgTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroRacaTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroPessoaTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultCadastroFisicaTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultPublicPaisTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPublicUfTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPublicMunicipioTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPublicDistritoTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultCadastroCodigoCartorioInepTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultModulesEducacensoCursoSuperiorTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesEducacensoIesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesEducacensoOrgaoRegionalTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesEtapasEducacensoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesLinguaIndigenaEducacensoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesFormulaMediaTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesTabelaArredondamentoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesTabelaArredondamentoValorTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesRegraAvaliacaoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultModulesTipoVeiculoTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultPortalAcessoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalFuncionarioTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalFuncionarioVinculoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalAgendaTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalImagemTipoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalImagemTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalMenuMenuTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalMenuSubMenuTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPortalMenuFuncionarioTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultPmicontrolesisTutormenuTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmicontrolesisMenuTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultUrbanoTipoLogradouroTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultPmieducarTipoUsuarioTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarUsuarioTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarInstituicaoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarAbandonoTipoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarConfiguracoesGeraisTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarEscolaLocalizacaoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarHistoricoEducarTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarHistoricoGradeCursoTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarMenuTipoUsuarioTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarTipoAutorTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultPmieducarTurmaTurnoTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultRelatorioSituacaoMatriculaTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultMenusTableSeeder::class]);

        Artisan::call('db:seed', ['--class' => DefaultManagerRolesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultManagerAccessCriteriasTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultManagerLinkTypesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => DefaultEmployeeGraduationDisciplines::class]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::raw('TRUNCATE employee_graduation_disciplines CASCADE;');
        DB::raw('TRUNCATE manager_link_types CASCADE;');
        DB::raw('TRUNCATE manager_access_criterias CASCADE;');
        DB::raw('TRUNCATE manager_roles CASCADE;');
        DB::raw('TRUNCATE menus CASCADE;');
        DB::raw('TRUNCATE relatorio.situacao_matricula CASCADE;');
        DB::raw('TRUNCATE pmieducar.turma_turno CASCADE;');
        DB::raw('TRUNCATE pmieducar.tipo_autor CASCADE;');
        DB::raw('TRUNCATE pmieducar.menu_tipo_usuario CASCADE;');
        DB::raw('TRUNCATE pmieducar.historico_grade_curso CASCADE;');
        DB::raw('TRUNCATE pmieducar.historico_educar CASCADE;');
        DB::raw('TRUNCATE pmieducar.escola_localizacao CASCADE;');
        DB::raw('TRUNCATE pmieducar.configuracoes_gerais CASCADE;');
        DB::raw('TRUNCATE pmieducar.abandono_tipo CASCADE;');
        DB::raw('TRUNCATE pmieducar.instituicao CASCADE;');
        DB::raw('TRUNCATE pmieducar.usuario CASCADE;');
        DB::raw('TRUNCATE pmieducar.tipo_usuario CASCADE;');
        DB::raw('TRUNCATE urbano.tipo_logradouro CASCADE;');
        DB::raw('TRUNCATE pmicontrolesis.menu CASCADE;');
        DB::raw('TRUNCATE pmicontrolesis.tutormenu CASCADE;');
        DB::raw('TRUNCATE portal.menu_funcionario CASCADE;');
        DB::raw('TRUNCATE portal.menu_submenu CASCADE;');
        DB::raw('TRUNCATE portal.menu_menu CASCADE;');
        DB::raw('TRUNCATE portal.imagem CASCADE;');
        DB::raw('TRUNCATE portal.imagem_tipo CASCADE;');
        DB::raw('TRUNCATE portal.agenda CASCADE;');
        DB::raw('TRUNCATE portal.funcionario_vinculo CASCADE;');
        DB::raw('TRUNCATE portal.funcionario CASCADE;');
        DB::raw('TRUNCATE portal.acesso CASCADE;');
        DB::raw('TRUNCATE modules.tipo_veiculo CASCADE;');
        DB::raw('TRUNCATE modules.regra_avaliacao CASCADE;');
        DB::raw('TRUNCATE modules.tabela_arredondamento_valor CASCADE;');
        DB::raw('TRUNCATE modules.tabela_arredondamento CASCADE;');
        DB::raw('TRUNCATE modules.formula_media CASCADE;');
        DB::raw('TRUNCATE modules.lingua_indigena_educacenso CASCADE;');
        DB::raw('TRUNCATE modules.etapas_educacenso CASCADE;');
        DB::raw('TRUNCATE modules.educacenso_orgao_regional CASCADE;');
        DB::raw('TRUNCATE modules.educacenso_ies CASCADE;');
        DB::raw('TRUNCATE modules.educacenso_curso_superior CASCADE;');
        DB::raw('TRUNCATE cadastro.codigo_cartorio_inep CASCADE;');
        DB::raw('TRUNCATE public.distrito CASCADE;');
        DB::raw('TRUNCATE public.municipio CASCADE;');
        DB::raw('TRUNCATE public.uf CASCADE;');
        DB::raw('TRUNCATE public.pais CASCADE;');
        DB::raw('TRUNCATE cadastro.fisica CASCADE;');
        DB::raw('TRUNCATE cadastro.pessoa CASCADE;');
        DB::raw('TRUNCATE cadastro.raca CASCADE;');
        DB::raw('TRUNCATE cadastro.orgao_emissor_rg CASCADE;');
        DB::raw('TRUNCATE cadastro.estado_civil CASCADE;');
        DB::raw('TRUNCATE cadastro.escolaridade CASCADE;');
        DB::raw('TRUNCATE cadastro.deficiencia CASCADE;');
        DB::raw('TRUNCATE acesso.sistema CASCADE;');
    }
}
