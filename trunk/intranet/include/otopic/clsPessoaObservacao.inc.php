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


class clsPessoaObservacao
{
	var $cod_pessoa_observacao;
	var $ref_cod_pessoa_auxiliar;
	var $ref_idpes;
	var $obs;
	var $data_edicao;
		
	var $campos_lista;
	var $todos_campos;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsPessoaObservacao( $int_cod_pessoa_observacao = null, $int_ref_cod_pessoa_auxiliar = null, $int_ref_idpes = null, $str_obs = null, $str_data_edicao = null )
	{
		if(is_numeric($int_cod_pessoa_observacao))
		{
			$this->cod_pessoa_observacao = $int_cod_pessoa_observacao;
		}
		
		if(is_numeric($int_ref_cod_pessoa_auxiliar))
		{
			$obj_pessoa_auxiliar = new clsPessoaAuxiliar($int_ref_cod_pessoa_auxiliar);
			if($obj_pessoa_auxiliar->detalhe())
			{
				$this->ref_cod_pessoa_auxiliar = $int_ref_cod_pessoa_auxiliar;
			}
		}
		
		if(is_numeric($int_ref_idpes))
		{
			$obj_pessoa = new clsPessoa_($int_ref_idpes);
			if($obj_pessoa->detalhe())
			{
				$this->ref_idpes = $int_ref_idpes;
			}
		}
		
		if(is_string($str_obs))
		{
			$this->obs = $str_obs;
		}
		
		if(is_string($str_data_edicao))
		{
			$this->data_edicao = $str_data_edicao;
		}

		$this->campos_lista = $this->todos_campos = "cod_pessoa_observacao, ref_cod_pessoa_auxiliar, ref_idpes, obs, data_edicao";
		
		$this->tabela = "pmiotopic.pessoa_observacao";
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$campos = "";
		$valoes = "";
		$virgula = "";

		if($this->ref_cod_pessoa_auxiliar || $this->ref_idpes)
		{
			if($this->ref_cod_pessoa_auxiliar)
			{
				$campos .= "ref_cod_pessoa_auxiliar";
				$valoes .= "'{$this->ref_cod_pessoa_auxiliar}'";
				$virgula = ",";
			}
			elseif ($this->ref_idpes)
			{
				$campos .= "$virgula ref_idpes";
				$valoes .= "$virgula '{$this->ref_idpes}'";
				$virgula = ",";
			}
			
			if($this->obs)
			{
				$campos .= "$virgula obs";
				$valoes .= "$virgula '{$this->obs}'";
				$virgula = ",";
			}
			
			if($this->data_edicao)
			{
				$campos .= "$virgula data_edicao";
				$valoes .= "$virgula '{$this->data_edicao}'";
				$virgula = ",";
			}
			
			$db = new clsBanco();
			$db->Consulta("INSERT INTO {$this->tabela} ( $campos ) VALUES ( $valoes )");
			return $db->InsertId("pmiotopic.pessoa_observacao_cod_pessoa_observacao_seq");
			
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
		if($this->cod_pessoa_observacao)
		{
			$set = "";
			$virgula = "";
			
			if($this->ref_cod_pessoa_auxiliar)
			{
				$set .= "ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}'";
				$virgula = ",";
			}
			elseif ($this->ref_idpes)
			{
				$set .= "$virgula ref_idpes = '{$this->ref_idpes}'";
				$virgula = ",";
			}
			
			if($this->obs)
			{
				$set .= "$virgula obs = '{$this->obs}'";
				$virgula = ",";
			}
			
			if($this->data_edicao)
			{
				$set .= "$virgula data_edicao = '{$this->data_edicao}'";
				$virgula = ",";
			}
			
			$db = new clsBanco();
//			echo "UPDATE {$this->tabela} SET $set WHERE cod_pessoa_observacao = '{$this->cod_pessoa_observacao}'"; die();
			$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE cod_pessoa_observacao = '{$this->cod_pessoa_observacao}'");
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
		// verifica se existe um ID definido para delecao
		if($this->cod_pessoa_observacao)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE cod_pessoa_observacao = '{$this->cod_pessoa_observacao}'");
			return true;
		}
		return false;
	}
	
	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setcampos_lista($str_campos)
	{
		$this->campos_lista = $str_campos;
	}
	
	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetcampos_lista()
	{
		$this->campos_lista = $this->todos_campos;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_cod_pessoa_auxiliar = null, $int_ref_idpes = null, $str_obs = null, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false, $str_data_edicao_ini = false, $str_data_edicao_fim = false )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if(is_numeric($int_ref_cod_pessoa_auxiliar))
		{
			$where .= " $and ref_cod_pessoa_auxiliar = '$int_ref_cod_pessoa_auxiliar'";
			$and = " AND ";
		}
		elseif (is_string($int_ref_cod_pessoa_auxiliar))
		{
			$where .= " $and ref_cod_pessoa_auxiliar IN ($int_ref_cod_pessoa_auxiliar)";
			$and = " AND ";
		}
		
		if(is_numeric($int_ref_idpes))
		{
			$where .= " $and ref_idpes = '$int_ref_idpes'";
			$and = " AND ";
		}
		elseif(is_string($int_ref_idpes))
		{
			$where .= " $and ref_idpes IN ($int_ref_idpes)";
			$and = " AND ";
		}
		
		if(is_string($str_obs))
		{
			$where .= " $and obs ILIKE '%$str_obs%'";
			$and = " AND ";
		}
		
		if(is_string($str_data_edicao_ini))
		{
			if(!$str_data_edicao_fim)
			{
				$where .= "{$and} data_edicao >= '$str_data_edicao_ini 00:00:00' AND data_edicao <= '$str_data_edicao_ini 23:59:59'";
				$and = " AND ";
			}
			else
			{ 
				$where .= "{$and}data_edicao >= '$str_data_edicao_ini'";
				$and = " AND ";
			}
		}
		
		if(is_string($str_data_edicao_fim))
		{
			$where .= "{$and}data_edicao <= '$str_data_edicao_fim'";
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
		
		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		
//		echo "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit"; die();
		$db->Consulta( "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit" );
		
		$resultado = array();
		$countCampos = count( explode( ",", $this->campos_lista ) );
		
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			
			if($countCampos > 1 )
			{
				$tupla["total"] = $total;
				$resultado[] = $tupla;
			}
			else 
			{
				$resultado[] = $tupla["$this->campos_lista"];
			}
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
		if($this->cod_pessoa_observacao)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE cod_pessoa_observacao = '{$this->cod_pessoa_observacao}'" );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				
				$this->ativo = $tupla['ativo'];
				
				return $tupla;
			}
		}elseif($this->ref_idpes){
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE ref_idpes = '{$this->ref_idpes}'" );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				
				$this->ativo = $tupla['ativo'];
				
				return $tupla;
			}
		}elseif ($this->ref_cod_pessoa_auxiliar)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}'" );
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
