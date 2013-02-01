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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmiacoes/geral.inc.php");
class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute;- Sistema de Cadastro de A&ccedil;&oatilde;es do Governo - Detalhe de a&ccedil;&otilde;es do Governo!" );
		$this->processoAp = "551";
	}
}

class indice extends clsDetalhe
{


	function Gerar()
	{
		$cod_acao_governo = @$_GET['cod_acao_governo'];
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		 $_SESSION["display"] =   $_GET["display"] ?  $_GET["display"] : $_SESSION["display"];
		$_SESSION['acao_det'] = $cod_acao_governo ;
		@session_write_close();

		$this->titulo = "Detalhe de a&ccedil;&otilde;es do Governo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		if(!(int)$cod_acao_governo)
			header("Location: acoes_acao_lst.php");

		$obj_acao_governo = new clsPmiacoesAcaoGoverno($cod_acao_governo);
		$det_acao_governo = $obj_acao_governo->detalhe();

		if(!$det_acao_governo = $obj_acao_governo->detalhe() )
			header("Location: acoes_acao_lst.php");

		if($det_acao_governo['numero_acao'])
			$this->addDetalhe( array("N&uacute;mero a&ccedil;&atilde;o", "{$det_acao_governo['numero_acao']}") );
		$this->addDetalhe( array("Nome da a&ccedil;&atilde;o", "{$det_acao_governo['nm_acao']}") );
		$this->addDetalhe( array("Descri&ccedil;&atilde;o da a&ccedil;&atilde;o", "{$det_acao_governo['descricao']}") );
		$det_acao_governo['data_inauguracao'] = dataToBrasil($det_acao_governo['data_inauguracao']);
		$this->addDetalhe( array("Data inaugura&ccedil;&atilde;o", "{$det_acao_governo['data_inauguracao']}") );
		$det_acao_governo['valor'] = str_replace(".",",",$det_acao_governo['valor']);
		$this->addDetalhe( array("Valor", "{$det_acao_governo['valor']}") );
		$this->addDetalhe( array("Destaque",$det_acao_governo['destaque'] == 0 ? "N&atilde;o" : "Sim"));
		$this->addDetalhe( array("Status",$det_acao_governo['status_acao'] == 0 ? "Pendente" : "Confirmado"));

		$display = $_SESSION["display"] == "inline" ? "inline" : "none";

		$det_acoes = $this->detAcoes($cod_acao_governo);
		if($det_acoes){
			if($display == "none")
				$func = "acoes_acao_det.php?cod_acao_governo={$cod_acao_governo}&display=inline";
			else
				$func = "acoes_acao_det.php?cod_acao_governo={$cod_acao_governo}&display=none";
			$this->addDetalhe(array("Detalhes da A&ccedil;&atilde;o", "<a href='$func' >Mostrar detalhe</a><div id='det_pree' name='det_pree' style='display:{$display};'>".$det_acoes."</div>"));
		}

		$this->url_novo = "acoes_acao_cad.php";

		$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];

