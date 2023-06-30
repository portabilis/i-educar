<?php

return new class extends clsListagem
{
    public $cd_agenda;

    public $nm_agenda;

    public function Gerar()
    {
        $this->titulo = 'Agendas Admin';

        $this->addCabecalhos(coluna: ['Agenda']);

        $this->campoTexto(nome: 'pesquisa', campo: 'Agenda', valor: '', tamanhovisivel: 50, tamanhomaximo: 255);

        $where = '';

        if (!empty($_GET['pesquisa'])) {
            $pesquisa = str_replace(search: ' ', replace: '%', subject: $_GET['pesquisa']);
            $where = "WHERE nm_agenda ILIKE '%{$pesquisa}%'";
            $pesquisa = str_replace(search: '%', replace: ' ', subject: $_GET['pesquisa']);
        }

        $db = new clsBanco();
        $total = $db->UnicoCampo(consulta: "SELECT COUNT(0) FROM portal.agenda {$where}");

        // Paginador
        $limite = 15;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

        $sql = "SELECT cod_agenda, nm_agenda, ref_ref_cod_pessoa_own FROM portal.agenda {$where} ORDER BY nm_agenda ASC LIMIT $limite OFFSET $iniciolimit";

        $db2 = new clsBanco();
        $db2->Consulta(consulta: $sql);
        while ($db2->ProximoRegistro()) {
            [$cod_agenda, $nm_agenda, $cod_pessoa_own] = $db2->Tupla();
            $this->addLinhas(linha: ["<a href='agenda_admin_det.php?cod_agenda={$cod_agenda}'><img src='imagens/noticia.jpg' border=0>$nm_agenda</a>"]);
        }

        // Paginador
        $this->addPaginador2(strUrl: 'agenda_admin_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        $this->acao = 'go("agenda_admin_cad.php")';
        $this->nome_acao = 'Novo';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de agendas');
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '343';
    }
};
