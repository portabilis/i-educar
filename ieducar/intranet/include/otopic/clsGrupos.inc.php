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

class clsGrupos
{
	var $cod_grupos;
	var $ref_pessoa_exc;
	var $ref_pessoa_cad;
	var $nm_grupo;
	var $ativo;
	var $data_cadastro;
	var $data_exclusao;
	var $atendimento;
	var $camposLista;
	var $todosCampos;
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object:clsnatureza
	 */
	function clsGrupos( $int_cod_grupos = false, $int_ref_pessoa_cad = false, $int_ref_pessoa_exc = false, $str_nm_grupo = false,  $ativo=false, $int_atendimento = false )
	{
		$this->cod_grupos = is_numeric($int_cod_grupos) ? $int_cod_grupos : false;
		$this->ref_pessoa_cad = is_numeric($int_ref_pessoa_cad) ? $int_ref_pessoa_cad : false;
		$this->ref_pessoa_exc = is_numeric($int_ref_pessoa_exc) ? $int_ref_pessoa_exc: false;
		$this->nm_grupo = is_string($str_nm_grupo) ? $str_nm_grupo : false;
		$this->ativo = is_numeric($ativo) ? $ativo : 1;
		$this->atendimento = is_numeric($int_atendimento) ? $int_atendimento : false;

		$this->camposLista = $this->todosCampos = "cod_grupos, ref_pessoa_cad, ref_pessoa_exc, nm_grupo, data_cadastro, data_exclusao, ativo, atendimento";
		$this->tabela = "pmiotopic.grupos";
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
		if( $this->ref_pessoa_cad && $this->nm_grupo )
		{
			$campos = "";
			$valores= "";

			if($this->atendimento)
			{
				$campos .= ", atendimento";
				$valores .= ", '$this->atendimento'";
			}
			//echo ( "INSERT INTO {$this->tabela} (ref_pessoa_cad, nm_grupo, data_cadastro$campos ) VALUES ( '{$this->ref_pessoa_cad}', '{$this->nm_grupo}', NOW()$valores )" );
			//die();
			$db->Consulta( "INSERT INTO {$this->tabela} (ref_pessoa_cad, nm_grupo, data_cadastro$campos ) VALUES ( '{$this->ref_pessoa_cad}', '{$this->nm_grupo}', NOW()$valores )" );
			// Retorna Id cadastrado
			return $db->InsertId("pmiotopic.grupos_cod_grupos_seq");
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
		if( $this->cod_grupos && $this->ref_pessoa_cad && $this->nm_grupo)
		{
			$setVir = ", ";
			if($this->atendimento)
			{
				$set .= "$setVir atendimento = '$this->atendimento'";
				$setVir = ", ";

			}

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET ref_pessoa_cad = '{$this->ref_pessoa_cad}', nm_grupo = '{$this->nm_grupo}'$set WHERE cod_grupos = '{$this->cod_grupos}' " );
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
		if( $this->cod_grupos && $this->ref_pessoa_exc  )
		{
				$db = new clsBanco();
				$this->detalhe();
				$this->ativo++;
				$db->Consulta("UPDATE $this->tabela SET ativo='{$this->ativo}', data_exclusao=NOW(), ref_pessoa_exc = '$this->ref_pessoa_exc' WHERE cod_grupos = '{$this->cod_grupos}' ");
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
		$this->camposLista = $str_campos;
	}

	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetCamposLista()
	{
		$this->camposLista = $this->todosCampos;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_nm_grupo = false, $int_ref_pessoa_cad = false, $str_ordenacao = false, $date_cadastro_ini=false, $date_cadastro_fim=false, $int_ativo=1, $date_exclusao_ini=false, $date_exclusao_fim=false, $int_limite_ini = false, $int_limite_qtd = false, $int_atendimento = 0, $int_cod_grupos = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $int_ref_pessoa_cad ) )
		{
			$where .= "{$whereAnd}ref_pessoa_cad = '$int_ref_pessoa_cad'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_pessoa_exc ) )
		{
			$where .= "{$whereAnd}ref_pessoa_exc = '$int_ref_pessoa_exc'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_nm_grupo ) )
		{
			$where .= "{$whereAnd}nm_grupo LIKE '%$str_nm_grupo%'";
			$whereAnd = " AND ";
		}

		if( is_string( $date_cadastro_ini ) )
		{
			$where .= "{$whereAnd}data_cadastro >= '$date_cadastro_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_cadastro_fim ) )
		{
			$where .= "{$whereAnd}data_cadastro <= '$date_cadastro_fim'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_exclusao_ini ) )
		{
			$where .= "{$whereAnd}data_exclusao >= '$date_exclusao_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_exclusao_fim ) )
		{
			$where .= "{$whereAnd}data_exclusao <= '$date_exclusao_fim'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ativo ) )
		{
			$where .= "{$whereAnd}ativo = '$int_ativo'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_atendimento ) )
		{
			$where .= "{$whereAnd}atendimento = '$int_atendimento'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cod_grupos ) )
		{
			$where .= "{$whereAnd}cod_grupos = '$int_cod_grupos'";
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
		$db->Consulta( "SELECT ".$this->camposLista." FROM {$this->tabela} $where $orderBy $limit" );

		$resultado = array();
		$countCampos = count( explode( ",", $this->camposLista ) );

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
				$resultado[] = $tupla["$this->camposLista"];
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
		if($this->cod_grupos)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cod_grupos, ref_pessoa_cad, ref_pessoa_exc, nm_grupo, data_cadastro, data_exclusao, ativo, atendimento FROM {$this->tabela} WHERE cod_grupos = '$this->cod_grupos' ");
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
