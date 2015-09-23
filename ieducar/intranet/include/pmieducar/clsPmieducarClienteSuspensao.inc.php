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
* Criado em 20/07/2006 11:27 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarClienteSuspensao
{
	var $sequencial;
	var $ref_cod_cliente;
	var $ref_cod_motivo_suspensao;
	var $ref_usuario_libera;
	var $ref_usuario_suspende;
	var $dias;
	var $data_suspensao;
	var $data_liberacao;

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
	function clsPmieducarClienteSuspensao( $sequencial = null, $ref_cod_cliente = null, $ref_cod_motivo_suspensao = null, $ref_usuario_libera = null, $ref_usuario_suspende = null, $dias = null, $data_suspensao = null, $data_liberacao = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}cliente_suspensao";

		$this->_campos_lista = $this->_todos_campos = "sequencial, ref_cod_cliente, ref_cod_motivo_suspensao, ref_usuario_libera, ref_usuario_suspende, dias, data_suspensao, data_liberacao";

		if( is_numeric( $ref_usuario_suspende ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_suspende );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_suspende = $ref_usuario_suspende;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_suspende = $ref_usuario_suspende;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_suspende}'" ) )
				{
					$this->ref_usuario_suspende = $ref_usuario_suspende;
				}
			}
		}
		if( is_numeric( $ref_usuario_libera ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_libera );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_libera = $ref_usuario_libera;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_libera = $ref_usuario_libera;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_libera}'" ) )
				{
					$this->ref_usuario_libera = $ref_usuario_libera;
				}
			}
		}
		if( is_numeric( $ref_cod_motivo_suspensao ) )
		{
			if( class_exists( "clsPmieducarMotivoSuspensao" ) )
			{
				$tmp_obj = new clsPmieducarMotivoSuspensao( $ref_cod_motivo_suspensao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_motivo_suspensao = $ref_cod_motivo_suspensao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_motivo_suspensao = $ref_cod_motivo_suspensao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.motivo_suspensao WHERE cod_motivo_suspensao = '{$ref_cod_motivo_suspensao}'" ) )
				{
					$this->ref_cod_motivo_suspensao = $ref_cod_motivo_suspensao;
				}
			}
		}
		if( is_numeric( $ref_cod_cliente ) )
		{
			if( class_exists( "clsPmieducarCliente" ) )
			{
				$tmp_obj = new clsPmieducarCliente( $ref_cod_cliente );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_cliente = $ref_cod_cliente;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_cliente = $ref_cod_cliente;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.cliente WHERE cod_cliente = '{$ref_cod_cliente}'" ) )
				{
					$this->ref_cod_cliente = $ref_cod_cliente;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_numeric( $dias ) )
		{
			$this->dias = $dias;
		}
		if( is_string( $data_suspensao ) )
		{
			$this->data_suspensao = $data_suspensao;
		}
		if( is_string( $data_liberacao ) )
		{
			$this->data_liberacao = $data_liberacao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->ref_cod_motivo_suspensao ) && is_numeric( $this->ref_usuario_suspende ) && is_numeric( $this->dias ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			$sequencial = $db->CampoUnico( "SELECT COUNT(*)+1 FROM pmieducar.cliente_suspensao WHERE ref_cod_cliente = {$this->ref_cod_cliente}" );
			if( is_numeric( $sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_cliente ) )
			{
				$campos .= "{$gruda}ref_cod_cliente";
				$valores .= "{$gruda}'{$this->ref_cod_cliente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_motivo_suspensao ) )
			{
				$campos .= "{$gruda}ref_cod_motivo_suspensao";
				$valores .= "{$gruda}'{$this->ref_cod_motivo_suspensao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_libera ) )
			{
				$campos .= "{$gruda}ref_usuario_libera";
				$valores .= "{$gruda}'{$this->ref_usuario_libera}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_suspende ) )
			{
				$campos .= "{$gruda}ref_usuario_suspende";
				$valores .= "{$gruda}'{$this->ref_usuario_suspende}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias ) )
			{
				$campos .= "{$gruda}dias";
				$valores .= "{$gruda}'{$this->dias}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_suspensao";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_string( $this->data_liberacao ) )
			{
				$campos .= "{$gruda}data_liberacao";
				$valores .= "{$gruda}'{$this->data_liberacao}'";
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_cliente ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_usuario_libera ) )
			{
				$set .= "{$gruda}ref_usuario_libera = '{$this->ref_usuario_libera}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_suspende ) )
			{
				$set .= "{$gruda}ref_usuario_suspende = '{$this->ref_usuario_suspende}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias ) )
			{
				$set .= "{$gruda}dias = '{$this->dias}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_suspensao ) )
			{
				$set .= "{$gruda}data_suspensao = '{$this->data_suspensao}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_liberacao = NOW()";
			$gruda = ", ";

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}'" );
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
	function lista( $int_sequencial = null, $int_ref_cod_cliente = null, $int_ref_cod_motivo_suspensao = null, $int_ref_usuario_libera = null, $int_ref_usuario_suspende = null, $int_dias = null, $date_data_suspensao_ini = null, $date_data_suspensao_fim = null, $date_data_liberacao_ini = null, $date_data_liberacao_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_cliente ) )
		{
			$filtros .= "{$whereAnd} ref_cod_cliente = '{$int_ref_cod_cliente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_motivo_suspensao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_motivo_suspensao = '{$int_ref_cod_motivo_suspensao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_libera ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_libera = '{$int_ref_usuario_libera}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_suspende ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_suspende = '{$int_ref_usuario_suspende}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dias ) )
		{
			$filtros .= "{$whereAnd} dias = '{$int_dias}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_suspensao_ini ) )
		{
			$filtros .= "{$whereAnd} data_suspensao >= '{$date_data_suspensao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_suspensao_fim ) )
		{
			$filtros .= "{$whereAnd} data_suspensao <= '{$date_data_suspensao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_liberacao_ini ) )
		{
			$filtros .= "{$whereAnd} data_liberacao >= '{$date_data_liberacao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_liberacao_fim ) )
		{
			$filtros .= "{$whereAnd} data_liberacao <= '{$date_data_liberacao_fim}'";
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->ref_cod_motivo_suspensao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_motivo_suspensao = '{$this->ref_cod_motivo_suspensao}'" );
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->ref_cod_motivo_suspensao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_motivo_suspensao = '{$this->ref_cod_motivo_suspensao}'" );
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->ref_cod_motivo_suspensao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_motivo_suspensao = '{$this->ref_cod_motivo_suspensao}'" );
		return true;
		*/


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