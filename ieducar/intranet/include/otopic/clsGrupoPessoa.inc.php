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

class clsGrupoPessoa
{	
	var $ref_idpes;
	var $ref_cod_grupos;
	var $ref_pessoa_exc;
	var $ref_grupos_exc;
	var $ref_pessoa_cad;
	var $ref_grupos_cad;
	var $ativo;
	var $data_cadastro;
	var $data_exclusao;
	var $ref_cod_auxiliar_cad;
	var $ref_ref_cod_atendimento_cad;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object:clsnatureza
	 */
	function clsGrupoPessoa( $int_ref_idpes = false, $int_ref_cod_grupos = false, $int_ref_pessoa_cad = false, $int_ref_pessoa_exc = false, $int_ref_grupos_cad = false, $int_ref_grupos_exc = false, $ativo=1, $int_ref_cod_auxiliar_cad = false, $int_ref_ref_cod_antendimento_cad = false )
	{
		$this->ref_idpes = is_numeric($int_ref_idpes) ? $int_ref_idpes : false;
		$this->ref_cod_grupos = is_numeric($int_ref_cod_grupos) ? $int_ref_cod_grupos : false;
		$this->ref_pessoa_cad = is_numeric($int_ref_pessoa_cad) ? $int_ref_pessoa_cad : false;
		$this->ref_grupos_cad = is_numeric($int_ref_grupos_cad) ? $int_ref_grupos_cad : false;
		$this->ref_pessoa_exc = is_numeric($int_ref_pessoa_exc) ? $int_ref_pessoa_exc : false;
		$this->ref_grupos_exc = is_numeric($int_ref_grupos_exc) ? $int_ref_grupos_exc : false;
		$this->ativo = is_numeric($ativo) ? $ativo : false;
		$this->data_cadastro = is_string($data_cadastro) ? $data_cadastro : false;
		$this->data_exclusao = is_string($data_exclusao) ? $data_exclusao : false;
		$this->ref_cod_auxiliar_cad = is_numeric($int_ref_cod_auxiliar_cad) ? $int_ref_cod_auxiliar_cad : false;
		$this->ref_ref_cod_atendimento_cad = is_numeric($int_ref_ref_cod_antendimento_cad) ? $int_ref_ref_cod_antendimento_cad : false;
		
		$this->tabela = "pmiotopic.grupopessoa";
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
		if( $this->ref_idpes && ( ($this->ref_pessoa_cad && $this->ref_grupos_cad) || ($this->ref_cod_auxiliar_cad && $this->ref_ref_cod_atendimento_cad) ) && $this->ref_cod_grupos )
		{
			$campos = "";
			$valores = "";
			
			if($this->ref_pessoa_cad && $this->ref_grupos_cad)
			{
				$campos .= ", ref_pessoa_cad, ref_grupos_cad";
				$valores .= ", '$this->ref_pessoa_cad', '$this->ref_grupos_cad'";
			}
			elseif($this->ref_cod_auxiliar_cad && $this->ref_ref_cod_atendimento_cad)
			{
				$campos .= ", ref_cod_auxiliar_cad, ref_ref_cod_atendimento_cad";
				$valores .= ", $this->ref_cod_auxiliar_cad, $this->ref_ref_cod_atendimento_cad"; 			
			}			
		
			
			$db->Consulta( "INSERT INTO {$this->tabela} (ref_idpes,ref_cod_grupos, data_cadastro, ativo $campos) VALUES ( '{$this->ref_idpes}', '{$this->ref_cod_grupos}', NOW(), '$this->ativo'$valores )" );
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
		if( $this->ref_idpes && $this->ref_cod_grupos && $this->ativo && ( ($this->ref_pessoa_cad && $this->ref_grupos_cad) || ($this->ref_cod_auxiliar_cad && $this->ref_ref_cod_atendimento_cad) ))
		{
			$set = "";
			
			if($this->ref_pessoa_cad && $this->ref_grupos_cad)
			{
				$set .= ", ref_pessoa_cad = '{$this->ref_pessoa_cad}', ref_grupos_cad = '{$this->ref_grupos_cad}'";
			}
			elseif($this->ref_cod_auxiliar_cad && $this->ref_ref_cod_atendimento_cad)
			{
				$set .= ", ref_cod_auxiliar_cad = '{$this->ref_cod_auxiliar_cad}', ref_ref_cod_atendimento_cad = '{$this->ref_ref_cod_atendimento_cad}'";
			}
			
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET  data_cadastro= NOW(), ativo = '{$this->ativo}'$set WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_grupos = '{$this->ref_cod_grupos}'");
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
		if( $this->ref_idpes && $this->ref_pessoa_exc && $this->ref_cod_grupos  )
		{
			$db = new clsBanco();
			$this->detalhe();
			$this->ativo++;
			$db->Consulta("UPDATE $this->tabela SET ativo=2, data_exclusao=NOW(), ref_pessoa_exc = '$this->ref_pessoa_exc' WHERE ref_idpes = '{$this->ref_idpes}' AND ref_cod_grupos = '{$this->ref_cod_grupos}' ");
			return true;
		}
		return false;
	}
	
	function exclui_todos( )
	{
		// verifica se existe um ID definido para delecao
		if( $this->ref_cod_grupos && $this->ref_pessoa_exc  )
		{
			$db = new clsBanco();
			$this->detalhe();
			$this->ativo++;
			$db->Consulta("UPDATE $this->tabela SET ativo=2, data_exclusao=NOW(), ref_pessoa_exc = '$this->ref_pessoa_exc' WHERE ref_cod_grupos = '{$this->ref_cod_grupos}' ");
			return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_idpes = false, $int_ref_cod_grupos = false, $str_ordenacao = false, $int_ativo=1, $int_limite_ini = false, $int_limite_qtd = false, $int_ref_cod_auxiliar_cad = false, $int_ref_ref_cod_atendimento_cad = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $int_ref_idpes) )
		{
			$where .= "{$whereAnd}ref_idpes = '$int_ref_idpes'";
			$whereAnd = " AND ";
		}
		elseif (is_array($int_ref_idpes))
		{
			foreach ($int_ref_idpes as $id)
			{
				$where .= "{$whereAnd}ref_idpes = '$id'";
				$whereAnd = " AND ";
			}
		}
		
		if( is_numeric( $int_ref_cod_grupos) )
		{
			$where .= "{$whereAnd}ref_cod_grupos = '$int_ref_cod_grupos'";
			$whereAnd = " AND ";
		}		
		
		if( is_numeric( $int_ativo ) )
		{
			$where .= "{$whereAnd}ativo = '$int_ativo'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_auxiliar_cad ))
		{
			$where .= "{$whereAnd}ref_cod_auxiliar_cad = '$int_ref_cod_auxiliar_cad'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_atendimento_cad ))
		{
			$where .= "{$whereAnd}ref_ref_cod_atendimento_cad = '$int_ref_ref_cod_atendimento_cad'";
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

		//echo "SELECT ref_idpes, ref_cod_grupos, ref_pessoa_cad, ref_pessoa_exc, ref_grupos_cad, ref_grupos_exc, data_cadastro, data_exclusao, ativo FROM {$this->tabela} $where $orderBy $limit"; die();
		$db->Consulta( "SELECT ref_idpes, ref_cod_grupos, ref_pessoa_cad, ref_pessoa_exc, ref_grupos_cad, ref_grupos_exc, data_cadastro, data_exclusao, ativo, ref_cod_auxiliar_cad, ref_ref_cod_atendimento_cad FROM {$this->tabela} $where $orderBy $limit" );
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
		if($this->ref_idpes && $this->ref_cod_grupos)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT ref_idpes, ref_cod_grupos, ref_pessoa_cad, ref_pessoa_exc, ref_grupos_cad, ref_grupos_exc, data_cadastro, data_exclusao, ativo, ref_cod_auxiliar_cad, ref_ref_cod_atendimento_cad FROM {$this->tabela} WHERE ref_idpes = '$this->ref_idpes' AND ref_cod_grupos = '$this->ref_cod_grupos' ");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->ativo = $tupla['ativo'];
				return $tupla;
			}
		}
		return false;
	}
	
	function meusGrupos ($int_ref_idpes , $str_ordenacao = false, $int_ativo=1, $int_limite_ini = false, $int_limite_qtd = false, $array_isin = false )
	{
		$orderBy = "";
		if( is_string( $str_ordenacao ) )
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		if(is_array($array_isin) )
		{
			$WHERE .= " AND ref_cod_grupos in (".implode($array_isin).")";
		}
		if( is_numeric( $int_ativo ) )
		{
			$WHERE .= " AND ativo = $int_ativo";
		}
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		// Seleciona o total de registro da tabela
		$db = new clsBanco();
		$db->Consulta( " (select ref_cod_grupos, 1 as tipo FROM pmiotopic.grupomoderador WHERE ref_ref_cod_pessoa_fj = $int_ref_idpes $WHERE UNION select ref_cod_grupos, 2 as tipo FROM pmiotopic.grupopessoa WHERE ref_idpes = $int_ref_idpes $WHERE) $orderBy $limit " );
		$total = $db->Num_Linhas();
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
	
	function pessoasGrupo ($int_ref_cod_grupo, $str_ordenacao = false, $int_ativo=1, $int_limite_ini = false, $int_limite_qtd = false )
	{
		
		$orderBy = "";
		$WHERE = "";
		if( is_string( $str_ordenacao ) )
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		if( is_numeric( $int_ativo ) )
		{
			$WHERE = "AND ativo = $int_ativo";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		// Seleciona o total de registro da tabela
		$db = new clsBanco();
		$db->Consulta( " (select ref_ref_cod_pessoa_fj as id, 1 as tipo FROM pmiotopic.grupomoderador WHERE ref_cod_grupos = $int_ref_cod_grupo $WHERE
						UNION
						select ref_idpes as id, 2 as tipo FROM pmiotopic.grupopessoa WHERE ref_cod_grupos = $int_ref_cod_grupo  $WHERE) $orderBy $limit " );
		$total = $db->Num_Linhas();
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
}
?>
