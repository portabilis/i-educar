<?php

return new class extends clsListagem {
    public function Gerar()
    {
        $this->titulo = 'Vínculos';

        $nome_ = $_GET['nome_'] ?? null;

        $this->addCabecalhos(['Nome']);
        $this->campoTexto('nome_', 'Nome', $nome_, '50', '255');

        $db = new clsBanco();
        $sql  = 'SELECT cod_funcionario_vinculo, nm_vinculo FROM portal.funcionario_vinculo';
        $where = '';
        $where_and = '';

        if (!empty($nome_)) {
            $name = $db->escapeString($nome_);
            $where .= $where_and." nm_vinculo LIKE '%{$name}%' ";
        }

        if ($where) {
            $where = " WHERE $where";
        }

        $sql .= $where.' ORDER BY nm_vinculo';

        $db->Consulta("SELECT count(*) FROM portal.funcionario_vinculo $where");
        $db->ProximoRegistro();

        list($total) = $db->Tupla();

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
        $sql .= " LIMIT $iniciolimit,$limite";
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            list($cod_func_vinculo, $nome) = $db->Tupla();
            $this->addLinhas([ "<img src='imagens/noticia.jpg' border=0> <a href='funcionario_vinculo_det.php?cod_func=$cod_func_vinculo'>$nome</a>"]);
        }

        $this->largura = '100%';

        // Paginador
        $this->addPaginador2('funcionario_vinculo_lst.php', $total, $_GET, $this->nome, $limite);
        $this->acao = 'go("funcionario_vinculo_cad.php")';
        $this->nome_acao = 'Novo';

        $this->breadcrumb('Listagem de vínculos');
    }

    public function Formular()
    {
        $this->title = 'Vínculo Funcionários!';
        $this->processoAp = '190';
    }
};
