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
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");

class clsNotas
{
	var $sequencial;
	var $ref_idpes;
	var $ref_pessoa_exc;
	var $ref_pessoa_cad;
	var $nota;
	var $ativo;
	var $data_cadastro;
	var $data_exclusao;
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object:clsnatureza
	 */
	function clsNotas( $int_ref_idpes = false, $int_ref_pessoa_cad = false, $int_ref_pessoa_exc = false, $str_nota = false,  $ativo=false, $sequencial = false )
	{
		$this->ref_idpes = is_numeric($int_ref_idpes) ? $int_ref_idpes : false;
		$this->ref_pessoa_cad = is_numeric($int_ref_pessoa_cad) ? $int_ref_pessoa_cad : false;
		$this->ref_pessoa_exc = is_numeric($int_ref_pessoa_exc) ? $int_ref_pessoa_exc : false;
		$this->nota = is_string($str_nota) ? $str_nota : false;
		$this->ativo = is_numeric($ativo) ? $ativo : false;
		$this->sequencial = is_numeric($sequencial) ? $sequencial: false;
		
		$this->tabela = "pmiotopic.notas";
	}
	
	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificacoes de campos obrigatorios para insercao
		if( $this->ref_idpes && $this->ref_pessoa_cad && $this->nota )
		{
			$db->Consulta( "SELECT MAX(sequencial) as seq FROM {$this->tabela} WHERE ref_idpes = $this->ref_idpes GROUP BY ref_idpes" );
			if($db->ProximoRegistro())
			{
				list($this->sequencial) = $db->Tupla();
				$this->sequencial++;
			}else 
			{
				$this->sequencial = 1;
			}
			$db->Consulta( "INSERT INTO {$this->tabela} (ref_idpes,sequencial, ref_pessoa_cad, nota, data_cadastro ) VALUES ( '{$this->ref_idpes}', '{$this->sequencial}', '{$this->ref_pessoa_cad}', '{$this->nota}', NOW() )" );
			// Retorna Id cadastrado
			return $this->ref_idpes;
		}
		return false;
	}
	
	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{
		// verifica campos obrigatorios para edicao
		if( $this->ref_idpes && $this->ref_pessoa_cad && $this->nota && $this->sequencial)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET ref_pessoa_cad = '{$this->ref_pessoa_cad}', nota = '{$this->nota}' WHERE ref_idpes = '{$this->ref_idpes}' AND sequencial = '{$this->sequencial}' " );
			return true;
		}
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui( )
	{
		// verifica se existe um ID definido para delecao
		if( $this->ref_idpes && $this->ref_pessoa_exc && $this->sequencial  )
		{
				$db = new clsBanco();
				$this->detalhe();
				$this->ativo++;

				$db->Consulta("UPDATE $this->tabela SET ativo='{$this->ativo}', data_exclusao=NOW(), ref_pessoa_exc = '$this->ref_pessoa_exc' WHERE ref_idpes = '{$this->ref_idpes}' AND sequencial = '{$this->sequencial}' ");
				return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_idpes = false, $int_ref_pessoa_cad = false, $str_ordenacao = false, $date_cadastro_ini=false, $date_cadastro_fim=false, $int_ativo=1, $date_exclusao_ini=false, $date_exclusao_fim=false, $int_limite_ini = false, $int_limite_qtd = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $int_ref_pessoa_cad ) )
		{
			$where .= "{$whereAnd}ref_pessoa_cad = '$int_ref_pessoa_cad'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_pessoa_exc ) )
		{
			$where .= "{$whereAnd}ref_pessoa_exc = '$int_ref_pessoa_exc'";
			$whereAnd = " AND ";
		}		
		
		if( is_numeric( $int_ref_idpes ) )
		{
			$where .= "{$whereAnd}ref_idpes = '$int_ref_idpes'";
			$whereAnd = " AND ";
		}
		
		if( is_string( $date_cadastro_ini ) )
		{
			$where .= "{$whereAnd}data_cadastro >= '$date_cadastro_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_cadastro_fim ) )
		{
			$where .= "{$whereAnd}data_cadastro <= '$date_cadastro_fim'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_exclusao_ini ) )
		{
			$where .= "{$whereAnd}data_exclusao >= '$date_exclusao_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_exclusao_fim ) )
		{
			$where .= "{$whereAnd}data_exclusao <= '$date_exclusao_fim'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ativo ) )
		{
			$where .= "{$whereAnd}ativo = '$int_ativo'";
			$whereAnd = " AND ";
		}
		
		$orderBy = "";
		if( is_string( $str_ordenacao ) )
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		// Seleciona o total de registro da tabela
		$db = new clsBanco();
		$total = $db->CampoUnico( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );

		$db->Consulta( "SELECT ref_idpes, ref_pessoa_cad, ref_pessoa_exc, nota, data_cadastro, data_exclusao, ativo, sequencial FROM {$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			$tupla["total"] = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	} 
	
	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->ref_idpes && $this->sequencial)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT ref_idpes, ref_pessoa_cad, ref_pessoa_exc, nota, data_cadastro, data_exclusao, ativo, sequencial FROM {$this->tabela} WHERE ref_idpes = '$this->ref_idpes' AND sequencial = '$this->sequencial' ");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->ativo = $tupla['ativo'];
				return $tupla;
			}
		}
		return false;
	}
}
?>
