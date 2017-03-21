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

class clsOcupacao
{
	var $idocup;
	var $descricao;

	var $tabela;
	var $schema;

	/**
	 * Construtor
	 *
	 * @return Object:clsOcupacao
	 */
	function clsOcupacao( $idocup=false, $descricao=false)
	{
		$this->idocup    = $idocup;
		$this->descricao = $descricao;
		
		$this->tabela = "ocupacao";
		$this->schema = "cadastro";
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
		if(is_numeric($this->idocup) && is_string($this->descricao))
		{
			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} (idocup, descricao) VALUES ($this->idocup, $this->descricao)" );
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
		if(is_numeric($this->idocup) && is_string($this->descricao))
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET descricao = '$this->descricao' WHERE idocup = '$this->idocup' " );
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
		if( is_numeric($this->idocup) && 	is_string($this->descricao))
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idocup = {$this->idocup}");
			return true;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_idocup=false, $str_descricao=false, $str_ordenacao="descricao", $int_limite_ini=0, $int_limite_qtd=20 )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if(is_numeric($int_idocup))
		{
			$where .= "{$whereAnd}idocup = '$int_idocup'";
			$whereAnd = " AND ";
		}
		if(is_string($str_descricao))
		{
			$where .= "{$whereAnd}descricao ILIKE  '%$str_descricao%'";
		}
		
		$orderBy = "";
		if(is_string($str_ordenacao))
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		$limit = "";
		if(is_numeric($int_limite_ini) &&   is_numeric($int_limite_qtd))
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT idocup, descricao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
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
		if($this->idocup)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idocup, descricao FROM {$this->schema}.{$this->tabela} WHERE idocup = {$this->idocup}");
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