<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

//class clsIndexBase extends clsBase
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
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		$_SESSION["campo1"] = $_GET["campo1"] ? $_GET["campo1"] : $_SESSION["campo1"];
		$_SESSION["campo2"] = $_GET["campo2"] ? $_GET["campo2"] : $_SESSION["campo2"];
		$this->ref_cod_biblioteca = $this->ref_cod_biblioteca ? $this->ref_cod_biblioteca : $_GET['ref_cod_biblioteca'];
		session_write_close();

		$this->titulo = "Cliente - Listagem";

		/*foreach( $_SESSION AS $var => $val ) // passa todos os valores obtidos no SESSION para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;
		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;*/

		$this->addCabecalhos( array(
			"Login",
			"Cliente"
		) );

		$this->campoTexto( "nm_cliente", "Cliente", $this->nm_cliente, 30, 255, false );
		$this->campoNumero( "login", "Login", $this->login, 9, 9 );
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
				null,
				null,
				null,
				null,
				$this->login,
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
				null,
				null,
				null,
				null,
				$this->login,
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
				if ( is_string( $_SESSION['campo1'] ) && is_string( $_SESSION['campo2'] ) )
					$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_cliente']}', '{$registro['nome']}'); addVal1('{$_SESSION['campo2']}','{$registro['nome']}', '{$registro['cod_cliente']}'); fecha();\"";
				else if ( is_string( $_SESSION['campo1'] ) )
					$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_cliente']}', '{$registro['nome']}'); fecha();\"";
				$this->addLinhas( array(
					"<a href=\"javascript:void(0);\" {$script}>{$registro["login"]}</a>",
					"<a href=\"javascript:void(0);\" {$script}>{$registro["nome"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_pesquisa_cliente_lst.php", $total, $_GET, $this->nome, $this->limite );
		$this->largura = "100%";
	}
}
// cria uma extensao da classe base
//$pagina = new clsBase();

$pagina->SetTitulo( "{$pagina->_instituicao} i-Educar - Cliente" );
$pagina->processoAp = "0";
$pagina->renderMenu = false;
$pagina->renderMenuSuspenso = false;
	$pagina = new clsBase();
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
		obj						= window.parent.document.getElementById( campo );
		novoIndice              = obj.options.length;
		obj.options[novoIndice] = new Option( opcao );
		opcao                   = obj.options[novoIndice];
		opcao.value				= valor;
		opcao.selected			= true;
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