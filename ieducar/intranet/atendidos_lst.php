<?php

return new class extends clsListagem {
    public function Gerar()
    {
        $this->titulo = 'Pessoas Físicas';

        $this->addCabecalhos(coluna: ['Nome', 'CPF']);
        $this->campoTexto(nome: 'nm_pessoa', campo: 'Nome', valor: $this->getQueryString(name: 'nm_pessoa'), tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoCpf(nome: 'id_federal', campo: 'CPF', valor: $this->getQueryString(name: 'id_federal'));

        $par_nome = $this->getQueryString(name: 'nm_pessoa') ?: false;
        $par_id_federal = idFederal2Int(str: $this->getQueryString(name: 'id_federal')) ?: false;

        $objPessoa = new clsPessoaFisica();

        // Paginador
        $limite = 10;
        $iniciolimit = ($this->getQueryString(name: "pagina_{$this->nome}")) ? $this->getQueryString(name: "pagina_{$this->nome}")*$limite-$limite: 0;

        $pessoas = $objPessoa->lista(str_nome: $par_nome, numeric_cpf: $par_id_federal, inicio_limite: $iniciolimit, qtd_registros: $limite);
        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $cod = $pessoa['idpes'];
                $nome = $pessoa['nome'];

                if ($pessoa['nome_social']) {
                    $nome = $pessoa['nome_social'] . '<br> <i>Nome de registro: </i>' . $pessoa['nome'];
                }

                $total = $pessoa['total'];
                $cpf = $pessoa['cpf'] ? int2CPF(int: $pessoa['cpf']) : '';
                $this->addLinhas(linha: ["<img src='imagens/noticia.jpg' border=0><a href='atendidos_det.php?cod_pessoa={$cod}'>$nome</a>", $cpf ]);
            }
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 43, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->acao = 'go("atendidos_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->addPaginador2(strUrl: 'atendidos_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        $this->breadcrumb(currentPage: 'Listagem de pessoa física', breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']);
    }

    public function Formular()
    {
        $this->title = 'Pessoas Físicas';
        $this->processoAp = '43';
    }
};
