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
require_once 'include/localizacaoSistema.php';

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Diaria" );
		$this->processoAp = "293";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	var $secretaria;
	
	 
	function Gerar()
	{
		$this->titulo = "Di&aacute;rias";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );	
		$this->addCabecalhos( array( "Secretaria", "Funcion&aacute;rio", "Partida", "Chegada", "Valor", "Nº Di&aacute;ria" ) );
		if($_GET['ref_cod_setor'])	
			$this->ref_cod_setor = @$_GET['ref_cod_setor'];		


		$lst_setores = array( "" => "Selecione" );
		$obj_setor = new clsSetor();
		$lst_setor = $obj_setor->lista( null, null, null, null, null, null, null, null, null, null, 0 );
		if( is_array( $lst_setor ) && count( $lst_setor ) )
		{
			foreach ( $lst_setor AS $linha )
			{
				$lst_setores[$linha["cod_setor"]] = $linha["nm_setor"];
			}
		}
		$this->campoLista( "ref_cod_setor", "Secretaria", $lst_setores, $this->ref_cod_setor, "", false, "", "", false, false );
		
		$where = "";
		$gruda = "";
		
		$where = " WHERE ativo = 't' ";
		
		if ( ! empty( $_GET['ref_cod_setor'] ) )
		{
			$where .= " AND ref_cod_setor = {$this->ref_cod_setor}";
		}
		
		$db = new clsBanco();
		$db2 = new clsBanco();
		$total = $db->UnicoCampo( "SELECT count(0) FROM pmidrh.diaria $where" );
		
		// Paginador
		$limite = 15;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		$strLimit = " LIMIT $iniciolimit, $limite";
		
		$objPessoa = new clsPessoaFisica();
		
		$sql = "SELECT cod_diaria, ref_funcionario, data_partida, data_chegada, COALESCE(vl100,0) + COALESCE(vl75,0) + COALESCE(vl50,0) + COALESCE(vl25,0) AS valor, ref_cod_setor, num_diaria, data_pedido  FROM pmidrh.diaria $where ORDER BY num_diaria ASC $strLimit";
		$db->Consulta( $sql );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_diaria, $idpes, $data_partida, $data_chegada, $valor, $ref_cod_setor, $num_diaria, $data_pedido ) = $db->Tupla();
			
			$data_chegada = date( "d/m/Y H:i", strtotime( $data_chegada ) );
			$data_partida = date( "d/m/Y H:i", strtotime( $data_partida ) );
			

			if( $ref_cod_setor ) 
			{
				$obj_setor = new clsSetor( $ref_cod_setor );
				$det_setor = $obj_setor->detalhe();
				$secretaria = $det_setor['nm_setor'];
			}
			else
			{
				$secretaria = "";
			}

			list( $nome ) = $objPessoa->queryRapida( $idpes, "nome" );
			if( strlen( $nome ) > 40 )
			{
				$nome = substr( $nome, 0, 37 );
			}
			
			$valor = number_format( $valor, 2, ",", "." );
			
			$data = dataFromPgToBr( $data_pedido );
			$data = explode( "/", $data );
			$num_diaria = sprintf("%06d",$num_diaria);
			
			$this->addLinhas( array( 
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'><img src='imagens/noticia.jpg' border=0>$secretaria</a>", 
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'>$nome</a>", 
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'>$data_partida</a>", 
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'>$data_chegada</a>", 
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'>$valor</a>",
			"<a href='diaria_det.php?cod_diaria={$cod_diaria}'>{$num_diaria}/{$data[2]}</a>"
			 ) );
		}
		
		// Paginador
		$this->addPaginador2( "diaria_lst.php", $total, $_GET, $this->nome, $limite );
		
		$this->acao = "go(\"diaria_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    ""                  => "Diárias"
                ));
                $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>