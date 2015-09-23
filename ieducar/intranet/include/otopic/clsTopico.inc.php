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


class clsTopico
{
	var $cod_topico;
	var $ref_idpes_cad;
	var $ref_cod_grupos_cad;
	var $ref_idpes_exc;
	var $ref_cod_grupos_exc;
	var $assunto;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	var $schema = "pmiotopic";
	var $tabela = "topico";

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsTopico( $int_cod_topico = false, $int_ref_idpes_cad = false, $int_ref_cod_grupos_cad = false, $int_ref_idpes_exc = false, $int_ref_cod_grupos_exc = false, $str_assunto = false, $int_ativo = false )
	{
		if(is_numeric($int_cod_topico))
		{
			$this->cod_topico = $int_cod_topico;
		}
		
		if(is_numeric($int_ref_idpes_cad))
		{
			$this->ref_idpes_cad = $int_ref_idpes_cad;
		}
		
		if(is_numeric($int_ref_cod_grupos_cad))
		{
			$this->ref_cod_grupos_cad = $int_ref_cod_grupos_cad;
		}		
		if(is_numeric($int_ref_idpes_exc))
		{
			$this->ref_idpes_exc = $int_ref_idpes_exc;
		}
		
		if(is_numeric($int_ref_cod_grupos_exc))
		{
			$this->ref_cod_grupos_exc = $int_ref_cod_grupos_exc;
		}
		
		if(is_string($str_assunto))
		{
			$this->assunto = $str_assunto;
		}
		
		if(is_numeric($int_ativo))
		{
			$this->ativo = $int_ativo;
		}
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificações de campos obrigatorios para inserção
		if( $this->ref_idpes_cad && $this->ref_cod_grupos_cad && $this->assunto )
		{
			$db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( ref_idpes_cad, ref_cod_grupos_cad, assunto, data_cadastro $campos ) VALUES ( '$this->ref_idpes_cad', '{$this->ref_cod_grupos_cad}', '{$this->assunto}', NOW() $valores )");
			return $db->InsertId("pmiotopic.topico_cod_topico_seq");
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
		if( $this->cod_topico && $this->ref_idpes_cad && $this->ref_cod_grupos_cad && $this->assunto)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET ref_idpes_cad = '{$this->ref_idpes_cad}', ref_cod_grupos_cad = '{$this->ref_cod_grupos_cad}', assunto = '{$this->assunto}', data_cadastro = NOW() WHERE cod_topico = '{$this->cod_topico}' ");
			return true;
		}
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui()
	{
		if( $this->cod_topico && $this->ref_cod_grupos_exc && $this->ref_idpes_exc)
		{
			$this->detalhe();
			$this->ativo++;
			
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET ref_idpes_exc = '{$this->ref_idpes_exc}', ref_cod_grupos_exc = '{$this->ref_cod_grupos_exc}', data_exclusao = NOW(), ativo = '$this->ativo' WHERE cod_topico = '$this->cod_topico' ");
					
			return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_idpes_cad = false, $int_ref_cod_grupos_cad = false, $str_assunto = false, $str_data_cadastro_ini = false, $str_data_cadastro_fim = false, $str_data_exclusao_ini = false, $str_data_exclusao_fim = false, $int_ativo = false, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false, $arrayint_idnotin =false, $arrayint_idin =false)
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if( is_numeric( $int_ref_idpes_cad) )
		{
			$where .= " $and ref_idpes_cad = '$int_ref_idpes_cad'";
			$and = " AND ";
		}		
		
		if( is_numeric( $int_ref_cod_grupos_cad) )
		{
			$where .= " $and ref_cod_grupos_cad = '$int_ref_cod_grupos_cad'";
			$and = " AND ";
		}		
		if( is_string( $str_assunto) )
		{
			$where .= " $and assunto ILIKE '%$str_assunto%'";
			$and = " AND ";
		}
		
		if( is_string( $str_data_cadastro_ini) )
		{
			$where .= " $and data_cadastro >= '$str_data_cadastro_ini' ";
			$and = " AND ";
		}	
		
		if( is_string( $str_data_cadastro_fim) )
		{
			$where .= " $and data_cadastro <= '$str_data_cadastro_fim' ";
			$and = " AND ";
		}
		
		if( is_string( $str_data_exclusao_ini) )
		{
			$where .= " $and data_exclusao >= '$str_data_exclusao_ini'";
			$and = " AND ";
		}
		
		if( is_string( $str_data_exclusao_fim) )
		{
			$where .= " $and data_exclusao >= '$str_data_exclusao_fim'";
			$and = " AND ";
		}
		
		if( is_numeric( $int_ativo) )
		{
			$where .= " $and ativo = '$int_ativo'";
			$and = " AND ";
		}
		if( is_array( $arrayint_idnotin ) )
		{
			$ok = true;
			foreach ( $arrayint_idnotin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$and}cod_topico NOT IN ( " . implode( ",", $arrayint_idnotin ) . " )";
				$and = " AND ";
			}
		}
		if( is_array( $arrayint_idin ) )
		{
			$ok = true;
			foreach ( $arrayint_idin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$and}cod_topico IN ( " . implode( ",", $arrayint_idin ) . " )";
				$and = " AND ";
			}
		}
		$orderBy = "";
		if( is_string( $str_order_by))
		{
			$orderBy = "ORDER BY $str_order_by";
		}
		
		if($where)
		{
			$where = " WHERE $where";
		}
		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->Consulta( "SELECT cod_topico, ref_idpes_cad, ref_cod_grupos_cad, assunto, data_cadastro, data_exclusao, ativo FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
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
		if( $this->cod_topico )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_topico, ref_idpes_cad, ref_cod_grupos_cad, assunto, data_cadastro, data_exclusao, ativo FROM {$this->schema}.{$this->tabela} WHERE  cod_topico = '{$this->cod_topico}'  " );
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
