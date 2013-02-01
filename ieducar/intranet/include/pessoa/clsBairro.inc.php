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


class clsBairro
{
	var $idbai;
	var $idmun;
	var $geom;
	var $nome;
	
	var $idpes_cad;
	var $idpes_rev;
	var $origem_gravacao;
	var $operacao;
	var $idsis_cad;
	var $idsis_rev;
	
	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsBairro
	 */
	function clsBairro( $int_idbai = false, $int_idmun=false, $str_geom=false, $str_nome=false , $int_idpes_cad = false, $int_idpes_rev = false, $str_origem_gravacao = false, $str_operacao=false, $int_idsis_cad=false, $int_idsis_rev=false )
	{
		$this->idbai = $int_idbai;
		
		$objMun = new clsMunicipio($int_idmun);
		
		if($objMun->detalhe())
		{
			$this->idmun = $int_idmun;
		}
		
		$this->geom = $str_geom;
		$this->nome = $str_nome;

		$this->idpes_cad = $int_idpes_cad;
		$this->idpes_rev = $int_idpes_rev;
		$this->idsis_cad = $int_idsis_cad;
		$this->idsis_rev = $int_idsis_rev;
		$this->operacao = $str_operacao;
		$this->origem_gravacao = $str_origem_gravacao;
		
		$this->tabela = "bairro";
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
		if( is_numeric( $this->idmun ) && is_string( $this->nome ) )
		{
			$campos = "";
			$values = "";
			
			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$campos .= ", idpes_cad";
				$values .= ", '{$this->idpes_cad}'";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$campos .= ", idsis_cad";
				$values .= ", '{$this->idsis_cad}'";
			}
			if( is_string( $this->operacao) )
			{
				$campos .= ", operacao";
				$values .= ", '{$this->operacao}'";
			}
			if( is_string( $this->origem_gravacao) )
			{
				$campos .= ", origem_gravacao";
				$values .= ", '{$this->origem_gravacao}'";
			}
			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idmun, nome, data_cad$campos ) VALUES ( '{$this->idmun}', '{$this->nome}', NOW()$values )" );

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
		if( is_numeric( $this->idmun ) && is_string( $this->nome ) )
		{
			$set = "SET idmun = '{$this->idmun}', nome = '{$this->nome}'";

			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else 
			{
				$set .= ", geom = NULL";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idbai = '$this->idbai'" );
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
		if(is_numeric($this->idbai))
		{
			$objEndPes = new clsEnderecoPessoa();
			$listaEndPes = $objEndPes->lista(false, false, false, false, false, false, $this->idbai);
			
			$objCepLogBai = new clsCepLogradouroBairro();
			$listaCepLogBai = $objCepLogBai->lista(false, false, $this->idbai);
			
			if(!count($listaEndPes) && !count($listaCepLogBai))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idbai={$this->idbai}");
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_idmun=false, $str_geom=false, $str_nome=false, $int_limite_ini=false, $int_limite_qtd=false, $str_orderBy = false, $array_idbai_notin = false ,$id_bairro = false)
	{
		
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $int_idmun ) )
		{
			$where .= "{$whereAnd}idmun = '$int_idmun'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$where .= "{$whereAnd}nome LIKE '%$str_nome%'";
			$whereAnd = " AND ";
		}
		
		if(is_array($array_idbai_notin))
		{
			$implode = implode(",",$array_idbai_notin);
			$where .= "{$whereAnd}idbai NOT IN ($implode)";
			$whereAnd = " AND ";
		}
		
		if(is_numeric($id_bairro))
		{
			$where .= "{$whereAnd}idbai = '$id_bairro'";
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
		$db->Consulta( "SELECT idbai,idmun, geom, nome FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["idmun"] = new clsMunicipio( $tupla["idmun"] );
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
		if($this->idbai)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idbai, idmun, geom, nome FROM {$this->schema}.{$this->tabela} WHERE idbai='{$this->idbai}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idbai = $tupla["idbai"];
				$this->idmun = $tupla["idmun"];
				$this->geom = $tupla["geom"];
				$this->nome = $tupla["nome"];

				$tupla["idmun"] = new clsMunicipio( $tupla["idmun"] );
				return $tupla;
			}
		}
		return false;
	}
	
	/**
	 * Retorna o ultimo registro cadastrado
	 *
	 * @return integer
	 */
	function UltimaChave()
	{
		$db = new clsBanco();
		$db->Consulta("SELECT MAX(idbai) FROM {$this->schema}.{$this->tabela}");
		if( $db->ProximoRegistro() )
		{	
			list($chave) = $db->Tupla();								
			return $chave;
		}		
		return false;
	}
	
}
?>
