<?php

return new class extends clsListagem {

    public $limite;
    public $offset;
    public $ref_serie_origem;
    public $ref_serie_destino;
    public $ref_curso_origem;
    public $ref_curso_destino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Sequ&ecirc;ncia Enturma&ccedil;&atilde;o - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Curso Origem',
            'S&eacute;rie Origem',
            'Curso Destino',
            'S&eacute;rie Destino'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }
        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        if ($nivel_usuario == 1) {
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = [ '' => 'Selecione' ];
            $objInstituicao->setOrderby('nm_instituicao ASC');
            $lista = $objInstituicao->lista();
            if (is_array($lista)) {
                foreach ($lista as $linha) {
                    $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                }
            }
            $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, '', null, null, null, null, false);
        } else {
            $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
            $obj_usuario_det = $obj_usuario->detalhe();
            $this->ref_cod_instituicao = $obj_usuario_det['ref_cod_instituicao'];
        }

        $opcoes = [ '' => 'Selecione' ];
        $opcoes_ = [ '' => 'Selecione' ];

        // EDITAR
        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarCurso();
            $objTemp->setOrderby('nm_curso');
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_curso']] = $registro['nm_curso'];
                    $opcoes_[$registro['cod_curso']] = $registro['nm_curso'];
                }
            }
        }

        $this->campoLista('ref_curso_origem', 'Curso Origem', $opcoes, $this->ref_curso_origem, '', true, '', '', false, false);
        $this->campoLista('ref_curso_destino', ' Curso Destino', $opcoes_, $this->ref_curso_destino, '', false, '', '', false, false);

        // primary keys

        $opcoes = [ '' => 'Selecione' ];
        $opcoes_ = [ '' => 'Selecione' ];

        if ($this->ref_curso_origem) {
            $objTemp = new clsPmieducarSerie();
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_origem, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_serie']] = $registro['nm_serie'];
                }
            }
        }
        if ($this->ref_curso_destino) {
            $objTemp = new clsPmieducarSerie();
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_destino, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_[$registro['cod_serie']] = $registro['nm_serie'];
                }
            }
        }

        $this->campoLista('ref_serie_origem', 'S&eacute;rie Origem', $opcoes, $this->ref_serie_origem, null, true, '', '', false, false);
        $this->campoLista('ref_serie_destino', ' S&eacute;rie Destino', $opcoes_, $this->ref_serie_destino, '', false, '', '', false, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_sequencia_serie = new clsPmieducarSequenciaSerie();
        $obj_sequencia_serie->setOrderby('data_cadastro ASC');
        $obj_sequencia_serie->setLimite($this->limite, $this->offset);

        $lista = $obj_sequencia_serie->lista(
            $this->ref_serie_origem,
            $this->ref_serie_destino,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_curso_origem,
            $this->ref_curso_destino,
            $this->ref_cod_instituicao
        );

        $total = $obj_sequencia_serie->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_serie_origem = new clsPmieducarSerie($registro['ref_serie_origem']);
                $det_ref_serie_origem = $obj_ref_serie_origem->detalhe();
                $serie_origem = $det_ref_serie_origem['nm_serie'];
                $registro['ref_curso_origem'] = $det_ref_serie_origem['ref_cod_curso'];

                $obj_ref_curso_origem = new clsPmieducarCurso($registro['ref_curso_origem']);
                $det_ref_curso_origem = $obj_ref_curso_origem->detalhe();
                $registro['ref_curso_origem'] = $det_ref_curso_origem['nm_curso'];
                $registro['ref_cod_instituicao'] = $det_ref_curso_origem['ref_cod_instituicao'];

                $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_instituicao['nm_instituicao'];

                $obj_ref_serie_destino = new clsPmieducarSerie($registro['ref_serie_destino']);
                $det_ref_serie_destino = $obj_ref_serie_destino->detalhe();
                $serie_destino = $det_ref_serie_destino['nm_serie'];
                $registro['ref_curso_destino'] = $det_ref_serie_destino['ref_cod_curso'];

                $obj_ref_curso_destino = new clsPmieducarCurso($registro['ref_curso_destino']);
                $det_ref_curso_destino = $obj_ref_curso_destino->detalhe();
                $registro['ref_curso_destino'] = $det_ref_curso_destino['nm_curso'];

                $lista_busca = [
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}\">{$registro['ref_curso_origem']}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}\">{$serie_origem}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}\">{$registro['ref_curso_destino']}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}\">{$serie_destino}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_sequencia_serie_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_sequencia_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de sequências de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-sequencia-serie-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Sequência Enturmação';
        $this->processoAp = 587;
    }
};
