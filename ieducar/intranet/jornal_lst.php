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
		$this->SetTitulo( "{$this->_instituicao} Jornal!" );
		$this->processoAp = "34";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Jornal do Munic&iacute;pio";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$ano = @$_GET['ano'];
		$edicao = @$_GET['edicao'];
		$data_i = @$_GET['data_i'];

		$this->campoTexto( "ano", "Ano",  $ano, "2", "4", true );
		$this->campoTexto( "edicao", "Edi&ccedil;&atilde;o",  $edicao, "2", "4", true );
		$this->campoData( "data_i", "Data",  $data_i, "20", "", true );

		$db = new clsBanco();

		$sql  = "SELECT j.cod_jor_edicao,j.jor_ano_edicao, j.jor_edicao, j.jor_dt_inicial, j.jor_dt_final FROM jor_edicao j";

		$where = " ";
		$where_and = " WHERE";
		if (!empty($ano))
		{
			$where .= $where_and." jor_ano_edicao = {$ano}";
			$where_and = " AND";
		}
		if (!empty($edicao))
		{
			$where .= $where_and." jor_edicao = {$edicao}";
			$where_and = " AND";
		}
		if (!empty($data_i))
		{
			$data_i = date("Y-m-d", strtotime(substr($data_i,0,19)));
			$where .= $where_and." (jor_dt_inicial <= $data_i AND";
			$where .= " jor_dt_final >= $data_i)";
		}
		
		$sql .= $where." ORDER BY j.jor_dt_inicial DESC";

		$db->Consulta( "SELECT count(*) FROM jor_edicao {$where}" );
		$db->ProximoRegistro();
		list ($total) = $db->Tupla();
		$total_tmp = $total;

		if (@$_GET['iniciolimit'])
			$iniciolimit = @$_GET['iniciolimit'];
		else
			$iniciolimit = "0";

		$limite = 10;
		if ($total >$limite)
		{
			$iniciolimit_ = $iniciolimit * $limite;
			$limit = " LIMIT {$iniciolimit_}, $limite";
		}

		$sql .= $limit;

		$this->addCabecalhos( array( "Data", "Edi&ccedil;&atilde;o", "Tamanho(em Kb)") );

		$db->Consulta( $sql );
		while ($db->ProximoRegistro())
		{
			list ($cod,$ano, $edicao, $data_inicial, $data_final, $extra) = $db->Tupla();
			$data_inicial = date('d/m/Y', strtotime(substr($data_inicial,0,19) ));
			$data_final= date('d/m/Y', strtotime(substr($data_final,0,19) ));

			if (empty($edicao)) $edicao = "EXTRA";

			$teste = explode ("/", $data_inicial);
			if($teste[0] < 10) $data_inicial = $data_inicial;

			$teste = explode ("/", $data_final);
			if($teste[0] < 10) $data_final = $data_final;
			
			$sql_tmp = "SELECT jor_caminho FROM jor_arquivo WHERE ref_cod_jor_edicao = {$cod}";
			$db_tmp = new clsBanco();
			$db_tmp->Consulta($sql_tmp);
			$tamanho = 0;
			while($db_tmp->ProximoRegistro())
			{
				list($arquivo) = $db_tmp->Tupla();
				$tamanho+= ceil(filesize($arquivo)/1024);
			}

			if ($data_inicial != $data_final)
			{
				$this->addLinhas( array("<a href='jornal_det.php?cod_jornal={$cod}'><img src='imagens/noticia.jpg' border=0>$data_inicial à $data_final</a>", $edicao, $tamanho) );
			}
			else
			{
				$this->addLinhas( array( "<a href='jornal_det.php?cod_jornal={$cod}'><img src='imagens/noticia.jpg' border=0>$data_final</a>", $edicao, $tamanho) );
			}
		}

		$this->acao = "go(\"jornal_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
		$this->paginador("jornal_lst.php?ano={$_GET['ano']}&edicao={$_GET['edicao']}&data_i={$_GET['data_i']}",$total_tmp,$limite,@$_GET['pos_atual']);

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>