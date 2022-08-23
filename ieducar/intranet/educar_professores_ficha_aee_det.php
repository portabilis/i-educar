<?php

return new class extends clsDetalhe {
    public $titulo;
    public $id;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;
    public $ref_cod_infra_predio_comodo;
    public $nm_turma;
    public $sgl_turma;
    public $max_aluno;
    public $multiseriada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_turma_tipo;
    public $hora_inicial;
    public $hora_final;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;
    public $ref_cod_instituicao;
    public $ref_cod_curso;
    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $ref_cod_instituicao_regente;
    public $ref_cod_regente;

    public function Gerar()
    {
        $this->titulo = 'Ficha AEE - Detalhe';
        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesFichaAee($this->id);
        $registro = $tmp_obj->detalhe();

        if (!$registro['detalhes']) {
            $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
        }

        $obj = new clsPmieducarTurma($registro['detalhes']['ref_cod_turma']);
        $resultado = $obj->getGrau();

        if ($registro['detalhes']['id']) {
            $this->addDetalhe(
                [
                    'ID',
                    $registro['detalhes']['id']
                ]
            );
        }

        if ($registro['detalhes']['data']) {
            $this->addDetalhe(
                [
                    'Data',
                    dataToBrasil($registro['detalhes']['data'])
                ]
            );
        }

        if ($registro['detalhes']['ref_cod_matricula']) {
            $this->addDetalhe(
                [
                    'Aluno',
                   $registro['detalhes']['aluno']
                ]
            );
        }
      
        if ($registro['detalhes']['necessidades_aprendizagem']) {
            $this->addDetalhe(
                [
                    'Necessidades de Aprendizagem',
                    $registro['detalhes']['necessidades_aprendizagem']
                ]
            );
        }

        if ($registro['detalhes']['caracterizacao_pedagogica']) {
            $this->addDetalhe(
                [
                    'Caracterização Pedagógica',
                    $registro['detalhes']['caracterizacao_pedagogica']
                ]
            );
        }

        if ($registro['detalhes']['escola']) {
            $this->addDetalhe(
                [
                    'Escola',
                    $registro['detalhes']['escola']
                ]
            );
        }

        if ($registro['detalhes']['ref_cod_turma']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['detalhes']['turma']
                ]
            );
        }

        if ($registro['detalhes']['professor']) {
            $this->addDetalhe(
                [
                    'Professor',
                    $registro['detalhes']['professor']
                ]
            );
        }         

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_professores_ficha_aee_cad.php';
        
                $this->url_editar = 'educar_professores_ficha_aee_cad.php?id=' . $registro['detalhes']['id'];

        }

        $this->url_cancelar = 'educar_professores_ficha_aee_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe Ficha Aluno AEE', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);

        // $this->addBotao('Excluir', "");
    }

    

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaExclusaoTemp.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular()
    {
        $this->title = 'Ficha AEE - Detalhe';
        $this->processoAp = 58;
    }
};
