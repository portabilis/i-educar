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
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 13/02/2007 14:26 pelo gerador automatico de classes
*/

require_once( "include/urbano/geral.inc.php" );

class clsUrbanoCepLogradouroBairro
{
	var $idlog;
	var $cep;
	var $idbai;
	var $idpes_rev;
	var $data_rev;
	var $origem_gravacao;
	var $idpes_cad;
	var $data_cad;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;

	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;

	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;

	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;

	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;


	/**
	 * Construtor (PHP 4)
	 * 
	 * @param integer idlog
	 * @param integer cep
	 * @param integer idbai
	 * @param integer idpes_rev
	 * @param string data_rev
	 * @param string origem_gravacao
	 * @param integer idpes_cad
	 * @param string data_cad
	 * @param string operacao
	 * @param integer idsis_rev
	 * @param integer idsis_cad
	 *
	 * @return object
	 */
	function clsUrbanoCepLogradouroBairro( $idlog = null, $cep = null, $idbai = null, $idpes_rev = null, $data_rev = null, $origem_gravacao = null, $idpes_cad = null, $data_cad = null, $operacao = null, $idsis_rev = null, $idsis_cad = null )
	{
		$db = new clsBanco();
		$this->_schema = "urbano.";
		$this->_tabela = "{$this->_schema}cep_logradouro_bairro";

		$this->_campos_lista = $this->_todos_campos = "clb.idlog, clb.cep, clb.idbai, clb.idpes_rev, clb.data_rev, clb.origem_gravacao, clb.idpes_cad, clb.data_cad, clb.operacao, clb.idsis_rev, clb.idsis_cad";

		if( is_numeric( $idsis_rev ) )
		{
			if( class_exists( "clsAcessoSistema" ) )
			{
				$tmp_obj = new clsAcessoSistema( $idsis_rev );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idsis_rev = $idsis_rev;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idsis_rev = $idsis_rev;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM acesso.sistema WHERE idsis = '{$idsis_rev}'" ) )
				{
					$this->idsis_rev = $idsis_rev;
				}
			}
		}
		if( is_numeric( $idsis_cad ) )
		{
			if( class_exists( "clsAcessoSistema" ) )
			{
				$tmp_obj = new clsAcessoSistema( $idsis_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idsis_cad = $idsis_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idsis_cad = $idsis_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM acesso.sistema WHERE idsis = '{$idsis_cad}'" ) )
				{
					$this->idsis_cad = $idsis_cad;
				}
			}
		}
		if( is_numeric( $idpes_rev ) )
		{
			if( class_exists( "clsCadastroPessoa" ) )
			{
				$tmp_obj = new clsCadastroPessoa( $idpes_rev );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idpes_rev = $idpes_rev;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idpes_rev = $idpes_rev;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.pessoa WHERE idpes = '{$idpes_rev}'" ) )
				{
					$this->idpes_rev = $idpes_rev;
				}
			}
		}
		if( is_numeric( $idpes_cad ) )
		{
			if( class_exists( "clsCadastroPessoa" ) )
			{
				$tmp_obj = new clsCadastroPessoa( $idpes_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idpes_cad = $idpes_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idpes_cad = $idpes_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.pessoa WHERE idpes = '{$idpes_cad}'" ) )
				{
					$this->idpes_cad = $idpes_cad;
				}
			}
		}
		if( is_numeric( $cep ) && is_numeric( $idlog ) )
		{
			if( class_exists( "clsUrbanoCepLogradouro" ) )
			{
				$tmp_obj = new clsUrbanoCepLogradouro( $cep, $idlog );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->cep = $cep;
						$this->idlog = $idlog;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->cep = $cep;
						$this->idlog = $idlog;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM urbano.cep_logradouro WHERE cep = '{$cep}' AND idlog = '{$idlog}'" ) )
				{
					$this->cep = $cep;
					$this->idlog = $idlog;
				}
			}
		}
		if( is_numeric( $idbai ) )
		{
			if( class_exists( "clsPublicBairro" ) )
			{
				$tmp_obj = new clsPublicBairro( null, null, $idbai );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idbai = $idbai;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idbai = $idbai;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM public.bairro WHERE idbai = '{$idbai}'" ) )
				{
					$this->idbai = $idbai;
				}
			}
		}


		if( is_string( $data_rev ) )
		{
			$this->data_rev = $data_rev;
		}
		if( is_string( $origem_gravacao ) )
		{
			$this->origem_gravacao = $origem_gravacao;
		}
		if( is_string( $data_cad ) )
		{
			$this->data_cad = $data_cad;
		}
		if( is_string( $operacao ) )
		{
			$this->operacao = $operacao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->idlog ) && is_numeric( $this->cep ) && is_numeric( $this->idbai ) && is_string( $this->origem_gravacao ) && is_string( $this->operacao ) && is_numeric( $this->idsis_cad ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->idlog ) )
			{
				$campos .= "{$gruda}idlog";
				$valores .= "{$gruda}'{$this->idlog}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cep ) )
			{
				$campos .= "{$gruda}cep";
				$valores .= "{$gruda}'{$this->cep}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idbai ) )
			{
				$campos .= "{$gruda}idbai";
				$valores .= "{$gruda}'{$this->idbai}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_rev ) )
			{
				$campos .= "{$gruda}idpes_rev";
				$valores .= "{$gruda}'{$this->idpes_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_rev ) )
			{
				$campos .= "{$gruda}data_rev";
				$valores .= "{$gruda}'{$this->data_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->origem_gravacao ) )
			{
				$campos .= "{$gruda}origem_gravacao";
				$valores .= "{$gruda}'{$this->origem_gravacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$campos .= "{$gruda}idpes_cad";
				$valores .= "{$gruda}'{$this->idpes_cad}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cad";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_string( $this->operacao ) )
			{
				$campos .= "{$gruda}operacao";
				$valores .= "{$gruda}'{$this->operacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_rev ) )
			{
				$campos .= "{$gruda}idsis_rev";
				$valores .= "{$gruda}'{$this->idsis_rev}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$campos .= "{$gruda}idsis_cad";
				$valores .= "{$gruda}'{$this->idsis_cad}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;
		}
		return false;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		if( is_numeric( $this->idbai ) && is_numeric( $this->idlog ) && is_numeric( $this->cep ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->idpes_rev ) )
			{
				$set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_rev ) )
			{
				$set .= "{$gruda}data_rev = '{$this->data_rev}'";
				$gruda = ", ";
			}
			if( is_string( $this->origem_gravacao ) )
			{
				$set .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cad ) )
			{
				$set .= "{$gruda}data_cad = '{$this->data_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->operacao ) )
			{
				$set .= "{$gruda}operacao = '{$this->operacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_rev ) )
			{
				$set .= "{$gruda}idsis_rev = '{$this->idsis_rev}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$set .= "{$gruda}idsis_cad = '{$this->idsis_cad}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE idbai = '{$this->idbai}' AND idlog = '{$this->idlog}' AND cep = '{$this->cep}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param integer int_idpes_rev
	 * @param string date_data_rev_ini
	 * @param string date_data_rev_fim
	 * @param string str_origem_gravacao
	 * @param integer int_idpes_cad
	 * @param string date_data_cad_ini
	 * @param string date_data_cad_fim
	 * @param string str_operacao
	 * @param integer int_idsis_rev
	 * @param integer int_idsis_cad
	 *
	 * @return array
	 */
	function lista( $int_idpes_rev = null, $date_data_rev_ini = null, $date_data_rev_fim = null, $str_origem_gravacao = null, $int_idpes_cad = null, $date_data_cad_ini = null, $date_data_cad_fim = null, $str_operacao = null, $int_idsis_rev = null, $int_idsis_cad = null, $int_idpais = null, $str_sigla_uf = null, $int_idmun = null, $int_idlog = null, $int_cep = null, $int_idbai = null )
	{
		$select  = ", l.nome AS nm_logradouro, m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ";
		$from = "clb, public.logradouro l, public.municipio m, public.uf u, public.pais p ";
		
		$sql = "SELECT {$this->_campos_lista}{$select} FROM {$this->_tabela} {$from}";
		$whereAnd = " AND ";

		$filtros = " WHERE clb.idlog = l.idlog AND l.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ";

		if( is_numeric( $int_idlog ) )
		{
			$filtros .= "{$whereAnd} clb.idlog = '{$int_idlog}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cep ) )
		{
			$filtros .= "{$whereAnd} clb.cep = '{$int_cep}'";
			$whereAnd = " AND ";
		} 
		if( is_numeric( $int_idbai ) )
		{
			$filtros .= "{$whereAnd} clb.idbai = '{$int_idbai}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpes_rev ) )
		{
			$filtros .= "{$whereAnd} clb.idpes_rev = '{$int_idpes_rev}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_rev_ini ) )
		{
			$filtros .= "{$whereAnd} clb.data_rev >= '{$date_data_rev_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_rev_fim ) )
		{
			$filtros .= "{$whereAnd} clb.data_rev <= '{$date_data_rev_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_origem_gravacao ) )
		{
			$filtros .= "{$whereAnd} clb.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpes_cad ) )
		{
			$filtros .= "{$whereAnd} clb.idpes_cad = '{$int_idpes_cad}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cad_ini ) )
		{
			$filtros .= "{$whereAnd} clb.data_cad >= '{$date_data_cad_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cad_fim ) )
		{
			$filtros .= "{$whereAnd} clb.data_cad <= '{$date_data_cad_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_operacao ) )
		{
			$filtros .= "{$whereAnd} clb.operacao LIKE '%{$str_operacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idsis_rev ) )
		{
			$filtros .= "{$whereAnd} clb.idsis_rev = '{$int_idsis_rev}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idsis_cad ) )
		{
			$filtros .= "{$whereAnd} clb.idsis_cad = '{$int_idsis_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpais ) )
		{
			$filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sigla_uf ) )
		{
			$filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmun ) )
		{
			$filtros .= "{$whereAnd} m.idmun = '{$int_idmun}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//echo "<pre>"; print_r($sql);die;
		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$from}{$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function listabai( $int_idmun = null, $int_idlog = null, $int_cep = null, $int_idbai = null )
	{
		$select  = "l.nome AS nm_logradouro, m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ";
		$from = "public.logradouro l, public.municipio m, public.uf u, public.pais p ";
		
		$sql = "SELECT {$this->_campos_lista}{$select} FROM {$this->_tabela} {$from}";
		$whereAnd = " AND ";

		$filtros = " WHERE l.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais  ";

		if( is_numeric( $int_idlog ) )
		{
			$filtros .= "{$whereAnd} clb.idlog = '{$int_idlog}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cep ) )
		{
			$filtros .= "{$whereAnd} clb.cep = '{$int_cep}'";
			$whereAnd = " AND ";
		} 
		if( is_numeric( $int_idbai ) )
		{
			$filtros .= "{$whereAnd} clb.idbai = '{$int_idbai}'";
			$whereAnd = " AND ";
		}
		
		if( is_numeric( $int_idmun ) )
		{
			$filtros .= "{$whereAnd} m.idmun = '{$int_idmun}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$from}{$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->idbai ) && is_numeric( $this->idlog ) && is_numeric( $this->cep ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} clb WHERE clb.idbai = '{$this->idbai}' AND clb.idlog = '{$this->idlog}' AND clb.cep = '{$this->cep}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->idbai ) && is_numeric( $this->idlog ) && is_numeric( $this->cep ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE idbai = '{$this->idbai}' AND idlog = '{$this->idlog}' AND cep = '{$this->cep}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function listaLogradouro( $idbai = null )
	{
		if( is_numeric( $idbai ) )
		{
			$db = new clsBanco();
			$db->Consulta( "
				SELECT
					DISTINCT clb.idlog
					, l.nome
				FROM
					urbano.cep_logradouro_bairro clb
					, public.logradouro l
				WHERE
					clb.idbai = '{$idbai}'
					AND clb.idlog = l.idlog
				ORDER BY
					l.nome ASC
				" );
	
			while ( $db->ProximoRegistro() )
			{
				$resultado[] = $db->Tupla();
			}
			return $resultado;
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->idbai ) && is_numeric( $this->idlog ) && is_numeric( $this->cep ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE idbai = '{$this->idbai}' AND idlog = '{$this->idlog}' AND cep = '{$this->cep}'" );
		return true;
		*/

		
		}
		return false;
	}

	/**
	 * Exclui todos os registros
	 *
	 * @return bool
	 */
	function excluirTodos( $idlog )
	{
		if( is_numeric( $idlog ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE idlog = '{$idlog}'" );
			return true;
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}

	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
	 */
	function resetCamposLista()
	{
		$this->_campos_lista = $this->_todos_campos;
	}

	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}

	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

}
?>