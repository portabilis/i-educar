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

//phpinfo();


$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Jornal" );
		$this->processoAp = "34";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Jornal do Munic&iacute;pio";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_jornal = @$_GET['cod_jornal'];

		$db = new clsBanco();
		$db->Consulta( "SELECT jor_ano_edicao, jor_edicao, jor_dt_inicial, jor_dt_final FROM jor_edicao WHERE cod_jor_edicao={$cod_jornal}" );
		if ($db->ProximoRegistro())
		{
			list ($ano, $edicao, $data_inicial, $data_final) = $db->Tupla();
			$data_final= date('d/m/Y', strtotime(substr($data_final,0,19) ));
			$data_inicial= date('d/m/Y', strtotime(substr($data_inicial,0,19) ));
			
			$this->addDetalhe( array("Ano", $ano) );

			if (empty($edicao))
			{
				$edicao = "EXTRA";
			}

			$teste = explode ("/", $data_inicial);
			if($teste[2] < 10) $data_inicial = "0".$data_inicial;

			$teste = explode ("/", $data_final);
			if($teste[2] < 10) $data_final = "0".$data_final;

			$this->addDetalhe( array("Edi&ccedil;&atilde;o", $edicao) );

			if ($data_inicial != $data_final)
			{
				$this->addDetalhe( array("Data Inicial", $data_inicial) );
				$this->addDetalhe( array("Data Final", $data_final) );
			}
			else
			{
				$this->addDetalhe( array("Data", $data_inicial) );
			}
			
			$sql_tmp = "SELECT jor_caminho FROM jor_arquivo WHERE ref_cod_jor_edicao = {$cod_jornal}";
			$db_tmp = new clsBanco();
			$db_tmp->Consulta($sql_tmp);
			while($db_tmp->ProximoRegistro())
			{
				list($arquivo) = $db_tmp->Tupla();
				$tamanho= ceil(filesize($arquivo)/1024);
				$this->addDetalhe( array("Tamanho", $tamanho) );
				$this->addDetalhe( array("Visualizar", "<a href='$arquivo'>clique aqui</a>") );
			}
			
			

		}

		
		$this->url_novo = "jornal_cad.php";
		$this->url_editar = "jornal_cad.php?cod_jornal=$cod_jornal";
		$this->url_cancelar = "jornal_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>