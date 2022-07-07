<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LegacyCreateTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:create:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates tests for legacy pages';

    /**
     * Create the test file.
     *
     * @param string $class
     * @param string $route
     *
     * @return boolean
     */
    private function createTestFile(string $class, string $route): bool
    {
        $replaces = [
            'BaseTestName' => $class,
            'ProjectRoute' => $route
        ];

        $stub = file_get_contents(
            resource_path('stubs/base-test.php')
        );

        $stub = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );

        $filename = base_path('tests/Pages/Intranet/' . $class . '.php');

        if (file_exists($filename)) {
            return false;
        }

        return file_put_contents($filename, $stub) !== false;
    }

    /**
     * Executa o comando.
     *
     * @return int
     */
    public function handle()
    {
        $allViews = $this->getAllViews();

        $allViewsFiltered = $this->excludeRouters($allViews);

        $routersInfo = $this->processRoutersInformation($allViewsFiltered);

        foreach ($routersInfo as $router) {
            $created = $this->createTestFile($router['className'], $router['route']);

            if ($created === false) {
                $this->warn(
                    "Teste {$router['className']} criado para a rota {$router['route']} já criado ou houve falha"
                );

                continue;
            }

            $this->info("Teste {$router['className']} criado para a rota {$router['route']}");
        }

        $this->info('Testes criados com sucesso!');
    }

    /**
     * Busca todas os arquivos da pasta intranet para gerar os testes de rota
     *
     * @return array
     */
    private function getAllViews(): array
    {
        $files = glob(__DIR__ . '/../../../ieducar/intranet/*.php');
        $filtered = $this->filterOnlineHttpView($files);

        return $this->cleanStringUrl($filtered);
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function filterOnlineHttpView(array $files)
    {
        $filtered = [];
        foreach ($files as $file) {
            if (preg_match('/^((?!xml).)*.php/', $file)) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

    /**
     * Pega o nome do arquivo/rota
     *
     * @param array $data
     *
     * @return array
     */
    private function cleanStringUrl(array $data)
    {
        $cleanRouter = [];
        foreach ($data as $item) {
            $array = explode('/', $item);
            $cleanRouter[] = end($array);
        }

        return $cleanRouter;
    }

    /**
     * Faz a montagem da informações para processamento
     *
     * @param array $allViews
     *
     * @return array
     */
    private function processRoutersInformation(array $allViews)
    {
        $routersInformation = [];
        foreach ($allViews as $view) {
            $className = $this->processClassName($view);

            if ($this->isClassWithKnownErrors($className)) {
                continue;
            }

            $routersInformation[] = [
                'className' => $className . 'Test',
                'route' => '/intranet/' . $view,
            ];
        }

        return $routersInformation;
    }

    /**
     * @param $view
     *
     * @return string|string[]
     */
    private function processClassName($view)
    {
        $view = str_replace(['.php', '.inc', '.ajax'], '', $view);

        return str_replace(' ', '', ucwords(str_replace('_', ' ', $view)));
    }

    /**
     * Remove rota/arquivo da lista rotas que vão ser geradas
     *
     * @param array $allViews
     *
     * @return array
     */
    private function excludeRouters(array $allViews): array
    {
        $excludeRoutersList = $this->excludeRoutersList();
        foreach ($allViews as $key => $view) {
            if (in_array($view, $excludeRoutersList, true)) {
                unset($allViews[$key]);
            }
        }

        return array_values($allViews);
    }

    /**
     * Valida se esta na lista de exclusão
     *
     * @param string $className
     *
     * @return bool
     */
    private function isClassWithKnownErrors(string $className)
    {
        $excludeClasseNameList = $this->excludeClasseNameList();

        return in_array($className, $excludeClasseNameList, true);
    }

    /**
     * Lista com rotas/arquivos para não processar
     *
     * @return string[]
     */
    private function excludeRoutersList(): array
    {
        return [
            'S3.php',
            's3_config.php',
            'upload.php',
            'file_check.php',
            'file_check_just_pdf.php',
            'educar_arruma_nota_avaliacao.php',
            'educar_exemplar_emprestimo_cad.php',
            'educar_servidor_nivel_cad.php',
            'educar_subniveis_cad.php',
            'educar_coffebreak_tipo_lst.php',
            'transporte_itinerario_del.php',
            'educar_turma_cad.php',
            'educar_matricula_modalidade_ensino.php',
            'educar_busca_ativa_lst.php',
            'educar_busca_ativa_cad.php',
            'migra_alunos.php',
            'index.php',
        ];
    }

    /**
     * Lista de Classes com erros conhecidos
     *
     * @return string[]
     */
    private function excludeClasseNameList()
    {
        return [
            'EducarAbandonoTipoDet',
            'EducarAcervoAssuntoDet',
            'EducarAcervoAutorDet',
            'EducarAcervoDet',
            'EducarAcervoEditoraDet',
            'EducarAcervoIdiomaDet',
            'EducarAlunoBeneficioDet',
            'EducarAlunoDet',
            'EducarAvaliacaoDesempenhoDet',
            'EducarBibliotecaDadosDet',
            'EducarBibliotecaDet',
            'EducarBloqueioAnoLetivoDet',
            'EducarBloqueioLancamentoFaltasNotasDet',
            'EducarCalendarioAnotacaoCad',
            'EducarCalendarioAnotacaoDet',
            'EducarCalendarioAnotacaoLst',
            'EducarCalendarioDiaMotivoDet',
            'EducarCategoriaNivelDet',
            'EducarCategoriaObraDet',
            'EducarClienteDet',
            'EducarClienteTipoDet',
            'EducarCoffebreakTipoDet',
            'EducarConsultaMovimentoGeralLst',
            'EducarConsultaMovimentoMensalLst',
            'EducarCursoDet',
            'EducarCursoDetV',
            'EducarDeficienciaDet',
            'EducarDefinirClienteTipoDet',
            'EducarDisciplinaDependenciaCad',
            'EducarDisciplinaDependenciaDet',
            'EducarDisciplinaDependenciaLst',
            'EducarDisciplinaTopicoDet',
            'EducarDispensaDisciplinaCad',
            'EducarDispensaDisciplinaDet',
            'EducarDispensaDisciplinaLst',
            'EducarDistribuicaoUniformeDet',
            'EducarDistribuicaoUniformeLst',
            'EducarEscolaDet',
            'EducarEscolaLocalizacaoDet',
            'EducarEscolaRedeEnsinoDet',
            'EducarEscolaSerieDet',
            'EducarEscolaridadeDet',
            'EducarExemplarDet',
            'EducarExemplarDevolucaoDet',
            'EducarExemplarEmprestimoDet',
            'EducarExemplarTipoDet',
            'EducarFaltaAlunoCad',
            'EducarFaltaAtrasoDet',
            'EducarFaltaNotaAlunoCad',
            'EducarFaltaNotaAlunoDet',
            'EducarFonteDet',
            'EducarFuncaoDet',
            'EducarHabilitacaoDet',
            'EducarHabilitacaoCad',
            'EducarHabilitacaoLst',
            'EducarHistoricoEscolarDet',
            'EducarHistoricoEscolarLst',
            'EducarFaltaNotaAlunoLst',
            'EducarSerieCadPop',
            'EducarSeriePreRequisitoLst',
            'EducarInfraComodoFuncaoDet',
            'EducarInfraPredioComodoDet',
            'EducarInfraPredioDet',
            'EducarIniciarAnoLetivo',
            'EducarMatriculaAbandonoCad',
            'EducarMatriculaCad',
            'EducarMatriculaDet',
            'EducarMatriculaEtapaTurmaCad',
            'EducarMatriculaFormandoCad',
            'EducarMatriculaHistoricoCad',
            'EducarMatriculaLst',
            'EducarMatriculaOcorrenciaDisciplinarDet',
            'EducarMatriculaOcorrenciaDisciplinarLst',
            'EducarMatriculaReclassificarCad',
            'EducarMatriculaTurmaCad',
            'EducarMatriculaTurmaDet',
            'EducarMatriculaTurmaLst',
            'EducarMatriculaTurmaTipoAeeCad',
            'EducarMatriculaTurmaTurnoCad',
            'EducarModuloDet',
            'EducarMotivoAfastamentoDet',
            'EducarMotivoBaixaDet',
            'EducarMotivoSuspensaoDet',
            'EducarNivelCad',
            'EducarNivelEnsinoDet',
            'EducarOperadorDet',
            'EducarPagamentoMultaCad',
            'EducarPagamentoMultaDet',
            'EducarPreRequisitoDet',
            'EducarProjetoDet',
            'EducarQuadroHorarioHorariosCad',
            'EducarRacaDet',
            'EducarReligiaoDet',
            'EducarReservaVagaDet',
            'EducarReservadaVagaDet',
            'EducarReservasCad',
            'EducarReservasDet',
            'EducarSequenciaSerieDet',
            'EducarSerieDet',
            'EducarSeriePreRequisitoCad',
            'EducarSeriePreRequisitoDet',
            'EducarSerieVagaDet',
            'EducarServidorAlocacaoCad',
            'EducarServidorAlocacaoDet',
            'EducarServidorAlocacaoLst',
            'EducarServidorDet',
            'EducarServidorFormacaoDet',
            'EducarServidorSubstituicaoCad',
            'EducarServidorVinculoTurmaDet',
            'EducarSituacaoCad',
            'EducarSituacaoDet',
            'EducarTipoDispensaDet',
            'EducarTipoEnsinoDet',
            'EducarTipoOcorrenciaDisciplinarDet',
            'EducarTipoRegimeDet',
            'EducarTransferenciaTipoDet',
            'EducarTurmaDet',
            'EducarTurmaTipoDet',
            'FuncionarioVinculoDet',
            'Logof',
            'Manutencao',
            'PesquisaFuncionarioLst',
            'PortalAcessoDet',
            'PublicDistritoDet',
            'PublicMunicipioDet',
            'PublicPaisDet',
            'PublicUfDet',
            'TransporteEmpresaDet',
            'TransporteItinerarioCad',
            'TransporteMotoristaDet',
            'TransportePessoaDet',
            'TransportePontoDet',
            'TransporteRotaDet',
            'TransporteVeiculoDet',
            'EducarBuscaAtivaLstTest',
            'EducarBuscaAtivaCadTest'
        ];
    }
}
