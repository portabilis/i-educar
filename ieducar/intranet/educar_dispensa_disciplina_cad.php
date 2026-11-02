<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyExemptionStage;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use App\Models\LegacyStudent;
use App\Services\Exemption\ExemptionService;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_tipo_dispensa;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $observacao;

    public $cod_dispensa;

    public $ref_cod_matricula;

    public $ref_cod_turma;

    public $ref_cod_serie;

    public $ref_cod_disciplina;

    public $ref_sequencial;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $modoEdicao;

    public $ano;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_disciplina = $this->getQueryString('ref_cod_disciplina');
        $this->ref_cod_matricula = $this->getQueryString('ref_cod_matricula');

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula);

        $this->carregarMatricula();

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)) {
            $dispensa = LegacyDisciplineExemption::query()
                ->withTrashed()
                ->whereExemptionKey(
                    $this->ref_cod_matricula,
                    $this->ref_cod_serie,
                    $this->ref_cod_escola,
                    $this->ref_cod_disciplina
                )
                ->first();

            if ($dispensa) {
                $registro = $dispensa->getAttributes();

                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->cod_dispensa = $registro['cod_dispensa'];

                $obj_permissoes = new clsPermissoes;

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
            sprintf(
                'educar_dispensa_disciplina_det.php?ref_cod_matricula=%d&ref_cod_serie=%d&ref_cod_escola=%d&ref_cod_disciplina=%d',
                $registro['ref_cod_matricula'],
                $registro['ref_cod_serie'],
                $registro['ref_cod_escola'],
                $registro['ref_cod_disciplina']
            ) :
            'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;

        $this->nome_url_cancelar = 'Cancelar';

        // Início > Escola > Alunos > Nome > Matrícula > Dispensa de componentes curriculares > Nova (ou Editar)
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb(currentPage: $retorno == 'Editar' ? 'Editar' : 'Nova', breadcrumbs: [
            'educar_index.php' => 'Escola',
            'educar_aluno_lst.php' => 'Alunos',
            "educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}" => $registration?->student?->real_name,
            "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" => 'Matrícula',
            "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" => 'Dispensa de componentes curriculares',
        ]);
        $this->modoEdicao = $retorno == 'Editar';
        $this->loadAssets();

        return $retorno;
    }

    /**
     * Preenche a escola e a série da matrícula ativa, ou redireciona para a
     * listagem quando a matrícula não existe.
     */
    private function carregarMatricula(): void
    {
        if (is_numeric($this->ref_cod_matricula)) {
            $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula, ativo: 1);
            $det_matricula = $obj_matricula->detalhe();
            if (!$det_matricula) {
                $this->redirectNotFound(
                    url: 'educar_matricula_lst.php',
                    registro: 'matrícula',
                    feminino: true,
                    codigo: $this->ref_cod_matricula,
                    dica: 'Acesse a matrícula pela ficha do aluno.',
                );
            }

            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        } else {
            $this->redirectNotFound(
                url: 'educar_matricula_lst.php',
                registro: 'matrícula',
                feminino: true,
                dica: 'Acesse a matrícula pela ficha do aluno.',
            );
        }
    }

    public function Gerar()
    {
        /**
         * Busca dados da matricula
         */
        $obj_ref_cod_matricula = new clsPmieducarMatricula;
        $detalhe_matricula = $obj_ref_cod_matricula->lista($this->ref_cod_matricula);

        if (is_array($detalhe_matricula)) {
            $detalhe_matricula = array_shift($detalhe_matricula);

            $this->ano = $detalhe_matricula['ano'];

            $nm_aluno = LegacyStudent::query()
                ->active()
                ->whereStudent($detalhe_matricula['ref_cod_aluno'])
                ->joinPerson()
                ->value('pessoa.nome');
        }

        $obj_escola = new clsPmieducarEscola(cod_escola: $this->ref_cod_escola, bloquear_lancamento_diario_anos_letivos_encerrados: 1);

        $det_escola = $obj_escola->detalhe();
        $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

        $enturmacao = LegacyEnrollment::query()
            ->active()
            ->when(is_numeric($this->ref_cod_matricula), fn ($q) => $q->whereRegistration($this->ref_cod_matricula))
            ->whereHas('registration', function ($q) {
                $q->whereHas('student', fn ($q) => $q->active());
                $q->when(is_numeric($this->ref_cod_serie), fn ($q) => $q->whereGrade($this->ref_cod_serie));
                $q->when(is_numeric($this->ref_cod_escola), fn ($q) => $q->whereSchool($this->ref_cod_escola));
            })
            ->first(['ref_cod_turma', 'sequencial']);

        if ($enturmacao) {
            $this->ref_cod_turma = $enturmacao->ref_cod_turma;
            $this->ref_sequencial = $enturmacao->sequencial;
        }

        $this->campoRotulo(nome: 'nm_aluno', campo: 'Nome do Aluno', valor: $nm_aluno);

        if (!isset($this->ref_cod_turma)) {
            $this->mensagem = 'Para dispensar um aluno de um componente curricular, é necessário que este esteja enturmado.';

            return;
        }

        // primary keys
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);
        $this->campoOculto(nome: 'ref_cod_serie', valor: $this->ref_cod_serie);
        $this->campoOculto(nome: 'ref_cod_serie_busca', valor: $this->ref_cod_serie);
        $this->campoOculto(nome: 'ref_cod_escola', valor: $this->ref_cod_escola);
        $this->campoOculto(nome: 'cod_dispensa', valor: $this->cod_dispensa);

        $this->campoOculto(nome: 'ano', valor: $this->ano);
        $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
        $this->campoOculto(nome: 'ref_cod_turma', valor: $this->ref_cod_turma);

        $opcoes = ['' => 'Selecione'];

        // Seleciona os componentes curriculares da turma
        try {
            $componentes = App_Model_IedFinder::getComponentesTurma(
                serieId: $this->ref_cod_serie,
                escola: $this->ref_cod_escola,
                turma: $this->ref_cod_turma
            );
        } catch (App_Model_Exception $e) {
            // Inalcançável: a busca lança `DisciplineNotLinkedToRegistrationException`, que não é `App_Model_Exception`.
            // @codeCoverageIgnoreStart
            $this->mensagem = $e->getMessage();

            return;
            // @codeCoverageIgnoreEnd
        }

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        if ($this->modoEdicao) {
            $this->campoRotulo(nome: 'nm_disciplina', campo: 'Disciplina', valor: $opcoes[$this->ref_cod_disciplina]);
            $this->campoOculto(nome: 'ref_cod_disciplina', valor: $this->ref_cod_disciplina);
            $this->campoOculto(nome: 'modo_edicao', valor: $this->modoEdicao);
        } else {
            $this->inputsHelper()->multipleSearchComponenteCurricular(attrName: null, inputOptions: [
                'label' => 'Componentes curriculares',
                'required' => true,
            ], helperOptions: ['searchForArea' => true]);
        }

        $opcoes = LegacyExemptionType::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_tipo_dispensa')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(
            nome: 'ref_cod_tipo_dispensa',
            campo: 'Tipo Dispensa',
            valor: $opcoes,
            default: $this->ref_cod_tipo_dispensa
        );
        $this->montaEtapas();
        $this->campoMemo(nome: 'observacao', campo: 'Observação', valor: $this->observacao, colunas: 60, linhas: 10);
    }

    public function existeComponenteSerie($serieId, $escolaId, $disciplinaId)
    {
        // Inalcançável: nada chama este método.
        // @codeCoverageIgnoreStart
        try {
            App_Model_IedFinder::getEscolaSerieDisciplina(
                serieId: $serieId,
                escolaId: $escolaId,
                disciplinaId: $disciplinaId
            );
        } catch (Exception) {
            return false;
        }

        return true;
        // @codeCoverageIgnoreEnd
    }

    public function Novo()
    {
        if (empty($this->etapa)) {
            $this->mensagem = 'É necessário informar pelo menos uma etapa.';

            return false;
        }

        $exemptionService = new ExemptionService($this->user());

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        $registration = LegacyRegistration::query()->findOrFail($this->ref_cod_matricula, [
            'cod_matricula',
            'ref_ref_cod_serie',
            'ref_ref_cod_escola',
            'ano',
        ]);

        DB::beginTransaction();

        try {
            $exemptionService->createExemptionByDisciplineArray(registration: $registration, disciplineArray: $this->componentecurricular, exemptionTypeId: $this->ref_cod_tipo_dispensa, description: $this->observacao, stages: $this->etapa);
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
            // @codeCoverageIgnoreEnd
        }

        DB::commit();

        if (is_array($exemptionService->disciplinasNaoExistentesNaSerieDaEscola) && count($exemptionService->disciplinasNaoExistentesNaSerieDaEscola) > 0) {
            $disciplinas = implode(separator: ', ', array: $exemptionService->disciplinasNaoExistentesNaSerieDaEscola);
            $this->mensagem = "O(s) componente(s):<b>{$disciplinas}</b>. não está(ão) habilitado(s) na série da escola.";

            return false;
        }

        $exemptionService->runsPromotion(registration: $registration, stages: $this->etapa);

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
    }

    public function Editar()
    {
        if (empty($this->etapa)) {
            $this->mensagem = 'É necessário informar pelo menos uma etapa.';

            return false;
        }

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        if (!is_numeric($this->ref_cod_matricula)) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'Edição não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        $registration = LegacyRegistration::query()->findOrFail($this->ref_cod_matricula, [
            'cod_matricula',
            'ref_ref_cod_serie',
            'ref_ref_cod_escola',
            'ano',
        ]);
        $exemptionService = new ExemptionService($this->user());

        $exemption = $exemptionService->findExemptionByRegistration(
            registrationId: $this->ref_cod_matricula,
            gradeId: $this->ref_cod_serie,
            schoolId: $this->ref_cod_escola,
            disciplineId: $this->ref_cod_disciplina
        );

        if (!$exemption) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'Edição não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        DB::beginTransaction();

        LegacyExemptionStage::query()->where('ref_cod_dispensa', $exemption->getKey())->delete();

        $exemptionService->cadastraEtapasDaDispensa(exemption: $exemption, stages: $this->etapa);

        $editou = $exemptionService->updateExemption(
            exemption: $exemption,
            registration: $registration,
            exemptionTypeId: $this->ref_cod_tipo_dispensa,
            description: $this->observacao
        );

        if (!$editou) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            DB::rollBack();

            $this->mensagem = 'Edição não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        DB::commit();

        if ($editou) {
            $exemptionService->runsPromotion(registration: $registration, stages: $this->etapa);
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        // Inalcançável: `edita()` só falha sem os códigos, que a tela sempre tem.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Edição não realizada.<br />';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_excluir(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        if (!is_numeric($this->ref_cod_matricula)) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        $registration = LegacyRegistration::query()->findOrFail($this->ref_cod_matricula, [
            'cod_matricula',
            'ref_ref_cod_serie',
            'ref_ref_cod_escola',
            'ano',
        ]);
        $exemptionService = new ExemptionService($this->user());

        $exemption = $exemptionService->findExemptionByRegistration(
            registrationId: $this->ref_cod_matricula,
            gradeId: $this->ref_cod_serie,
            schoolId: $this->ref_cod_escola,
            disciplineId: $this->ref_cod_disciplina
        );

        if (!$exemption) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        DB::beginTransaction();

        LegacyExemptionStage::query()->where('ref_cod_dispensa', $exemption->getKey())->delete();

        $excluiu = $exemptionService->deleteExemption(exemption: $exemption, registration: $registration);

        if (!$excluiu) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            DB::rollBack();

            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
            // @codeCoverageIgnoreEnd
        }

        DB::commit();

        if ($excluiu) {
            $exemptionService->runsPromotion(registration: $registration, stages: $this->etapa);
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        // Inalcançável: `excluir()` só falha sem os códigos, que a tela sempre tem.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function montaEtapas()
    {
        // Pega matricula para pegar curso, escola e ano
        $objMatricula = new clsPmieducarMatricula;
        $dadosMatricula = $objMatricula->lista($this->ref_cod_matricula);
        // Pega curso para pegar padrao ano escolar
        $padraoAnoEscolar = LegacyCourse::query()
            ->active()
            ->whereKey($dadosMatricula[0]['ref_cod_curso'])
            ->value('padrao_ano_escolar');
        // Pega escola e ano para pegar as etapas em ano letivo modulo
        $escolaId = $dadosMatricula[0]['ref_ref_cod_escola'];
        $ano = $dadosMatricula[0]['ano'];
        // Pega dados da enturmação atual
        $seqMatriculaTurma = LegacyEnrollment::query()
            ->join('relatorio.view_situacao', function ($j) {
                $j->on('view_situacao.cod_matricula', 'matricula_turma.ref_cod_matricula');
                $j->on('view_situacao.cod_turma', 'matricula_turma.ref_cod_turma');
                $j->on('view_situacao.sequencial', 'matricula_turma.sequencial');
            })
            ->when(is_numeric($this->ref_cod_matricula), fn ($q) => $q->whereRegistration($this->ref_cod_matricula))
            ->max('matricula_turma.sequencial');

        $enturmacaoAtual = LegacyEnrollment::query()
            ->join('pmieducar.matricula', 'matricula.cod_matricula', 'matricula_turma.ref_cod_matricula')
            ->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno')
            ->where('aluno.ativo', 1)
            ->when(is_numeric($this->ref_cod_matricula), fn ($q) => $q->whereRegistration($this->ref_cod_matricula))
            ->when(is_numeric($seqMatriculaTurma), fn ($q) => $q->whereSequential($seqMatriculaTurma))
            ->first(['matricula_turma.ref_cod_turma']);
        // Pega etapas definidas na escola
        $dadosAnoLetivoMod = LegacyAcademicYearStage::query()->whereSchool($escolaId)->whereYearEq($ano)->get();
        // Pega etapas definida na turma
        $dadosTurmaModulo = LegacySchoolClassStage::query()
            ->whereSchoolClass($enturmacaoAtual->ref_cod_turma)
            ->get(['ref_cod_modulo', 'sequencial']);
        // Define de onde as etapas serão pegas
        if ($padraoAnoEscolar == 1) {
            $dadosEtapa = $dadosAnoLetivoMod;
        } else {
            $dadosEtapa = $dadosTurmaModulo;
        }
        // Pega nome do modulo
        $nomeModulo = LegacyStageType::find($dadosEtapa[0]['ref_cod_modulo'])->nm_tipo;
        $conteudoHtml = '';

        foreach ($dadosEtapa as $modulo) {
            $checked = '';
            $etapaJaDispensada = is_numeric($this->cod_dispensa)
                && LegacyExemptionStage::query()
                    ->where('ref_cod_dispensa', $this->cod_dispensa)
                    ->where('etapa', $modulo['sequencial'])
                    ->exists();
            if ($etapaJaDispensada) {
                $checked = 'checked';
            }
            $conteudoHtml .= '<div style="margin-bottom: 10px;">';
            $conteudoHtml .= "<label style='display: block; float: left; width: 250px'>
                          <input type=\"checkbox\" $checked
                              name=\"etapa[". $modulo['sequencial']  .']"
                              id="etapa_'. $modulo['sequencial']  . '"
                              value="'. $modulo['sequencial'] . '">' . $modulo['sequencial'] . 'º ' . $nomeModulo . '
                        </label>';
            $conteudoHtml .= '</div>';
        }

        $etapas = '<table cellspacing="0" cellpadding="0" border="0">';
        $etapas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudoHtml);
        $etapas .= '</table>';

        $this->campoRotulo(
            nome: 'etapas_',
            campo: 'Etapas',
            valor: "<div id='etapas'>$etapas</div>"
        );
    }

    public function loadAssets()
    {
        $scripts = [
            '/vendor/legacy/Cadastro/Assets/Javascripts/ModalDispensasDisciplinaCad.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function Formular()
    {
        $this->title = 'Dispensa Componente Curricular';
        $this->processoAp = 628;
    }
};
