<?php

use App\Models\Backup;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $__titulo;

    public $__limite;

    public $__offset;

    public $idBackup;

    public $caminho;

    public $data_backup;

    public function Gerar()
    {
        $this->__titulo = 'Backups';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Download',
            'Data backup',
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoData(nome: 'data_backup', campo: 'Data backup', valor: $this->data_backup, obrigatorio: false);

        // Paginador
        $this->__limite = 10;

        $query = Backup::query()
            ->orderBy('data_backup', 'DESC');

        if ($this->data_backup) {
            $query->where('data_backup', Portabilis_Date_Utils::brToPgSQL($this->data_backup));
        }

        $result = $query->paginate(perPage: $this->__limite, pageName: 'pagina_'.$this->data_backup);
        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        $baseDownloadUrl = route(name: 'backup.download');
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $dataBackup = Portabilis_Date_Utils::pgSQLToBr(timestamp: $registro['data_backup']);

                $url = $baseDownloadUrl . '?url=' . urlencode(string: $registro['caminho']);

                $this->addLinhas(linha: [
                    "<a href=\"$url\">{$registro['caminho']}</a>",
                    "<a href=\"$url\">{$dataBackup}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_backup_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->data_backup, intResultadosPorPagina: $this->__limite);

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Backups', breadcrumbs: [
            url(path: 'intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Backups';
        $this->processoAp = '9998858';
    }
};
