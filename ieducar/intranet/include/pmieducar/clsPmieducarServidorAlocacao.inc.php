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

class clsPmieducarServidorAlocacao
{
	var $cod_servidor_alocacao;
	var $ref_ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $ref_cod_servidor;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $carga_horaria;
	var $periodo;

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
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_group_by;


	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarServidorAlocacao( $cod_servidor_alocacao = null, $ref_ref_cod_instituicao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_escola = null, $ref_cod_servidor = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $carga_horaria = null, $periodo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor_alocacao";

		$this->_campos_lista = $this->_todos_campos = "cod_servidor_alocacao, ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, data_cadastro, data_exclusao, ativo, carga_horaria, periodo";

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
		if( is_numeric( $ref_cod_escola ) )
		{
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$tmp_obj = new clsPmieducarEscola( $ref_cod_escola );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'" ) )
				{
					$this->ref_cod_escola = $ref_cod_escola;
				}
			}
		}

		if( is_numeric( $ref_cod_servidor ) && is_numeric( $ref_ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_cod_servidor,null,null,null,null,null,null, $ref_ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
						$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
						$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_servidor = $ref_cod_servidor;
					$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
				}
			}
		}


		if ( is_numeric( $cod_servidor_alocacao ) )
		{
			$this->cod_servidor_alocacao = $cod_servidor_alocacao;
		}
		if ( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if ( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if ( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if ( is_string( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
		}
		if ( is_numeric( $periodo ) )
		{
			$this->periodo = $periodo;
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_servidor ) && is_string( $this->carga_horaria ) && ( $this->periodo ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_servidor ) )
			{
				$campos .= "{$gruda}ref_cod_servidor";
				$valores .= "{$gruda}'{$this->ref_cod_servidor}'";
				$gruda = ", ";
			}
			if( is_string( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( ( $this->periodo ) )
			{
				$campos .= "{$gruda}periodo";
				$valores .= "{$gruda}'{$this->periodo}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_servidor_alocacao_seq");
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
		if( is_numeric( $this->cod_servidor_alocacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
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
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_servidor ) )
			{
				$set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( ( $this->periodo ) )
			{
				$set .= "{$gruda}periodo = '{$this->periodo}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'" );
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
	function lista( $int_cod_servidor_alocacao = null, $int_ref_ref_cod_instituicao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_escola = null, $int_ref_cod_servidor = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_carga_horaria = null, $int_periodo = null,$bool_busca_nome = false, $boo_professor = null )
	{

		$filtros = "";
		$whereAnd = " WHERE ";

		if(is_bool($bool_busca_nome) && $bool_busca_nome == true)
		{
			$join = ", cadastro.pessoa p ";
			$filtros .= "{$whereAnd} sa.ref_cod_servidor = p.idpes";
			$whereAnd = " AND ";
			$this->_campos_lista .= ",p.nome";
		}

		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} sa{$join}";

		if( is_numeric( $int_cod_servidor_alocacao ) )
		{
			$filtros .= "{$whereAnd} sa.cod_servidor_alocacao = '{$int_cod_servidor_alocacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} sa.ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} sa.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} sa.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} sa.ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_servidor ) )
		{
			$filtros .= "{$whereAnd} sa.ref_cod_servidor = '{$int_ref_cod_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_carga_horaria ) )
		{
			$filtros .= "{$whereAnd} sa.carga_horaria = '{$int_carga_horaria}'";
			$whereAnd = " AND ";
		}
		if( ( $int_periodo ) )
		{
			$filtros .= "{$whereAnd} sa.periodo = '{$int_periodo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} sa.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} sa.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} sa.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} sa.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} sa.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} sa.ativo = '0'";
			$whereAnd = " AND ";
		}

		if(is_bool($boo_professor))
		{
			$not = $boo_professor? "=" : "!=";
			$filtros .= "{$whereAnd} EXISTS( SELECT 1 FROM pmieducar.servidor_funcao,pmieducar.funcao WHERE ref_cod_funcao = cod_funcao AND ref_cod_servidor = sa.ref_cod_servidor AND sa.ref_ref_cod_instituicao = ref_ref_cod_instituicao AND professor $not 1)";
			$whereAnd = " AND ";
		}



		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getGroupBy() . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} sa {$join} {$filtros}" );

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


	function listaEscolas($int_ref_ref_cod_instituicao = null)
	{

		if( is_numeric( $int_ref_ref_cod_instituicao ) )
		{

			$sql = "SELECT DISTINCT ref_cod_escola FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}' AND ativo = '1'";

			$db = new clsBanco();
			$resultado = array();

			$db->Consulta( $sql );

			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla;
			}

			if( count( $resultado ) )
			{
				return $resultado;
			}

			return false;
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
		if( is_numeric( $this->cod_servidor_alocacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'" );
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
		if( is_numeric( $this->cod_servidor_alocacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'" );
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
		if( is_numeric( $this->cod_servidor_alocacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Exclui um registro passando cod_Servidor escola e horario
	 *
	 * @return bool
	 */
	function excluir_horario()
	{
//		echo "if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->hora_inicial ) && is_numeric( $this->hora_final ))";die;
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->periodo ) )
		{


			//delete
		$db = new clsBanco();
		//echo "DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND hora_inicial = '{$this->hora_inicial}' AND hora_final = '{$this->hora_final}' " ;
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND periodo = '$this->periodo'" );
		return true;


		//$this->ativo = 0;
		//	return $this->edita();
		}
		return false;
	}

	/**
	 * Realiza a substituicao de um servidor
	 *
	 * @return bool
	 */
	function substituir_servidor($int_ref_cod_servidor_substituto)
	{
		if( is_numeric( $int_ref_cod_servidor_substituto ) && is_numeric( $this->ref_ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $int_ref_cod_servidor_substituto,null,null,null,null,null,null,null, $this->ref_ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( !$tmp_obj->existe() )
					{
						return false;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( !$tmp_obj->detalhe() )
					{
						return false;
					}
				}
			}
			else
			{
				if( !$db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'" ) )
				{
					return false;
				}
			}
		}
//		echo "if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->periodo ) && is_numeric( $this->carga_horaria ))";die;
		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola ) &&  is_numeric( $this->periodo ) && is_string( $this->carga_horaria ) )
		{


			//delete
			$db = new clsBanco();
			//echo "UPDATE {$this->_tabela} SET ref_cod_servidor='$int_ref_cod_servidor_substituto'  WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND carga_horaria = '{$this->carga_horaria}' AND periodo = '{$this->periodo}'" ;
			$db->Consulta( "UPDATE {$this->_tabela} SET ref_cod_servidor='$int_ref_cod_servidor_substituto'  WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND carga_horaria = '{$this->carga_horaria}' AND periodo = '{$this->periodo}'" );
			return true;


		//$this->ativo = 0;
		//	return $this->edita();
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
	 * Define campo para ser utilizado na clause group by
	 *
	 * @return null
	 */
	function setGroupBy( $strNomeCampo )
	{
		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_group_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo group by
	 *
	 * @return string
	 */
	function getGroupBy()
	{
		if( is_string( $this->_campo_group_by ) )
		{
			return " GROUP BY {$this->_campo_group_by} ";
		}
		return "";
	}
}
?>