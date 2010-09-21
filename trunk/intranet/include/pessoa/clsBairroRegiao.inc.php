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
require_once ("include/Geral.inc.php");


class clsBairroRegiao
{
	var $ref_cod_regiao;
	var $ref_idbai;
	var $campos_lista;
	var $todos_campos;

	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsBairro
	 */
	function clsBairroRegiao( $int_ref_cod_regiao = false, $int_ref_idbai=false)
	{
		$this->ref_cod_regiao = $int_ref_cod_regiao;
		$this->ref_idbai = $int_ref_idbai;
		$this->tabela = "bairro_regiao";
		$this->campos_lista = $this->todos_campos = "ref_cod_regiao, ref_idbai";

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
		if( is_numeric( $this->ref_cod_regiao ) && is_numeric( $this->ref_idbai ) )
		{
			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( ref_cod_regiao, ref_idbai ) VALUES ( '{$this->ref_cod_regiao}', '{$this->ref_idbai}')" );
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
		if( is_numeric( $this->ref_cod_regiao ) && is_numeric( $this->ref_idbai ) )
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET ref_cod_regiao = '$this->ref_cod_regiao' WHERE ref_idbai = '$this->ref_idbai' " );
			return true;
		}
		return false;
	}

	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui($cod_regiao_excluir = false) 
	{
		
		// Se passar o cod da regiao, excluir todos os vinculos de bairro da regiao
		if($cod_regiao_excluir)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_cod_regiao = {$cod_regiao_excluir} ");
			return true;
		}
		elseif(is_numeric($this->ref_idbai))
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_idbai = {$this->ref_idbai} ");
			return true;
		}
		return false;
	}

	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setCamposLista($str_campos)
	{
		$this->campos_lista = $str_campos;
	}
	
	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetCamposLista()
	{
		$this->campos_lista = $this->todos_campos;
	}	
	
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $ref_cod_regiao = false,$int_limite_ini=false, $int_limite_qtd=false, $str_orderBy = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $ref_cod_regiao) )
		{
			$where .= "{$whereAnd}ref_cod_regiao = '$ref_cod_regiao' ";
			$whereAnd = " AND ";
		}
		if($str_orderBy)
		{
			$orderBy = "ORDER BY $str_orderBy";
		}

		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}

		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT $this->campos_lista FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
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
		if($this->cod_regiao)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT ref_cod_regiao, ref_ibai FROM {$this->schema}.{$this->tabela} WHERE cod_regiao='{$this->cod_regiao}'");
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
