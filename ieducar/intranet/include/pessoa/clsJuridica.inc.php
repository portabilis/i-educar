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

class clsJuridica
{
	var $idpes;
	var $idpes_cad;
	var $idpes_rev;
	var $cnpj;
	var $fantasia;
	var $insc_estadual;
	var $capital_social;

	var $tabela;
	var $schema;

	/**
	 * Construtor
	 *
	 * @return Object:clsEstadoCivil
	 */
	function clsJuridica( $idpes = false, $cnpj = false, $fantasia = false, $insc_estadual = false, $capital_social = false, $idpes_cad =false, $idpes_rev =false )
	{
		$objPessoa = new clsPessoa_($idpes);
		if($objPessoa->detalhe())
		{
			$this->idpes = $idpes;
		}

		$this->cnpj = $cnpj;
		$this->fantasia = $fantasia;
		$this->insc_estadual = $insc_estadual;
		$this->capital_social = $capital_social;
		$this->idpes_cad = $idpes_cad ? $idpes_cad : $_SESSION['id_pessoa'];
		$this->idpes_rev = $idpes_rev ? $idpes_rev : $_SESSION['id_pessoa'];


		$this->tabela = "juridica";
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

		if( is_numeric($this->idpes) && is_numeric($this->cnpj) && is_numeric($this->idpes_cad))
		{
			$campos = "";
			$valores = "";
			if($this->fantasia )
			{
				$campos  .= ", fantasia";
				$valores .= ", '$this->fantasia'";
			}
			if( is_numeric( $this->insc_estadual ) )
			{
				$campos .= ", insc_estadual";
				$valores .= ", '$this->insc_estadual' ";
			}
			if( is_string( $this->capital_social ) )
			{
				$campos .= ", capital_social";
				$valores .= ", '{$this->capital_social}' ";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} (idpes, cnpj, origem_gravacao, idsis_cad, data_cad, operacao, idpes_cad $campos) VALUES ($this->idpes, '$this->cnpj', 'M', 17, NOW(), 'I', '$this->idpes_cad' $valores)" );
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
		if(is_numeric($this->idpes) && is_numeric($this->idpes_rev))
		{
			$set = "";
			$gruda = "";
			if(is_string($this->fantasia) )
			{
				$set = " fantasia = '$this->fantasia' ";
				$gruda = ", ";
			}
			if( is_numeric( $this->insc_estadual ) )
			{
				if( $this->insc_estadual )
				{
					$set .= "$gruda insc_estadual = '$this->insc_estadual' ";
				}
				else
				{
					$set .= "$gruda insc_estadual = NULL ";
				}
			}
			else
			{
				$set .= "$gruda insc_estadual = NULL ";
			}

			if(is_string($this->capital_social) )
			{
				$set .= "{$gruda} capital_social = '$this->capital_social' ";
				$gruda = ", ";
			}
			if($this->idpes_rev)
			{
				$set .= "{$gruda} idpes_rev = '$this->idpes_rev' ";
			}
			if($set)
			{
				$db = new clsBanco();
				$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} SET $set WHERE idpes = '$this->idpes' " );
				return true;
			}
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
		if( is_numeric($this->idpes))
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes}");
			return true;
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_fantasia = false, $str_insc_estadual = false, $int_cnpj = false, $str_ordenacao = false, $int_limite_ini=false, $int_limite_qtd=false, $arrayint_idisin = false, $arrayint_idnotin = false, $int_idpes = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		$join = "";
		if(is_string($str_fantasia))
		{
			$where .= "{$whereAnd} (fcn_upper_nrm(fantasia) LIKE fcn_upper_nrm('%$str_fantasia%') OR fcn_upper_nrm(nome) LIKE fcn_upper_nrm('%$str_fantasia%'))";
			$whereAnd = " AND ";
		}
		if(is_string($str_insc_estadual))
		{
			$where .= "{$whereAnd}insc_estadual ILIKE  '%$str_insc_estadual%'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_idpes))
		{
			$where .= "{$whereAnd}idpes = '$int_idpes'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_cnpj ) )
		{

			$i = 0;
			while (substr($int_cnpj,$i,1) == 0) {
				$i++;
			}
			if($i > 0)
			{
				$int_cnpj = substr($int_cnpj,$i);
			}
			$where .= "{$whereAnd} cnpj ILIKE  '%$int_cnpj%' ";
			$whereAnd = " AND ";
		}

		if( is_array( $arrayint_idisin ) )
		{
			$ok = true;
			foreach ( $arrayint_idisin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes IN ( " . implode( ",", $arrayint_idisin ) . " )";
				$whereAnd = " AND ";
			}
		}

		if( is_array( $arrayint_idnotin ) )
		{
			$ok = true;
			foreach ( $arrayint_idnotin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes NOT IN ( " . implode( ",", $arrayint_idnotin ) . " )";
				$whereAnd = " AND ";
			}
		}

		$orderBy = "";
		if(is_string($str_ordenacao))
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		$limit = "";
		if($int_limite_ini !== false && $int_limite_qtd !== false)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}


		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.v_pessoa_juridica $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.v_pessoa_juridica $where $orderBy $limit" );
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
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes}");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		elseif($this->cnpj)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.{$this->tabela} WHERE cnpj = {$this->cnpj}");
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