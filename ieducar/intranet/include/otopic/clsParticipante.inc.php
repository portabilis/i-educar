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


class clsParticipante
{
	var $ref_ref_idpes;
	var $ref_ref_cod_grupos;
	var $ref_cod_reuniao;
	var $sequencial;
	var $data_chegada;
	var $data_saida;
	
	var $schema = "pmiotopic";
	var $tabela = "participante";

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsParticipante( $int_ref_ref_idpes = false, $int_ref_ref_cod_grupos = false, $int_ref_cod_reuniao = false, $int_sequencial = false, $date_data_chegada = false, $date_data_saida = false )
	{
		if(is_numeric($int_ref_ref_idpes))
		{
			$objFuncionario = new clsPessoaFisica($int_ref_ref_idpes);
			if($objFuncionario->detalhe())
			{
			   $this->ref_ref_idpes = $int_ref_ref_idpes;
			}
		}
		
		if(is_numeric($int_ref_ref_cod_grupos))
		{
			$objGrupos = new clsGrupos($int_ref_ref_cod_grupos);
			if($objGrupos->detalhe())
			{
			   $this->ref_ref_cod_grupos = $int_ref_ref_cod_grupos;
			}
		}
		
		if(is_numeric($int_ref_cod_reuniao))
		{
			$obj= new clsReuniao($int_ref_cod_reuniao);
			if($obj->detalhe())
			{
			   $this->ref_cod_reuniao = $int_ref_cod_reuniao;
			}
		}
		$this->data_chegada = is_string($date_data_chegada) ? $date_data_chegada : false;
		$this->data_saida = is_string($date_data_saida) ? $date_data_saida: false;
		$this->sequencial = is_numeric($int_sequencial) ? $int_sequencial : false;
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificações de campos obrigatorios para inserï¿½ï¿½o
		if( $this->ref_ref_idpes && $this->ref_ref_cod_grupos && $this->ref_cod_reuniao && $this->data_chegada)
		{
			$campos = "";
			$valores = "";
			
			$this->sequencial = $db->UnicoCampo("SELECT MAX(sequencial) as seq FROM $this->schema.$this->tabela WHERE ref_ref_cod_grupos = $this->ref_ref_cod_grupos AND ref_ref_idpes = $this->ref_ref_idpes AND ref_cod_reuniao = $this->ref_cod_reuniao GROUP BY ref_cod_reuniao, ref_ref_idpes, ref_ref_cod_grupos");
			if($this->sequencial)
			{
				$this->sequencial++;
			}else 
			{
				$this->sequencial = 1;
			}
			
			if($this->data_saida)
			{
				$campos .= ", data_saida";
				$valores .= ", '$this->data_saida'";
			}
			$db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( ref_ref_idpes, ref_ref_cod_grupos, ref_cod_reuniao, sequencial, data_chegada$campos ) VALUES ( '$this->ref_ref_idpes', '{$this->ref_ref_cod_grupos}', '$this->ref_cod_reuniao', '{$this->sequencial}', '$this->data_chegada'$valores )");
			return true;
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
		if( $this->ref_ref_idpes && $this->ref_ref_cod_grupos && $this->ref_cod_reuniao && $this->sequencial && $this->data_saida)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET  data_saida = '{$this->data_saida}' WHERE ref_ref_idpes = '{$this->ref_ref_idpes}' AND ref_ref_cod_grupos = '{$this->ref_ref_cod_grupos}' AND sequencial = '$this->sequencial' AND ref_cod_reuniao = '$this->ref_cod_reuniao' ");
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
		
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_ref_idpes = false, $int_ref_ref_cod_grupos = false, $int_ref_cod_reuniao = false, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if( is_numeric( $int_ref_ref_idpes) )
		{
			$where .= " $and ref_ref_idpes = '$int_ref_ref_idpes'";
			$and = " AND ";
		}		
		
		if( is_numeric( $int_ref_ref_cod_grupos) )
		{
			$where .= " $and ref_ref_cod_grupos = '$int_ref_ref_cod_grupos'";
			$and = " AND ";
		}		
		
		if( is_numeric( $int_ref_cod_reuniao) )
		{
			$where .= " $and ref_cod_reuniao = '$int_ref_cod_reuniao'";
			$and = " AND ";
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
		
		if($int_limite_ini && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->Consulta( "SELECT ref_ref_idpes, ref_ref_cod_grupos, sequencial, ref_cod_reuniao, data_chegada, data_saida FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
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

		if( $this->ref_ref_idpes && $this->ref_ref_cod_grupos && $this->ref_cod_reuniao && $this->sequencial )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_ref_idpes, ref_ref_cod_grupos, sequencial, ref_cod_reuniao, data_chegada, data_saida  FROM {$this->schema}.{$this->tabela} WHERE ref_ref_idpes = '{$this->ref_ref_idpes}' AND ref_ref_cod_grupos = '{$this->ref_ref_cod_grupos}' AND sequencial = '$this->sequencial' AND ref_cod_reuniao = '$this->ref_cod_reuniao' " );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		return false;
	}
}
?>
