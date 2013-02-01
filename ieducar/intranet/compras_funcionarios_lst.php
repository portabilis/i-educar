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
		$this->SetTitulo( "{$this->_instituicao} Compras - Funcionários" );
		$this->processoAp = "137";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Compras - Funcionários";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->addCabecalhos( array( "Funcionário" ) );
		
		
		$db = new clsBanco();
		$db->Consulta( "SELECT count(0) FROM compras_funcionarios" );
		$db->ProximoRegistro();
		list ($total) = $db->Tupla();
		$total_tmp = $total;
		$iniciolimit = (@$_GET['iniciolimit']) ? @$_GET['iniciolimit'] : "0";
		$limite = 10;
		if ($total > $limite)
		{
			$iniciolimit_ = $iniciolimit *$limite;
			$limit = " LIMIT {$iniciolimit_}, $limite";
		}		
		
		$db->Consulta( "SELECT pessoa.idpes, pessoa.nome FROM cadastro.pessoa pessoa, compras_funcionarios compras WHERE pessoa.idpes = compras.ref_ref_cod_pessoa_fj ORDER BY pessoa.nome ASC {$limit}" );
		while ($db->ProximoRegistro())
		{
			list ($cod, $nome) = $db->Tupla();
			$this->addLinhas( array( "<a href=\"compras_funcionarios_cad.php?cod_pessoa_fj=$cod\"><img src='imagens/noticia.jpg' border=0>$nome</a>" ) );
		}
		$this->paginador( "compras_funcionarios_lst.php?", $total_tmp, $limite, @$_GET['pos_atual'] );

		$this->acao = "go(\"compras_funcionarios_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>