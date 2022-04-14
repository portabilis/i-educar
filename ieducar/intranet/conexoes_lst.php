<?php

return new class extends clsListagem {
    public function Gerar()
    {
        $this->titulo = 'Conexões';

        $this->addCabecalhos([ 'Data Hora', 'Local do Acesso']);

        // Paginador
        $limite = 20;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $id_pessoa = \Illuminate\Support\Facades\Auth::id();

        $sql = "SELECT b.data_hora, b.ip_externo FROM acesso b WHERE cod_pessoa={$id_pessoa}";
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

        $db = new clsBanco();
        $total = $db->UnicoCampo("SELECT count(*) FROM acesso WHERE cod_pessoa={$id_pessoa} $where");

        $sql .= " $where ORDER BY b.data_hora DESC LIMIT $iniciolimit, $limite";

        $db->Consulta($sql);
        while ($db->ProximoRegistro()) {
            list($data_hora, $ip_externo) = $db->Tupla();

            $local = $ip_externo == '200.215.80.163' ? 'Prefeitura' : 'Externo';

            $this->addLinhas(["<img src='imagens/noticia.jpg' border=0>$data_hora", $local ]);
        }

        /*$this->acao = "go(\"bairros_cad.php\")";
        $this->nome_acao = "Novo";*/

        $opcoes[''] = 'Escolha uma opção...';
        $opcoes['P'] = 'Prefeitura';
        $opcoes['X'] = 'Externo';

        $this->campoLista('status', 'Status', $opcoes, $_GET['status']);

        $this->campoData('data_inicial', 'Data Inicial', $_GET['data_inicial']);
        $this->campoData('data_final', 'Data Final', $_GET['data_final']);

        $this->addPaginador2('conexoes_lst.php', $total, $_GET, $this->nome, $limite);

        $this->largura = '100%';

        $this->breadcrumb('Listagem de conexões realizadas');
    }

    public function Formular()
    {
        $this->title = 'Conexões!';
        $this->processoAp = '157';
    }
};