		//*
		$obj = new clsSetor();
		$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));
		//*

		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_pai);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();

		$obj_acao = new clsPmiacoesAcaoGoverno($cod_acao_governo);
		$obj_acao_det = $obj_acao->detalhe();
		$status = $obj_acao_det["status_acao"];


		//**
			$func_cad = $obj_acao_det["ref_funcionario_cad"];
			$obj_funcionario = new clsFuncionario($func_cad);
			$detalhe_func = $obj_funcionario->detalhe();
			$setor_cad = $detalhe_func["ref_cod_setor_new"];
			$setor_cad = array_shift(array_reverse($obj->getNiveis($setor_cad)));
		//**


		if(($obj_secretaria_responsavel_det != false && $status == 0) || ($setor_cad == $setor_pai && $status == 0 ) || ($obj_secretaria_responsavel_det != false && $status == 1))
		{
			$this->url_editar = "acoes_acao_cad.php?cod_acao_governo={$cod_acao_governo}";
			$this->array_botao = array("Categorias","Setores","Arquivos","Fotos","Noticias","Fotos Portal");


			$this->array_botao_url_script = array("showExpansivel( 500,300, \"<iframe name=\\\"miolo\\\" id=\\\"miolo\\\" frameborder=\\\"0\\\" height=\\\"100%\\\" width=\\\"500\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"acoes_categoria.php?cod_acao_governo={$cod_acao_governo}&limpa=1\\\"></iframe>\");","showExpansivel( 500,300, \"<iframe name=\\\"miolo\\\" id=\\\"miolo\\\" frameborder=\\\"0\\\" height=\\\"100%\\\" width=\\\"500\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"acoes_setor.php?cod_acao_governo={$cod_acao_governo}&limpa=1\\\"></iframe>\");","showExpansivel( 500,300, \"<iframe name=\\\"miolo\\\" id=\\\"miolo\\\" frameborder=\\\"0\\\" height=\\\"100%\\\" width=\\\"500\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"acoes_arquivo.php?cod_acao_governo={$cod_acao_governo}&limpa=1\\\"></iframe>\");","showExpansivel( 500,300, \"<iframe name=\\\"miolo\\\" id=\\\"miolo\\\" frameborder=\\\"0\\\" height=\\\"100%\\\" width=\\\"500\\\" marginheight=\\\"0\\\" marginwidth=\\\"0\\\" src=\\\"acoes_foto.php?cod_acao_governo={$cod_acao_governo}&limpa=1\\\"></iframe>\");","window.location=\"acoes_noticia.php?cod_acao_governo={$cod_acao_governo}&limpa=1\"","window.location=\"acoes_foto_portal.php?cod_acao_governo={$cod_acao_governo}&limpa=1\"");


			if($obj_secretaria_responsavel_det != false && $status == 0)
			{
				$ativar_nome = "Incluir A&ccedil;&atilde;o";
				$ativar_link = "if(confirm(\"Deseja incluir a ação?\"))window.location=\"acoes_acao_incluir_cad.php?cod_acao_governo={$cod_acao_governo}&status=1\"";

				$this->array_botao[] = $ativar_nome;
				$this->array_botao_url_script[] = $ativar_link;
			}
			elseif($obj_secretaria_responsavel_det != false && $status == 1)
			{
				$ativar_nome = "Remarcar como pendente";
				$ativar_link = "if(confirm(\"Deseja marcar a ação como pendente?\"))window.location=\"acoes_acao_incluir_cad.php?cod_acao_governo={$cod_acao_governo}&status=0\"";

				$this->array_botao[] = $ativar_nome;
				$this->array_botao_url_script[] = $ativar_link;
			}

			if($obj_secretaria_responsavel_det != false && $status )
			{
				if($obj_acao_det["destaque"] == 0)
				{
					$ativar_nome = "Marcar como Destaque";
					$ativar_link = "window.location=\"acoes_acao_destaque.php?cod_acao_governo={$cod_acao_governo}&destaque=1\"";

					$this->array_botao[] = $ativar_nome;
					$this->array_botao_url_script[] = $ativar_link;
				}
				else
				{
					$ativar_nome = "Desmarcar Destaque";
					$ativar_link = "window.location=\"acoes_acao_destaque.php?cod_acao_governo={$cod_acao_governo}&destaque=0\"";

					$this->array_botao[] = $ativar_nome;
					$this->array_botao_url_script[] = $ativar_link;
				}
			}

		}
		$this->url_cancelar = "acoes_acao_lst.php";


		$this->largura = "100%";
	}

	//***
	// Inicio detalhe do preenchimento da CP
	//***
	function detAcoes($cod_acao_governo)
	{

		$existe  = false;

		$obj_categoria = new clsPmiacoesAcaoGovernoCategoria();
		$obj_categoria->_campos_lista = "ref_cod_categoria";
		$lista_categoria = $obj_categoria->lista(null,$cod_acao_governo);
		$tabela = "<table border=0 cellpadding=2 width='100%'>";

		if($lista_categoria)
		{

			$existe  = true;
			$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Categorias</b></td></tr><tr><td>";
			$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='50%'>";
			$tabela .= "<tr bgcolor='#A1B3BD'><th>Categoria</th><th width='70'>Excluir</th></tr>";
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			foreach ($lista_categoria as $categoria){
				$obj_nm_categoria = new clsPmiacoesCategoria($categoria);
				$det_categoria = $obj_nm_categoria->detalhe();
				$tabela .= "<tr bgcolor='$cor'><td style='padding-left:20px'><img src=\"imagens/noticia.jpg\" border='0'> {$det_categoria['nm_categoria']}</td><td><a href='acoes_categoria.php?cod_acao_governo={$cod_acao_governo}&remover_categoria={$categoria}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>";
			}
			$tabela .= "</table></td></tr>";
		}

		$obj_setores = new clsPmiacoesAcaoGovernoSetor();
		$obj_setores->_campos_lista = "ref_cod_setor";
		$lista_setores = $obj_setores->lista($cod_acao_governo);

		if($lista_setores)
		{
			$existe  = true;
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Setores</b></td></tr><tr><td>";
			$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='50%'>";
			$tabela .= "<tr bgcolor='#A1B3BD'><th>Setor</th><th width='70'>Excluir</th></tr>";
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			foreach ($lista_setores as $setores){
				$obj_nm_setor = new clsSetor($setores);
				$det_setor = $obj_nm_setor->detalhe();
				$tabela .= "<tr bgcolor='$cor'><td style='padding-left:20px'><img src=\"imagens/noticia.jpg\" border='0'> {$det_setor['sgl_setor']}</td><td><a href='acoes_setor.php?cod_acao_governo={$cod_acao_governo}&remover_setor={$setores}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>";
			}

			$tabela .= "</table></td></tr>";
		}
	

	//fotos
		$obj_fotos = new clsPmiacoesAcaoGovernoFoto();
		$obj_fotos->_campos_lista = "cod_acao_governo_foto, nm_foto, caminho, to_char(data_foto,'dd/mm/yyyy') as data_foto";
		$lista_fotos = $obj_fotos->lista(null,null,$cod_acao_governo);
		if($lista_fotos)
		{

			$existe  = true;
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Fotos</b></td></tr><tr><td>";
			$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='100%'>";
			$tabela .= "<tr bgcolor='#A1B3BD'><th>Foto</th><th>Data</th><th width='100%'>Tï¿½tulo</th><th width='70'>Excluir</th></tr>";


			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			foreach ($lista_fotos as $foto)
			{
				$data= $foto["data_foto"];
				$tabela .= "<tr bgcolor=$cor align='center'><td><a href='javascript:void(0)' onclick='openfotoAcoes(\"arquivos/acoes/fotos/big/{$foto["caminho"]}\")' alt='Clique na imagem para maximizar'><img src='arquivos/acoes/fotos/small/{$foto["caminho"]}' border='0'></a></td><td width='20'>{$data}</td><td align='left'>{$foto["nm_foto"]}</td><td align='center'><a href='acoes_foto.php?cod_acao_governo={$cod_acao_governo}&remover_foto={$foto["cod_acao_governo_foto"]}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>";
			}
			$tabela .= "</table></td></tr>";

		}

				//arquivos
		$obj_fotos = new clsPmiacoesAcaoGovernoArquivo();
		$obj_fotos->_campos_lista = "cod_acao_governo_arquivo,nm_arquivo, caminho_arquivo";
		$lista_fotos = $obj_fotos->lista(null,null,$cod_acao_governo);
		if($lista_fotos)
		{

			$existe  = true;
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Arquivos</b></td></tr><tr><td>";
			$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='50%'>";
			$tabela .= "<tr bgcolor='#A1B3BD'><th width='60%'>Nome</th><th>Arquivo</th><th width='70'>Excluir</th></tr>";

			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
			foreach ($lista_fotos as $foto)
			{

				$data= date("d/m/Y", strtotime(substr($foto["data_foto"],0,19)) );

				$tabela .= "<tr bgcolor=$cor align='center'><td align='left' width='80%'>{$foto["nm_arquivo"]}</td><td><a href='{$foto["caminho_arquivo"]}'\" target=\"_blank\"><img src='imagens/nvp_icon_download.gif' border='0' align='bottom'><br>Visualizar</td><td align='center'><a href='acoes_arquivo.php?cod_acao_governo={$cod_acao_governo}&remover_arquivo={$foto["cod_acao_governo_arquivo"]}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>";
			}
			$tabela .= "</table></td></tr>";
		}

			$obj_noticias = new clsPmiacoesAcaoGovernoNoticia();
			$obj_noticias->_campos_lista = "ref_cod_not_portal";
			$lista_noticias = $obj_noticias->lista($cod_acao_governo);

			if($lista_noticias)
			{
				$existe  = true;
				$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
				$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Noticias Portal</b></td></tr>";
				$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
				$noticias_in = implode(",",$lista_noticias);

				$db = new clsBanco();

				$db->Consulta( "SELECT n.data_noticia, n.titulo, n.cod_not_portal FROM not_portal n where  n.cod_not_portal in($noticias_in) ORDER BY n.data_noticia DESC {$limit}" );
				$tabela .= "<tr><td colspan='2'><table border=0 cellpadding=2 width='100%'>";
				$tabela .= "<tr bgcolor='#A1B3BD' align='center'><td style='padding-left:20px'> <b>Data</b> </td><td><b>Titulo</b></td><td width='70'><b>Excluir</b></td></tr>";
				while ($db->ProximoRegistro())
				{
					list ($data, $titulo, $id_noticia) = $db->Tupla();
					$data= date("d/m/Y", strtotime(substr($data,0,19)) );

					$tabela .= "<tr bgcolor='$cor'><td style='padding-left:20px' width='100'><img src=\"imagens/noticia.jpg\" border='0'> {$data} </td><td>{$titulo}</td><td align='center'><a href='acoes_noticia.php?cod_acao_governo={$cod_acao_governo}&remover_noticia={$id_noticia}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>";
				}

				$tabela .= "</table></td></tr>";
			}


			$obj_fotos_portal = new clsPmiacoesAcaoGovernoFotoPortal();
			$obj_fotos_portal->_campos_lista = "ref_cod_foto_portal";
			$lista_fotos = $obj_fotos_portal->lista($cod_acao_governo);

			if($lista_fotos)
			{

				$existe  = true;
				$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
				$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Fotos Portal</b></td></tr><tr><td>";
				$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='100%'>";
				$tabela .= "<tr bgcolor='#A1B3BD'><th>Foto</th><th>Data</th><th width='60%'>Tï¿½tulo</th><th width='70'>Excluir</th></tr>";

				$fotos_in = implode(",",$lista_fotos);

				$db = new clsBanco();
				$db->Consulta( "SELECT f.cod_foto_portal,f.titulo, f.descricao, f.data_foto, f.caminho, f.nm_credito, f.altura, f.largura FROM foto_portal f WHERE cod_foto_portal in($fotos_in)" );
				$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
				while ($db->ProximoRegistro())
				{

					list ($cod_foto_portal,$titulo, $descricao, $data_foto,$caminho,$nm_credito) = $db->Tupla();
					$data= date("d/m/Y", strtotime(substr($data,0,19)) );
					$rowspan = "";
					if($descricao){
						$rowspan = "rowspan='2'";
						$descricao = "<tr bgcolor=$cor><td colspan='2'><div><b>Descri&ccedil;&atilde;o:</b> {$descricao}</div></td></tr>";
					}
					$tabela .= "<tr bgcolor=$cor align='center'><td $rowspan><img src='fotos/small/{$caminho}' border='0'></td><td>{$data}</td><td align='left'>{$titulo}</td><td $rowspan><a href='acoes_foto_portal.php?cod_acao_governo={$cod_acao_governo}&remover_foto={$cod_foto_portal}&display=inline' ><img src=\"imagens/nvp_bola_xis.gif\" border=0 style='padding-left:10px;'></a></td></tr>{$descricao}";
				}
				$tabela .= "</table></td></tr>";

			}

		$tabela .="</table>";

		return $existe == true ?  $tabela :  false;
	}
	//***
	// Fim detalhe do preenchimento da CP
	//***
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();

