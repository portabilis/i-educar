<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Reservas" );
        $this->processoAp = "609";
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_reserva;
    var $ref_usuario_libera;
    var $ref_usuario_cad;
    var $ref_cod_cliente;
    var $data_reserva;
    var $data_prevista_disponivel;
    var $data_retirada;
    var $ref_cod_exemplar;

    function Gerar()
    {
        $this->titulo = "Reservas - Detalhe";


        $this->cod_reserva=$_GET["cod_reserva"];

        $tmp_obj = new clsPmieducarReservas( $this->cod_reserva );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_reservas_lst.php');
        }

            $obj_ref_cod_exemplar = new clsPmieducarExemplar( $registro["ref_cod_exemplar"] );
            $det_ref_cod_exemplar = $obj_ref_cod_exemplar->detalhe();

                $acervo = $det_ref_cod_exemplar["ref_cod_acervo"];
                $obj_acervo = new clsPmieducarAcervo($acervo);
                $det_acervo = $obj_acervo->detalhe();
                $titulo_exemplar = $det_acervo["titulo"];

            $obj_cliente = new clsPmieducarCliente( $registro["ref_cod_cliente"] );
            $det_cliente = $obj_cliente->detalhe();
            $ref_idpes = $det_cliente["ref_idpes"];
            $obj_pessoa = new clsPessoa_($ref_idpes);
            $det_pessoa = $obj_pessoa->detalhe();
            $registro["ref_cod_cliente"] = $det_pessoa["nome"];

        if( $registro["ref_cod_cliente"] )
        {
            $this->addDetalhe( array( "Cliente", "{$registro["ref_cod_cliente"]}") );
        }
        if( $titulo_exemplar )
        {
            $this->addDetalhe( array( "Obra", "{$titulo_exemplar}") );
        }
        if( $registro["ref_cod_exemplar"] )
        {
            $this->addDetalhe( array( "Tombo", "{$registro["ref_cod_exemplar"]}") );
        }
        if( $registro["data_reserva"] )
        {
            $this->addDetalhe( array( "Data Reserva", dataFromPgToBr( $registro["data_reserva"], "d/m/Y" ) ) );
        }
        if( $registro["data_prevista_disponivel"] )
        {
            $this->addDetalhe( array( "Data Prevista Dispon&iacute;vel", dataFromPgToBr( $registro["data_prevista_disponivel"], "d/m/Y" ) ) );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 609, $this->pessoa_logada, 11 ) )
        {
            $this->url_novo = "educar_reservas_login_cad.php";
        }

        $this->url_cancelar = "educar_reservas_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da reserva', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
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
