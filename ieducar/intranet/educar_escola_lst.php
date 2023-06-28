<?php

return new class extends clsListagem
{
    public $limite;

    public $offset;

    public $cod_escola;

    public $ref_usuario_cad;

    public $ref_usuario_exc;

    public $ref_cod_instituicao;

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
            $this->campoLista(nome: 'ref_cod_instituicao', campo: 'Instituição', valor: $opcoes, default: $this->ref_cod_instituicao, acao: false, descricao: false, complemento: false, desabilitado: false);
        } else {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
            if ($this->ref_cod_instituicao) {
                $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
            } else {
                exit('Erro: Usuário não é do nivel poli-institucional e não possui uma instituição');
            }
        }

        $this->addCabecalhos($cabecalhos);

        $this->campoTexto(nome: 'nm_escola', campo: 'Escola', valor: $this->nm_escola, tamanhovisivel: 30, tamanhomaximo: 255);

        // Filtros de Foreign Keys
        $this->limite = 10;
        $obj_escola = new clsPmieducarEscola();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_escola->codUsuario = $this->pessoa_logada;
        }

        if ($this->pagina_formulario) {
            $obj_escola->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: ($this->pagina_formulario - 1) * $this->limite);
        } else {
            $obj_escola->setLimite($this->limite);
        }

        $obj_escola->setOrderby('nome');
        $lista = $obj_escola->lista(
            int_ref_cod_instituicao: $this->ref_cod_instituicao,
            int_ativo: 1,
            str_nome: $this->nm_escola,
            cod_usuario: $this->pessoa_logada
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

        $this->addPaginador2(strUrl: 'educar_escola_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_escola_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de escolas', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Escola';
        $this->processoAp = 561;
    }
};
