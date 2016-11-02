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
* Criado em 11/08/2006 17:43 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarFaltaAluno
{
	var $cod_falta_aluno;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_disciplina;
	var $ref_cod_matricula;
	var $faltas;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $modulo;
	var $ref_cod_curso_disciplina;

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
	function clsPmieducarFaltaAluno( $cod_falta_aluno = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_serie = null, $ref_cod_escola = null, $ref_cod_disciplina = null, $ref_cod_matricula = null, $faltas = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $modulo = null, $ref_cod_curso_disciplina = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}falta_aluno";

		$this->_campos_lista = $this->_todos_campos = "cod_falta_aluno, ref_usuario_exc, ref_usuario_cad, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, ref_cod_matricula, faltas, data_cadastro, data_exclusao, ativo, modulo, ref_cod_curso_disciplina";

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
		if( is_numeric( $ref_cod_matricula ) )
		{
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$tmp_obj = new clsPmieducarMatricula( $ref_cod_matricula );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_matricula = $ref_cod_matricula;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_matricula = $ref_cod_matricula;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.matricula WHERE ref_cod_matricula = '{$ref_cod_matricula}'" ) )
				{
					$this->ref_cod_matricula = $ref_cod_matricula;
				}
			}
		}
		if( is_numeric( $ref_cod_disciplina ) && is_numeric( $ref_cod_escola ) && is_numeric( $ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerieDisciplina( $ref_cod_serie, $ref_cod_escola, $ref_cod_disciplina );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_disciplina = $ref_cod_disciplina;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_disciplina = $ref_cod_disciplina;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie_disciplina WHERE ref_cod_disciplina = '{$ref_cod_disciplina}' AND ref_cod_escola = '{$ref_cod_escola}' AND ref_cod_serie = '{$ref_cod_serie}'" ) )
				{
					$this->ref_cod_disciplina = $ref_cod_disciplina;
					$this->ref_cod_escola = $ref_cod_escola;
					$this->ref_cod_serie = $ref_cod_serie;
				}
			}
		}
		if( is_numeric( $ref_cod_curso_disciplina ) )
		{
			if( class_exists( "clsPmieducarDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarDisciplina( $ref_cod_curso_disciplina );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_curso_disciplina = $ref_cod_curso_disciplina;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_curso_disciplina = $ref_cod_curso_disciplina;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.disciplina WHERE cod_disciplina = '{$ref_cod_curso_disciplina}'" ) )
				{
					$this->ref_cod_curso_disciplina = $ref_cod_curso_disciplina;
				}
			}
		}

		if( is_numeric( $cod_falta_aluno ) )
		{
			$this->cod_falta_aluno = $cod_falta_aluno;
		}
		if( is_numeric( $faltas ) )
		{
			$this->faltas = $faltas;
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
		if( is_numeric( $modulo ) )
		{
			$this->modulo = $modulo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
//		echo "if( is_numeric( {$this->ref_usuario_cad} ) && is_numeric( {$this->ref_cod_matricula} ) && is_numeric( {$this->faltas} ) && is_numeric( {$this->modulo} ) )<br>";
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->faltas ) && is_numeric( $this->modulo ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$campos .= "{$gruda}ref_cod_matricula";
				$valores .= "{$gruda}'{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->faltas ) )
			{
				$campos .= "{$gruda}faltas";
				$valores .= "{$gruda}'{$this->faltas}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->modulo ) )
			{
				$campos .= "{$gruda}modulo";
				$valores .= "{$gruda}'{$this->modulo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_curso_disciplina";
				$valores .= "{$gruda}'{$this->ref_cod_curso_disciplina}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_falta_aluno_seq");
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
		if( is_numeric( $this->cod_falta_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

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
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->faltas ) )
			{
				$set .= "{$gruda}faltas = '{$this->faltas}'";
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
			if( is_numeric( $this->modulo ) )
			{
				$set .= "{$gruda}modulo = '{$this->modulo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso_disciplina ) )
			{
				$set .= "{$gruda}ref_cod_curso_disciplina = '{$this->ref_cod_curso_disciplina}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_falta_aluno = '{$this->cod_falta_aluno}'" );
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
	function lista( $int_cod_falta_aluno = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_serie = null, $int_ref_cod_escola = null, $int_ref_cod_disciplina = null, $int_ref_cod_matricula = null, $int_faltas = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_modulo = null, $int_ref_cod_curso_disciplina = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_falta_aluno ) )
		{
			$filtros .= "{$whereAnd} cod_falta_aluno = '{$int_cod_falta_aluno}'";
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
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_matricula ) )
		{
			$filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_faltas ) )
		{
			$filtros .= "{$whereAnd} faltas >= '{$int_faltas}'";
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
		if( is_numeric( $int_modulo ) )
		{
			$filtros .= "{$whereAnd} modulo = '{$int_modulo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso_disciplina ) )
		{
			$filtros .= "{$whereAnd} ref_cod_curso_disciplina = '{$int_ref_cod_curso_disciplina}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

//		echo "<!-- $sql -->";
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
	 * Retorna a quantidade total de faltas da matricula $cod_matricula em todas as disciplinas
	 *
	 * @param int $cod_matricula
	 * @return int
	 */
	function total_faltas($cod_matricula)
	{
		$db = new clsBanco();
		if( is_numeric($cod_matricula) )
		{
			return $db->CampoUnico("SELECT SUM(faltas) FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}'");
		}
		return 0;
	}

	/**
	 * Retorna a quantidade total de faltas da matricula $cod_matricula na disciplina $cod_disciplina
	 *
	 * @param int $cod_matricula
	 * @param int $cod_disciplina
	 * @return int
	 */
	function total_faltas_disciplina($cod_matricula,$cod_disciplina,$cod_serie)
	{
		$db = new clsBanco();
		if( is_numeric($cod_matricula) && is_numeric($cod_disciplina) )
		{
			return $db->CampoUnico("SELECT SUM(faltas) FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_cod_disciplina = '{$cod_disciplina}' AND ref_cod_serie = '{$cod_serie}'");
		}
		return 0;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->cod_falta_aluno ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_falta_aluno = '{$this->cod_falta_aluno}'" );
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
		if( is_numeric( $this->cod_falta_aluno ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_falta_aluno = '{$this->cod_falta_aluno}'" );
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
		if( is_numeric( $this->cod_falta_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_falta_aluno = '{$this->cod_falta_aluno}'" );
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