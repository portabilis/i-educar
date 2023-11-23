<?php

use App\Models\Employee;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Services\iDiarioService;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $id;

    public $ano;

    public $servidor_id;

    public $funcao_exercida;

    public $tipo_vinculo;

    public $permite_lancar_faltas_componente;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ref_cod_turma;

    public $unidades_curriculares;

    public $turma_estrutura_curricular;

    public $nm_turma;

    public $copia = false;

    public $apresentar_outras_unidades_curriculares_obrigatorias = false;

    public $outras_unidades_curriculares_obrigatorias;

    public function Inicializar()
    {
        $this->id = $this->getQueryString(name: 'id');
        $this->servidor_id = $this->getQueryString(name: 'ref_cod_servidor');
        $this->ref_cod_instituicao = $this->getQueryString(name: 'ref_cod_instituicao');

        // URL para redirecionamento
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        if ($obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->fexcluir = true;
        }

        $retorno = 'Novo';

        if (is_numeric(value: $this->id)) {
            $registro = LegacySchoolClassTeacher::find($this->id);

            if ($registro) {
                $this->ref_cod_turma = $registro['turma_id'];
                $this->funcao_exercida = $registro['funcao_exercida'];
                $this->tipo_vinculo = $registro['tipo_vinculo'];
                $this->permite_lancar_faltas_componente = $registro['permite_lancar_faltas_componente'];
                $this->turma_turno_id = $registro['turno_id'];
                $this->nm_turma = $registro['nm_turma'];

                $obj_turma = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
                $obj_turma = $obj_turma->detalhe();
                $this->ref_cod_escola = $obj_turma['ref_ref_cod_escola'];

                $this->ref_cod_curso = $obj_turma['ref_cod_curso'];
                $this->ref_cod_serie = $obj_turma['ref_ref_cod_serie'];
                $this->turma_estrutura_curricular = $obj_turma['estrutura_curricular'];
                $this->apresentar_outras_unidades_curriculares_obrigatorias = $registro->schoolClass->outras_unidades_curriculares_obrigatorias ?? false;
                $this->outras_unidades_curriculares_obrigatorias = $registro['outras_unidades_curriculares_obrigatorias'];

                if (is_string(value: $registro['unidades_curriculares'])) {
                    $this->unidades_curriculares = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $registro['unidades_curriculares']));
                }

                if (!isset($_GET['copia'])) {
                    $retorno = 'Editar';
                }

                if (isset($_GET['copia'])) {
                    $this->copia = true;
                    $this->ano = date(format: 'Y');
                }
            }
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? 'educar_servidor_vinculo_turma_det.php?id=' . $this->id
            : $backUrl;

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Vínculo do professor à turma', breadcrumbs: [
            'educar_servidores_index.php' => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $ano = null;

        if ($this->id) {
            $detProfessorTurma = LegacySchoolClassTeacher::find($this->id);
            $ano = $detProfessorTurma['ano'];
            $this->ano = $ano; //o inputsHelper necessita do valor para poder filtrar as turmas deste ano
        }

        if (isset($_GET['copia'])) {
            $this->ano = $ano = date(format: 'Y');
            $this->ref_cod_turma = LegacySchoolClass::query()
                ->where('ref_ref_cod_escola', $this->ref_cod_escola)
                ->where('ref_ref_cod_serie', $this->ref_cod_serie)
                ->where('ref_cod_curso', $this->ref_cod_curso)
                ->where('ano', $this->ano)
                ->where('nm_turma', $this->nm_turma)
                ->value('cod_turma');
        }

        $this->campoOculto(nome: 'id', valor: $this->id);
        $this->campoOculto(nome: 'servidor_id', valor: $this->servidor_id);
        $this->campoOculto(nome: 'copia', valor: (int) $this->copia);
        $this->campoOculto(nome: 'apresentar_outras_unidades_curriculares_obrigatorias', valor: $this->apresentar_outras_unidades_curriculares_obrigatorias);

        $this->inputsHelper()->dynamic(helperNames: 'ano', inputOptions: ['value' => (is_null(value: $ano) ? date(format: 'Y') : $ano)]);
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola', 'curso', 'serie', 'turma']);

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
        $this->campoOculto(nome: 'obrigar_campos_censo', valor: (int) $obrigarCamposCenso);

        $resources = SelectOptions::funcoesExercidaServidor();
        $options = [
            'label' => 'Função exercida',
            'resources' => $resources,
            'value' => $this->funcao_exercida,
        ];
        $this->inputsHelper()->select(attrName: 'funcao_exercida', inputOptions: $options);

        $helperOptions = ['objectName' => 'unidades_curriculares'];
        $options = [
            'label' => 'Unidade(s) curricular(es) que leciona',
            'label_hint' => 'Campo referente a unidade(s) curricular(es) da turma',
            'size' => 50,
            'required' => false,
            'options' => [
                'values' => $this->unidades_curriculares,
                'all_values' => UnidadesCurriculares::getDescriptiveValues(),
            ],
        ];
        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $this->inputsHelper()->select(attrName: 'outras_unidades_curriculares_obrigatorias', inputOptions: [
            'label' => 'Outra(s) unidade(s) curricular(es) obrigatória(s);',
            'resources' => [
                '' => 'Selecione',
                0 => 'Não',
                1 => 'Sim',
            ],
            'value' => $this->outras_unidades_curriculares_obrigatorias,
            'required' => false,
        ]);

        $resources = SelectOptions::tiposVinculoServidor();
        $options = [
            'label' => 'Tipo do vínculo',
            'resources' => $resources,
            'value' => $this->tipo_vinculo,
            'required' => false,
        ];
        $this->inputsHelper()->select(attrName: 'tipo_vinculo', inputOptions: $options);

        $turnos = [
            null => 'Selecione',
            clsPmieducarTurma::TURNO_MATUTINO => 'Matutino',
            clsPmieducarTurma::TURNO_VESPERTINO => 'Vespertino',
        ];
        $turma = LegacySchoolClass::with('course:cod_curso,modalidade_curso')->find($this->ref_cod_turma, [
            'cod_turma',
            'ref_cod_curso',
        ]);

        if ($turma && $turma->course->modalidade_curso === ModalidadeCurso::EJA) {
            $turnos[clsPmieducarTurma::TURNO_NOTURNO] = 'Noturno';
        }

        $options = [
            'label' => 'Turno',
            'resources' => $turnos,
            'value' => $this->turma_turno_id,
            'required' => false,
            'label_hint' => 'Preencha apenas se o servidor atuar em algum turno específico',
        ];

        if ($this->tipoacao === 'Editar' && $this->existeLancamentoIDiario(professorId: $this->servidor_id, turmaId: $this->ref_cod_turma)) {
            $options['disabled'] = true;
        }

        $this->inputsHelper()->select(attrName: 'turma_turno_id', inputOptions: $options);

        $options = [
            'label' => 'Professor de área específica?',
            'value' => $this->permite_lancar_faltas_componente,
            'help' => 'Marque esta opção somente se o professor leciona uma disciplina específica na turma selecionada.',
        ];

        $this->inputsHelper()->checkbox(attrName: 'permite_lancar_faltas_componente', inputOptions: $options);
        $this->inputsHelper()->checkbox(attrName: 'selecionar_todos', inputOptions: ['label' => 'Selecionar/remover todos']);
        $this->inputsHelper()->multipleSearchComponenteCurricular(attrName: null, inputOptions: ['label' => 'Componentes lecionados', 'required' => false], helperOptions: ['searchForArea' => true, 'allDisciplinesMulti' => true]);

        $scripts = [
            '/vendor/legacy/Cadastro/Assets/Javascripts/ServidorVinculoTurma.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function Novo()
    {
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        if (!isset($this->ref_cod_turma)) {
            $this->mensagem = 'É necessário selecionar uma turma';

            return false;
        }

        if (!$this->validaCamposCenso()) {
            return false;
        }

        if (!$this->validaVinculoEscola()) {
            return false;
        }

        $professorTurma = new clsModulesProfessorTurma(
            id: null,
            ano: $this->ano,
            instituicao_id: $this->ref_cod_instituicao,
            servidor_id: $this->servidor_id,
            turma_id: $this->ref_cod_turma,
            funcao_exercida: $this->funcao_exercida,
            tipo_vinculo: $this->tipo_vinculo,
            permite_lancar_faltas_componente: $this->permite_lancar_faltas_componente,
            turno_id: $this->turma_turno_id,
            outras_unidades_curriculares_obrigatorias: $this->outras_unidades_curriculares_obrigatorias
        );
        $id = $professorTurma->existe2();
        if ($id) {
            $link = "<a href=\"educar_servidor_vinculo_turma_det.php?id=$id\"><b>Acesse aqui</b></a>";
            $this->mensagem = "Já existe um vínculo para o(a) professor(a) nesta turma na escola e ano letivo selecionado. $link";

            return false;
        }

        $professorTurmaId = $professorTurma->cadastra();
        $professorTurma->gravaComponentes(professor_turma_id: $professorTurmaId, componentes: $this->componentecurricular);

        $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
        $this->simpleRedirect(url: $backUrl);
    }

    public function Editar()
    {
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        $this->unidades_curriculares = $this->transformArrayInString(value: $this->unidades_curriculares);

        $professorTurma = new clsModulesProfessorTurma(
            id: $this->id,
            ano: $this->ano,
            instituicao_id: $this->ref_cod_instituicao,
            servidor_id: $this->servidor_id,
            turma_id: $this->ref_cod_turma,
            funcao_exercida: $this->funcao_exercida,
            tipo_vinculo: $this->tipo_vinculo,
            permite_lancar_faltas_componente: $this->permite_lancar_faltas_componente,
            turno_id: $this->turma_turno_id,
            unidades_curriculares: $this->unidades_curriculares,
            outras_unidades_curriculares_obrigatorias: $this->outras_unidades_curriculares_obrigatorias
        );

        if (!$this->validaCamposCenso()) {
            return false;
        }

        if (!$this->validaVinculoEscola()) {
            return false;
        }

        $id = $professorTurma->existe2();
        if ($id) {
            $link = "<a href=\"educar_servidor_vinculo_turma_det.php?id=$id\"><b>Acesse aqui</b></a>";
            $this->mensagem = "Já existe um vínculo para o(a) professor(a) nesta turma na escola e ano letivo selecionado. $link";

            return false;
        }

        $editou = $professorTurma->edita();

        if ($editou) {
            $professorTurma->gravaComponentes(professor_turma_id: $this->id, componentes: $this->componentecurricular);
            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: $backUrl);
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        if (empty($this->id)) {
            $this->simpleRedirect(url: url(path: '/intranet/educar_servidor_vinculo_turma_lst.php'));
        }

        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        $professorTurma = new clsModulesProfessorTurma(id: $this->id);
        $professorTurma->excluiComponentes(professor_turma_id: $this->id);
        $professorTurma->excluir();

        $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect(url: $backUrl);
    }

    private function validaCamposCenso()
    {
        if (!$this->validarCamposObrigatoriosCenso()) {
            return true;
        }

        return $this->validaFuncaoExercida();
    }

    public function validaVinculoEscola()
    {
        $instituicao = LegacyInstitution::find(id: $this->ref_cod_instituicao);

        if (!$instituicao->bloquear_vinculo_professor_sem_alocacao_escola) {
            return true;
        }

        /** @var Employee $servidor */
        $servidor = Employee::findOrFail(id: $this->servidor_id);

        $vinculoEscola = $servidor->schools()
            ->where(column: 'ref_cod_escola', operator: $this->ref_cod_escola)
            ->withPivotValue(column: 'ano', value: $this->ano)
            ->exists();

        if ($vinculoEscola) {
            return true;
        }

        $this->mensagem = 'Não é possível cadastrar o vínculo pois o servidor não está alocado na escola selecionada.';

        return false;
    }

    private function validaFuncaoExercida()
    {
        $obj_turma = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
        $turma = $obj_turma->detalhe();

        if (empty($turma)) {
            return true;
        }

        $funcoesEad = [
            FuncaoExercida::DOCENTE_TITULAR_EAD,
            FuncaoExercida::DOCENTE_TUTOR_EAD,
        ];

        $etapas_instrutor_educacao_pŕofissional = [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68];

        if ($this->funcao_exercida == FuncaoExercida::INSTRUTOR_EDUCACAO_PROFISSIONAL && (($turma['estrutura_curricular'] && !in_array(needle: '2', haystack: transformStringFromDBInArray(string: $turma['estrutura_curricular']), strict: true)) || !in_array(needle: $turma['etapa_educacenso'], haystack: $etapas_instrutor_educacao_pŕofissional, strict: true))) {
            $opcoes = \Str::replaceLast(search: ', ', replace: ' ou ', subject: implode(separator: ', ', array: $etapas_instrutor_educacao_pŕofissional));
            $this->mensagem = "O campo: <b>Função exercida</b> pode ser <b>Instrutor da Educação Profissional</b> apenas quando o campo <b>Estrutura Curricular</b> da turma for: <b>Itinerário formativo</b> e o campo <b>Etapa de ensino</b> for uma das opções: {$opcoes}.";

            return false;
        }

        if ($turma['tipo_mediacao_didatico_pedagogico'] == TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA && !in_array(needle: $this->funcao_exercida, haystack: $funcoesEad)) {
            $this->mensagem = 'O campo: <b>Função exercida</b> deve ser <b>Docente titular</b> ou <b>Docente tutor</b>, quando o campo: <b>Tipo de mediação didático-pedagógica</b> da turma for: <b>Educação a Distância</b>.';

            return false;
        }

        if ($turma['tipo_atendimento'] != TipoAtendimentoTurma::ESCOLARIZACAO && $this->funcao_exercida == FuncaoExercida::AUXILIAR_EDUCACIONAL) {
            $this->mensagem = 'O campo: <b>Função exercida</b> não pode ser: <b>Auxiliar/Assistente Educacional</b> quando o tipo de atendimento da turma for: <b>' . TipoAtendimentoTurma::getDescriptiveValues()[$turma['tipo_atendimento']] . '</b>';

            return false;
        }

        if ($turma['tipo_atendimento'] != TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR && $this->funcao_exercida == FuncaoExercida::MONITOR_ATIVIDADE_COMPLEMENTAR) {
            $this->mensagem = 'O campo: <b>Função exercida</b> não pode ser: <b> Profissional/Monitor de Atividade Complementar </b> quando o tipo de atendimento da turma for: <b>' . TipoAtendimentoTurma::getDescriptiveValues()[$turma['tipo_atendimento']] . '</b>';

            return false;
        }

        return true;
    }

    /**
     * Verifica se existe lançamento no iDiario
     *
     * @param int $professorId
     * @param int $turmaId
     * @return bool
     */
    private function existeLancamentoIDiario($professorId, $turmaId)
    {
        try {
            /** @var iDiarioService $iDiarioService */
            $iDiarioService = app(abstract: iDiarioService::class);
        } catch (RuntimeException) {
            return false;
        }

        if ($iDiarioService->getTeacherClassroomsActivity(teacherId: $professorId, classroomId: $turmaId)) {
            return true;
        }

        return false;
    }

    private function transformArrayInString($value): ?string
    {
        return is_array(value: $value) ? implode(separator: ',', array: $value) : null;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor vínculo turma';
        $this->processoAp = 635;
    }
};
