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
require_once( "include/pmiacoes/geral.inc.php" );
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de Ações do Governo - Listagem de a&ccedil;&otilde;es do Governo" );
		$this->processoAp = "551";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		@session_start();
			$id_pessoa = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet",false );


		$this->titulo = "Listagem de a&ccedil;&otilde;es do Governo";

		$nm_acao = @$_GET['nm_acao'];
		$cod_acao_governo = @$_GET['cod_acao_governo'];
		$status = $_GET['status'] == "2" ? "" : $_GET['status'];		//!is_null($_GET['status']) ? $_GET['status'] : 1;

		// Filtros de Busca
		$this->campoTexto("cod_acao_governo","C&oacute;digo  da a&ccedil;&atilde;o",$cod_acao_governo,50,255);
		$this->campoTexto("nm_acao","Nome da a&ccedil;&atilde;o",$nm_acao,50,255);

		$combo = array('0' => "Pendente",'1' => "Ativo",'2' => "Ambos" );


		$this->campoLista("status", "Status", $combo,  $_GET['status'], false, false, false, false,false,true);


		$this->addCabecalhos( array(  "Nome da a&ccedil;&atilde;o", "Status") );
		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->__nome}"] ) ? $_GET["pagina_{$this->__nome}"]*$limite-$limite: 0;

		$Obj_acoes = new clsPmiacoesAcaoGoverno();
		$Obj_acoes->setOrderby("nm_acao");
		$Obj_acoes->setLimite($limite, $iniciolimit);
		$Lista_acoes = $Obj_acoes->lista($cod_acao_governo,null,null,null,null,$status,1,$nm_acao);
		if($Lista_acoes)
		{
			//$numero_acao  = 0;
			foreach ($Lista_acoes as $acao)
			{
				$status = $acao["status_acao"] == 0 ? "Pendente" : "Ativo";
		//		$numero_acao_texto = "";
			//	if($acao["status_acao"] == 1)
			//	{
					//$numero_acao_texto = ++$numero_acao;

			//	}
				$acao['numero_acao'] =  $acao['numero_acao'] > 0 ? $acao['numero_acao'] : "" ;// = $acao['numero_acao'] != 0 || $acao['numero_acao'] != "" : $acao['numero_acao'] : "";
				$this->addLinhas(array(" <img src='imagens/noticia.jpg' border=0><a href='acoes_acao_det.php?cod_acao_governo={$acao['cod_acao_governo']}'>{$acao['nm_acao']}</a>",$status));

			}
		}

		$total = $Obj_acoes->_total;

		// Paginador
		$this->addPaginador2( "acoes_acao_lst.php", $total, $_GET, $this->__nome, $limite );

		$this->acao = "go(\"acoes_acao_cad.php\")";
		$this->nome_acao = "Novo";

		// Define Largura da Página
		$this->largura = "100%";
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();

?>