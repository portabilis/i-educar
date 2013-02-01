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


class clsSituacao
{
	var $cod_situacao;
	var $ref_cod_pessoa_auxiliar;
	var $ref_idpes;
	var $situacao;
		
	var $campos_lista;
	var $todos_campos;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsSituacao( $int_cod_situacao = null, $int_ref_cod_pessoa_auxiliar = null, $int_ref_idpes = null, $str_situacao = null )
	{
		
		if(is_numeric($int_cod_situacao))
		{
			$this->cod_situacao = $int_cod_situacao;
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
				$this->ref_idpes= $int_ref_idpes;
			}
		}
		
		if(is_string($str_situacao))
		{
			$this->situacao = $str_situacao;
		}
		
		$this->campos_lista = $this->todos_campos = "cod_situacao, ref_cod_pessoa_auxiliar, ref_idpes, situacao";
		
		$this->tabela = "pmiotopic.situacao";
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if($this->situacao && ($this->ref_idpes || $this->ref_cod_pessoa_auxiliar))
		{
			$campos = "situacao";
			$valoes = "'{$this->situacao}'";
			
			if($this->ref_cod_pessoa_auxiliar)
			{
				$campos .= ", ref_cod_pessoa_auxiliar";
				$valoes .= ", '{$this->ref_cod_pessoa_auxiliar}'";
			}
			elseif ($this->ref_idpes)
			{
				$campos .= ", ref_idpes";
				$valoes .= ", '{$this->ref_idpes}'";
			}
			
			$db = new clsBanco();
			$db->Consulta("INSERT INTO {$this->tabela} ( $campos ) VALUES ( $valoes )");
			return $db->InsertId("pmiotopic.situacao_cod_situacao_seq");
			
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
		if($this->cod_situacao)
		{
			$set = "";
			$virgula = "";
			
			if($this->situacao)
			{
				$set.= "situacao = '{$this->situacao}'";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE cod_situacao = '{$this->cod_situacao}'");
			return true;
		}
		return false;
	}
	
	function trocaTipo($cod_situacao, $idpes)
	{
		if($idpes && $cod_situacao)
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->tabela} SET ref_cod_pessoa_auxiliar='NULL', ref_idpes='{$idpes}' WHERE cod_situacao='{$cod_situacao}'");
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
		if($this->cod_situacao)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE cod_situacao = '{$this->cod_situacao}'");
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
	function lista( $int_ref_cod_pessoa_auxiliar = null, $int_ref_idpes = null, $str_situacao = null, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false )
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
			$where .= " $and ref_cod_pessoa_auxiliar IN ({$int_ref_cod_pessoa_auxiliar})";
			$and = " AND ";
		}
		
		if(is_numeric($int_ref_idpes))
		{
			$where .= " $and ref_idpes = '$int_ref_idpes'";
			$and = " AND ";
		}
		elseif (is_string($int_ref_idpes))
		{
			$where .= " $and ref_idpes IN ({$int_ref_idpes})";
			$and = " AND ";
		}
		
		if(is_string($str_situacao))
		{
			$where .= " $and situacao = '$str_situacao'";
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
		//echo "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit"."<br>";
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
		if($this->cod_situacao)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE cod_situacao = '{$this->cod_situacao}'" );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				
				//$this->ativo = $tupla['ativo'];
				
				return $tupla;
			}
		}
		return false;
	}
}
?>
