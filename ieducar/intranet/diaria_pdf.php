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
require_once ("include/relatorio.inc.php");

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
		$this->titulo = "Diaria - PDF";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_diaria = @$_GET['cod_diaria'];

		$db = new clsBanco();
		$db2 = new clsBanco();

		$db->Consulta( "SELECT ref_funcionario_cadastro, ref_cod_diaria_grupo, ref_funcionario, conta_corrente, agencia, banco,  dotacao_orcamentaria,  objetivo, data_partida, data_chegada, estadual, destino, data_pedido, vl100,  vl75, vl50, vl25, ref_cod_setor, num_diaria FROM pmidrh.diaria WHERE cod_diaria='{$cod_diaria}' AND ativo = 't'" );
		if( $db->ProximoRegistro() )
		{
			list( $ref_funcionario_cadastro, $ref_cod_diaria_grupo, $ref_funcionario, $conta_corrente, $agencia, $banco, $dotacao_orcamentaria, $objetivo, $data_partida, $data_chegada, $estadual, $destino, $data_pedido, $vl100, $vl75, $vl50, $vl25, $ref_cod_setor, $num_diaria ) = $db->Tupla();

			$altura_linhas = 15;
			$this->url_cancelar = "diaria_det.php?cod_diaria={$cod_diaria}";

			$num_diaria = sprintf("%06d",$num_diaria);
			$data = dataFromPgToBr( $data_pedido );
			$data = explode( "/", $data );
			
			$relatorio = new relatorios( "Detalhamento da Diária {$num_diaria}/{$data['2']}", 110, false, "SEGPOG - Departamento de Logística" );

			$relatorio->novalinha( array( "N°. do roteiro", "{$num_diaria}/{$data['2']}" ), 0, $altura_linhas );

			$nome_grupo = $db2->CampoUnico( "SELECT desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" );
			$relatorio->novalinha( array( "Grupo", $nome_grupo ), 0, $altura_linhas );

			$objPessoa = new clsPessoaFisica( $ref_funcionario );
			$detalhePessoa = $objPessoa->detalhe();
			$relatorio->novalinha( array( "Funcionário", $detalhePessoa["nome"] ), 0, $altura_linhas );
			$relatorio->novalinha( array( "CPF", int2CPF( $detalhePessoa["cpf"] ) ), 0, $altura_linhas );

			$objFuncionario = new clsFuncionario( $ref_funcionario );
			$detalheFuncionario = $objFuncionario->detalhe();
			$relatorio->novalinha( array( "Matricula", $detalheFuncionario["matricula"] ), 0, $altura_linhas );

			if( $ref_cod_setor )
			{
				$dba = new clsBanco();
				$nm_secretaria = $dba->CampoUnico(" SELECT nm_setor FROM pmidrh.setor WHERE cod_setor = {$ref_cod_setor}");
				
				$relatorio->novalinha( array( "Secretaria", $nm_secretaria ), 0, $altura_linhas );
			}

			$relatorio->novalinha( array( "Conta Corrente", $conta_corrente ), 0, $altura_linhas );
			if( strlen( $agencia ) < 5 )
			{
				$agencia = str_repeat( "0", 5 - strlen( $agencia ) ) . $agencia;
			}
			$relatorio->novalinha( array( "Agência", $agencia ), 0, $altura_linhas );

			if( strlen( $banco ) < 3 )
			{
				$banco = str_repeat( "0", 3 - strlen( $banco ) ) . $banco;
			}
			$relatorio->novalinha( array( "Banco", $banco ), 0, $altura_linhas );

			$relatorio->novalinha( array( "Dotação orçamentária", $dotacao_orcamentaria ), 0, $altura_linhas );
			$relatorio->novalinha( array( "Objetivo", $objetivo ), 0, 45 );

			$objPessoa = new clsPessoa_( $ref_funcionario_cadastro );
			$detalhePessoa = $objPessoa->detalhe();
			$relatorio->novalinha( array( "Ultimo Editor", $detalhePessoa["nome"] ), 0, $altura_linhas );

			$relatorio->novalinha( array( "Data Pedido", date( "d/m/Y", strtotime( substr( $data_pedido, 0, 16 ) ) ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "Data Partida", date( "d/m/Y H:i", strtotime( substr( $data_partida, 0, 16 ) ) ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "Data Chegada", date( "d/m/Y H:i", strtotime( substr( $data_chegada, 0, 16 ) ) ) ), 0, $altura_linhas );

			$estadual = ( $estadual ) ? "Sim": "Não";
			$relatorio->novalinha( array( "Estadual", $estadual ), 0, $altura_linhas );

			$relatorio->novalinha( array( "Destino", $destino ), 0, $altura_linhas );

			$relatorio->novalinha( array( "100%", number_format( $vl100, 2, ",", "." ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "75%", number_format( $vl75, 2, ",", "." ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "50%", number_format( $vl50, 2, ",", "." ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "25%", number_format( $vl25, 2, ",", "." ) ), 0, $altura_linhas );
			$relatorio->novalinha( array( "Total", number_format( $vl25 + $vl50 + $vl75 + $vl100 , 2, ",", "." ) ), 0, $altura_linhas, true );

			$link = $relatorio->fechaPdf();
			$this->addDetalhe( array( "Arquivo", "<a href=\"{$link}\">{$link}</a>" ) );

			header( "location: {$link}" );
			die();
		}
		else
		{
			$this->url_cancelar = "diaria_lst.php";
			$this->addDetalhe( array( "Erro", "Codigo de diaria invalido" ) );
		}

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>