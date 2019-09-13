<?php

use Illuminate\Support\Facades\Session;

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndex extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "0";
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
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
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $login;
    var $nm_cliente;
    var $ref_cod_biblioteca;

    function Gerar()
    {
        foreach ($_GET as $campo => $valor)
        {
            $this->$campo = $valor;
        }
        Session::put([
            'campo1' => $_GET["campo1"] ?? Session::get('campo1'),
            'campo2' => $_GET["campo2"] ?? Session::get('campo2'),
        ]);
        Session::save();
        Session::start();

        $this->ref_cod_biblioteca = $this->ref_cod_biblioteca ? $this->ref_cod_biblioteca : $_GET['ref_cod_biblioteca'];

        $this->titulo = "Cliente - Listagem";

        $this->addCabecalhos( array(
            "Código",
            "Cliente"
        ) );

        $this->campoTexto( "nm_cliente", "Cliente", $this->nm_cliente, 30, 255, false );
        $this->campoNumero( "codigo", "Código", $this->codigo, 9, 9 );
        $this->campoOculto("ref_cod_biblioteca",$this->ref_cod_biblioteca);

        if (isset($_GET["ref_cod_biblioteca"]))
            $this->ref_cod_biblioteca = $_GET["ref_cod_biblioteca"];

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acervo = new clsPmieducarCliente();
        $obj_acervo->setOrderby( "nome ASC" );
        $obj_acervo->setLimite( $this->limite, $this->offset );
        
        if ($this->ref_cod_biblioteca)
        {
            $lista = $obj_acervo->listaPesquisaCliente(
                $this->codigo,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->nm_cliente,
                $this->ref_cod_biblioteca
            );
        }
        else
        {
            $lista = $obj_acervo->lista(
                $this->codigo,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->nm_cliente
            );
        }

        $total = $obj_acervo->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $campo1 = Session::get('campo1');
                $campo2 = Session::get('campo2');
                if ( is_string( $campo1 ) && is_string( $campo2 ) )
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_cliente']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_cliente']}'); fecha();\"";
                else if ( is_string( $campo1 ) )
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_cliente']}', '{$registro['nome']}'); fecha();\"";
                $this->addLinhas( array(
                    "<a href=\"javascript:void(0);\" {$script}>{$registro["cod_cliente"]}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro["nome"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_pesquisa_cliente_lst.php", $total, $_GET, $this->nome, $this->limite );
        $this->largura = "100%";
    }
}
// cria uma extensao da classe base
//$pagina = new clsIndexBase();
$pagina = new clsIndex();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>

function addVal1( campo, valor, opcao )
{
    if ( window.parent.document.getElementById( campo ).type == "select-one" )
    {
        obj                     = window.parent.document.getElementById( campo );
        novoIndice              = obj.options.length;
        obj.options[novoIndice] = new Option( opcao );
        opcao                   = obj.options[novoIndice];
        opcao.value             = valor;
        opcao.selected          = true;
        obj.onchange();
    }
    else if ( window.parent.document.getElementById( campo ) )
    {
        obj       =  window.parent.document.getElementById( campo );
        obj.value = valor;
    }
}

function fecha()
{
    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
}
</script>
