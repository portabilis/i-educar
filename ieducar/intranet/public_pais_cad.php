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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Pais" );
        $this->processoAp = "753";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $idpais;
    var $nome;
    var $geom;
    var $cod_ibge;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->idpais=$_GET["idpais"];


        if( is_numeric( $this->idpais ) )
        {
            $obj = new clsPublicPais( $this->idpais );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


//              $this->fexcluir = true;

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "public_pais_det.php?idpais={$registro["idpais"]}" : "public_pais_lst.php";
        $this->nome_url_cancelar = "Cancelar";

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_enderecamento_index.php"    => "Endereçamento",
         ""        => "{$nomeMenu} pa&iacute;s"             
    ));
    $this->enviaLocalizacao($localizacao->montar());        
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "idpais", $this->idpais );

        // text
        $this->campoTexto( "nome", "Nome", $this->nome, 30, 60, true );

        $this->inputsHelper()->integer(
            'cod_ibge',
            array(
                'label' => 'Código INEP',
                'required' => false,
                'label_hint' => 'Somente números',
                'max_length' => 12,
                'placeholder' => 'INEP'
            )
        );

        $this->campoNumero( "cod_ibge", "Código INEP", $this->cod_ibge, 30, 8, true );
    }

    function Novo()
    {


        $obj = new clsPublicPais( null, $this->nome, $this->geom, $this->cod_ibge );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $enderecamento = new clsPublicPais($cadastrou);
            $enderecamento->cadastrou = $cadastrou;
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("Endereçamento de País", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPublicPais\nvalores obrigatorios\nis_numeric( $this->idpais ) && is_string( $this->nome )\n-->";
        return false;
    }

    function Editar()
    {


        $enderecamentoDetalhe = new clsPublicPais($this->idpais);
        $enderecamentoDetalhe->cadastrou = $this->idpais;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();
//echo "<pre>";print_r($enderecamentoDetalheAntes); die;
        $obj = new clsPublicPais( $this->idpais, $this->nome, $this->geom, $this->cod_ibge );
        $editou = $obj->edita();
        if( $editou )
        {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("Endereçamento de País", $this->pessoa_logada, $this->idpais);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPublicPais\nvalores obrigatorios\nif( is_numeric( $this->idpais ) )\n-->";
        return false;
    }

    function Excluir()
    {


        $obj = new clsPublicPais( $this->idpais );

        $enderecamento = $obj->detalhe();
        $enderecamentoDetalhe->cadastrou = $this->cadastrou;

        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("Endereçamento de País", $this->pessoa_logada, $this->cadastrou);
            $auditoria->exclusao($enderecamento);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPublicPais\nvalores obrigatorios\nif( is_numeric( $this->idpais ) )\n-->";
        return false;
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
