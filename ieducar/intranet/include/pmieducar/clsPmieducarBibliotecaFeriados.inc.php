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
* Criado em 04/08/2006 11:12 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarBibliotecaFeriados
{
	var $cod_feriado;
	var $ref_cod_biblioteca;
	var $nm_feriado;
	var $descricao;
	var $data_feriado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

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
	 * @return object
	 */
	function clsPmieducarBibliotecaFeriados( $cod_feriado = null, $ref_cod_biblioteca = null, $nm_feriado = null, $descricao = null, $data_feriado = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}biblioteca_feriados";

		$this->_campos_lista = $this->_todos_campos = "cod_feriado, ref_cod_biblioteca, nm_feriado, descricao, data_feriado, data_cadastro, data_exclusao, ativo";

		if( is_numeric( $ref_cod_biblioteca ) )
		{
			if( class_exists( "clsPmieducarBiblioteca" ) )
			{
				$tmp_obj = new clsPmieducarBiblioteca( $ref_cod_biblioteca );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.biblioteca WHERE cod_biblioteca = '{$ref_cod_biblioteca}'" ) )
				{
					$this->ref_cod_biblioteca = $ref_cod_biblioteca;
				}
			}
		}


		if( is_numeric( $cod_feriado ) )
		{
			$this->cod_feriado = $cod_feriado;
		}
		if( is_string( $nm_feriado ) )
		{
			$this->nm_feriado = $nm_feriado;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( ( $data_feriado ) )
		{
			$this->data_feriado = $data_feriado;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_biblioteca ) && is_string( $this->nm_feriado ) && ( $this->data_feriado ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$campos .= "{$gruda}ref_cod_biblioteca";
				$valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_feriado ) )
			{
				$campos .= "{$gruda}nm_feriado";
				$valores .= "{$gruda}'{$this->nm_feriado}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( ( $this->data_feriado ) )
			{
				$campos .= "{$gruda}data_feriado";
				$valores .= "{$gruda}'{$this->data_feriado}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_feriado_seq");
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
		if( is_numeric( $this->cod_feriado ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_feriado ) )
			{
				$set .= "{$gruda}nm_feriado = '{$this->nm_feriado}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( ( $this->data_feriado ) )
			{
				$set .= "{$gruda}data_feriado = '{$this->data_feriado}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_feriado = '{$this->cod_feriado}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_feriado = null, $int_ref_cod_biblioteca = null, $str_nm_feriado = null, $str_descricao = null, $time_data_feriado_ini = null, $time_data_feriado_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_feriado ) )
		{
			$filtros .= "{$whereAnd} cod_feriado = '{$int_cod_feriado}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_feriado ) )
		{
			$filtros .= "{$whereAnd} nm_feriado LIKE '%{$str_nm_feriado}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( ( $time_data_feriado_ini ) )
		{
			$filtros .= "{$whereAnd} data_feriado >= '{$time_data_feriado_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_data_feriado_fim ) )
		{
			$filtros .= "{$whereAnd} data_feriado <= '{$time_data_feriado_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

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

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->cod_feriado ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_feriado = '{$this->cod_feriado}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->cod_feriado ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_feriado = '{$this->cod_feriado}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
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
		if( is_numeric( $this->cod_feriado ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_feriado = '{$this->cod_feriado}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a uma biblioteca
	 */
	function  excluirTodos( $ref_cod_biblioteca )
	{
		if ( is_numeric( $ref_cod_biblioteca ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$ref_cod_biblioteca}'" );
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