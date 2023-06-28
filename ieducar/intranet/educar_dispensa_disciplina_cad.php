<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;
use App\Models\LegacyStageType;
use App\Services\Exemption\ExemptionService;

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

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula);

        if (is_numeric($this->ref_cod_matricula)) {
            $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula, ativo: 1);
            $det_matricula = $obj_matricula->detalhe();
            $this->redirectIf(condition: !$det_matricula, url: 'educar_matricula_lst.php');
            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        } else {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)) {
            $obj = new clsPmieducarDispensaDisciplina(
                ref_cod_matricula: $this->ref_cod_matricula,
                ref_cod_serie: $this->ref_cod_serie,
                ref_cod_escola: $this->ref_cod_escola,
                ref_cod_disciplina: $this->ref_cod_disciplina
            );

            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->cod_dispensa = $registro['cod_dispensa'];

                $obj_permissoes = new clsPermissoes();

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

        $this->breadcrumb(currentPage: 'Dispensa de componentes curriculares', breadcrumbs: ['educar_index.php' => 'Escola']);
        $this->modoEdicao = $retorno == 'Editar';
        $this->loadAssets();

        return $retorno;
    }

    public function Gerar()
    {
        /**
         * Busca dados da matricula
         */
        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $detalhe_matricula = $obj_ref_cod_matricula->lista($this->ref_cod_matricula);

        if (is_array($detalhe_matricula)) {
            $detalhe_matricula = array_shift($detalhe_matricula);

            $this->ano = $detalhe_matricula['ano'];

            $obj_aluno = new clsPmieducarAluno();
            $det_aluno = $obj_aluno->lista(int_cod_aluno: $detalhe_matricula['ref_cod_aluno'], int_ativo: 1);
            $det_aluno = array_shift($det_aluno);
        }

        $obj_escola = new clsPmieducarEscola(cod_escola: $this->ref_cod_escola, bloquear_lancamento_diario_anos_letivos_encerrados: 1);

        $det_escola = $obj_escola->detalhe();
        $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $lst_matricula_turma = $obj_matricula_turma->lista(int_ref_cod_matricula: $this->ref_cod_matricula, int_ativo: 1, int_ref_cod_serie: $this->ref_cod_serie, int_ref_cod_escola: $this->ref_cod_escola);

        if (is_array($lst_matricula_turma)) {
            $det = array_shift($lst_matricula_turma);
            $this->ref_cod_turma = $det['ref_cod_turma'];
            $this->ref_sequencial = $det['sequencial'];
        }

        $this->campoRotulo(nome: 'nm_aluno', campo: 'Nome do Aluno', valor: $det_aluno['nome_aluno']);

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
            $this->mensagem = $e->getMessage();

            return;
        }

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        if ($this->modoEdicao) {
            $this->campoRotulo(nome: 'nm_disciplina', campo: 'Disciplina', valor: $opcoes[$this->ref_cod_disciplina]);
            $this->campoOculto(nome: 'ref_cod_disciplina', valor: $this->ref_cod_disciplina);
            $this->campoOculto(nome: 'modo_edicao', valor: $this->modoEdicao);
        } else {
            $this->inputsHelper()->multipleSearchComponenteCurricular(attrName: null, inputOptions: ['label' => 'Componentes lecionados', 'required' => true], helperOptions: ['searchForArea' => true]);
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
    }

    public function Novo()
    {
        if (empty($this->etapa)) {
            $this->mensagem = 'É necessário informar pelo menos uma etapa.';

            return false;
        }

        $exemptionService = new ExemptionService($this->user());

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        $disciplinasNaoExistentesNaSerieDaEscola = [];

        $registration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
        $exemptionService->createExemptionByDisciplineArray(registration: $registration, disciplineArray: $this->componentecurricular, exemptionTypeId: $this->ref_cod_tipo_dispensa, description: $this->observacao, stages: $this->etapa);

        if (is_array($exemptionService->disciplinasNaoExistentesNaSerieDaEscola) && count($exemptionService->disciplinasNaoExistentesNaSerieDaEscola) > 0) {
            $disciplinas = implode(separator: ', ', array: $disciplinasNaoExistentesNaSerieDaEscola);
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

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $dadosDaDispensa = $this->obtemDadosDaDispensa();
        $objetoDispensa = $this->montaObjetoDispensa($dadosDaDispensa);

        $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
        $objDispensaEtapa->excluirTodos($dadosDaDispensa['cod_dispensa']);

        $exemptionService = new ExemptionService($this->user());
        $exemption = LegacyDisciplineExemption::findOrFail($objetoDispensa->detalhe()['cod_dispensa']);
        $exemptionService->cadastraEtapasDaDispensa(exemption: $exemption, stages: $this->etapa);

        $editou = $objetoDispensa->edita();
        if ($editou) {
            $registration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
            $exemptionService->runsPromotion(registration: $registration, stages: $this->etapa);
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $dadosDaDispensa = $this->obtemDadosDaDispensa();
        $objetoDispensa = $this->montaObjetoDispensa($dadosDaDispensa);

        $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
        $objDispensaEtapa->excluirTodos($this->cod_dispensa);
        $excluiu = $objetoDispensa->excluir();

        if ($excluiu) {
            $exemptionService = new ExemptionService($this->user());
            $registration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
            $exemptionService->runsPromotion(registration: $registration, stages: $this->etapa);
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function montaEtapas()
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
        $dadosMatriculaTurma = $objMatriculaTurma->lista(int_ref_cod_matricula: $this->ref_cod_matricula, int_sequencial: $seqMatriculaTurma);
        //Pega etapas definidas na escola
        $dadosAnoLetivoMod = LegacyAcademicYearStage::query()->whereSchool($escolaId)->whereYearEq($ano)->get();
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
        $nomeModulo = LegacyStageType::find($dadosEtapa[0]['ref_cod_modulo'])->nm_tipo;
        $conteudoHtml = '';

        foreach ($dadosEtapa as $modulo) {
            $checked = '';
            $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa(ref_cod_dispensa: $this->cod_dispensa, etapa: $modulo['sequencial']);
            $verificaSeExiste = $objDispensaEtapa->existe();
            if ($verificaSeExiste) {
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

    public function obtemDadosDaDispensa()
    {
        return [
            'cod_dispensa' => $this->cod_dispensa,
            'ref_cod_matricula' => $this->ref_cod_matricula,
            'ref_cod_serie' => $this->ref_cod_serie,
            'ref_cod_escola' => $this->ref_cod_escola,
            'ref_cod_disciplina' => $this->ref_cod_disciplina,
            'ref_usuario_exc' => $this->pessoa_logada,
            'ref_usuario_cad' => $this->pessoa_logada,
            'ref_cod_tipo_dispensa' => $this->ref_cod_tipo_dispensa,
            'observacao' => $this->observacao,
            'etapas' => $this->etapa,
        ];
    }

    public function montaObjetoDispensa($dadosDaDispensa = [])
    {
        return new clsPmieducarDispensaDisciplina(
            ref_cod_matricula: $dadosDaDispensa['ref_cod_matricula'],
            ref_cod_serie: $dadosDaDispensa['ref_cod_serie'],
            ref_cod_escola: $dadosDaDispensa['ref_cod_escola'],
            ref_cod_disciplina: $dadosDaDispensa['ref_cod_disciplina'],
            ref_usuario_exc: $dadosDaDispensa['ref_usuario_exc'],
            ref_usuario_cad: $dadosDaDispensa['ref_usuario_cad'],
            ref_cod_tipo_dispensa: $dadosDaDispensa['ref_cod_tipo_dispensa'],
            ativo: 1,
            observacao: $dadosDaDispensa['observacao']
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
