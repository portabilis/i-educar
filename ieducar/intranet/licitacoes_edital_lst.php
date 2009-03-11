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
		$this->SetTitulo( "{$this->_instituicao} Edital" );
		$this->processoAp = "239";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$db = new clsBanco();
		$db2 = new clsBanco();
		$this->titulo = "Editais";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->addCabecalhos( array( "Número da Licitação", "Modalidade", "Versão", "Atualizado em" ) );
		
		$db->Consulta( "SELECT MAX( cod_compras_editais_editais ) AS cod_compras_editais_editais, ref_cod_compras_licitacoes, MAX( versao ) AS versao, MAX( data_hora ) AS data_hora FROM compras_editais_editais GROUP BY ref_cod_compras_licitacoes ORDER BY ref_cod_compras_licitacoes DESC" );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_compras_editais_editais, $ref_cod_compras_licitacoes, $versao, $data_hora ) = $db->Tupla();
			$db2->Consulta( "SELECT numero, nm_modalidade FROM compras_licitacoes, compras_modalidade WHERE cod_compras_licitacoes = '{$ref_cod_compras_licitacoes}' AND ref_cod_compras_modalidade = cod_compras_modalidade" );
			$db2->ProximoRegistro();
			list( $numero_licitacao, $nm_modalidade ) = $db2->Tupla();
			
			$this->addLinhas( array( "<a href='licitacoes_edital_det.php?cod_edital=$cod_compras_editais_editais'><img src='imagens/noticia.jpg' border=0>$numero_licitacao</a>", "<a href='licitacoes_edital_det.php?cod_edital=$cod_compras_editais_editais'>$nm_modalidade</a>", $versao, date( "d/m/Y H:i", strtotime(substr( $data_hora,0,19) ) ) ) );
		}
		$this->paginador("licitacoes_edital_lst.php?",$total_tmp,$limite,@$_GET['pos_atual'] );

		$this->acao = "go(\"licitacoes_edital_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>