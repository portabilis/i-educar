<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidorAfastamento
{
	var $ref_cod_servidor;
	var $sequencial;
	var $ref_cod_motivo_afastamento;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $data_retorno;
	var $data_saida;
	var $ativo;
	var $ref_cod_instituicao;

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
	function clsPmieducarServidorAfastamento( $ref_cod_servidor = null, $sequencial = null, $ref_cod_motivo_afastamento = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $data_retorno = null, $data_saida = null, $ativo = null, $ref_cod_instituicao = null )
	{
	  $db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor_afastamento";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_servidor, sequencial, ref_cod_motivo_afastamento, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, data_retorno, data_saida, ativo";

		if( is_numeric( $ref_cod_motivo_afastamento ) )
		{
			if( class_exists( "clsPmieducarMotivoAfastamento" ) )
			{
				$tmp_obj = new clsPmieducarMotivoAfastamento( $ref_cod_motivo_afastamento, null, null,null, null, null, null, 1, $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_motivo_afastamento = $ref_cod_motivo_afastamento;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_motivo_afastamento = $ref_cod_motivo_afastamento;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.motivo_afastamento WHERE cod_motivo_afastamento = '{$ref_cod_motivo_afastamento}' AND ref_cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_motivo_afastamento = $ref_cod_motivo_afastamento;
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
		if( is_numeric( $ref_cod_servidor ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_cod_servidor, null, null, null, null, null, 1, $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_servidor = $ref_cod_servidor;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_string( $data_retorno ) )
		{
			$this->data_retorno = $data_retorno;
		}
		if( is_string( $data_saida ) )
		{
			$this->data_saida = $data_saida;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_cod_motivo_afastamento ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->data_saida ) && ( $this->ref_cod_instituicao ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_servidor ) )
			{
				$campos .= "{$gruda}ref_cod_servidor";
				$valores .= "{$gruda}'{$this->ref_cod_servidor}'";
				$gruda = ", ";
			}
			$this->sequencial = $db->CampoUnico( "SELECT COALESCE( MAX( sa.sequencial ), 0 ) + 1 AS sequencial
												    FROM pmieducar.servidor_afastamento sa
												   WHERE sa.ref_cod_servidor        = {$this->ref_cod_servidor}
												     AND sa.ref_ref_cod_instituicao = {$this->ref_cod_instituicao}" );
			$campos .= "{$gruda}sequencial";
			$valores .= "{$gruda}'{$this->sequencial}'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_motivo_afastamento ) )
			{
				$campos .= "{$gruda}ref_cod_motivo_afastamento";
				$valores .= "{$gruda}'{$this->ref_cod_motivo_afastamento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if ( $this->data_retorno != '' ) {
				if( is_string( $this->data_retorno ) )
				{
					$campos .= "{$gruda}data_retorno";
					$valores .= "{$gruda}'{$this->data_retorno}'";
					$gruda = ", ";
				}
			}
			if( is_string( $this->data_saida ) )
			{
				$campos .= "{$gruda}data_saida";
				$valores .= "{$gruda}'{$this->data_saida}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
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
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_motivo_afastamento ) )
			{
				$set .= "{$gruda}ref_cod_motivo_afastamento = '{$this->ref_cod_motivo_afastamento}'";
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
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_string( $this->data_retorno ) && $this->data_retorno != '' )
			{
				$set .= "{$gruda}data_retorno = '{$this->data_retorno}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_saida ) )
			{
				$set .= "{$gruda}data_saida = '{$this->data_saida}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
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
	function lista( $int_ref_cod_servidor = null, $int_sequencial = null, $int_ref_cod_motivo_afastamento = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $date_data_retorno_ini = null, $date_data_retorno_fim = null, $date_data_saida_ini = null, $date_data_saida_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_servidor ) )
		{
			$filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_motivo_afastamento ) )
		{
			$filtros .= "{$whereAnd} ref_cod_motivo_afastamento = '{$int_ref_cod_motivo_afastamento}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
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
		if( is_string( $date_data_retorno_ini ) )
		{
			$filtros .= "{$whereAnd} data_retorno >= '{$date_data_retorno_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_retorno_fim ) )
		{
			$filtros .= "{$whereAnd} data_retorno <= '{$date_data_retorno_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_saida_ini ) )
		{
			$filtros .= "{$whereAnd} data_saida >= '{$date_data_saida_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_saida_fim ) )
		{
			$filtros .= "{$whereAnd} data_saida <= '{$date_data_saida_fim}'";
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
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
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
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
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
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}' AND ref_ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
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
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND sequencial = '{$this->sequencial}'" );
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

	/**
	 * Retorna um character dizendo se o servidor está afastado (campo sequencial) ou não (0)
	 *
	 * @return array
	 */
	function afastado( $int_ref_cod_servidor = null, $int_ref_cod_instituicao = null )
	{
		if( is_numeric( $int_ref_cod_servidor ) && is_numeric( $int_ref_cod_instituicao ) )
		{
			$db = new clsBanco();
			return $db->CampoUnico( "SELECT CASE WHEN MAX( sa.sequencial ) > 0 THEN MAX( sa.sequencial )
										    ELSE 0
										    END
									   FROM pmieducar.servidor_afastamento sa
									  WHERE sa.ref_cod_servidor 	   = {$int_ref_cod_servidor}
										AND sa.ref_ref_cod_instituicao = {$int_ref_cod_instituicao}
										AND (sa.data_retorno            IS NULL
										   OR sa.data_retorno          > NOW() )
										AND sa.ativo                   = 1" );
		}
		return false;
	}
}