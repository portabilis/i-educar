<?php

return new class extends clsListagem {
    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_escola;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $ref_cod_escola_rede_ensino;
    public $ref_idpes;
    public $sigla;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_escola;

    public function Gerar()
    {
        $this->titulo = 'Escola - Listagem';

        $obj_permissoes = new clsPermissoes();

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $cabecalhos = ['Escola'];
        $nivel = $this->user()->getLevel();

        if ($nivel == 1) {
            $cabecalhos[] = 'Instituição';
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = ['' => 'Selecione'];
            $objInstituicao->setOrderby('nm_instituicao ASC');
            $lista = $objInstituicao->lista();
            if (is_array($lista)) {
                foreach ($lista as $linha) {
                    $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                }
            }
            $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        } else {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
            if ($this->ref_cod_instituicao) {
                $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
            } else {
                die('Erro: Usuário não é do nivel poli-institucional e não possui uma instituição');
            }
        }

        $this->addCabecalhos($cabecalhos);

        $this->campoTexto('nm_escola', 'Escola', $this->nm_escola, 30, 255, false);

        // Filtros de Foreign Keys
        $this->limite = 10;
        $obj_escola = new clsPmieducarEscola();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_escola->codUsuario = $this->pessoa_logada;
        }

        if ($this->pagina_formulario) {
            $obj_escola->setLimite($this->limite, ($this->pagina_formulario - 1) * $this->limite);
        } else {
            $obj_escola->setLimite($this->limite);
        }

        $obj_escola->setOrderby('nome');
        $lista = $obj_escola->lista(
            null,
            null,
            null,
            $this->ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->nm_escola,
            null,
            $this->pessoa_logada
        );

        $total = $obj_escola->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $linha = [];

                $linha[] = "<a href=\"educar_escola_det.php?cod_escola={$registro['cod_escola']}\">{$registro['nome']}</a>";
                if ($nivel == 1) {
                    $objInstituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                    $detInstituicao = $objInstituicao->detalhe();

                    $linha[] = "<a href=\"educar_escola_det.php?cod_escola={$registro['cod_escola']}\">{$detInstituicao['nm_instituicao']}</a>";
                }
                $this->addLinhas($linha);
            }
        }

        $this->addPaginador2('educar_escola_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_escola_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de escolas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Escola';
        $this->processoAp = 561;
    }
};
