<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once 'Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Backups" );
        $this->processoAp = "9998858";
        $this->addEstilo('localizacaoSistema');
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
        if(is_array($lista) && count($lista))
        {
            foreach ($lista AS $registro)
            {
                $dataBackup = Portabilis_Date_Utils::pgSQLToBr($registro['data_backup']);

                $this->addLinhas( array(
                    "<a href=\"{$registro["caminho"]}\">{$registro["caminho"]}</a>",
                    "<a href=\"{$registro["caminho"]}\">{$dataBackup}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_backup_lst.php", $total, $_GET, $this->data_backup, $this->__limite );

        $obj_permissao = new clsPermissoes();   

        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_configuracoes_index.php"    => "Configurações",
         ""                                  => "Backups"
    ));
    $this->enviaLocalizacao($localizacao->montar());        
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
