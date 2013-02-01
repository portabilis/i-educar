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

class clsFisicaCpf
{
	var $idpes;
	var $cpf;
	var $idpes_cad;
	var $idpes_rev;

	var $tabela;
	var $schema;

	/**
	 * Construtor
	 *
	 * @return Object:clsFisicaCpf
	 */
	function clsFisicaCpf( $idpes=false, $cpf=false, $idpes_cad = false, $idpes_rev = false)
	{
		$this->idpes = $idpes;
		$this->idpes_cad = $idpes_cad? $idpes_cad : $_SESSION['id_pessoa'];
		$this->idpes_rev = $idpes_rev? $idpes_rev : $_SESSION['id_pessoa'];
		$this->cpf   = $cpf;

		$this->tabela = "fisica";
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
		if( is_numeric($this->idpes) && is_numeric($this->cpf) && $this->idpes_cad)
		{
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET cpf = '$this->cpf'  WHERE idpes = '$this->idpes' " );
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
		//die( "is_numeric($this->idpes) && is_numeric($this->cpf)");
		if(is_numeric($this->idpes) && is_numeric($this->cpf) && is_numeric($this->idpes_rev))
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET cpf = '$this->cpf'  WHERE idpes = '$this->idpes' " );
			return true;
		}
		return false;
	}

	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_idpes=false, $int_cpf=false, $str_ordenacao="idpes", $int_limite_ini=false, $int_limite_qtd=false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if(is_numeric($int_idpes))
		{
			$where .= "{$whereAnd}idpes = '$int_idpes'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_cpf))
		{
			$where .= "{$whereAnd}cpf ILIKE '%$int_cpf%' OR cpf ILIKE '$int_cpf%' ";
		}

		$orderBy = "";
		if(is_string($str_ordenacao))
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		$limit = "";
		if(is_numeric($int_limite_ini) && is_numeric($int_limite_qtd))
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}

		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
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
	 * Exibe uma lista de idpes baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function listaCod( $int_idpes=false, $int_cpf=false, $str_ordenacao="idpes", $int_limite_ini=false, $int_limite_qtd=false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if(is_numeric($int_idpes))
		{
			$where .= "{$whereAnd}idpes = '$int_idpes'";
			$whereAnd = " AND ";
		}

		if(is_numeric($int_cpf))
		{
			$temp_cpf = $int_cpf + 0;
			$where .= "{$whereAnd}cpf ILIKE '%$int_cpf%' OR cpf ILIKE '$temp_cpf%' ";
		}

		$orderBy = "";
		if(is_string($str_ordenacao))
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		$limit = "";
		if(is_numeric($int_limite_ini) && is_numeric($int_limite_qtd))
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}

		$db = new clsBanco();
		$db->Consulta( "SELECT idpes FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla['idpes'];
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
		if( $this->cpf )
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE cpf = {$this->cpf} ");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		elseif($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes} ");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}

		return false;
	}
	function detalheCPF()
	{
		$db = new clsBanco();
		$db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE cpf = {$this->cpf}");
		if( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			return $tupla;
		}
		return false;
	}
}

?>