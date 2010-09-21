<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Obra" );
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

	var $ref_cod_biblioteca;
	var $ref_cod_exemplar;
	var $nm_obra;
	var $titulo_obra;
	var $ref_cod_acervo;
	var $ref_acervo_autor;
	var $isbn;

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
			$_SESSION["campo3"] = $_GET["campo3"] ? $_GET["campo3"] : $_SESSION["campo3"];
		session_write_close();

		$this->titulo = "Obra - Listagem";

		$this->addCabecalhos( array(
			"Obra",
			"Autor",
			"ISBN"
		) );

		$this->campoTexto( "titulo_obra", "Obra", $this->nm_obra, 30, 255, false );
		$this->campoTexto( "ref_acervo_autor", "Autor", $this->ref_acervo_autor, 30, 255, false );
		$this->campoNumero( "isbn", "ISBN", $this->isbn, 15, 15, false );
		$this->ref_cod_biblioteca = $_SESSION["campo3"];

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_acervo = new clsPmieducarAcervo();
		$obj_acervo->setOrderby( "titulo ASC" );
		$obj_acervo->setLimite( $this->limite, $this->offset );
		
		$lista = $obj_acervo->lista(
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			$this->titulo_obra,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			$this->isbn,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca,
			null,
			null,
			$this->ref_acervo_autor
		);
		$total = $obj_acervo->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
//				echo $registro["cod_acervo_auto"];die;
				$obj_acervo_autor = new clsPmieducarAcervoAutor($registro["cod_acervo_autor"]);
				$det_acervo_autor = $obj_acervo_autor->detalhe();
				$registro["cod_acervo_autor"] = $det_acervo_autor["nm_autor"];
				$script = " onclick=\"addVal1('{$_SESSION['campo1']}',{$registro['cod_acervo']}); addVal1('{$_SESSION['campo2']}','{$registro['titulo']}'); addVal1('cod_biblioteca','{$this->ref_cod_biblioteca}'); fecha();\"";
				$this->addLinhas( array(
					"<a href=\"javascript:void(0);\" {$script}>{$registro["titulo"]}</a>",
					"<a href=\"javascript:void(0);\" {$script}>{$registro["cod_acervo_autor"]}</a>",
					"<a href=\"javascript:void(0);\" {$script}>{$registro["isbn"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_pesquisa_obra_lst.php", $total, $_GET, $this->nome, $this->limite );
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
<script>

function addVal1( campo, valor )
{
	obj = window.parent.document.getElementById( campo );
	obj.value = valor;
}

function fecha()
{
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	if( window.parent.document.getElementById('passo') )
	{
		window.parent.document.getElementById('passo').value = 2;
		window.parent.document.forms[0].submit();
	}
}
</script>