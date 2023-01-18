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
        $this->ref_cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $this->titulo = 'Servidor Formacao - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Nome Formação',
            'Tipo'
        ]);

        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
        $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);

        // Filtros
        $this->campoTexto(
            nome: 'nm_formacao',
            campo: 'Nome da Formação',
            valor: $this->nm_formacao,
            tamanhovisivel: 30,
            tamanhomaximo: 255
        );

        $opcoes = [
            '' => 'Selecione',
            'C' => 'Cursos',
            'T' => 'Títulos',
            'O' => 'Concursos'
        ];

        $this->campoLista(nome: 'tipo', campo: 'Tipo de Formação', valor: $opcoes, default: $this->tipo);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_servidor_formacao = new clsPmieducarServidorFormacao();
        $obj_servidor_formacao->setOrderby(strNomeCampo: 'nm_formacao ASC');
        $obj_servidor_formacao->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        if (!isset($this->tipo)) {
            $this->tipo = null;
        }

        $lista = $obj_servidor_formacao->lista(
            int_ref_cod_servidor: $this->ref_cod_servidor,
            str_nm_formacao: $this->nm_formacao,
            str_tipo: $this->tipo,
            date_data_exclusao_ini: 1
        );

        $total = $obj_servidor_formacao->_total;

        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $path = 'educar_servidor_formacao_det.php';

        // Monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // Pega detalhes de foreign_keys
                $obj_ref_usuario_exc = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();

                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $obj_ref_cod_servidor = new clsPmieducarServidor(cod_servidor: $registro['ref_cod_servidor']);
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
                    ]
                ];

                $this->addLinhas(linha: [
                    $url->l(text: $registro['nm_formacao'], path: $path, options: $options),
                    $url->l(text: $registro['tipo'], path: $path, options: $options)
                ]);

                $this->tipo = '';
            }
        }

        $this->addPaginador2(strUrl: 'educar_servidor_formacao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao[] = 'Novo';
            $this->array_botao_url[] = sprintf(
                'educar_servidor_formacao_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            );
        }

        $this->array_botao[] = 'Voltar';
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
