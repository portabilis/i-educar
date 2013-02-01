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
		$this->SetTitulo( "{$this->_instituicao} Grupos de Email!" );
		$this->processoAp = "85";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Grupos ";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet");
		$this->addCabecalhos( array( "Nome", "Quantidade de Usuários"));
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_mailling_grupo,nm_grupo FROM mailling_grupo" );
		while ($db->ProximoRegistro())
		{
			list ($cod_grupo, $nome) = $db->Tupla();
			$dba = new clsBanco();
			$dba->Consulta("SELECT count(*) FROM mailling_grupo_email WHERE ref_cod_mailling_grupo = $cod_grupo ");
			if($dba->ProximoRegistro() )
			{
				list($numero_usuarios) = $dba->Tupla();
			}
			else 
			{
				$numero_usuarios= 0;
			}
			
			$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0>&nbsp;&nbsp;<a href='mailling_grupos_det.php?id_grupo=$cod_grupo'>$nome</a>", $numero_usuarios) );
		}
		$this->acao = "go(\"mailling_grupos_cad.php\")";
		$this->nome_acao = "Novo";
		$this->largura = "100%";
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();

?>