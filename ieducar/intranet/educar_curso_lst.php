<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $carga_horaria;
    public $ato_poder_publico;
    public $habilitacao;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;

    public function Gerar()
    {
        $this->titulo = 'Curso - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
      'Curso',
      'Nível Ensino',
      'Tipo Ensino'
    ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, false);

        // outros de Foreign Keys
        $opcoes = ['' => 'Selecione'];

        $todos_niveis_ensino = "nivel_ensino = new Array();\n";
        $objTemp = new clsPmieducarNivelEnsino();
        $lista = $objTemp->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $todos_niveis_ensino .= "nivel_ensino[nivel_ensino.length] = new Array({$registro['cod_nivel_ensino']},'{$registro['nm_nivel']}', {$registro['ref_cod_instituicao']});\n";
            }
        }
        echo "<script>{$todos_niveis_ensino}</script>";

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarNivelEnsino();
            $lista = $objTemp->lista(
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
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_nivel_ensino']] = $registro['nm_nivel'];
                }
            }
        }

        $this->campoLista(
            'ref_cod_nivel_ensino',
            'Nível Ensino',
            $opcoes,
            $this->ref_cod_nivel_ensino,
            null,
            null,
            null,
            null,
            null,
            false
        );

        $opcoes = ['' => 'Selecione'];

        $todos_tipos_ensino = "tipo_ensino = new Array();\n";
        $objTemp = new clsPmieducarTipoEnsino();
        $objTemp->setOrderby('nm_tipo');
        $lista = $objTemp->lista(null, null, null, null, null, null, 1);

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $todos_tipos_ensino .= "tipo_ensino[tipo_ensino.length] = new Array({$registro['cod_tipo_ensino']},'{$registro['nm_tipo']}', {$registro['ref_cod_instituicao']});\n";
            }
        }
        echo "<script>{$todos_tipos_ensino}</script>";

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarTipoEnsino();
            $objTemp->setOrderby('nm_tipo');

            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_tipo_ensino']}"] = $registro['nm_tipo'];
                }
            }
        }

        $this->campoLista(
            'ref_cod_tipo_ensino',
            'Tipo Ensino',
            $opcoes,
            $this->ref_cod_tipo_ensino,
            '',
            false,
            '',
            '',
            '',
            false
        );

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby('nm_curso ASC');
        $obj_curso->setLimite($this->limite, $this->offset);

        $lista = $obj_curso->lista(
            null,
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
            null,
            null,
            1,
            null,
            $this->ref_cod_instituicao
        );

        $total = $obj_curso->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
                $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
                $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

                $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
                $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
                $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $nomeCurso = empty($registro['descricao']) ? $registro['nm_curso'] : "{$registro['nm_curso']} ({$registro['descricao']})";

                $lista_busca = [
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$nomeCurso}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_nivel_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_tipo_ensino']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_curso_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(566, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_curso_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de cursos', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ .'/scripts/extra/educar-curso-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }
};
