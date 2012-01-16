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
* Criado em 31/07/2006 16:40 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAnoLetivoModulo
{
	var $ref_ano;
	var $ref_ref_cod_escola;
	var $sequencial;
	var $ref_cod_modulo;
	var $data_inicio;
	var $data_fim;

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
	function clsPmieducarAnoLetivoModulo( $ref_ano = null, $ref_ref_cod_escola = null, $sequencial = null, $ref_cod_modulo = null, $data_inicio = null, $data_fim = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}ano_letivo_modulo";

		$this->_campos_lista = $this->_todos_campos = "ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo, data_inicio, data_fim";

		if( is_numeric( $ref_cod_modulo ) )
		{
			if( class_exists( "clsPmieducarModulo" ) )
			{
				$tmp_obj = new clsPmieducarModulo( $ref_cod_modulo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_modulo = $ref_cod_modulo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_modulo = $ref_cod_modulo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.modulo WHERE cod_modulo = '{$ref_cod_modulo}'" ) )
				{
					$this->ref_cod_modulo = $ref_cod_modulo;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_escola ) && is_numeric( $ref_ano ) )
		{
			if( class_exists( "clsPmieducarEscolaAnoLetivo" ) )
			{
				$tmp_obj = new clsPmieducarEscolaAnoLetivo( $ref_ref_cod_escola, $ref_ano );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
						$this->ref_ano = $ref_ano;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
						$this->ref_ano = $ref_ano;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_ano_letivo WHERE ref_cod_escola = '{$ref_ref_cod_escola}' AND ano = '{$ref_ano}'" ) )
				{
					$this->ref_ref_cod_escola = $ref_ref_cod_escola;
					$this->ref_ano = $ref_ano;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $data_inicio ) )
		{
			$this->data_inicio = $data_inicio;
		}
		if( is_string( $data_fim ) )
		{
			$this->data_fim = $data_fim;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_modulo ) && is_string( $this->data_inicio ) && is_string( $this->data_fim ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_ano ) )
			{
				$campos .= "{$gruda}ref_ano";
				$valores .= "{$gruda}'{$this->ref_ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_modulo ) )
			{
				$campos .= "{$gruda}ref_cod_modulo";
				$valores .= "{$gruda}'{$this->ref_cod_modulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inicio ) )
			{
				$campos .= "{$gruda}data_inicio";
				$valores .= "{$gruda}'{$this->data_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_fim ) )
			{
				$campos .= "{$gruda}data_fim";
				$valores .= "{$gruda}'{$this->data_fim}'";
				$gruda = ", ";
			}

      // ativa escolaAnoLetivo se estiver desativado
      // (quando o escolaAnoLetivo é 'excluido' o registro não é removido)
      $escolaAnoLetivo = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola, 
                                                         $this->ref_ano,
                                                         null,
                                                         $_SESSION['id_pessoa'],
                                                         null,
                                                         null,
                                                         null,
                                                         1);
      $escolaAnoLetivoDetalhe = $escolaAnoLetivo->detalhe();

      if (isset($escolaAnoLetivoDetalhe['ativo']) and $escolaAnoLetivoDetalhe['ativo'] != '1')
        $escolaAnoLetivo->edita();

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
		if( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_modulo ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->data_inicio ) )
			{
				$set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_fim ) )
			{
				$set .= "{$gruda}data_fim = '{$this->data_fim}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'" );
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
	function lista( $int_ref_ano = null, $int_ref_ref_cod_escola = null, $int_sequencial = null, $int_ref_cod_modulo = null, $date_data_inicio_ini = null, $date_data_inicio_fim = null, $date_data_fim_ini = null, $date_data_fim_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_ano ) )
		{
			$filtros .= "{$whereAnd} ref_ano = '{$int_ref_ano}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_modulo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_modulo = '{$int_ref_cod_modulo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_ini ) )
		{
			$filtros .= "{$whereAnd} data_inicio >= '{$date_data_inicio_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_fim ) )
		{
			$filtros .= "{$whereAnd} data_inicio <= '{$date_data_inicio_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_fim_ini ) )
		{
			$filtros .= "{$whereAnd} data_fim >= '{$date_data_fim_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_fim_fim ) )
		{
			$filtros .= "{$whereAnd} data_fim <= '{$date_data_fim_fim}'";
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
		if( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_modulo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'" );
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
		if( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_modulo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'" );
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
		if( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_modulo ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a uma escola e a um ano
	 */
	function  excluirTodos()
	{
		if ( is_numeric( $this->ref_ano ) && is_numeric( $this->ref_ref_cod_escola ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'" );
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

	/**
	 * Retorna a menor data dos modulos de uma escola e ano
	 *
	 * @return array
	 */
	function menorData( $ref_ano, $ref_ref_cod_escola )
	{
		if( is_numeric( $ref_ano ) && is_numeric( $ref_ref_cod_escola ) )
		{
			$db = new clsBanco();
			$resultado = $db->CampoUnico( "SELECT
								MIN( data_inicio )
							FROM
								pmieducar.ano_letivo_modulo
							WHERE
								ref_ano = '{$ref_ano}'
								AND ref_ref_cod_escola = '{$ref_ref_cod_escola}'" );
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna a maior data dos modulos de uma escola e ano
	 *
	 * @return array
	 */
	function maiorData( $ref_ano, $ref_ref_cod_escola )
	{
		if( is_numeric( $ref_ano ) && is_numeric( $ref_ref_cod_escola ) )
		{
			$db = new clsBanco();
			$resultado = $db->CampoUnico( "SELECT
								MAX( data_fim )
							FROM
								pmieducar.ano_letivo_modulo
							WHERE
								ref_ano = '{$ref_ano}'
								AND ref_ref_cod_escola = '{$ref_ref_cod_escola}'" );
			return $resultado;
		}
		return false;
	}

}
?>
