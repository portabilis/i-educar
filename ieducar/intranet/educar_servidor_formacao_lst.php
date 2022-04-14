<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_formacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_servidor;
    public $nm_formacao;
    public $tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $this->titulo = 'Servidor Formacao - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
      'Nome Formação',
      'Tipo'
    ]);

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

        // Filtros
        $this->campoTexto(
            'nm_formacao',
            'Nome da Formação',
            $this->nm_formacao,
            30,
            255,
            false
        );

        $opcoes = [
      ''  => 'Selecione',
      'C' => 'Cursos',
      'T' => 'Títulos',
      'O' => 'Concursos'
    ];

        $this->campoLista('tipo', 'Tipo de Formação', $opcoes, $this->tipo);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_servidor_formacao = new clsPmieducarServidorFormacao();
        $obj_servidor_formacao->setOrderby('nm_formacao ASC');
        $obj_servidor_formacao->setLimite($this->limite, $this->offset);

        if (! isset($this->tipo)) {
            $this->tipo = null;
        }

        $lista = $obj_servidor_formacao->lista(
            null,
            null,
            null,
            $this->ref_cod_servidor,
            $this->nm_formacao,
            $this->tipo,
            null,
            null,
            null,
            1
        );

        $total = $obj_servidor_formacao->_total;

        // UrlHelper
        $url  = CoreExt_View_Helper_UrlHelper::getInstance();
        $path = 'educar_servidor_formacao_det.php';

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // Pega detalhes de foreign_keys
                $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();

                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $obj_ref_cod_servidor = new clsPmieducarServidor($registro['ref_cod_servidor']);
                $det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();

                $registro['ref_cod_servidor'] = $det_ref_cod_servidor['cod_servidor'];

                if ($registro['tipo'] == 'C') {
                    $registro['tipo'] = 'Curso';
                } elseif ($registro['tipo'] == 'T') {
                    $registro['tipo'] = 'Título';
                } else {
                    $registro['tipo'] = 'Concurso';
                }

                $options = [
          'query' => [
            'cod_formacao' => $registro['cod_formacao']
        ]];

                $this->addLinhas([
          $url->l($registro['nm_formacao'], $path, $options),
          $url->l($registro['tipo'], $path, $options)
        ]);

                $this->tipo = '';
            }
        }

        $this->addPaginador2('educar_servidor_formacao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->array_botao[]     = 'Novo';
            $this->array_botao_url[] = sprintf(
                'educar_servidor_formacao_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            );
        }

        $this->array_botao[]     = 'Voltar';
        $this->array_botao_url[] = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Servidor Formação';
        $this->processoAp = 635;
    }
};
