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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Fotos!" );
		$this->processoAp = "0";
		$this->renderMenu = false;
	}
}

class indice extends clsListagem
{	
	function Gerar()
	{
		@session_start();
	
		$_SESSION["campo"] = isset($_GET["campo"]) ? $_GET["campo"] : $_SESSION["campo"];
		
		$_SESSION["campo1"] = isset($_GET["campo1"]) ? $_GET["campo1"] : $_SESSION["campo1"];
		$_SESSION["campo2"] = isset($_GET["campo2"]) ? $_GET["campo2"] : $_SESSION["campo2"];
		$_SESSION["campo3"] = isset($_GET["campo3"]) ? $_GET["campo3"] : $_SESSION["campo3"];
		$this->nome = "form1";
		
		$this->titulo = "Fotos";

		
		$this->addCabecalhos( array("Selecionar", "Data", "T&iacute;tulo", "Criador") );
		
		
		//***
		// INICIO FILTROS
		//***
		$this->campoTexto("titulo", "T&iacute;tulo", $_GET["titulo"], 40, 255);
		
		//***
		// FIM FILTROS
		//***
		
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$objPessoa = new clsPessoaFisica();
		$db = new clsBanco();
		
		if(!empty($_GET["titulo"]))
			$where = " and f.titulo ilike '%{$_GET["titulo"]}%' ";
					
		$total = $db->CampoUnico("SELECT COUNT(0) FROM pmicontrolesis.foto_evento f,cadastro.pessoa p WHERE f.ref_ref_cod_pessoa_fj = p.idpes {$where}");
		$db->Consulta("SELECT f.ref_ref_cod_pessoa_fj, 
		       				  f.cod_foto_evento, 
						      to_char(f.data_foto,'dd/mm/yyyy'),
						      f.titulo, 
						      f.descricao, 
						      f.caminho,
						      p.nome
					     FROM pmicontrolesis.foto_evento     f,
					          cadastro.pessoa p
					    WHERE f.ref_ref_cod_pessoa_fj = p.idpes
					    {$where}		    
					 ORDER BY f.data_foto DESC
				        LIMIT $iniciolimit,$limite");
		
		while ($db->ProximoRegistro())
		{
			list ( $cod_pessoa, $id_foto, $data, $titulo, $descricao, $caminho, $nome) = $db->Tupla();
			$campo = @$_SESSION["campo"];
			
			$campo3 = @$_SESSION["campo3"];	
			
			if(strpos($campo3,"acoes") == 1){
				$onclick = "javascript:enviar(\"{$_SESSION["campo1"]}\",\"{$id_foto}\",\"{$titulo}\",\"div_dinamico_0\")";
			}else{
				$onclick = "javascript:retorna(\"{$this->nome}\", \"{$campo}\", \"{$id_foto}\");";
			}
			$this->addLinhas( array("<center><a href='javascript:void(0);' onclick='$onclick'><img src='fotos/small/{$caminho}' border=0></a></center>", $data, $titulo, $nome." ".$sobrenome) );
		}
		$this->addPaginador2( "add_fotos_evento.php", $total, $_GET, $this->nome, $limite );
		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>

<script>
//script utilizado pelo pmiacoes
function setFiltro()
{
	alert("filtro");
}
function enviar( campo, valor, texto, div )
{

	window.parent.addSel( campo, valor,texto );
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));	
}	
</script>