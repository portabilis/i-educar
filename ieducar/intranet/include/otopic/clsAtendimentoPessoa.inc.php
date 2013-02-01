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


class clsAtendimentoPessoa
{
	var $ref_cod_atendimento;
	var $ref_ref_cod_pessoa_fj;
	var $master;
	
	var $tabela = "pmiotopic.atendimento_pessoa";

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsAtendimentoPessoa( $int_ref_cod_atendimento = false, $int_ref_ref_cod_pessoa_fj = false, $int_master = false )
	{
		if(is_numeric($int_ref_cod_atendimento))
		{
			$this->ref_cod_atendimento = $int_ref_cod_atendimento;
		}
		
		if(is_numeric($int_ref_ref_cod_pessoa_fj))
		{
			$this->ref_ref_cod_pessoa_fj = $int_ref_ref_cod_pessoa_fj;
		}
		if($int_master == 0 || $int_master == 1)
		{
			$this->master = $int_master;
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
		// verificações de campos obrigatorios para inserï¿½ï¿½o
		if( $this->ref_cod_atendimento && $this->ref_ref_cod_pessoa_fj && $this->master!==false )
		{
			$db->Consulta("INSERT INTO {$this->tabela} ( ref_cod_atendimento, ref_ref_cod_pessoa_fj, master ) VALUES ( '$this->ref_cod_atendimento', '{$this->ref_ref_cod_pessoa_fj}', '{$this->master}')");
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
		if( $this->ref_cod_atendimento && $this->ref_ref_cod_pessoa_fj && $this->master )
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET master = '{$this->master}' WHERE ref_cod_atendimento = {$this->ref_cod_atendimento} AND ref_ref_cod_pessoa_fj={$this->ref_ref_cod_pessoa_fj}");
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
		if( $this->ref_cod_atendimento && $this->ref_ref_cod_pessoa_fj )
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE ref_cod_atendimento = {$this->ref_cod_atendimento} AND ref_ref_cod_pessoa_fj={$this->ref_ref_cod_pessoa_fj}");
					
			return true;
		}
		return false;
	}
	
	function excluiTodos()
	{
		if( $this->ref_cod_atendimento )
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE ref_cod_atendimento = {$this->ref_cod_atendimento} AND master={$this->master}");
					
			return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_cod_atendimento = false, $int_ref_ref_cod_pessoa_fj = false, $int_master = false, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false)
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if( is_numeric( $int_ref_cod_atendimento) )
		{
			$where .= " $and ref_cod_atendimento = '$int_ref_cod_atendimento'";
			$and = " AND ";
		}		
		
		if( is_numeric( $int_ref_ref_cod_pessoa_fj) )
		{
			$where .= " $and ref_ref_cod_pessoa_fj = '$int_ref_ref_cod_pessoa_fj'";
			$and = " AND ";
		}		
		
		if( $int_master==0 || $int_master==1)
		{
			if(is_numeric($int_master))
			{
				$where .= " $and master = '$int_master'";
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
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		//echo ( "SELECT ref_cod_atendimento, ref_ref_cod_pessoa_fj, master FROM {$this->tabela} $where $orderBy $limit" );
		//die();
		$db->Consulta( "SELECT ref_cod_atendimento, ref_ref_cod_pessoa_fj, master FROM {$this->tabela} $where $orderBy $limit" );
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
		if( $this->ref_cod_atendimento && $this->ref_ref_cod_pessoa_fj )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_cod_atendimento, ref_ref_cod_pessoa_fj, master FROM {$this->tabela} WHERE  ref_cod_atendimento = '{$this->ref_cod_atendimento}' AND ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' " );
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
