<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once 'Portabilis/Date/Utils.php';

use App\Services\BackupUrlPresigner;

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Backups" );
        $this->processoAp = "9998858";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $__offset;

    var $idBackup;
    var $caminho;
    var $data_backup;

    function Gerar()
    {
        $this->__titulo = "Backups";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Download",
            "Data backup"
        ) );

        // Filtros de Foreign Keys


        // outros Filtros
        $this->campoData( "data_backup", "Data backup", $this->data_backup, false);


        // Paginador
        $this->__limite = 10;
        $this->__offset = ( $_GET["pagina_{$this->data_backup}"] ) ? $_GET["pagina_{$this->data_backup}"]*$this->__limite-$this->__limite: 0;

        $objBackup = new clsPmieducarBackup();

        $objBackup->setOrderby( "data_backup DESC" );
        $objBackup->setLimite( $this->__limite, $this->__offset );

        $lista = $objBackup->lista(null, null, Portabilis_Date_Utils::brToPgSQL($this->data_backup));

        $total = $objBackup->_total;

        // monta a lista
        $baseDownloadUrl = route('backup.download');
        if(is_array($lista) && count($lista))
        {
            foreach ($lista AS $registro)
            {
                $dataBackup = Portabilis_Date_Utils::pgSQLToBr($registro['data_backup']);

                $url = $baseDownloadUrl . '?url=' . urlencode($registro['caminho']);

                $this->addLinhas( array(
                    "<a href=\"$url\">{$registro["caminho"]}</a>",
                    "<a href=\"$url\">{$dataBackup}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_backup_lst.php", $total, $_GET, $this->data_backup, $this->__limite );

        $obj_permissao = new clsPermissoes();

        $this->largura = "100%";

        $this->breadcrumb('Backups', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
