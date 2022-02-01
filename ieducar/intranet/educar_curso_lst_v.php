<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $__pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $__offset;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $ref_cod_tipo_avaliacao;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $frequencia_minima;
    public $media;
    public $media_exame;
    public $falta_ch_globalizada;
    public $carga_horaria;
    public $ato_poder_publico;
    public $edicao_final;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;
    public $hora_falta;

    public function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = 'Curso - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Curso',
            'Nivel Ensino',
            'Tipo Ensino',
            'Instituicão'
        ]);

        $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, false);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarNivelEnsino();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
            }
        }

        $this->campoLista('ref_cod_nivel_ensino', 'Nivel Ensino', $opcoes, $this->ref_cod_nivel_ensino);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTipoEnsino();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_ensino']}";
            }
        }

        $this->campoLista('ref_cod_tipo_ensino', 'Tipo Ensino', $opcoes, $this->ref_cod_tipo_ensino);

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby('nm_curso ASC');
        $obj_curso->setLimite($this->__limite, $this->__offset);

        $lista = $obj_curso->lista(
            null,
            null,
            $this->ref_cod_nivel_ensino,
            $this->ref_cod_tipo_ensino,
            null,
            $this->nm_curso,
            null,
            null,
            null,
            null,
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
            null,
            null,
            null,
            null
        );

        $total = $obj_curso->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br'] = date('d/m/Y H:i', $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr($registro['data_exclusao'], 0, 16));
                $registro['data_exclusao_br'] = date('d/m/Y H:i', $registro['data_exclusao_time']);

                $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
                $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
                $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

                $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
                $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
                $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                $this->addLinhas([
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['nm_curso']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_nivel_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_tipo_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_instituicao']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_curso_lst.php', $total, $_GET, $this->nome, $this->__limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(0, $this->pessoa_logada, 0)) {
            $this->acao = 'go("educar_curso_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '0';
    }
};
