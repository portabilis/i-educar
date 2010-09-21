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


class clsUf
{
	var $sigla_uf;
	var $nome;
	var $geom;
	var $idpais;


	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsUf
	 */
	function clsUf( $str_sigla_uf=false, $str_nome=false, $str_geom=false, $int_idpais=false )
	{
		global $coreExt;
		$this->config = $coreExt['Config'];

		$this->sigla_uf = $str_sigla_uf;
		$this->nome = $str_nome;
		$this->geom = $str_geom;

		$objPais = new clsPais($int_idpais);
		if($objPais->detalhe())
		{
			$this->idpais = $int_idpais;
		}

		$this->tabela = "uf";
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
		if( is_string( $this->sigla_uf )  && is_string( $this->nome ) )
		{
			$campos = "";
			$values = "";

			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}
			if( is_numeric( $this->idpais ) )
			{
				$campos .= ", idpais";
				$values .= ", '{$this->idpais}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( sigla_uf, nome$campos ) VALUES ( '{$this->sigla_uf}', '{$this->nome}'" );

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
		if( is_string( $this->nome ) )
		{
			$set = "SET nome = '{$this->nome}'";

			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else
			{
				$set .= ", geom = NULL";
			}

			if( is_numeric( $this->idpais ) )
			{
				$set .= ", idpais = '{$this->idpais}'";
			}
			else
			{
				$set .= ", idpais = NULL";
			}

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idlog = '$this->idlog'" );
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
		if(is_string($this->sigla_uf))
		{
			$objCidade = new clsMunicipio();
			$listaCidade = $objCidade->lista(false,$this->sigla_uf );

			if(!count($listaCidade))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE sigla_uf={$this->sigla_uf}");
				return true;
			}
			return false;
		}
		return false;
	}

  /**
   * Retorna um array com os registros da tabela public.uf
   * @return array
   */
  public function lista($str_nome = FALSE, $str_geom = FALSE, $int_idpais = FALSE,
    $int_limite_ini = FALSE, $int_limite_qtd = FALSE, $str_orderBy = 'sigla_uf ASC')
  {
    $whereAnd = 'WHERE ';

    if (is_string($str_nome)) {
      $where   .= "{$whereAnd}nome LIKE '%$str_nome%'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_geom)) {
      $where   .= "{$whereAnd}geom LIKE '%$str_geom%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idpais)) {
      $where   .= "{$whereAnd}idpais = '$int_idpais'";
      $whereAnd = ' AND ';
    }
    else {
      $idpais = $this->config->app->locale->country;
      $where .= "{$whereAnd}idpais = '$idpais'";
      $whereAnd = ' AND ';
    }

    if ($str_orderBy) {
      $orderBy = "ORDER BY $str_orderBy";
    }

    $limit = '';
    if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
      $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
    }

    $db = new clsBanco();
    $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
    $db->ProximoRegistro();

    $total = $db->Campo('total');

    $db->Consulta("SELECT sigla_uf, nome, geom, idpais FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
    $resultado = array();

    while ($db->ProximoRegistro())
    {
      $tupla = $db->Tupla();
      $tupla['idpais'] = new clsPais($tupla['idpais']);
      $tupla['total']  = $total;
      $resultado[] = $tupla;
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->sigla_uf)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT sigla_uf, nome, geom, idpais FROM {$this->schema}.{$this->tabela} WHERE sigla_uf='{$this->sigla_uf}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->sigla_uf = $tupla["sigla_uf"];
				$this->nome = $tupla["nome"];
				$this->geom = $tupla["geom"];
				$this->idpais = $tupla["idpais"];

				$tupla["idpais"] = new clsPais(  $tupla["idpais"] );

				return $tupla;
			}
		}
		return false;
	}
}
?>