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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/relatorio.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Relatorio" );
		$this->processoAp = "241";
	}
}

class indice extends clsCadastro
{
	var $edital;
	var $rel_edicao = false;

	function Inicializar()
	{
		if( is_numeric( $_GET["edital"] ) )
		{
			$this->rel_edicao = true;
			$this->Novo();
		}
		return "Novo";
	}

	function Gerar()
	{
		$db = new clsBanco();
		$db2 = new clsBanco();
		$this->titulo = "Editais - Empresas";

		if( ! $this->rel_edicao )
		{
			$lista = array();
			$db->Consulta( "SELECT MAX( cod_compras_editais_editais ) AS cod_compras_editais_editais, ref_cod_compras_licitacoes, MAX( versao ) AS versao, MAX( data_hora ) AS data_hora FROM compras_editais_editais GROUP BY ref_cod_compras_licitacoes ORDER BY ref_cod_compras_licitacoes DESC" );
			while ( $db->ProximoRegistro() )
			{
				list ( $cod_compras_editais_editais, $ref_cod_compras_licitacoes, $versao, $data_hora ) = $db->Tupla();
				$db2->Consulta( "SELECT numero, nm_modalidade FROM compras_licitacoes, compras_modalidade WHERE cod_compras_licitacoes = '{$ref_cod_compras_licitacoes}' AND ref_cod_compras_modalidade = cod_compras_modalidade" );
				$db2->ProximoRegistro();
				list( $numero_licitacao, $nm_modalidade ) = $db2->Tupla();
				$arr_modalidade = explode( " ", $nm_modalidade );
				$nm_modalidade = "";
				foreach ( $arr_modalidade AS $key => $valor )
				{
					$nm_modalidade .= substr( $valor, 0, 1 );
				}
				$lista[$ref_cod_compras_licitacoes] = "$numero_licitacao - $nm_modalidade - Versao: $versao";
			}
			asort($lista);
			reset($lista);
			if( count( $lista ) )
			{
				$this->campoLista( "edital", "Edital", $lista, $this->edital );
			}
			else
			{
				$this->campoRotulo( "aviso", "Aviso", "Nenhum edital disponivel" );
			}
		}
	}

	function Novo()
	{
		$db = new clsBanco();
		$db2 = new clsBanco();

		$edital = $_REQUEST["edital"];

		//echo $edital;

		$num_edital = $db->CampoUnico( "SELECT numero FROM compras_licitacoes WHERE cod_compras_licitacoes = '{$edital}'" );

		$titulo = ( $this->rel_edicao ) ? "Relatorio de empresas notificadas na alteracao do edital: $num_edital - data (" . date( "Y/m/d", time() ) . ")": "Relatorio de downloads, edital: $num_edital";
		$relatorio = new relatorios( $titulo );
		$conteudo = false;

		//$relatorio->novalinha( array( "Nome da empresa", " CNPJ", "e-mail" ), 0, 15, true );
		$db->Consulta( "SELECT ref_cod_compras_editais_empresa, ref_cod_compras_editais_editais, data_hora FROM compras_editais_editais_empresas WHERE ref_cod_compras_editais_editais IN ( SELECT cod_compras_editais_editais FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = '{$_REQUEST["edital"]}' )" );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_compras_editais_empresa, $cod_edital, $data_hora )= $db->Tupla();
			$db2->Consulta( "SELECT cnpj, nm_empresa, email, telefone, fax, cep, bairro, cidade, ref_sigla_uf, endereco, nome_contato FROM compras_editais_empresa WHERE cod_compras_editais_empresa = $cod_compras_editais_empresa" );
			$db2->ProximoRegistro();
			list( $cnpj, $nm_empresa, $email, $telefone, $fax, $cep, $bairro, $cidade, $ref_estado, $endereco, $nome_contato ) = $db2->Tupla();

			$db2->Consulta( "SELECT nm_estado FROM spdu_estado WHERE sigla = '$ref_estado'" );
			$db2->ProximoRegistro();
			list( $nm_estado ) = $db2->Tupla();

			$db2->Consulta( "SELECT versao FROM compras_editais_editais WHERE cod_compras_editais_editais = $cod_edital" );
			$db2->ProximoRegistro();
			list( $versao ) = $db2->Tupla();

			$relatorio->novalinha( array( "Empresa:", $nm_empresa ), 0, 13, true );
			$relatorio->novalinha( array( "CNPJ", $cnpj ) );
			$relatorio->novalinha( array( "e-mail", $email ) );
			$relatorio->novalinha( array( "Versao do edital", $versao) );
			$relatorio->novalinha( array( "Data", date( "d/m/Y H:i", strtotime( substr($data_hora,0,19) ) ) ) );
			$relatorio->novalinha( array( "Cidade", "$cidade - $nm_estado" ) );
			$relatorio->novalinha( array( "Endereco", "$bairro - $endereco - $cep" ) );
			$relatorio->novalinha( array( "Fone - Fax", "$telefone - $fax" ) );
			$relatorio->novalinha( array( "Contato", $nome_contato ) );
			$conteudo = true;
		}
		if( $conteudo )
		{
			$link = $relatorio->fechaPdf();
			$this->campoRotulo( "arquivo", "Arquivo", "<a href=\"$link\">$titulo</a>"  );
			return true;
		}
		else
		{
			$this->mensagem = "Nenhuma informa&ccedil;&atilde;o para este relat&oacute;rio.";
			$this->campoRotulo( "aviso", "Aviso", "Nenhuma informacao para este Edital" );
			return false;
		}
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>