<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $__offset;

    public $idBackup;
    public $caminho;
    public $data_backup;

    public function Gerar()
    {
        $this->__titulo = 'Backups';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Download',
            'Data backup'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoData('data_backup', 'Data backup', $this->data_backup, false);

        // Paginador
        $this->__limite = 10;
        $this->__offset = ($_GET["pagina_{$this->data_backup}"]) ? $_GET["pagina_{$this->data_backup}"]*$this->__limite-$this->__limite: 0;

        $objBackup = new clsPmieducarBackup();

        $objBackup->setOrderby('data_backup DESC');
        $objBackup->setLimite($this->__limite, $this->__offset);

        $lista = $objBackup->lista(null, null, Portabilis_Date_Utils::brToPgSQL($this->data_backup));

        $total = $objBackup->_total;

        // monta a lista
        $baseDownloadUrl = route('backup.download');
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $dataBackup = Portabilis_Date_Utils::pgSQLToBr($registro['data_backup']);

                $url = $baseDownloadUrl . '?url=' . urlencode($registro['caminho']);

                $this->addLinhas([
                    "<a href=\"$url\">{$registro['caminho']}</a>",
                    "<a href=\"$url\">{$dataBackup}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_backup_lst.php', $total, $_GET, $this->data_backup, $this->__limite);

        $this->largura = '100%';

        $this->breadcrumb('Backups', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Backups';
        $this->processoAp = '9998858';
    }
};
