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


class clsPessoaAuxiliarTelefone
{
	var $ref_cod_pessoa_auxiliar;
	var $ddd;
	var $fone;
		
	var $campos_lista;
	var $todos_campos;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsPessoaAuxiliarTelefone( $int_ref_cod_pessoa_auxiliar = null, $int_ddd = null, $int_fone = null)
	{
		if(is_numeric($int_ref_cod_pessoa_auxiliar))
		{
			$obj_pessoa_auxiliar = new clsPessoaAuxiliar($int_ref_cod_pessoa_auxiliar);
			if($obj_pessoa_auxiliar->detalhe())
			{
				$this->ref_cod_pessoa_auxiliar = $int_ref_cod_pessoa_auxiliar;
			}
		}
		
		if(is_numeric($int_ddd))
		{
			$this->ddd = $int_ddd;
		}
		
		if(is_numeric($int_fone))
		{
			$this->fone = $int_fone;
		}

		$this->campos_lista = $this->todos_campos = "ref_cod_pessoa_auxiliar, ddd, fone";
		
		$this->tabela = "pmiotopic.pessoa_auxiliar_telefone";
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
		
		if($this->ref_cod_pessoa_auxiliar && $this->ddd && $this->fone)
		{
			if(!$this->detalhe())
			{
				$campos = "ref_cod_pessoa_auxiliar, ddd, fone";
				$valoes = "'{$this->ref_cod_pessoa_auxiliar}', '{$this->ddd}', '{$this->fone}'";
				
				$db = new clsBanco();
				$db->Consulta("INSERT INTO {$this->tabela} ( $campos ) VALUES ( $valoes )");
				return true;
			}
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
		if($this->ref_cod_pessoa_auxiliar && $this->ddd && $this->fone)
		{
			$set = "ddd = '{$this->ddd}', fone = '{$this->fone}'";
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}' AND ddd = '{$this->ddd}' AND fone = '{$this->fone}'");
			return true;
		}
		return false;
	}
	
	/**
	 * Remove todos os registros pertencentes a uma pessoa
	 *
	 * @return bool
	 */
	function excluiTodos( )
	{
		// verifica se existe um ID definido para delecao
		if($this->ref_cod_pessoa_auxiliar)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}'");
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
		if($this->ref_cod_pessoa_auxiliar && $this->ddd && $this->fone)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}' AND ddd = '{$this->ddd}' AND fone = '{$this->fone}'");
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
	function lista( $int_ref_cod_pessoa_auxiliar = false, $int_ddd = false, $int_fone = false, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false )
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
			$where .= " $and ref_cod_pessoa_auxiliar IN ({$int_ref_cod_pessoa_auxiliar})'";
			$and = " AND ";
		}
		
		if(is_numeric($int_ddd))
		{
			$where .= " $and ddd = '$int_ddd'";
			$and = " AND ";
		}
		
		if(is_numeric($int_fone))
		{
			$where .= " $and fone = '$int_fone'";
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
		
		//echo "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit"; die();
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
		if($this->ref_cod_pessoa_auxiliar && $this->ddd && $this->fone)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE ref_cod_pessoa_auxiliar = '{$this->ref_cod_pessoa_auxiliar}' AND ddd = '{$this->ddd}' AND fone = '{$this->fone}'" );
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
