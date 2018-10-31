<?php

use iEducar\Modules\Servidores\Model\FuncaoExercida;
use iEducar\Modules\Servidores\Model\TipoVinculo;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesProfessorTurma.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Servidores - Servidor vínculo turma');
        $this->processoAp = 635;
    }
}

class indice extends clsCadastro
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

    public function Inicializar()
    {
        $this->id = $this->getQueryString('id');
        $this->servidor_id = $this->getQueryString('ref_cod_servidor');
        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');

        // URL para redirecionamento
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
            $this->fexcluir = true;
        }

        $retorno = 'Novo';

        if (is_numeric($this->id)) {
            $obj = new clsModulesProfessorTurma($this->id);

            $registro = $obj->detalhe();

            if ($registro) {
                $this->ref_cod_turma = $registro['turma_id'];
                $this->funcao_exercida = $registro['funcao_exercida'];
                $this->tipo_vinculo = $registro['tipo_vinculo'];
                $this->permite_lancar_faltas_componente = $registro['permite_lancar_faltas_componente'];
                $this->turma_turno_id = $registro['turno_id'];

                $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
                $obj_turma = $obj_turma->detalhe();
                $this->ref_cod_escola = $obj_turma['ref_ref_cod_escola'];

                $this->ref_cod_curso = $obj_turma['ref_cod_curso'];
                $this->ref_cod_serie = $obj_turma['ref_ref_cod_serie'];

                if (!isset($_GET['copia'])) {
                    $retorno = 'Editar';
                }

                if (isset($_GET['copia'])) {
                    $this->ano = date('Y');
                }
            }
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? 'educar_servidor_vinculo_turma_det.php?id=' . $this->id
            : $backUrl;

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Vínculo do professor à turma', [
            'educar_servidores_index.php' => 'Servidores'
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $ano = null;

        if ($this->id) {
            $objProfessorTurma = new clsModulesProfessorTurma($this->id);
            $detProfessorTurma = $objProfessorTurma->detalhe();
            $ano = $detProfessorTurma['ano'];
        }

        if (isset($_GET['copia'])) {
            $ano = null;
        }

        $this->campoOculto('id', $this->id);
        $this->campoOculto('servidor_id', $this->servidor_id);
        $this->inputsHelper()->dynamic('ano', ['value' => (is_null($ano) ? date('Y') : $ano)]);
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie']);
        $this->inputsHelper()->dynamic(['turma'], ['required' => !is_null($this->ref_cod_turma)]);

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
        $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

        $resources = FuncaoExercida::getDescriptiveValues();
        $resources = array_replace([null => 'Selecione'], $resources);

        $options = [
            'label' => 'Função exercida',
            'resources' => $resources,
            'value' => $this->funcao_exercida
        ];
        $this->inputsHelper()->select('funcao_exercida', $options);

        $resources = TipoVinculo::getDescriptiveValues();
        $resources = array_replace([null => 'Nenhum'], $resources);

        $options = [
            'label' => 'Tipo do vínculo',
            'resources' => $resources,
            'value' => $this->tipo_vinculo,
            'required' => false
        ];
        $this->inputsHelper()->select('tipo_vinculo', $options);

        $this->inputsHelper()->turmaTurno([
            'required' => false
        ]);

        $options = [
            'label' => 'Professor de área específica?',
            'value' => $this->permite_lancar_faltas_componente,
            'help' => 'Marque esta opção somente se o professor leciona uma disciplina específica na turma selecionada.'
        ];

        $this->inputsHelper()->checkbox('permite_lancar_faltas_componente', $options);
        $this->inputsHelper()->checkbox('selecionar_todos', ['label' => 'Selecionar/remover todos']);
        $this->inputsHelper()->multipleSearchComponenteCurricular(null, ['label' => 'Componentes lecionados', 'required' => true], ['searchForArea' => true]);

        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/ServidorVinculoTurma.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Novo()
    {
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        if ($this->ref_cod_turma) {
            $professorTurma = new clsModulesProfessorTurma(null, $this->ano, $this->ref_cod_instituicao, $this->servidor_id, $this->ref_cod_turma, $this->funcao_exercida, $this->tipo_vinculo, $this->permite_lancar_faltas_componente, $this->turma_turno_id);
            if ($professorTurma->existe2()) {
                $this->mensagem .= 'Não é possível cadastrar pois já existe um vínculo com essa turma.<br>';

                return false;
            } else {
                $professorTurmaId = $professorTurma->cadastra();
                $professorTurma->gravaComponentes($professorTurmaId, $this->componentecurricular);
            }
        } else {
            $turmas = new clsPmieducarTurmas();
            foreach ($turmas->lista(null, null, null, $this->ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->ano) as $reg) {
                $professorTurma = new clsModulesProfessorTurma(null, $this->ano, $this->ref_cod_instituicao, $this->servidor_id, $reg['cod_turma'], $this->funcao_exercida, $this->tipo_vinculo, $this->permite_lancar_faltas_componente, $this->turma_turno_id);
                // FIxME entender qual é o objeto correto
                $professorTurmaId = $obj->cadastra();
                // FIXME #parameters
                $professorTurma->gravaComponentes($professorTurmaId, null);
            }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        header('Location: ' . $backUrl);
        die();
    }

    public function Editar()
    {
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        $professorTurma = new clsModulesProfessorTurma(
            $this->id,
            $this->ano,
            $this->ref_cod_instituicao,
            $this->servidor_id,
            $this->ref_cod_turma,
            $this->funcao_exercida,
            $this->tipo_vinculo,
            $this->permite_lancar_faltas_componente,
            $this->turma_turno_id
        );

        if ($professorTurma->existe2()) {
            $this->mensagem .= 'Não é possível cadastrar pois já existe um vínculo com essa turma.<br>';

            return false;
        }

        $editou = $professorTurma->edita();

        if ($editou) {
            $professorTurma->gravaComponentes($this->id, $this->componentecurricular);
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            header('Location: ' . $backUrl);
            die();
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $backUrl = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, $backUrl);

        $professorTurma = new clsModulesProfessorTurma($this->id);
        $professorTurma->excluiComponentes($this->id);
        $professorTurma->excluir();

        $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
        header('Location:' . $backUrl);
        die();
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
