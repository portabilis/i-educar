<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $id;
    public $ano;
    public $servidor_id;
    public $funcao_exercida;
    public $tipo_vinculo;

    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $ref_cod_turma;

    public function Gerar()
    {
        $this->servidor_id    = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $this->titulo = 'Servidor Vínculo Turma - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
      'Ano',
      'Escola',
      'Curso',
      'Série',
      'Turma',
      'Função exercida',
      'Tipo de vínculo'
    ]);

        $this->campoOculto('ref_cod_servidor', $this->servidor_id);

        $this->inputsHelper()->dynamic(['ano', 'instituicao','escola','curso','serie', 'turma'], ['required' => false]);

        $resources_funcao = SelectOptions::funcoesExercidaServidor();
        $options = ['label' =>'Função exercida', 'resources' => $resources_funcao, 'value' => $this->funcao_exercida];
        $this->inputsHelper()->select('funcao_exercida', $options);

        $resources_tipo = SelectOptions::tiposVinculoServidor();
        $options = ['label' => 'Tipo do vínculo', 'resources' => $resources_tipo, 'value' => $this->tipo_vinculo];
        $this->inputsHelper()->select('tipo_vinculo', $options);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_vinculo = new clsModulesProfessorTurma();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_vinculo->codUsuario = $this->pessoa_logada;
        }

        $obj_vinculo->setOrderby(' nm_escola, nm_curso, nm_serie, nm_turma ASC');
        $obj_vinculo->setLimite($this->limite, $this->offset);

        if (! isset($this->tipo)) {
            $this->tipo = null;
        }

        $lista = $obj_vinculo->lista(
            $this->servidor_id,
            $this->ref_cod_instituicao,
            $this->ano,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie,
            $this->ref_cod_turma,
            $this->funcao_exercida,
            $this->tipo_vinculo
        );

        $total = $obj_vinculo->_total;

        // UrlHelper
        $url  = CoreExt_View_Helper_UrlHelper::getInstance();
        $path = 'educar_servidor_vinculo_turma_det.php';

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $options = [
          'query' => [
            'id' => $registro['id']
        ]];

                $this->addLinhas([
          $url->l($registro['ano'], $path, $options),
          $url->l($registro['nm_escola'], $path, $options),
          $url->l($registro['nm_curso'], $path, $options),
          $url->l($registro['nm_serie'], $path, $options),
          $url->l($registro['nm_turma'], $path, $options),
          $url->l($resources_funcao[$registro['funcao_exercida']], $path, $options),
          $url->l($resources_tipo[$registro['tipo_vinculo']], $path, $options)
        ]);
            }
        }

        $this->addPaginador2('educar_servidor_vinculo_turma_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
            $this->array_botao_url[] = sprintf(
                'educar_servidor_vinculo_turma_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->servidor_id,
                $this->ref_cod_instituicao
            );
        }

        $this->array_botao[]     = 'Voltar';
        $this->array_botao_url[] = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $this->largura = '100%';

        $this->breadcrumb('Registro de vínculos do professor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Vínculo Turma';
        $this->processoAp = 635;
    }
};