?>
<script>

function trocaDisplay(id)
{
	var element = document.getElementById(id);
  	element.style.display = (element.style.display == "none") ? "inline" : "none";
}

var new_window
function winOpen( url)
{
	var winl = (screen.width - 500) / 2;
    var wint = (screen.height - 300) / 2;
	apr = 'toolbar=no,location=no,status=no,menubar=no,resizable=yes,width=500,height=300,scrollbars=yes,screenX=' + winl + ',screenY=' + wint;
	new_window = window.open( url, 'nova_janela',apr );
	new_window.focus();
}

function openfotoAcoes( arquivo)
{

	var winl = (screen.width - 500) / 2;
    var wint = (screen.height - 300) / 2;
	apr = 'toolbar=no,location=no,status=no,menubar=no, scrollbars=no,resizable=yes,screenX=' + winl + ',screenY=' + wint;
	var popup = window.open( '', 'JANELA_FOTO',apr );
	popup.document.open();
	with(popup.document){

		write('<html><head><title></title></head><body topmargin=0 leftmargin=0 onload=resizeTo(document.getElementById("im").width,document.getElementById("im").height);><img id="im" src="' +arquivo +  '" border=0></body></html>');
		popup.document.close();

	}

	popup.focus();
}
function isEmpty(msg){

	alert(msg);
	return false;
}
</script>