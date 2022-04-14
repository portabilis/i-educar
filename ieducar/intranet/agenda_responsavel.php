<?php

return new class extends clsListagem {
    public $cd_agenda;
    public $nm_agenda;

    public function Gerar()
    {
        $this->pessoa = $this->pessoa_logada;

        $this->titulo = 'Agendas que eu posso editar';

        $this->addCabecalhos([ 'Agenda' ]);

        $this->campoTexto('pesquisa', 'Agenda', '', 50, 255);

        $db = new clsBanco();

        $and = '';

        if (!empty($_GET['pesquisa'])) {
            $pesquisa = str_replace(' ', '%', $_GET['pesquisa']);
            $and = "AND nm_agenda ilike ('%{$pesquisa}%')";
            $pesquisa = str_replace('%', ' ', $_GET['pesquisa']);
        }

        $db = new clsBanco();
        $total = $db->UnicoCampo("SELECT COUNT(0) + (SELECT COUNT(*) FROM portal.agenda_responsavel WHERE ref_ref_cod_pessoa_fj = {$this->pessoa} ) FROM portal.agenda WHERE ref_ref_cod_pessoa_own = {$this->pessoa} {$and} ");

        // Paginador
        $limite = 15;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $sql = "SELECT cod_agenda, 1 AS minha FROM agenda WHERE ref_ref_cod_pessoa_own = {$this->pessoa} {$and} UNION SELECT ref_cod_agenda, 0 AS minha FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = {$this->pessoa} ORDER BY minha DESC";

        $db1 = new clsBanco();
        $db1->Consulta($sql);
        while ($db1->ProximoRegistro()) {
            list($cd_agenda, $propriedade) = $db1->Tupla();

            $db2 = new clsBanco();
            $db2->Consulta("SELECT nm_agenda, ref_ref_cod_pessoa_own FROM agenda WHERE cod_agenda = {$cd_agenda} {$and}");
            while ($db2->ProximoRegistro()) {
                list($nm_agenda, $cod_pessoa_own) = $db2->Tupla();
                $this->addLinhas([
                "<a href='agenda.php?cod_agenda={$cd_agenda}'><img src='imagens/noticia.jpg' border=0>$nm_agenda</a>"]);
            }
        }

        // Paginador
        $this->addPaginador2('agenda_responsavel.php', $total, $_GET, $this->nome, $limite);

        $this->largura = '100%';

        $this->breadcrumb('Agendas');
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '341';
    }
};
