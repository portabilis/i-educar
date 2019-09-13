<?php


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Calendario Anotacao" );
        $this->processoAp = "620";
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
    
    var $cod_calendario_anotacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_anotacao;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    
    var $dia;
    var $mes;
    var $ano;
    var $ref_cod_calendario_ano_letivo;
    
    function Gerar()
    {
        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;
                    
        $this->titulo = "Calendario Anotacao - Detalhe";
        

        $this->cod_calendario_anotacao=$_GET["cod_calendario_anotacao"];

        $tmp_obj = new clsPmieducarCalendarioAnotacao( $this->cod_calendario_anotacao );
        $registro = $tmp_obj->detalhe();
        
        if( ! $registro )
        {
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_ano_letivo_lst.php')
            );
        }
        
        if( $registro["cod_calendario_anotacao"] )
        {
            $this->addDetalhe( array( "Calendario Anotac&atilde;o", "{$registro["cod_calendario_anotacao"]}") );
        }
        if( $registro["nm_anotacao"] )
        {
            $this->addDetalhe( array( "Nome Anotac&atilde;o", "{$registro["nm_anotacao"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descric&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7 ) )
        {
        $this->url_novo = "educar_calendario_anotacao_cad.php";
        $this->url_editar = "educar_calendario_anotacao_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}&cod_calendario_anotacao={$registro["cod_calendario_anotacao"]}";
        }

        $this->url_cancelar = "educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}";
        $this->largura = "100%";
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
