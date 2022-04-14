<?php

return new class extends clsListagem {
    public function Gerar()
    {
        $this->titulo = 'Conexões';

        $this->addCabecalhos([ 'Data Hora', 'Local do Acesso', 'Ip Interno', 'Pessoa']);

        // Paginador
        $limite = 30;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $sql = 'SELECT b.data_hora, b.ip_externo, b.ip_interno, n.nome FROM acesso b, cadastro.pessoa n WHERE b.cod_pessoa=n.idpes ';

        if (!empty($_GET['status'])) {
            if ($_GET['status'] == 'P') {
                $where .= ' AND ip_externo = \'200.215.80.163\'';
            } elseif ($_GET['status'] == 'X') {
                $where .= ' AND ip_externo <> \'200.215.80.163\'';
            }
        }
        if (!empty($_GET['data_inicial'])) {
            $data = explode('/', $_GET['data_inicial']);
            $where .= " AND data_hora >= '{$data[2]}-{$data[1]}-{$data[0]}'";
        }

        if (!empty($_GET['data_final'])) {
            $data = explode('/', $_GET['data_final']);
            $where .= " AND data_hora <= '{$data[2]}-{$data[1]}-{$data[0]}'";
        }

        if (!empty($_GET['ip_pesquisa'])) {
            $where .= " AND ( (ip_interno like ('{$_GET['ip_pesquisa']}')) OR (ip_externo like ('{$_GET['ip_pesquisa']}')) )";
        }

        if (!empty($_GET['pessoa_nome'])) {
            $nome_pessoa = str_replace(' ', '%', $_GET['pessoa_nome']);
            $where .= " AND n.nome LIKE ('%{$nome_pessoa}%')";
        }

        $db = new clsBanco();
        $total = $db->UnicoCampo("SELECT count(*) FROM acesso b, cadastro.pessoa n WHERE b.cod_pessoa=n.idpes $where");

        $sql .= " $where ORDER BY b.data_hora DESC LIMIT $iniciolimit, 30";
        //  die($sql);
        $db->Consulta($sql);
        while ($db->ProximoRegistro()) {
            list($data_hora, $ip_externo, $ip_interno, $nm_pessoa) = $db->Tupla();

            $local = $ip_externo == '200.215.80.163' ? 'Prefeitura' : 'Externo - '.$ip_externo;
            $ip_interno = $ip_interno=='NULL' ? '&nbsp' : $ip_interno;

            $this->addLinhas(["<img src='imagens/noticia.jpg' border=0>$data_hora", $local, $ip_interno, $nm_pessoa ]);
        }

        $opcoes[''] = 'Escolha uma opção...';
        $opcoes['P'] = 'Prefeitura';
        $opcoes['X'] = 'Externo';

        $this->campoLista('status', 'Status', $opcoes, $_GET['status']);

        $this->campoData('data_inicial', 'Data Inicial', $_GET['data_inicial']);
        $this->campoData('data_final', 'Data Final', $_GET['data_final']);

        $this->campoTexto('ip_pesquisa', 'IP', $_GET['ip_pesquisa'], 30, 30);
        $this->campoTexto('pessoa_nome', 'Funcionário', $_GET['pessoa_nome'], 30, 150);

        $this->addPaginador2('conexoes_todos_lst.php', $total, $_GET, $this->nome, $limite);

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Conexões!';
        $this->processoAp = '158';
    }
};
