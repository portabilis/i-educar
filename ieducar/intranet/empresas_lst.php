<?php

return new class extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Empresas';

        $this->addCabecalhos(coluna: ['Razão Social', 'Nome Fantasia']);

        $this->campoTexto(nome: 'fantasia', campo: 'Nome Fantasia', valor: $_GET['fantasia'], tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoTexto(nome: 'razao_social', campo: 'Razão Social', valor: $_GET['razao_social'], tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoCnpj(nome: 'id_federal', campo: 'CNPJ', valor: $_GET['id_federal']);

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;
        $par_razao = false;
        $par_cnpj = false;
        $opcoes = false;
        if ($_GET['fantasia']) {
            $par_fantasia = $_GET['fantasia'];
        }
        if ($_GET['razao_social']) {
            $par_razao = $_GET['razao_social'];

            $objPessoaFJ = new clsPessoaFj();
            $lista = $objPessoaFJ->lista(str_nome: $par_razao);
            if ($lista) {
                foreach ($lista as $pessoa) {
                    $opcoes[] = $pessoa['idpes'];
                }
            }
        }
        if ($_GET['id_federal']) {
            $par_cnpj = idFederal2Int(str: $_GET['id_federal']);
        }

        $objPessoa = new clsPessoaJuridica();
        $db = new clsBanco();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $objPessoa->codUsuario = $this->pessoa_logada;
        }

        $empresas = $objPessoa->lista(numeric_cnpj: $par_cnpj, str_fantasia: $par_fantasia, inicio_limit: $iniciolimit, fim_limite: $limite, str_ordenacao: 'fantasia asc', arrayint_idisin: $opcoes);
        if ($empresas) {
            foreach ($empresas as $empresa) {
                $total = $empresa['total'];
                $cod_empresa = $empresa['idpes'];
                $razao_social = $db->escapeString(string: $empresa['nome']);
                $nome_fantasia = $db->escapeString(string: $empresa['fantasia']);
                $this->addLinhas(linha: ["<a href='empresas_det.php?cod_empresa={$cod_empresa}'><img src='imagens/noticia.jpg' border=0>$razao_social</a>", "<a href='empresas_det.php?cod_empresa={$cod_empresa}'>{$nome_fantasia}</a>"]);
            }
        }
        // Paginador
        $this->addPaginador2(strUrl: ' empresas_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 41, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->acao = 'go("empresas_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de pessoas jurídicas', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = 41;
    }
};
