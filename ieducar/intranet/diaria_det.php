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

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Diaria" );
		$this->processoAp = "293";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do valor";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_diaria = @$_GET['cod_diaria'];
		
		$db = new clsBanco();
		$db2 = new clsBanco();
		
		$db->Consulta( "SELECT ref_funcionario_cadastro, ref_cod_diaria_grupo, ref_funcionario, conta_corrente, agencia, banco,  dotacao_orcamentaria,  objetivo, data_partida, data_chegada, estadual, destino, data_pedido, vl100,  vl75, vl50, vl25, ref_cod_setor, num_diaria FROM pmidrh.diaria WHERE cod_diaria='{$cod_diaria}'" );
		if( $db->ProximoRegistro() )
		{
			list( $ref_funcionario_cadastro, $ref_cod_diaria_grupo, $ref_funcionario, $conta_corrente, $agencia, $banco, $dotacao_orcamentaria, $objetivo, $data_partida, $data_chegada, $estadual, $destino, $data_pedido, $vl100, $vl75, $vl50, $vl25, $ref_cod_setor, $num_diaria ) = $db->Tupla();
			
			$this->addDetalhe( array( "N&deg;. do roteiro", "<span style=\"font-size: 14px;font-weight:bold;\">{$cod_diaria}</span>" ));
			
			$objPessoa = new clsPessoa_( $ref_funcionario_cadastro );
			$detalhePessoa = $objPessoa->detalhe();
			$this->addDetalhe( array( "Ultimo Editor", $detalhePessoa["nome"] ) );
			
			$nome_grupo = $db2->CampoUnico( "SELECT desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" );
			$this->addDetalhe( array( "Grupo", $nome_grupo ) );
			
			$objPessoa = new clsPessoaFisica( $ref_funcionario );
			$detalhePessoa = $objPessoa->detalhe();
			$this->addDetalhe( array( "Funcion&aacute;rio", $detalhePessoa["nome"] ) );
			$this->addDetalhe( array( "CPF", int2CPF( $detalhePessoa["cpf"] ) ) );
			
			$objFuncionario = new clsFuncionario( $ref_funcionario );
			$detalheFuncionario = $objFuncionario->detalhe();
			$this->addDetalhe( array( "Matricula", $detalheFuncionario["matricula"] ) );
			
			
			$data = dataFromPgToBr( $data_pedido );
			$data = explode( "/", $data );
			
			if( $ref_cod_setor ) 
			{
				$obj_setor = new clsSetor( $ref_cod_setor );
				$det_setor = $obj_setor->detalhe();
				$nm_secretaria = $det_setor['nm_setor'];
				
				$this->addDetalhe( array( "Secretaria", $nm_secretaria ) );
			}

			$this->addDetalhe( array( "Conta Corrente", $conta_corrente ) );
			if( strlen( $agencia ) < 5 )
			{
				$agencia = str_repeat( "0", 5 - strlen( $agencia ) ) . $agencia;
			}
			if( $agencia ) 
			{
				$this->addDetalhe( array( "Ag&ecirc;ncia", $agencia ) );
			}
			
			if( strlen( $banco ) < 3 )
			{
				$banco = str_repeat( "0", 3 - strlen( $banco ) ) . $banco;
			}
			if( $banco ) 
			{
				$this->addDetalhe( array( "Banco", $banco ) );
			}
			if( $dotacao_orcamentaria ) 
			{
				$this->addDetalhe( array( "Dota&ccedil;&atilde;o or&ccedil;ament&aacute;ria", $dotacao_orcamentaria ) );
			}
			if( $objetivo ) 
			{
				$this->addDetalhe( array( "Objetivo", $objetivo ) );
			}
			
			if( $data_pedido ) 
			{
				$this->addDetalhe( array( "Data Pedido", date( "d/m/Y", strtotime( substr( $data_pedido, 0, 16 ) ) ) ) );
			}
			
			if( $data_partida ) 
			{
				$this->addDetalhe( array( "Data Partida", date( "d/m/Y H:i", strtotime( substr( $data_partida, 0, 16 ) ) ) ) );
			}
			
			if( $data_chegada ) 
			{
				$this->addDetalhe( array( "Data Chegada", date( "d/m/Y H:i", strtotime( substr( $data_chegada, 0, 16 ) ) ) ) );
			}
			
			
			$estadual = ( $estadual ) ? "Sim": "N&atilde;o";
			$this->addDetalhe( array( "Estadual", $estadual ) );
			
			if( $destino ) 
			{
				$this->addDetalhe( array( "Destino", $destino ) );
			}
			
			$this->addDetalhe( array( "100%", number_format( $vl100, 2, ",", "." ) ) );
			$this->addDetalhe( array( "75%", number_format( $vl75, 2, ",", "." ) ) );
			$this->addDetalhe( array( "50%", number_format( $vl50, 2, ",", "." ) ) );
			$this->addDetalhe( array( "25%", number_format( $vl25, 2, ",", "." ) ) );
			$this->addDetalhe( array( "Total", "<span style=\"font-size: 12px;font-weight:bold;border-width:1px;border-color:#000000;border-style:solid;padding:2px;\">" . number_format( $vl25 + $vl50 + $vl75 + $vl100 , 2, ",", "." ) . "</span>" ) );
			
			if( $num_diaria ) 
			{
				$num_diaria = sprintf("%06d",$num_diaria);
				$this->addDetalhe( array( "Nº Di&aacute;ria", "{$num_diaria}/{$data['2']}" ) );
			}
			
			
			$this->url_editar = "diaria_cad.php?cod_diaria={$cod_diaria}";
			
			$this->array_botao[] = "Arquivo para impressão";
			$this->array_botao_url[] = "diaria_pdf.php?cod_diaria={$cod_diaria}";
		}
		else 
		{
			$this->addDetalhe( array( "Erro", "Codigo de diaria invalido" ) );
		}
		
		$this->url_novo = "diaria_cad.php";
		$this->url_cancelar = "diaria_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>