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
* Criado em 08/08/2006 17:35 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarTransferenciaSolicitacao
{
	var $cod_transferencia_solicitacao;
	var $ref_cod_transferencia_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_matricula_entrada;
	var $ref_cod_matricula_saida;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_transferencia;

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
	function clsPmieducarTransferenciaSolicitacao( $cod_transferencia_solicitacao = null, $ref_cod_transferencia_tipo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_matricula_entrada = null, $ref_cod_matricula_saida = null, $observacao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $data_transferencia = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}transferencia_solicitacao";

		$this->_campos_lista = $this->_todos_campos = "ts.cod_transferencia_solicitacao, ts.ref_cod_transferencia_tipo, ts.ref_usuario_exc, ts.ref_usuario_cad, ts.ref_cod_matricula_entrada, ts.ref_cod_matricula_saida, ts.observacao, ts.data_cadastro, ts.data_exclusao, ts.ativo, ts.data_transferencia";

		if( is_numeric( $ref_cod_transferencia_tipo ) )
		{
			if( class_exists( "clsPmieducarTransferenciaTipo" ) )
			{
				$tmp_obj = new clsPmieducarTransferenciaTipo( $ref_cod_transferencia_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_transferencia_tipo = $ref_cod_transferencia_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_transferencia_tipo = $ref_cod_transferencia_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.transferencia_tipo WHERE cod_transferencia_tipo = '{$ref_cod_transferencia_tipo}'" ) )
				{
					$this->ref_cod_transferencia_tipo = $ref_cod_transferencia_tipo;
				}
			}
		}
		if( is_numeric( $ref_usuario_exc ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'" ) )
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}
		if( is_numeric( $ref_usuario_cad ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'" ) )
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}
		if( is_numeric( $ref_cod_matricula_entrada ) )
		{
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$tmp_obj = new clsPmieducarMatricula( $ref_cod_matricula_entrada );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_matricula_entrada = $ref_cod_matricula_entrada;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_matricula_entrada = $ref_cod_matricula_entrada;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.matricula WHERE cod_matricula = '{$ref_cod_matricula_entrada}'" ) )
				{
					$this->ref_cod_matricula_entrada = $ref_cod_matricula_entrada;
				}
			}
		}
		if( is_numeric( $ref_cod_matricula_saida ) )
		{
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$tmp_obj = new clsPmieducarMatricula( $ref_cod_matricula_saida );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_matricula_saida = $ref_cod_matricula_saida;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_matricula_saida = $ref_cod_matricula_saida;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.matricula WHERE cod_matricula = '{$ref_cod_matricula_saida}'" ) )
				{
					$this->ref_cod_matricula_saida = $ref_cod_matricula_saida;
				}
			}
		}


		if( is_numeric( $cod_transferencia_solicitacao ) )
		{
			$this->cod_transferencia_solicitacao = $cod_transferencia_solicitacao;
		}
		if( is_string( $observacao ) )
		{
			$this->observacao = $observacao;
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
		if( is_string( $data_transferencia ) )
		{
			$this->data_transferencia = $data_transferencia;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_transferencia_tipo ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_matricula_saida ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_transferencia_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_transferencia_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_transferencia_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula_entrada ) )
			{
				$campos .= "{$gruda}ref_cod_matricula_entrada";
				$valores .= "{$gruda}'{$this->ref_cod_matricula_entrada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula_saida ) )
			{
				$campos .= "{$gruda}ref_cod_matricula_saida";
				$valores .= "{$gruda}'{$this->ref_cod_matricula_saida}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$campos .= "{$gruda}observacao";
				$valores .= "{$gruda}'{$this->observacao}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
//			$campos .= "{$gruda}ativo";
//			$valores .= "{$gruda}'1'";
//			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$campos .= "{$gruda}ativo";
				$valores .= "{$gruda}'{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_transferencia ) )
			{
				$campos .= "{$gruda}data_transferencia";
				$valores .= "{$gruda}'{$this->data_transferencia}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_transferencia_solicitacao_seq");
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
		if( is_numeric( $this->cod_transferencia_solicitacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_transferencia_tipo ) )
			{
				$set .= "{$gruda}ref_cod_transferencia_tipo = '{$this->ref_cod_transferencia_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula_entrada ) )
			{
				$set .= "{$gruda}ref_cod_matricula_entrada = '{$this->ref_cod_matricula_entrada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula_saida ) )
			{
				$set .= "{$gruda}ref_cod_matricula_saida = '{$this->ref_cod_matricula_saida}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$set .= "{$gruda}observacao = '{$this->observacao}'";
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
			if( is_string( $this->data_transferencia ) )
			{
				$set .= "{$gruda}data_transferencia = '{$this->data_transferencia}'";
				$gruda = ", ";
			}

//			die("UPDATE {$this->_tabela} SET $set WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'");
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'" );
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
	function lista( $int_cod_transferencia_solicitacao = null, $int_ref_cod_transferencia_tipo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_matricula_entrada = null, $int_ref_cod_matricula_saida = null, $str_observacao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $date_data_transferencia_ini = null, $date_data_transferencia_fim = null, $int_ref_cod_aluno = null, $entrada_aluno = false, $int_ref_cod_escola = null, $int_ref_cod_serie = null, $mes = null, $transferido = null, $bool_matricula_entrada = null, $parar=false )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ts, {$this->_schema}matricula m";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( !is_null($bool_matricula_entrada) )
		{
			if( $bool_matricula_entrada == true ) 
			{
				$filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada IS NOT NULL ";
				$whereAnd = " AND ";
			}
			else 
			{
				$filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada IS NULL ";
				$whereAnd = " AND ";
			}
		}
		if ($entrada_aluno == true)
		{
			$filtros .= "{$whereAnd}ts.ref_cod_matricula_entrada = m.cod_matricula";
			$whereAnd = " AND ";
		}
		else //if ($entrada_aluno == false)
		{
			$filtros .= "{$whereAnd}ts.ref_cod_matricula_saida = m.cod_matricula";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_cod_transferencia_solicitacao ) )
		{
			$filtros .= "{$whereAnd} ts.cod_transferencia_solicitacao = '{$int_cod_transferencia_solicitacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_transferencia_tipo ) )
		{
			$filtros .= "{$whereAnd} ts.ref_cod_transferencia_tipo = '{$int_ref_cod_transferencia_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ts.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ts.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_matricula_entrada ) )
		{
			$filtros .= "{$whereAnd} ts.ref_cod_matricula_entrada = '{$int_ref_cod_matricula_entrada}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_matricula_saida ) )
		{
			$filtros .= "{$whereAnd} ts.ref_cod_matricula_saida = '{$int_ref_cod_matricula_saida}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_observacao ) )
		{
			$filtros .= "{$whereAnd} ts.observacao LIKE '%{$str_observacao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} ts.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} ts.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} ts.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} ts.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ts.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ts.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_transferencia_ini ) )
		{
			$filtros .= "{$whereAnd} ts.data_transferencia >= '{$date_data_transferencia_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_transferencia_fim ) )
		{
			$filtros .= "{$whereAnd} ts.data_transferencia <= '{$date_data_transferencia_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_aluno ) )
		{
			$filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if($mes)
		{
			$mes = (int) $mes;
				$filtros .= "{$whereAnd} ( to_char(m.data_cadastro,'MM')::int = '$mes'
											OR to_char(m.data_exclusao,'MM')::int = '$mes' )";
			$whereAnd = " AND ";
		}
		if (is_bool($transferido))
		{
			if ($transferido == true)
			{
				$filtros .= "{$whereAnd} ts.data_transferencia IS NOT NULL";
				$whereAnd = " AND ";
			}
			else if ($transferido == false)
			{
				$filtros .= "{$whereAnd} ts.data_transferencia IS NULL";
				$whereAnd = " AND ";
			}
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
		if ($parar)
die($sql);
		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} ts, {$this->_schema}matricula m {$filtros}" );

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
		if( is_numeric( $this->cod_transferencia_solicitacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} ts WHERE ts.cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'" );
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
		if( is_numeric( $this->cod_transferencia_solicitacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'" );
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
		if( is_numeric( $this->cod_transferencia_solicitacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_transferencia_solicitacao = '{$this->cod_transferencia_solicitacao}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
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