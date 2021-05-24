<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyRegistration;
use App\Services\Exemption\ActiveSearchExemptionService;
use iEducar\Modules\School\Model\ActiveSearch;
use iEducar\Modules\School\Model\ExemptionType;
use iEducar\Support\View\SelectOptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_matricula_entrada;
    public $ref_cod_matricula_saida;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_transferencia;
    public $data_fim;
    public $ref_cod_instituicao;
    public $ref_cod_matricula;
    public $transferencia_tipo;
    public $ref_cod_aluno;
    public $nm_aluno;
    public $resultado_busca_ativa;
    public $data_inicio;
    public $ref_cod_tipo_dispensa;
    public $cod_dispensa;
    public $ref_cod_turma;
    public $ref_cod_serie;
    public $ref_cod_disciplina;
    public $ref_sequencial;
    public $ref_cod_escola;
    public $modoEdicao;
    public $start_date_timestamp;
    /**
     * @var array
     */
    public $etapa;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_matricula = $this->getQueryString('ref_cod_matricula');
        $this->ref_cod_tipo_dispensa = $this->getQueryString('ref_cod_tipo_dispensa');
        $this->start_date_timestamp = $this->getQueryString('data_cadastro');
        $this->etapa = explode(',', $this->getQueryString('etapa'));

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        if (empty($this->ref_cod_matricula)) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $obj_matricula = new clsPmieducarMatricula(
            $this->ref_cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,);
        $det_matricula = $obj_matricula->detalhe();
        $this->redirectIf(!$det_matricula, 'educar_matricula_lst.php');

        if (isset($this->ref_cod_tipo_dispensa)) {

            $startDate = Carbon::createFromTimestamp($this->start_date_timestamp)->format('Y-m-d');

            $legacyDisciplineExemption = LegacyDisciplineExemption::query()
                ->selectRaw("cod_dispensa, ref_cod_tipo_dispensa,
                        TO_CHAR(dispensa_disciplina.data_cadastro, 'YYYY-mm-dd')::date as data_cadastro,
                        TO_CHAR(dispensa_disciplina.data_fim, 'YYYY-mm-dd')::date as data_fim,
                        observacao, resultado_busca_ativa")
                ->distinct()
                ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
                ->join('pmieducar.dispensa_etapa', 'dispensa_etapa.ref_cod_dispensa', '=', 'cod_dispensa')
                ->where('ref_cod_matricula', $this->ref_cod_matricula)
                ->where('ref_cod_tipo_dispensa', $this->ref_cod_tipo_dispensa)
                ->where('tipo', ExemptionType::DISPENSA_BUSCA_ATIVA)
                ->whereIn('dispensa_etapa.etapa', $this->etapa)
                ->whereRaw('dispensa_disciplina.data_cadastro::DATE = ?', [$startDate]);

            $activeSearchExemptions = $legacyDisciplineExemption->first();

            if ($activeSearchExemptions) {
                foreach ($activeSearchExemptions->toArray() as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->breadcrumb('Registro do busca ativa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->url_cancelar = 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
        $this->nome_url_cancelar = 'Cancelar';

        $this->loadAssets();

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        $det_aluno = current($lst_aluno);

        $this->nm_aluno = $det_aluno['nome_aluno'];
        $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, false, false, false, '', '', '', '', true);
        $this->ref_cod_instituicao = $det_aluno['ref_cod_abandono_tipo'];

        $tiposDispensa = new clsPmieducarTipoDispensa();
        $tiposDispensaBuscaAtiva = $tiposDispensa->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao,
            ExemptionType::DISPENSA_BUSCA_ATIVA);

        foreach ($tiposDispensaBuscaAtiva as $buscaAtiva) {
            $selectOptions[$buscaAtiva['cod_tipo_dispensa']] = $buscaAtiva['nm_tipo'];
        }

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = ['label' => 'Tipo', 'resources' => $selectOptions, 'value' => $this->ref_cod_tipo_dispensa, 'required' => true];

        $this->inputsHelper()->select('ref_cod_tipo_dispensa', $options);

        $dataCadastro = $this->data_cadastro ? (new DateTime($this->data_cadastro))->format('d/m/Y') : null;

        $this->inputsHelper()->date('data_cadastro', [
            'label' => 'Data de início',
            'placeholder' => 'dd/mm/yyyy',
            'required' => true,
            'value' => $dataCadastro
        ]);

        $dataFim = $this->data_fim ? (new DateTime($this->data_fim))->format('d/m/Y') : null;

        $this->inputsHelper()->date('data_fim', [
            'label' => 'Data de retorno/abandono',
            'placeholder' => 'dd/mm/yyyy',
            'required' => false,
            'value' => $dataFim
        ]);

        $options = [
            'label' => 'Resultado da BA',
            'resources' => SelectOptions::activeSearchResultOptions(),
            'value' => $this->resultado_busca_ativa,
            'required' => true
        ];

        $this->inputsHelper()->select('resultado_busca_ativa', $options);

        $textAreaSettings = [
            'label' => 'Observação',
            'value' => $this->observacao,
            'max_length' => 300,
            'cols' => 60,
            'rows' => 5,
            'placeholder' => '',
            'required' => false
        ];
        $this->buildStagesElement();
        $this->inputsHelper()->textArea('observacao', $textAreaSettings);
        $this->campoOculto('cod_dispensa', $this->cod_dispensa);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $legacyRegistration = LegacyRegistration::findOrFail($this->ref_cod_matricula);

        $this->redirectIf(!$legacyRegistration, 'educar_matricula_lst.php');
        $this->ref_cod_escola = $legacyRegistration->ref_ref_cod_escola;
        $this->ref_cod_serie = $legacyRegistration->ref_ref_cod_serie;
        $this->ref_cod_aluno = $legacyRegistration->ref_cod_aluno;

        $legacyEnrolment = $legacyRegistration->activeEnrollments()->first();

        $this->ref_cod_turma = $legacyEnrolment->ref_cod_turma;
        $this->ref_sequencial = $legacyEnrolment->sequencial;

        $componentes = App_Model_IedFinder::getComponentesTurma(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_turma
        );
        $componentesId = [];
        foreach ($componentes as $componente) {
            $componentesId[] = $componente->id;
        }

        $stages = $this->etapa;
        $exemptionService = new ActiveSearchExemptionService($this->user());

        $this->data_cadastro = Carbon::createFromFormat('d/m/Y', $this->data_cadastro)->format('Y-m-d');
        if ($this->data_fim) {
            $this->data_fim = Carbon::createFromFormat('d/m/Y', $this->data_fim)->format('Y-m-d');
        }

        $this->resultado_busca_ativa = empty($this->resultado_busca_ativa) ? ActiveSearch::ACTIVE_SEARCH_IN_PROGRESS_RESULT : $this->resultado_busca_ativa;
        try {
            DB::beginTransaction();
            $registration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
            $exemptionService->createExemptionByDisciplineArray(
                $registration,
                $componentesId,
                $this->ref_cod_tipo_dispensa,
                $this->observacao,
                $stages,
                $this->data_cadastro,
                $this->data_fim,
                $this->resultado_busca_ativa
            );

            $exemptionService->runsPromotion($registration, $this->etapa);

        } catch (App_Model_Exception $e) {
            $this->mensagem = $e->getMessage();
            DB::rollBack();
            return false;
        } catch (ValidationException $e) {
            $this->mensagem = $e->validator->errors()->first();
            DB::rollBack();
            return false;
        }
        DB::commit();
        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';

        if ($this->resultado_busca_ativa === ActiveSearch::ACTIVE_SEARCH_ABANDONMENT_RESULT) {
            $this->simpleRedirect('educar_abandono_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula . '&ref_cod_aluno=' . $this->ref_cod_aluno);
        }

        $this->simpleRedirect('educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    public function Editar()
    {
        $this->mensagem = 'Edição efetuada com sucesso.<br />';
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $legacyDisciplineExemption = LegacyDisciplineExemption::query()
            ->select('dispensa_disciplina.*')
            ->distinct()
            ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
            ->join('pmieducar.dispensa_etapa', 'dispensa_etapa.ref_cod_dispensa', '=', 'cod_dispensa')
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('ref_cod_tipo_dispensa', $this->ref_cod_tipo_dispensa)
            ->where('tipo', ExemptionType::DISPENSA_BUSCA_ATIVA)
            ->whereIn('dispensa_etapa.etapa', $this->etapa);

        $activeSearchExemptions = $legacyDisciplineExemption->get();
        $exemptionService = new ActiveSearchExemptionService($this->user());

        $this->data_cadastro = Carbon::createFromFormat('d/m/Y', $this->data_cadastro)->format('Y-m-d');

        $this->data_fim = $this->data_fim ? Carbon::createFromFormat('d/m/Y', $this->data_fim)->format('Y-m-d') : null;
        $resultadoBuscaAtiva = empty($this->resultado_busca_ativa) ? ActiveSearch::ACTIVE_SEARCH_IN_PROGRESS_RESULT : $this->resultado_busca_ativa;
        try {
            DB::beginTransaction();
            foreach ($activeSearchExemptions as $activeSearchExemption) {
                $activeSearchExemption->stages()->delete();
                $exemptionService->cadastraEtapasDaDispensa($activeSearchExemption, $this->etapa);
                $activeSearchExemption->data_cadastro = $this->data_cadastro;
                $activeSearchExemption->data_fim = $this->data_fim;
                $activeSearchExemption->observacao = $this->observacao;
                $activeSearchExemption->resultado_busca_ativa = $resultadoBuscaAtiva;
                $activeSearchExemption->save();
                $exemptionService->runsPromotion($activeSearchExemption->registration()->first(), $this->etapa);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensagem = 'Edição não realizada: ' . $e->getMessage();
            return false;
        }

        if ($this->resultado_busca_ativa == ActiveSearch::ACTIVE_SEARCH_ABANDONMENT_RESULT) {
            $this->simpleRedirect('educar_abandono_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula . '&ref_cod_aluno=' . $this->ref_cod_aluno);
        }

        $this->simpleRedirect('educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $legacyDisciplineExemption = LegacyDisciplineExemption::query()
            ->select('dispensa_disciplina.*')
            ->distinct()
            ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
            ->join('pmieducar.dispensa_etapa', 'dispensa_etapa.ref_cod_dispensa', '=', 'cod_dispensa')
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('ref_cod_tipo_dispensa', $this->ref_cod_tipo_dispensa)
            ->where('tipo', ExemptionType::DISPENSA_BUSCA_ATIVA)
            ->whereIn('dispensa_etapa.etapa', $this->etapa);

        $activeSearchExemptions = $legacyDisciplineExemption->get();
        $exemptionService = new ActiveSearchExemptionService($this->user());

        try {
            DB::beginTransaction();
            foreach ($activeSearchExemptions as $activeSearchExemption) {
                $activeSearchExemption->stages()->delete();
                $activeSearchExemption->delete();
                $exemptionService->runsPromotion($activeSearchExemption->registration()->first(), $this->etapa);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensagem = 'Exclusão não realizada.';
            return false;
        }
        DB::commit();

        $this->mensagem = 'Exclusão efetuada com sucesso.';
        $this->simpleRedirect('educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    protected function loadAssets()
    {
        $jsFiles = ['/modules/BuscaAtiva/BuscaAtiva.js', '/modules/BuscaAtiva/educar-busca-ativa-cad.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $jsFiles);
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Busca Ativa';
        $this->processoAp = '578';

    }

    public function buildStagesElement()
    {
        //Pega matricula para pegar curso, escola e ano
        $objMatricula = new clsPmieducarMatricula();
        $dadosMatricula = $objMatricula->lista($this->ref_cod_matricula);
        //Pega curso para pegar padrao ano escolar
        $objCurso = new clsPmieducarCurso();
        $dadosCurso = $objCurso->lista($dadosMatricula[0]['ref_cod_curso']);
        $padraoAnoEscolar = $dadosCurso[0]['padrao_ano_escolar'];
        //Pega escola e ano para pegar as etapas em ano letivo modulo
        $escolaId = $dadosMatricula[0]['ref_ref_cod_escola'];
        $ano = $dadosMatricula[0]['ano'];
        //Pega dados da enturmação atual
        $objMatriculaTurma = new clsPmieducarMatriculaTurma();
        $seqMatriculaTurma = $objMatriculaTurma->getUltimaEnturmacao($this->ref_cod_matricula);
        $dadosMatriculaTurma = $objMatriculaTurma->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $seqMatriculaTurma);
        //Pega etapas definidas na escola
        $objAnoLetivoMod = new clsPmieducarAnoLetivoModulo();
        $dadosAnoLetivoMod = $objAnoLetivoMod->lista($ano, $escolaId);
        //Pega etapas definida na turma
        $objTurmaModulo = new clsPmieducarTurmaModulo();
        $dadosTurmaModulo = $objTurmaModulo->lista($dadosMatriculaTurma[0]['ref_cod_turma']);
        //Define de onde as etapas serão pegas
        if ($padraoAnoEscolar == 1) {
            $dadosEtapa = $dadosAnoLetivoMod;
        } else {
            $dadosEtapa = $dadosTurmaModulo;
        }
        //Pega nome do modulo
        $objModulo = new clsPmieducarModulo();
        $dadosModulo = $objModulo->lista($dadosEtapa[0]['ref_cod_modulo']);
        $nomeModulo = $dadosModulo[0]['nm_tipo'];

        foreach ($dadosEtapa as $modulo) {
            $checked = '';
            $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa($this->cod_dispensa, $modulo['sequencial']);
            $verificaSeExiste = $objDispensaEtapa->existe();
            if ($verificaSeExiste) {
                $checked = 'checked';
            }
            $conteudoHtml .= '<div style="margin-bottom: 10px;">';
            $conteudoHtml .= "<label style='display: block; float: left; width: 250px'>
                          <input type=\"checkbox\" $checked
                              name=\"etapa[" . $modulo['sequencial'] . ']"
                              id="etapa_' . $modulo['sequencial'] . '"
                              value="' . $modulo['sequencial'] . '">' . $modulo['sequencial'] . 'º ' . $nomeModulo . '
                        </label>';
            $conteudoHtml .= '</div>';
        }

        $etapas = '<table cellspacing="0" cellpadding="0" border="0">';
        $etapas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudoHtml);
        $etapas .= '</table>';

        $this->campoRotulo(
            'etapas_',
            'Etapas',
            "<div id='etapas'>$etapas</div>"
        );
    }
};
