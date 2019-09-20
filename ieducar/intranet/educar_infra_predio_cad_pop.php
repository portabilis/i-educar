<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
        $this->processoAp = "567";
        $this->renderBanner = false;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
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

    var $cod_infra_predio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_escola;
    var $nm_predio;
    var $desc_predio;
    var $endereco;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_infra_predio=$_GET["cod_infra_predio"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 567, $this->pessoa_logada,7, "educar_infra_predio_lst.php" );

//      if( is_numeric( $this->cod_infra_predio ) )
//      {
//
//          $obj = new clsPmieducarInfraPredio( $this->cod_infra_predio );
//          $registro  = $obj->detalhe();
//          if( $registro )
//          {
//              foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
//                  $this->$campo = $val;
//
//
//              //** verificao de permissao para exclusao
//              $this->fexcluir = $obj_permissoes->permissao_excluir(567,$this->pessoa_logada,7);
//              //**
//              $retorno = "Editar";
//          }
//          else
//          {
//              header( "Location: educar_infra_predio_lst.php" );
//              die();
//          }
//      }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}" : "educar_infra_predio_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
//      die();
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_infra_predio", $this->cod_infra_predio );

        if ($_GET['precisa_lista'])
        {
            $obrigatorio = true;
            $get_escola  = true;
            include("include/pmieducar/educar_campo_lista.php");
        }
        else
        {
            $this->campoOculto("ref_cod_escola", $this->ref_cod_escola);
        }
        // text
        $this->campoTexto( "nm_predio", "Nome Prédio", $this->nm_predio, 30, 255, true );
        $this->campoMemo( "desc_predio", "Descrição Prédio", $this->desc_predio, 60, 10, false );
        $this->campoMemo( "endereco", "Endereço", $this->endereco, 60, 2, true );




    }

    function Novo()
    {

//      die($this->ref_cod_escola);
        $obj = new clsPmieducarInfraPredio( $this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1 );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {

            echo "<script>
                        if (parent.document.getElementById('ref_cod_infra_predio').disabled)
                            parent.document.getElementById('ref_cod_infra_predio').options[0] = new Option('Selecione um prédio', '', false, false);
                        parent.document.getElementById('ref_cod_infra_predio').options[parent.document.getElementById('ref_cod_infra_predio').options.length] = new Option('$this->nm_predio', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_infra_predio').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_infra_predio').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
            return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {
    }

    function Excluir()
    {
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
<script>

<?php

if (!$_GET['precisa_lista'])
{
?>
    Event.observe(window, 'load', Init, false);

    function Init()
    {
        $('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
//      alert($F('ref_cod_escola'));
    }

<?php } ?>

</script>
