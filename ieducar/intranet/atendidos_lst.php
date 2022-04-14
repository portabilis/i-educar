<?php

return new class extends clsListagem {
    public function Gerar()
    {
        $this->titulo = 'Pessoas Físicas';

        $this->addCabecalhos(['Nome', 'CPF']);
        $this->campoTexto('nm_pessoa', 'Nome', $this->getQueryString('nm_pessoa'), '50', '255');
        $this->campoCpf('id_federal', 'CPF', $this->getQueryString('id_federal'));

        $par_nome = $this->getQueryString('nm_pessoa') ?: false;
        $par_id_federal = idFederal2Int($this->getQueryString('id_federal')) ?: false;

        $objPessoa = new clsPessoaFisica();

        // Paginador
        $limite = 10;
        $iniciolimit = ($this->getQueryString("pagina_{$this->nome}")) ? $this->getQueryString("pagina_{$this->nome}")*$limite-$limite: 0;

        $pessoas = $objPessoa->lista($par_nome, $par_id_federal, $iniciolimit, $limite);
        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $cod = $pessoa['idpes'];
                $nome = $pessoa['nome'];

                if ($pessoa['nome_social']) {
                    $nome = $pessoa['nome_social'] . '<br> <i>Nome de registro: </i>' . $pessoa['nome'];
                }

                $total = $pessoa['total'];
                $cpf = $pessoa['cpf'] ? int2CPF($pessoa['cpf']) : '';
                $this->addLinhas(["<img src='imagens/noticia.jpg' border=0><a href='atendidos_det.php?cod_pessoa={$cod}'>$nome</a>", $cpf ]);
            }
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(43, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("atendidos_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->addPaginador2('atendidos_lst.php', $total, $_GET, $this->nome, $limite);

        $this->breadcrumb('Listagem de pessoa física', ['educar_pessoas_index.php' => 'Pessoas']);
    }

    public function Formular()
    {
        $this->title = 'Pessoas Físicas';
        $this->processoAp = '43';
    }
};
