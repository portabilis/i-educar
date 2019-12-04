<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolStage;
use App\Services\PromotionService;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
require_once 'Avaliacao/Model/FaltaComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'modules/Avaliacao/Views/PromocaoApiController.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Dispensa Componente Curricular');
        $this->processoAp = 578;
    }
}

class indice extends clsCadastro
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

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_disciplina = $this->getQueryString('ref_cod_disciplina');
        $this->ref_cod_matricula  = $this->getQueryString('ref_cod_matricula');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula);

        if (is_numeric($this->ref_cod_matricula)) {
            $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, null, null, null, null, null, null, 1);
            $det_matricula = $obj_matricula->detalhe();
            $this->redirectIf(!$det_matricula, 'educar_matricula_lst.php');
            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie  = $det_matricula['ref_ref_cod_serie'];
        } else {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)) {
            $obj = new clsPmieducarDispensaDisciplina(
                $this->ref_cod_matricula,
                $this->ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_disciplina
            );

            $registro  = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->cod_dispensa = $registro['cod_dispensa'];

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
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

        $this->breadcrumb('Dispensa de componentes curriculares',['educar_index.php' => 'Escola']);
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
        $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
        $this->ano = $detalhe_aluno['ano'];

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'], null, null, null, null, null, null, null, null, null, 1));

        $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1);

        $det_escola = $obj_escola->detalhe();
        $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, 1, $this->ref_cod_serie, null, $this->ref_cod_escola );

        if (is_array($lst_matricula_turma)) {
            $det = array_shift($lst_matricula_turma);
            $this->ref_cod_turma  = $det['ref_cod_turma'];
            $this->ref_sequencial = $det['sequencial'];
        }

        $this->campoRotulo('nm_aluno', 'Nome do Aluno', $det_aluno['nome_aluno']);

        if (!isset($this->ref_cod_turma)) {
            $this->mensagem = 'Para dispensar um aluno de um componente curricular, é necessário que este esteja enturmado.';

            return;
        }

        // primary keys
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
        $this->campoOculto('cod_dispensa', $this->cod_dispensa);

        $this->campoOculto('ano', $this->ano);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

        $opcoes = ['' => 'Selecione'];

        // Seleciona os componentes curriculares da turma
        try {
            $componentes = App_Model_IedFinder::getComponentesTurma(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_turma
        );
        } catch (App_Model_Exception $e) {
            $this->mensagem = $e->getMessage();

            return;
        }

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        if ($this->modoEdicao) {
            $this->campoRotulo('nm_disciplina', 'Disciplina', $opcoes[$this->ref_cod_disciplina]);
            $this->campoOculto('ref_cod_disciplina', $this->ref_cod_disciplina);
            $this->campoOculto('modo_edicao', $this->modoEdicao);
        } else {
            $this->inputsHelper()->multipleSearchComponenteCurricular(null, ['label' => 'Componentes lecionados', 'required' => true], ['searchForArea' => true]);
        }

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPmieducarTipoDispensa();

        if ($this->ref_cod_instituicao) {
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao);
        } else {
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1);
        }

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['cod_tipo_dispensa']] = $registro['nm_tipo'];
            }
        }

        $this->campoLista(
            'ref_cod_tipo_dispensa',
            'Tipo Dispensa',
            $opcoes,
            $this->ref_cod_tipo_dispensa
        );
        $this->montaEtapas();
        $this->campoMemo('observacao', 'Observação', $this->observacao, 60, 10, false);
    }

    public function existeComponenteSerie($serieId, $escolaId, $disciplinaId)
    {
        try {
            App_Model_IedFinder::getEscolaSerieDisciplina(
                $serieId,
                $escolaId,
                null,
                $disciplinaId
            );
        } catch (Exception $e) {
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

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        $disciplinasNaoExistentesNaSerieDaEscola = [];

        foreach ($this->componentecurricular as $disciplinaId) {
            $this->ref_cod_disciplina = $disciplinaId;
            $dadosDaDispensa = $this->obtemDadosDaDispensa();
            $objetoDispensa = $this->montaObjetoDispensa($dadosDaDispensa);

            if (!$this->existeComponenteSerie($dadosDaDispensa['ref_cod_serie'], $dadosDaDispensa['ref_cod_escola'], $disciplinaId)) {
                $disciplinasNaoExistentesNaSerieDaEscola[] = $this->nomeDisciplina($disciplinaId);
                continue;
            }

            if ($objetoDispensa->existe()) {
                $dadosDaDispensa['cod_dispensa'] = $objetoDispensa->detalhe()['cod_dispensa'];
                $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
                $excluiDispensaEtapa = $objDispensaEtapa->excluirTodos($dadosDaDispensa['cod_dispensa']);
                $objetoDispensa->edita();
                $this->cadastraEtapasDaDispensa($dadosDaDispensa);
                continue;
            }

            $codigoDispensa = $objetoDispensa->cadastra();
            if (!$codigoDispensa) {
                $this->mensagem = 'Cadastro não realizado.<br />';

                return false;
            }
            $dadosDaDispensa['cod_dispensa'] = $codigoDispensa;
            $this->cadastraEtapasDaDispensa($dadosDaDispensa);
        }

        if (count($disciplinasNaoExistentesNaSerieDaEscola) > 0) {
            $disciplinas = implode(", ", $disciplinasNaoExistentesNaSerieDaEscola);
            $this->mensagem = "O(s) componente(s):<b>{$disciplinas}</b>. não está(ão) habilitado(s) na série da escola.";
            return false;
        }

        $this->rodaPromocao();
        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $dadosDaDispensa = $this->obtemDadosDaDispensa();
        $objetoDispensa = $this->montaObjetoDispensa($dadosDaDispensa);

        $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
        $excluiDispensaEtapa = $objDispensaEtapa->excluirTodos($dadosDaDispensa['cod_dispensa']);

        $this->cadastraEtapasDaDispensa($dadosDaDispensa);

        $editou = $objetoDispensa->edita();
        if ($editou) {
            $this->rodaPromocao();
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $dadosDaDispensa = $this->obtemDadosDaDispensa();
        $objetoDispensa = $this->montaObjetoDispensa($dadosDaDispensa);

        $objDispensaEtapa    = new clsPmieducarDispensaDisciplinaEtapa();
        $excluiDispensaEtapa = $objDispensaEtapa->excluirTodos($this->cod_dispensa);
        $excluiu = $objetoDispensa->excluir();

        if ($excluiu) {
            $this->rodaPromocao();
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function maiorEtapaUtilizada($registration)
    {
        $where = [
            'ref_ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_ano' => $registration->ano,
        ];

        $totalEtapas['total'] = LegacySchoolStage::query()->where($where)->count();
        $arrayEtapas = [];

        for ($i = 1; $i <= $totalEtapas['total']; $i++)
        {
            $arrayEtapas[$i] = strval($i);
        }

        $arrayEtapas = array_diff($arrayEtapas, $this->etapa);
        return max($arrayEtapas);
    }

    public function rodaPromocao()
    {
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $_GET['etapa'] = $this->maiorEtapaUtilizada($registration);
        $promocao = new PromotionService($registration->lastEnrollment()->first());
        $promocao->fakeRequest();
    }

    public function montaEtapas()
    {
        //Pega matricula para pegar curso, escola e ano
        $objMatricula        = new clsPmieducarMatricula();
        $dadosMatricula      = $objMatricula->lista($this->ref_cod_matricula);
        //Pega curso para pegar padrao ano escolar
        $objCurso            = new clsPmieducarCurso();
        $dadosCurso          = $objCurso->lista($dadosMatricula[0]['ref_cod_curso']);
        $padraoAnoEscolar    = $dadosCurso[0]['padrao_ano_escolar'];
        //Pega escola e ano para pegar as etapas em ano letivo modulo
        $escolaId            = $dadosMatricula[0]['ref_ref_cod_escola'];
        $ano                 = $dadosMatricula[0]['ano'];
        //Pega dados da enturmação atual
        $objMatriculaTurma   = new clsPmieducarMatriculaTurma();
        $seqMatriculaTurma   = $objMatriculaTurma->getUltimaEnturmacao($this->ref_cod_matricula);
        $dadosMatriculaTurma = $objMatriculaTurma->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $seqMatriculaTurma);
        //Pega etapas definidas na escola
        $objAnoLetivoMod     = new clsPmieducarAnoLetivoModulo();
        $dadosAnoLetivoMod   = $objAnoLetivoMod->lista($ano, $escolaId);
        //Pega etapas definida na turma
        $objTurmaModulo      = new clsPmieducarTurmaModulo();
        $dadosTurmaModulo    = $objTurmaModulo->lista($dadosMatriculaTurma[0]['ref_cod_turma']);
        //Define de onde as etapas serão pegas
        if ($padraoAnoEscolar == 1) {
            $dadosEtapa = $dadosAnoLetivoMod;
        } else {
            $dadosEtapa = $dadosTurmaModulo;
        }
        //Pega nome do modulo
        $objModulo           = new clsPmieducarModulo();
        $dadosModulo         = $objModulo->lista($dadosEtapa[0]['ref_cod_modulo']);
        $nomeModulo          = $dadosModulo[0]['nm_tipo'];

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
                              name=\"etapa[". $modulo['sequencial']  .']"
                              id="etapa_'. $modulo['sequencial']  . '"
                              value="'. $modulo['sequencial'] . '">' . $modulo['sequencial'] . 'º ' . $nomeModulo . '
                        </label>';
            $conteudoHtml .= '</div>';
        }

        $etapas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $etapas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudoHtml);
        $etapas .= '</table>';

        $this->campoRotulo(
            'etapas_',
            'Etapas',
            "<div id='etapas'>$etapas</div>"
        );
    }

    public function obtemDadosDaDispensa()
    {
        $dadosDaDispensa = [
            'cod_dispensa' => $this->cod_dispensa,
            'ref_cod_matricula' => $this->ref_cod_matricula,
            'ref_cod_serie' => $this->ref_cod_serie,
            'ref_cod_escola' => $this->ref_cod_escola,
            'ref_cod_disciplina' => $this->ref_cod_disciplina,
            'ref_usuario_exc' => $this->pessoa_logada,
            'ref_usuario_cad' => $this->pessoa_logada,
            'ref_cod_tipo_dispensa' => $this->ref_cod_tipo_dispensa,
            'observacao' => $this->observacao,
            'etapas' => $this->etapa
        ];

        return $dadosDaDispensa;
    }

    public function montaObjetoDispensa($dadosDaDispensa = [])
    {
        $objetoDispensa = new clsPmieducarDispensaDisciplina(
            $dadosDaDispensa['ref_cod_matricula'],
            $dadosDaDispensa['ref_cod_serie'],
            $dadosDaDispensa['ref_cod_escola'],
            $dadosDaDispensa['ref_cod_disciplina'],
            $dadosDaDispensa['ref_usuario_exc'],
            $dadosDaDispensa['ref_usuario_cad'],
            $dadosDaDispensa['ref_cod_tipo_dispensa'],
            null,
            null,
            1,
            $dadosDaDispensa['observacao']
        );

        return $objetoDispensa;
    }

    public function cadastraEtapasDaDispensa($dadosDaDispensa)
    {
        foreach ($dadosDaDispensa['etapas'] as $etapa) {
            $this->removeNotasDaDisciplinaNaEtapa(
                $dadosDaDispensa['ref_cod_matricula'],
                $dadosDaDispensa['ref_cod_disciplina'],
                $etapa
            );
            $this->removeFaltasDaDisciplinaNaEtapa(
                $dadosDaDispensa['ref_cod_matricula'],
                $dadosDaDispensa['ref_cod_disciplina'],
                $etapa
            );
            $objetoEtapaDaDispensa = new clsPmieducarDispensaDisciplinaEtapa($dadosDaDispensa['cod_dispensa'], $etapa);
            $cadastra = $objetoEtapaDaDispensa->cadastra();
        }
    }

    public function removeNotasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        $notaAlunoMapper = new Avaliacao_Model_NotaAlunoDataMapper();
        $notaAluno = $notaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        $notaAluno = $notaAluno[0]->id;
        if (empty($notaAluno)) {
            return false;
        }
        $notaComponenteCurricularMapper = new Avaliacao_Model_NotaComponenteDataMapper();
        $notaComponenteCurricular = $notaComponenteCurricularMapper->findAll([], [
            'nota_aluno_id' => $notaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa
        ]);
        if (empty($notaComponenteCurricular)) {
            return false;
        }
        $notaComponenteCurricularMapper->delete($notaComponenteCurricular[0]);

        $auditoria = new clsModulesAuditoriaGeral('nota_removida_dispensa', $this->pessoa_logada);
        $auditoria->exclusao([
            'nota_aluno_id' => $notaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa,
        ]);

        return true;
    }

    public function removeFaltasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        $faltaAlunoMapper = new Avaliacao_Model_FaltaAlunoDataMapper();
        $faltaAluno = $faltaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        $faltaAluno = $faltaAluno[0]->id;
        if (empty($faltaAluno)) {
            return false;
        }
        $faltaComponenteCurricularMapper = new Avaliacao_Model_FaltaComponenteDataMapper();
        $faltaComponenteCurricular = $faltaComponenteCurricularMapper->findAll([], [
            'falta_aluno_id' => $faltaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa
        ]);
        if (empty($faltaComponenteCurricular)) {
            return false;
        }
        $faltaComponenteCurricularMapper->delete($faltaComponenteCurricular[0]);

        $auditoria = new clsModulesAuditoriaGeral('falta_removida_dispensa', $this->pessoa_logada);
        $auditoria->exclusao([
            'falta_aluno_id' => $faltaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa,
        ]);

        return true;
    }

    private function nomeDisciplina($disciplinaId){
        $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
        $componenteCurricular = $mapper->find($disciplinaId)->nome;

        return $componenteCurricular;
    }

    public function loadAssets()
    {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/ModalDispensasDisciplinaCad.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
