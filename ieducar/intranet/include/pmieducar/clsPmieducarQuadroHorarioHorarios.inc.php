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
* Criado em 10/08/2006 11:25 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarQuadroHorarioHorarios
{
	var $ref_cod_quadro_horario;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_disciplina;
	var $sequencial;
	var $ref_cod_instituicao_substituto;
	var $ref_cod_instituicao_servidor;
	var $ref_servidor_substituto;
	var $ref_servidor;
	var $hora_inicial;
	var $hora_final;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $dia_semana;

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
	function clsPmieducarQuadroHorarioHorarios( $ref_cod_quadro_horario = null, $ref_ref_cod_serie = null, $ref_ref_cod_escola = null, $ref_ref_cod_disciplina = null, $sequencial = null, $ref_cod_instituicao_substituto = null, $ref_cod_instituicao_servidor = null, $ref_servidor_substituto = null, $ref_servidor = null, $hora_inicial = null, $hora_final = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $dia_semana = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}quadro_horario_horarios";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_quadro_horario, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, sequencial, ref_cod_instituicao_substituto, ref_cod_instituicao_servidor, ref_servidor_substituto, ref_servidor, hora_inicial, hora_final, data_cadastro, data_exclusao, ativo, dia_semana";

		if( is_numeric( $ref_servidor_substituto ) && is_numeric( $ref_cod_instituicao_substituto ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_servidor_substituto, null, null, null, null, null, null, $ref_cod_instituicao_substituto );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_servidor_substituto = $ref_servidor_substituto;
						$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_servidor_substituto = $ref_servidor_substituto;
						$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor_substituto}' AND ref_cod_instituicao = '{$ref_cod_instituicao_substituto}'" ) )
				{
					$this->ref_servidor_substituto = $ref_servidor_substituto;
					$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
				}
			}
		}

		if( is_numeric( $ref_servidor ) && is_numeric( $ref_cod_instituicao_servidor ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_servidor,null,null,null,null,null,null, $ref_cod_instituicao_servidor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_servidor = $ref_servidor;
						$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_servidor = $ref_servidor;
						$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor}' AND ref_cod_instituicao = '{$ref_cod_instituicao_servidor}'" ) )
				{
					$this->ref_servidor = $ref_servidor;
					$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
				}
			}
		}
		if ( is_numeric( $ref_servidor_substituto ) && is_numeric( $ref_cod_instituicao_substituto ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_servidor_substituto,null,null,null,null,null,null,null, $ref_cod_instituicao_substituto );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_servidor_substituto = $ref_servidor_substituto;
						$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_servidor_substituto = $ref_servidor_substituto;
						$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor_substituto}' AND ref_cod_instituicao = '{$ref_cod_instituicao_substituto}'" ) )
				{
					$this->ref_servidor_substituto = $ref_servidor_substituto;
					$this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_disciplina ) && is_numeric( $ref_ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarDisciplinaSerie" ) )
			{
				$tmp_obj = new clsPmieducarDisciplinaSerie( $ref_ref_cod_disciplina, $ref_ref_cod_serie, 1 );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_disciplina = $ref_ref_cod_disciplina;
						$this->ref_ref_cod_serie = $ref_ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_disciplina = $ref_ref_cod_disciplina;
						$this->ref_ref_cod_serie = $ref_ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.disciplina_serie WHERE ref_cod_disciplina = '{$ref_ref_cod_disciplina}' AND ref_cod_serie = '{$ref_ref_cod_serie}' AND ativo = '1'" ) )
				{
					$this->ref_ref_cod_disciplina = $ref_ref_cod_disciplina;
					$this->ref_ref_cod_serie = $ref_ref_cod_serie;
				}
			}
		}
		if ( is_numeric( $ref_ref_cod_escola ) && is_numeric( $ref_ref_cod_serie ) && is_numeric( $ref_ref_cod_disciplina ) )
		{
			if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerieDisciplina( $ref_ref_cod_serie, $ref_ref_cod_escola, $ref_ref_cod_disciplina, 1 );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_escola = '{$ref_ref_cod_escola}' AND ref_ref_cod_serie = '{$ref_ref_cod_serie}' AND ref_cod_disciplina = '{$ref_ref_cod_disciplina}' AND ativo = '1'" ) )
				{
					$this->ref_ref_cod_escola = $ref_ref_cod_escola;
				}
			}
		}
		if( is_numeric( $ref_cod_quadro_horario ) )
		{
			if( class_exists( "clsPmieducarQuadroHorario" ) )
			{
				$tmp_obj = new clsPmieducarQuadroHorario( $ref_cod_quadro_horario );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.quadro_horario WHERE cod_quadro_horario = '{$ref_cod_quadro_horario}'" ) )
				{
					$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( ( $hora_inicial ) )
		{
			$this->hora_inicial = $hora_inicial;
		}
		if( ( $hora_final ) )
		{
			$this->hora_final = $hora_final;
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
		if( is_numeric( $dia_semana ) )
		{
			$this->dia_semana = $dia_semana;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
//		echo "is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->ref_cod_instituicao_servidor ) && is_numeric( $this->ref_servidor ) && ( $this->hora_inicial ) && ( $this->hora_final ) && is_numeric( $this->dia_semana )";
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->ref_cod_instituicao_servidor ) && is_numeric( $this->ref_servidor ) && ( $this->hora_inicial ) && ( $this->hora_final ) && is_numeric( $this->dia_semana ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_quadro_horario ) )
			{
				$campos .= "{$gruda}ref_cod_quadro_horario";
				$valores .= "{$gruda}'{$this->ref_cod_quadro_horario}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->ref_ref_cod_disciplina}'";
				$gruda = ", ";
			}
			$this->sequencial = $db->CampoUnico( "SELECT ( COALESCE( MAX( sequencial ), 0 ) + 1 ) AS sequencial
												    FROM pmieducar.quadro_horario_horarios
												   WHERE ref_cod_quadro_horario = {$this->ref_cod_quadro_horario}
												     AND ref_cod_serie      = {$this->ref_ref_cod_serie}
												     AND ref_cod_escola		= {$this->ref_ref_cod_escola}" );
			$campos .= "{$gruda}sequencial";
			$valores .= "{$gruda}'{$this->sequencial}'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao_substituto ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao_substituto";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao_substituto}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_servidor ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao_servidor";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor_substituto ) )
			{
				$campos .= "{$gruda}ref_servidor_substituto";
				$valores .= "{$gruda}'{$this->ref_servidor_substituto}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor ) )
			{
				$campos .= "{$gruda}ref_servidor";
				$valores .= "{$gruda}'{$this->ref_servidor}'";
				$gruda = ", ";
			}
			if( ( $this->hora_inicial ) )
			{
				$campos .= "{$gruda}hora_inicial";
				$valores .= "{$gruda}'{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$campos .= "{$gruda}hora_final";
				$valores .= "{$gruda}'{$this->hora_final}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->dia_semana ) )
			{
				$campos .= "{$gruda}dia_semana";
				$valores .= "{$gruda}'{$this->dia_semana}'";
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
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->sequencial ) )
		{
			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_instituicao_substituto ) )
			{
				$set .= "{$gruda}ref_cod_instituicao_substituto = '{$this->ref_cod_instituicao_substituto}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}ref_cod_instituicao_substituto = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_servidor ) )
			{
				$set .= "{$gruda}ref_cod_instituicao_servidor = '{$this->ref_cod_instituicao_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor_substituto ) )
			{
				$set .= "{$gruda}ref_servidor_substituto = '{$this->ref_servidor_substituto}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}ref_servidor_substituto = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor ) )
			{
				$set .= "{$gruda}ref_servidor = '{$this->ref_servidor}'";
				$gruda = ", ";
			}
			if( ( $this->hora_inicial ) )
			{
				$set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$set .= "{$gruda}hora_final = '{$this->hora_final}'";
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
			if( is_numeric( $this->dia_semana ) )
			{
				$set .= "{$gruda}dia_semana = '{$this->dia_semana}'";
				$gruda = ", ";
			}
			if( $set )
			{
//				echo "UPDATE {$this->_tabela} SET $set WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'" ;die;
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'" );
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
	function lista( $int_ref_cod_quadro_horario = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_ref_cod_disciplina = null, $int_ref_ref_cod_turma = null, $int_sequencial = null, $int_ref_cod_instituicao_substituto = null, $int_ref_cod_instituicao_servidor = null, $int_ref_servidor_substituto = null, $int_ref_servidor = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_dia_semana = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} qhh";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_quadro_horario ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_quadro_horario = '{$int_ref_cod_quadro_horario}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_serie = '{$int_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_disciplina = '{$int_ref_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} qhh.sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_substituto ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_instituicao_substituto = '{$int_ref_cod_instituicao_substituto}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_servidor ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_servidor_substituto ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_servidor_substituto = '{$int_ref_servidor_substituto}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_servidor ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_servidor = '{$int_ref_servidor}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} qhh.hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} qhh.hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} qhh.hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} qhh.hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} qhh.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} qhh.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} qhh.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} qhh.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} qhh.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} qhh.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dia_semana ) )
		{
			$filtros .= "{$whereAnd} qhh.dia_semana = '{$int_dia_semana}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} qhh {$filtros}" );
	
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
	function detalhe($ref_cod_escola = null)
	{
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->sequencial ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		else if ( is_numeric( $ref_cod_escola) && is_numeric( $this->ref_cod_instituicao_servidor ) && is_numeric( $this->ref_servidor ) && is_string( $this->hora_inicial ) && is_string( $this->hora_final ) && is_numeric( $this->dia_semana ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_escola = {$ref_cod_escola} AND ref_cod_instituicao_servidor = {$this->ref_cod_instituicao_servidor} AND ref_servidor = {$this->ref_servidor} AND hora_inicial = '{$this->hora_inicial}' AND hora_final = '{$this->hora_final}' AND ativo = 1 AND dia_semana = {$this->dia_semana}" );
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
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_ref_cod_disciplina ) && is_numeric( $this->sequencial ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND ref_ref_cod_turma = '{$this->ref_ref_cod_turma}' AND sequencial = '{$this->sequencial}'" );
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
	 * Realiza a substituicao de um servidor
	 *
	 * @return bool
	 */
	function substituir_servidor($int_ref_cod_servidor_substituto)
	{
		if( is_numeric( $int_ref_cod_servidor_substituto ) && is_numeric( $this->ref_cod_instituicao_servidor ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $int_ref_cod_servidor_substituto,null,null,null,null,null,null,null, $this->ref_cod_instituicao_servidor );
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
				if( !$db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$int_ref_cod_servidor_substituto}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'" ) )
				{
					return false;
				}
			}
		}


		if( is_numeric( $this->ref_servidor ) && is_numeric( $this->ref_cod_instituicao_servidor ) )
		{


			//delete
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} SET ref_servidor='$int_ref_cod_servidor_substituto' , data_exclusao = NOW() WHERE ref_servidor = '{$this->ref_servidor}' AND ref_cod_instituicao_servidor = '{$this->ref_cod_instituicao_servidor}' " );
			return true;


		//$this->ativo = 0;
		//	return $this->edita();
		}
		return false;
	}

	/**
	 * Retorna um array com as turmas e horarios
	 *
	 * @return array
	 */
	function retornaHorario( $int_ref_cod_instituicao_servidor, $int_ref_ref_cod_escola, $int_ref_ref_cod_serie, $int_ref_ref_cod_turma, $int_dia_semana )
	{
		if( is_numeric( $int_ref_cod_instituicao_servidor ) && is_numeric( $int_ref_ref_cod_escola ) && is_numeric( $int_ref_ref_cod_serie ) && is_numeric( $int_ref_ref_cod_turma ) && is_numeric( $int_dia_semana ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT qhh.*
							  FROM {$this->_schema}quadro_horario_horarios qhh,
							       {$this->_schema}quadro_horario           qh,
							       {$this->_schema}turma					 t
							 WHERE qhh.ref_cod_serie				= t.ref_ref_cod_serie
							   AND qhh.ref_cod_escola				= t.ref_ref_cod_escola
							   AND t.cod_turma						= qh.ref_cod_turma
							   AND qhh.ref_cod_quadro_horario		= qh.cod_quadro_horario
							   AND t.cod_turma						= {$int_ref_ref_cod_turma}
							   AND qhh.ref_cod_instituicao_servidor = {$int_ref_cod_instituicao_servidor}
							   AND qhh.ref_cod_escola 				= {$int_ref_ref_cod_escola}
							   AND qhh.ref_cod_serie				= {$int_ref_ref_cod_serie}
							   AND qhh.dia_semana					= {$int_dia_semana}
							   AND qhh.ativo						= 1
						  ORDER BY hora_inicial" );

			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
			if( count( $resultado ) )
			{
				return $resultado;
			}
		}
		return false;
	}
	/**
	 * Exclui todos os registros de um quadro de horários
	 *
	 * @return bool
	 */
	function excluirTodos()
	{
		$db = new clsBanco();
		if( is_numeric( $this->ref_cod_quadro_horario ) )
		{
			$db->Consulta( "UPDATE {$this->_tabela} SET ativo = 0 WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}'" );
			return true;
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function listaHoras( $int_ref_cod_instituicao_servidor = null, $int_ativo = null, $int_dia_semana = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} qhh";
		$filtros = "";

		$whereAnd = " WHERE ";


		if( is_numeric( $int_ref_cod_instituicao_servidor ) )
		{
			$filtros .= "{$whereAnd} qhh.ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ativo ) )
		{
			$filtros .= "{$whereAnd} qhh.ativo = '{$int_ativo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dia_semana ) )
		{
			$filtros .= "{$whereAnd} qhh.dia_semana <> '{$int_dia_semana}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} qhh {$filtros}" );

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
}
?>