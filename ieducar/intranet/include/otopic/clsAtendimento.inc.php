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


class clsAtendimento
{
	var $cod_atendimento; 	
	var $ref_ref_cod_pessoa_exc; 	
	var $ref_ref_cod_pessoa_cad; 	
	var $descricao; 	
	var $data_cadastro; 	
	var $data_exclusao; 	
	var $ativo;
	
	var $tabela = "pmiotopic.atendimento";

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsAtendimento( $int_cod_atendimento = false, $int_ref_ref_cod_pessoa_exc = false, $int_ref_ref_cod_pessoa_cad = false, $str_descricao = false, $str_data_cadastro = false, $str_data_exclusao = false, $int_ativo = false )
	{
		if(is_numeric($int_cod_atendimento))
		{
			$this->cod_atendimento = $int_cod_atendimento;
		}
		
		if(is_numeric($int_ref_ref_cod_pessoa_exc))
		{
			$this->ref_ref_cod_pessoa_exc = $int_ref_ref_cod_pessoa_exc;
		}
		
		if(is_numeric($int_ref_ref_cod_pessoa_cad))
		{
			$this->ref_ref_cod_pessoa_cad = $int_ref_ref_cod_pessoa_cad;
		}
		
		if(is_string($str_descricao))
		{
			$this->descricao = $str_descricao;
		}
		
		if(is_string($str_data_cadastro))
		{
			$this->data_cadastro = $str_data_cadastro;
		}
		
		if(is_string($str_data_exclusao))
		{
			$this->data_exclusao = $str_data_exclusao;
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
		if( $this->ref_ref_cod_pessoa_cad && $this->descricao)
		{
			$campos = "";
			$valores= "";
			$db->Consulta("INSERT INTO {$this->tabela} ( ref_ref_cod_pessoa_cad, descricao, data_cadastro, ativo$campos ) VALUES ( '{$this->ref_ref_cod_pessoa_cad}', '{$this->descricao}', 'NOW()', '1' )");
			return $db->InsertId("pmiotopic.atendimento_cod_atendimento_seq");
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
		if( $this->cod_atendimento && $this->ref_ref_cod_pessoa_cad && $this->descricao)
		{
			$setVirgula = "";
			$set = "ref_ref_cod_pessoa_cad = '{$this->ref_ref_cod_pessoa_cad}', descricao = '{$this->descricao}', data_cadastro = 'NOW()'";
			$setVirgula = ", ";

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE cod_atendimento = '{$this->cod_atendimento}' ");
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
		if($this->cod_atendimento && $this->ref_ref_cod_pessoa_exc)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET ativo='2' ref_ref_cod_pessoa_exc='{$this->ref_ref_cod_pessoa_exc}', data_exclusao='NOW()' WHERE cod_atendimento = '{$this->cod_atendimento}' ");
			return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_cod_atendimento = false, $int_ref_ref_cod_pessoa_exc = false, $int_ref_ref_cod_pessoa_cad = false, $str_descricao = false, $str_data_cadastro_ini = false, $str_data_cadastro_fim = false, $str_data_exclusao_ini = false, $str_data_exclusao_fim = false, $int_ativo = 1, $int_limite_ini = 0, $int_limite_qtd = 20, $str_order_by = false )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if( is_numeric( $int_cod_atendimento) )
		{
			$where .= " $and cod_atendimento  = '$int_cod_atendimento'";
			$and = " AND ";
		}
		
		if( is_numeric( $int_ref_ref_cod_pessoa_exc) )
		{
			$where .= " $and ref_ref_cod_pessoa_exc  = '$int_ref_ref_cod_pessoa_exc'";
			$and = " AND ";
		}
		
		if( is_numeric( $int_ref_ref_cod_pessoa_cad) )
		{
			$where .= " $and ref_ref_cod_pessoa_cad = '$int_ref_ref_cod_pessoa_cad'";
			$and = " AND ";
		}
			
		if( is_string( $str_descricao) )
		{
			$where .= " $and descricao ILIKE '%$str_descricao%'";
			$and = " AND ";
		}
		
		if( is_string( $str_data_cadastro_ini) )
		{
			$where .= " $and data_cadastro = '$str_data_cadastro_ini' ";
			$and = " AND ";
		}	
		
		if( is_string( $str_data_cadastro_fim) )
		{
			$where .= " $and data_cadastro <= '$str_data_cadastro_fim' ";
			$and = " AND ";
		}
		
		if( is_string( $str_data_exclusao_ini) )
		{
			$where .= " $and data_exclusao >= '$str_data_exclusao_ini' ";
			$and = " AND ";
		}	
		
		if( is_string( $str_data_exclusao_fim) )
		{
			$where .= " $and data_exclusao <= '$str_data_exclusao_fim' ";
			$and = " AND ";
		}
		
		if( is_numeric( $int_ativo) )
		{
			$where .= " $and ativo = '$int_ativo'";
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
		
		if($limit)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		$db->Consulta( "SELECT cod_atendimento, ref_ref_cod_pessoa_exc, ref_ref_cod_pessoa_cad, descricao, data_cadastro, data_exclusao, ativo FROM {$this->tabela} $where $orderBy $limit" );
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
		if( $this->cod_atendimento )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_atendimento, ref_ref_cod_pessoa_exc, ref_ref_cod_pessoa_cad, descricao, data_cadastro, data_exclusao, ativo FROM {$this->tabela} WHERE cod_atendimento = '{$this->cod_atendimento}'" );
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
