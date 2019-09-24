<?php

use Illuminate\Support\Facades\Session;


$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Conexões!" );
        $this->processoAp = "157";
    }
}

class indice extends clsListagem
{
    function Gerar()
    {
        $this->titulo = "Conexões";


        $this->addCabecalhos( array( "Data Hora", "Local do Acesso") );

        // Paginador
        $limite = 20;
        $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $id_pessoa = Session::get('id_pessoa');

        $sql = "SELECT b.data_hora, b.ip_externo FROM acesso b WHERE cod_pessoa={$id_pessoa}";
        if (!empty($_GET['status']))
        {
            if ($_GET['status'] == 'P')
                $where .= " AND ip_externo = '200.215.80.163'";
            else if ($_GET['status'] == 'X')
                $where .= " AND ip_externo <> '200.215.80.163'";
        }
        if(!empty($_GET['data_inicial']))
        {
            $data = explode("/", $_GET['data_inicial']);
            $where .= " AND data_hora >= '{$data[2]}-{$data[1]}-{$data[0]}'";
        }

        if(!empty($_GET['data_final']))
        {
            $data = explode("/", $_GET['data_final']);
            $where .= " AND data_hora <= '{$data[2]}-{$data[1]}-{$data[0]}'";
        }

        $db = new clsBanco();
        $total = $db->UnicoCampo("SELECT count(*) FROM acesso WHERE cod_pessoa={$id_pessoa} $where");

        $sql .= " $where ORDER BY b.data_hora DESC LIMIT $iniciolimit, $limite";

        $db->Consulta( $sql );
        while ( $db->ProximoRegistro() )
        {
            list ($data_hora, $ip_externo) = $db->Tupla();

            $local = $ip_externo == '200.215.80.163' ? 'Prefeitura' : 'Externo';

            $this->addLinhas( array("<img src='imagens/noticia.jpg' border=0>$data_hora", $local ) );
        }

        /*$this->acao = "go(\"bairros_cad.php\")";
        $this->nome_acao = "Novo";*/

        $opcoes[""] = "Escolha uma opção...";
        $opcoes["P"] = "Prefeitura";
        $opcoes["X"] = "Externo";

        $this->campoLista( "status", "Status", $opcoes, $_GET['status'] );

        $this->campoData("data_inicial","Data Inicial",$_GET['data_inicial']);
        $this->campoData("data_final","Data Final",$_GET['data_final']);

        $this->addPaginador2( "conexoes_lst.php", $total, $_GET, $this->nome, $limite );

        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "Listagem de conex&otilde;es realizadas"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
