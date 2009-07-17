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

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Listagem de Ruas!" );
		$this->processoAp = "0";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;

	}
}

class miolo1 extends clsListagem
{
	var $funcao_js = "cv_libera_campos('cep_', 'ref_sigla_uf_', 'cidade', 'nm_bairro', 'ref_idtlog', 'nm_logradouro', 'isEnderecoExterno')";

	function Gerar()
	{
		@session_start();
		$_SESSION["campo1"] = $_GET["campo1"] ? $_GET["campo1"] : $_SESSION["campo1"];
		$_SESSION["campo2"] = $_GET["campo2"] ? $_GET["campo2"] : $_SESSION["campo2"];
		$_SESSION["campo3"] = $_GET["campo3"] ? $_GET["campo3"] : $_SESSION["campo3"];
		$_SESSION["campo4"] = $_GET["campo4"] ? $_GET["campo4"] : $_SESSION["campo4"];
		$_SESSION["campo5"] = $_GET["campo5"] ? $_GET["campo5"] : $_SESSION["campo5"];
		$_SESSION["campo6"] = $_GET["campo6"] ? $_GET["campo6"] : $_SESSION["campo6"];
		$_SESSION["campo7"] = $_GET["campo7"] ? $_GET["campo7"] : $_SESSION["campo7"];
		$_SESSION["campo8"] = $_GET["campo8"] ? $_GET["campo8"] : $_SESSION["campo8"];
		$_SESSION["campo9"] = $_GET["campo9"] ? $_GET["campo9"] : $_SESSION["campo9"];
		$_SESSION["campo10"] = $_GET["campo10"] ? $_GET["campo10"] : $_SESSION["campo10"];
		$_SESSION["campo11"] = $_GET["campo11"] ? $_GET["campo11"] : $_SESSION["campo11"];
		$_SESSION["campo12"] = $_GET["campo12"] ? $_GET["campo12"] : $_SESSION["campo12"];
		$_SESSION["campo13"] = $_GET["campo13"] ? $_GET["campo13"] : $_SESSION["campo13"];
		$this->nome = "form1";

		$this->funcao_js = "cv_libera_campos('{$_SESSION["campo10"]}', '{$_SESSION["campo11"]}', '{$_SESSION["campo7"]}', '{$_SESSION["campo1"]}', '{$_SESSION["campo12"]}', '{$_SESSION["campo4"]}', '{$_SESSION["campo9"]}')";

		$this->titulo = "Endere&ccedil;o";

		// Paginador
		$limite = 7;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		//***
		// INICIO FILTROS
		//***
		$this->campoTexto("nm_bairro", "Bairro", $_GET["nm_bairro"], 40, 255);
		$this->campoCep("nr_cep", "CEP", $_GET["nr_cep"]);
		$this->campoTexto("nm_logradouro", "Logradouro", $_GET["nm_logradouro"], 50, 255);
		$this->campoTexto("cidade", "Cidade", $_GET["cidade"], 60, 60);
		$obj_uf = new clsUf(false, false, 1);
		$lst_uf = $obj_uf->lista(false, false, false, false, false, "sigla_uf");
		$array_uf;
		foreach ($lst_uf as $uf)
		{
			$array_uf[$uf['sigla_uf']] = $uf['nome'];
		}
		if(!($_GET["ref_sigla_uf"]))
		{
			$_GET["ref_sigla_uf"] = "SC";
		}
		$this->campoLista("ref_sigla_uf", "UF", $array_uf, $_GET['ref_sigla_uf'], "", false, "");
		//***
		// FIM FILTROS
		//***

		$this->addCabecalhos( array("Bairro", "CEP", "Logradouro", "UF", "Cidade") );
		$select = "SELECT c.idlog, c.cep, c.idbai, u.sigla_uf, m.nome, t.idtlog,m.idmun FROM urbano.cep_logradouro_bairro c, public.bairro b, public.logradouro l, public.municipio m, public.uf u, urbano.tipo_logradouro t WHERE c.idlog = l.idlog AND c.idbai = b.idbai AND l.idmun = b.idmun AND l.idmun = m.idmun AND l.idtlog = t.idtlog AND m.sigla_uf = u.sigla_uf";
		$select_count = "SELECT count(*) FROM urbano.cep_logradouro_bairro c, public.bairro b, public.logradouro l, public.municipio m, public.uf u, urbano.tipo_logradouro t WHERE c.idlog = l.idlog AND c.idbai = b.idbai AND l.idmun = b.idmun AND l.idmun = m.idmun AND l.idtlog = t.idtlog AND m.sigla_uf = u.sigla_uf";

		if($_GET["nm_bairro"] || $_GET["nr_cep"] || $_GET["nm_logradouro"] || $_GET['ref_sigla_uf'] || $_GET['cidade'])
		{
			if($_GET["nr_cep"])
			{
				$num_cep = idFederal2int($_GET["nr_cep"]);
				$select .= " AND c.cep ILIKE '%{$num_cep}%'";
				$select_count .= " AND c.cep ILIKE '%{$num_cep}%'";
			}
			if($_GET["nm_bairro"])
			{
				$select .= " AND b.nome ILIKE '%{$_GET["nm_bairro"]}%'";
				$select_count .= " AND b.nome ILIKE '%{$_GET["nm_bairro"]}%'";
			}
			if($_GET["nm_logradouro"])
			{
				$select .= " AND l.nome ILIKE '%{$_GET["nm_logradouro"]}%'";
				$select_count .= " AND l.nome ILIKE '%{$_GET["nm_logradouro"]}%'";
			}
			if($_GET["ref_sigla_uf"])
			{
				$select .= " AND u.sigla_uf ILIKE '%{$_GET["ref_sigla_uf"]}%'";
				$select_count .= " AND u.sigla_uf ILIKE '%{$_GET["ref_sigla_uf"]}%'";
			}
			if($_GET["cidade"])
			{
				$select .= " AND m.nome ILIKE '%{$_GET["cidade"]}%'";
				$select_count .= " AND m.nome ILIKE '%{$_GET["cidade"]}%'";
			}
		}

		$select .= " LIMIT {$limite} OFFSET {$iniciolimit}";
		$db = new clsBanco();
		$total = $db->CampoUnico($select_count);
		$db->Consulta($select);
		while ( $db->ProximoRegistro() )
		{
			list( $idlog, $cep, $idbai, $uf, $cidade, $descricao,$id_mun ) = array('','','','','','','');

			list( $idlog, $cep, $idbai, $uf, $cidade, $descricao,$id_mun ) = $db->Tupla();

			$logradouro = new clsLogradouro($idlog);
			$detalhe_logradouro = $logradouro->detalhe();
			$bairro = new clsBairro($idbai);
			$detalhe_bairro = $bairro->detalhe();
			$cep2 = int2CEP($cep);
			$s_end = "0";
			$descricao = urlencode($descricao);

			if($_GET["param"])
			{


			$this->addLinhas(array("<a href='javascript:void(0);' onclick=\"setaCamposOuvidoria('{$cep}', '{$cep2}', '{$uf}', '{$uf}', '{$id_mun}', '{$cidade}', '{$detalhe_bairro["idbai"]}', '{$detalhe_bairro["nome"]}', '{$descricao}', '{$descricao}', '{$detalhe_logradouro["idlog"]}', '{$detalhe_logradouro["nome"]}')\">{$detalhe_bairro["nome"]}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"setaCamposOuvidoria('{$cep}', '{$cep2}', '{$uf}', '{$uf}', '{$id_mun}', '{$cidade}', '{$detalhe_bairro["idbai"]}', '{$detalhe_bairro["nome"]}', '{$descricao}', '{$descricao}', '{$detalhe_logradouro["idlog"]}', '{$detalhe_logradouro["nome"]}')\">{$cep2}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"setaCamposOuvidoria('{$cep}', '{$cep2}', '{$uf}', '{$uf}', '{$id_mun}', '{$cidade}', '{$detalhe_bairro["idbai"]}', '{$detalhe_bairro["nome"]}', '{$descricao}', '{$descricao}', '{$detalhe_logradouro["idlog"]}', '{$detalhe_logradouro["nome"]}')\">{$detalhe_logradouro["nome"]}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"setaCamposOuvidoria('{$cep}', '{$cep2}', '{$uf}', '{$uf}', '{$id_mun}', '{$cidade}', '{$detalhe_bairro["idbai"]}', '{$detalhe_bairro["nome"]}', '{$descricao}', '{$descricao}', '{$detalhe_logradouro["idlog"]}', '{$detalhe_logradouro["nome"]}')\">{$uf}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"setaCamposOuvidoria('{$cep}', '{$cep2}', '{$uf}', '{$uf}', '{$id_mun}', '{$cidade}', '{$detalhe_bairro["idbai"]}', '{$detalhe_bairro["nome"]}', '{$descricao}', '{$descricao}', '{$detalhe_logradouro["idlog"]}', '{$detalhe_logradouro["nome"]}')\">{$cidade}</a>"));
			} else {


			$this->addLinhas(array("<a href='javascript:void(0);' onclick=\"cv_set_campo('{$_SESSION['campo1']}', '{$detalhe_bairro["nome"]}', '{$_SESSION['campo2']}', '{$detalhe_bairro["idbai"]}', '{$_SESSION['campo3']}', '{$cep}', '{$_SESSION['campo4']}', '{$detalhe_logradouro["nome"]}', '{$_SESSION['campo5']}', '{$detalhe_logradouro["idlog"]}', '{$_SESSION['campo6']}', '{$uf}', '{$_SESSION['campo7']}', '{$cidade}', '{$_SESSION['campo8']}', '{$descricao}', '{$_SESSION['campo9']}', '{$s_end}', '{$_SESSION['campo10']}', '{$cep2}', '{$_SESSION['campo11']}', '{$uf}', '{$_SESSION['campo12']}','{$_SESSION['campo13']}', '{$id_mun}');\">{$detalhe_bairro["nome"]}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"cv_set_campo('{$_SESSION['campo1']}', '{$detalhe_bairro["nome"]}', '{$_SESSION['campo2']}', '{$detalhe_bairro["idbai"]}', '{$_SESSION['campo3']}', '{$cep}', '{$_SESSION['campo4']}', '{$detalhe_logradouro["nome"]}', '{$_SESSION['campo5']}', '{$detalhe_logradouro["idlog"]}', '{$_SESSION['campo6']}', '{$uf}', '{$_SESSION['campo7']}', '{$cidade}', '{$_SESSION['campo8']}', '{$descricao}', '{$_SESSION['campo9']}', '{$s_end}', '{$_SESSION['campo10']}', '{$cep2}', '{$_SESSION['campo11']}', '{$uf}', '{$_SESSION['campo12']}','{$_SESSION['campo13']}', '{$id_mun}');\">{$cep2}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"cv_set_campo('{$_SESSION['campo1']}', '{$detalhe_bairro["nome"]}', '{$_SESSION['campo2']}', '{$detalhe_bairro["idbai"]}', '{$_SESSION['campo3']}', '{$cep}', '{$_SESSION['campo4']}', '{$detalhe_logradouro["nome"]}', '{$_SESSION['campo5']}', '{$detalhe_logradouro["idlog"]}', '{$_SESSION['campo6']}', '{$uf}', '{$_SESSION['campo7']}', '{$cidade}', '{$_SESSION['campo8']}', '{$descricao}', '{$_SESSION['campo9']}', '{$s_end}', '{$_SESSION['campo10']}', '{$cep2}', '{$_SESSION['campo11']}', '{$uf}', '{$_SESSION['campo12']}','{$_SESSION['campo13']}', '{$id_mun}');\">{$detalhe_logradouro["nome"]}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"cv_set_campo('{$_SESSION['campo1']}', '{$detalhe_bairro["nome"]}', '{$_SESSION['campo2']}', '{$detalhe_bairro["idbai"]}', '{$_SESSION['campo3']}', '{$cep}', '{$_SESSION['campo4']}', '{$detalhe_logradouro["nome"]}', '{$_SESSION['campo5']}', '{$detalhe_logradouro["idlog"]}', '{$_SESSION['campo6']}', '{$uf}', '{$_SESSION['campo7']}', '{$cidade}', '{$_SESSION['campo8']}', '{$descricao}', '{$_SESSION['campo9']}', '{$s_end}', '{$_SESSION['campo10']}', '{$cep2}', '{$_SESSION['campo11']}', '{$uf}', '{$_SESSION['campo12']}','{$_SESSION['campo13']}', '{$id_mun}');\">{$uf}</a>",
			 					   "<a href='javascript:void(0);' onclick=\"cv_set_campo('{$_SESSION['campo1']}', '{$detalhe_bairro["nome"]}', '{$_SESSION['campo2']}', '{$detalhe_bairro["idbai"]}', '{$_SESSION['campo3']}', '{$cep}', '{$_SESSION['campo4']}', '{$detalhe_logradouro["nome"]}', '{$_SESSION['campo5']}', '{$detalhe_logradouro["idlog"]}', '{$_SESSION['campo6']}', '{$uf}', '{$_SESSION['campo7']}', '{$cidade}', '{$_SESSION['campo8']}', '{$descricao}', '{$_SESSION['campo9']}', '{$s_end}', '{$_SESSION['campo10']}', '{$cep2}', '{$_SESSION['campo11']}', '{$uf}', '{$_SESSION['campo12']}','{$_SESSION['campo13']}', '{$id_mun}');\">{$cidade}</a>"));
			}

		}


		$this->largura = "100%";
		$this->addPaginador2( "educar_pesquisa_cep_log_bairro.php", $total, $_GET, $this->nome, $limite );

		if($_GET["param"])
		{
	        $this->rodape = "
						<table border='0' cellspacing='0' cellpadding='0' width=\"100%\" align=\"center\">
						<tr width='100%'>
						<td>
						<div align='center'>[ <a href='javascript:void(0);' onclick=\"liberaCamposOuvidoria()\">Cadastrar Novo Endere&ccedil;o</a> ]</div>
						</td>
						</tr>
						</table>";

		} else {

        $this->rodape = "
						<table border='0' cellspacing='0' cellpadding='0' width=\"100%\" align=\"center\">
						<tr width='100%'>
						<td>
						<div align='center'>[ <a href='javascript:void(0);' onclick=\"{$this->funcao_js}\">Cadastrar Novo Endere&ccedil;o</a> ]</div>
						</td>
						</tr>
						</table>";
		}

		@session_write_close();
	}

}

/*
		if(!($this->renderMenu))
		{
			$saida = str_replace("<!-- #&RODAPE&# -->", "
			<table border='0' cellspacing='0' cellpadding='0' width=\"100%\" align=\"center\">
			<tr width='100%'>
			<td>
			<div align='center'>[ <a href='javascript:void(0);' onclick=\"{$this->funcao_js}\">Cadastrar Novo Endere&ccedil;o</a> ]</div>
			</td>
			</tr>
			</table>", $saida);
		}
*/


$pagina = new clsIndex();

$miolo = new miolo1();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
<script>
function setFiltro()
{
	alert("filtro");
	//alert(document.getElementById("nivel0").value);
}

/*
	Função especifica para Ouvidoria Atendimento Completo Cad
*/
function setaCamposOuvidoria(valor1, valor2, valor3, valor4, valor5, valor6, valor7, valor8, valor9, valor10, valor11, valor12)
{
	// Campo Oculto flag atualiza
	parent.document.getElementById("atualiza").value = "false";
	parent.document.getElementById("nendereco").value = "false";

	// Campo Oculto Cep
	obj1 = parent.document.getElementById("cep");
 	obj1.value = valor1;

 	// Campo Visivel Cep
	obj2 = parent.document.getElementById("cep_");
 	obj2.value = valor2;
	obj2.disabled = true;

	// Campo Oculto Sigla_uf
	obj3 = parent.document.getElementById("sigla_uf");
 	obj3.value = valor3;

 	// Campo Visivel Sigla_uf
	obj4 = parent.document.getElementById("sigla_uf_");
 	obj4.value = valor4;
	obj4.disabled = true;

	// Campo Oculto Cidade
	obj5 = parent.document.getElementById("cidade");
 	obj5.value = valor5;

 	// Campo Visivel Cidade
	obj6 = parent.document.getElementById("cidade_");
 	obj6.value = valor6;
	obj6.disabled = true;

	// Campo Oculto NMCidade
	obj14 = parent.document.getElementById("nmCidade");
 	obj14.value = valor6;

	// Campo Oculto Bairro
	obj7 = parent.document.getElementById("idbai");
 	obj7.value = valor7;

 	// Campo Visivel Bairro
	obj8 = parent.document.getElementById("bairro_");
 	obj8.value = valor8;
	obj8.disabled = true;

	obj13 = parent.document.getElementById("bairro");
 	obj13.value = valor8;

	// Campo Oculto Tipo Logradouro
	obj9 = parent.document.getElementById("idtlog");
 	obj9.value = valor9;

 	// Campo Visivel Tipo Logradouro
	obj10 = parent.document.getElementById("idtlog_");
 	obj10.value = valor10;
	obj10.disabled = true;

	// Campo Oculto Logradouro
	obj11 = parent.document.getElementById("idlog");
 	obj11.value = valor11;

 	// Campo Visivel Logradouro
	obj12 = parent.document.getElementById("logradouro_");
 	obj12.value = valor12;
	obj12.disabled = true;

	obj14 = parent.document.getElementById("logradouro");
 	obj14.value = valor12;

	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
}

function liberaCamposOuvidoria()
{
	parent.document.getElementById("atualiza").value = "false";
	parent.document.getElementById("nendereco").value = "true";
	// Campo Oculto Cep
	obj1 = parent.document.getElementById("cep");
 	obj1.value = null;

 	// Campo Visivel Cep
	obj2 = parent.document.getElementById("cep_");
 	obj2.value = null;
	obj2.disabled = false;

	// Campo Oculto Sigla_uf
	obj3 = parent.document.getElementById("sigla_uf");
 	obj3.value = null;

 	// Campo Visivel Sigla_uf
	obj4 = parent.document.getElementById("sigla_uf_");
 	obj4.value = null;
	obj4.disabled = false;

	// Campo Oculto Cidade
	obj5 = parent.document.getElementById("cidade");
 	obj5.value = null;

 	// Campo Visivel Cidade
	obj6 = parent.document.getElementById("cidade_");
 	obj6.value = null;
	obj6.disabled = false;

	// Campo Oculto Bairro
	obj7 = parent.document.getElementById("idbai");
 	obj7.value = null;

 	// Campo Visivel Bairro
	obj8 = parent.document.getElementById("bairro_");
 	obj8.value = null;
	obj8.disabled = false;

	obj13 = parent.document.getElementById("bairro");
 	obj13.value = null;

	// Campo Oculto Tipo Logradouro
	obj9 = parent.document.getElementById("idtlog");
 	obj9.value = null;

 	// Campo Visivel Tipo Logradouro
	obj10 = parent.document.getElementById("idtlog_");
 	obj10.value = null;
	obj10.disabled = false;

	// Campo Oculto Logradouro
	obj11 = parent.document.getElementById("idlog");
 	obj11.value = null;

 	// Campo Visivel Logradouro
	obj12 = parent.document.getElementById("logradouro_");
 	obj12.value = null;
	obj12.disabled = false;

	obj14 = parent.document.getElementById("logradouro");
 	obj14.value = null;

	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
}
</script>





