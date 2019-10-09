<?php


$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/clsAgenda.inc.php");

class clsIndex extends clsBase
{
    
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Agenda - Preferencias" );
        $this->processoAp = "345";
    }
}

class indice extends clsCadastro
{
    var $cod_agenda, 
        $ref_ref_cod_pessoa_exc,
        $ref_ref_cod_pessoa_cad,
        $nm_agenda,
        $publica,
        $envia_alerta,
        $data_cad,
        $data_edicao, 
        $ref_ref_cod_pessoa_own,
        $dono,
        $editar,
        $agenda_display,
        $pessoa_logada;

    function Inicializar()
    {
        $retorno = "Editar";
        $this->url_cancelar = "agenda.php";
        $this->nome_url_cancelar = "Voltar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "Editar prefer&ecirc;ncias da agenda"
    ));
    $this->enviaLocalizacao($localizacao->montar());        

        return $retorno;
    }

    function Gerar()
    {
        $db = new clsBanco();
        $db2 = new clsBanco();
        
        $objAgenda = new clsAgenda( $this->pessoa_logada, $this->pessoa_logada );
        $this->cod_agenda = $objAgenda->getCodAgenda();
        $this->envia_alerta = $objAgenda->getEnviaAlerta();
        $this->nm_agenda = $objAgenda->getNome();
        
        $this->campoOculto( "cod_agenda", $this->cod_agenda );
        $this->campoLista( "envia_alerta", "Envia Alerta", array( "N&atilde;o", "Sim" ), $this->envia_alerta );
        
        $db->Consulta( "SELECT ref_cod_agenda FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}' AND principal = 1" );
        if( $db->ProximoRegistro() )
        {
            list( $this->agenda_display ) = $db->Tupla();
        }
        else 
        {
            $this->agenda_display = $this->cod_agenda;
        }
        
        $agendas = array();
        $agendas[$this->cod_agenda] = "Minha agenda: {$this->nm_agenda}";
        $db->Consulta( "SELECT ref_cod_agenda, principal FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}'" );
        while ( $db->ProximoRegistro() )
        {
            list( $cod_agenda, $principal ) = $db->Tupla();
            $agendas[$cod_agenda] = $db2->CampoUnico( "SELECT nm_agenda FROM agenda WHERE cod_agenda = '{$cod_agenda}'" );
            if( $principal )
            {
                $this->agenda_display = $cod_agenda;
            }
        }
        $this->campoLista( "agenda_display", "Agenda exibida na pagina principal", $agendas, $this->agenda_display );
    }

    function Novo() 
    {
        return false;
    }

    function Editar() 
    {
        $db = new clsBanco();
        
        $objAgenda = new clsAgenda( $this->pessoa_logada, $this->pessoa_logada );
        $this->cod_agenda = $objAgenda->getCodAgenda();
        
        $set = "";
        $db = new clsBanco();
        
        if( is_numeric( $this->envia_alerta ) )
        {
            $set .= ", envia_alerta = '{$this->envia_alerta}'";
        }
        
        if( is_numeric( $this->agenda_display ) )
        {
            $db->Consulta( "UPDATE agenda_responsavel SET principal = 0 WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}'" );
            $db->Consulta( "UPDATE agenda_responsavel SET principal = 1 WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}' AND ref_cod_agenda = '{$this->agenda_display}'" );
        }

        $db->Consulta( "UPDATE portal.agenda SET ref_ref_cod_pessoa_exc = '{$this->pessoa_logada}', data_edicao = NOW() $set WHERE cod_agenda = '{$this->cod_agenda}'" );
        $this->simpleRedirect('agenda.php');
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>
