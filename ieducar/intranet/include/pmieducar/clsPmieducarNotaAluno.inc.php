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
* Criado em 11/08/2006 17:44 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarNotaAluno
{
	var $cod_nota_aluno;
	var $ref_sequencial;
	var $ref_ref_cod_tipo_avaliacao;
	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_disciplina;
	var $ref_cod_matricula;
	var $ref_ref_cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $modulo;
	var $ref_cod_curso_disciplina;
	var $nota;

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
	function clsPmieducarNotaAluno( $cod_nota_aluno = null, $ref_sequencial = null, $ref_ref_cod_tipo_avaliacao = null, $ref_cod_serie = null, $ref_cod_escola = null, $ref_cod_disciplina = null, $ref_cod_matricula = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $modulo = null, $ref_cod_curso_disciplina = null, $nota = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}nota_aluno";

		$this->_campos_lista = $this->_todos_campos = "cod_nota_aluno, ref_sequencial, ref_ref_cod_tipo_avaliacao, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, ref_cod_matricula, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, ativo, modulo, ref_cod_curso_disciplina, nota";

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
		if( is_numeric( $ref_ref_cod_tipo_avaliacao ) && is_numeric( $ref_sequencial ) )
		{
			if( class_exists( "clsPmieducarTipoAvaliacaoValores" ) )
			{
				$tmp_obj = new clsPmieducarTipoAvaliacaoValores( $ref_ref_cod_tipo_avaliacao, $ref_sequencial );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_tipo_avaliacao = $ref_ref_cod_tipo_avaliacao;
						$this->ref_sequencial = $ref_sequencial;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_tipo_avaliacao = $ref_ref_cod_tipo_avaliacao;
						$this->ref_sequencial = $ref_sequencial;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.tipo_avaliacao_valores WHERE ref_cod_tipo_avaliacao = '{$ref_ref_cod_tipo_avaliacao}' AND sequencial = '{$ref_sequencial}'" ) )
				{
					$this->ref_ref_cod_tipo_avaliacao = $ref_ref_cod_tipo_avaliacao;
					$this->ref_sequencial = $ref_sequencial;
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
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.matricula WHERE cod_matricula = '{$ref_cod_matricula}'" ) )
				{
					$this->ref_cod_matricula = $ref_cod_matricula;
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


		if( is_numeric( $cod_nota_aluno ) )
		{
			$this->cod_nota_aluno = $cod_nota_aluno;
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
		if( is_numeric( $nota ) )
		{
			$this->nota = $nota;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
//		echo "is_numeric( {$this->ref_sequencial} ) && is_numeric( {$this->ref_ref_cod_tipo_avaliacao} ) && is_numeric( {$this->ref_cod_matricula} ) && is_numeric( {$this->ref_usuario_cad} ) && is_numeric( {$this->modulo} )<br>";
		if ( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->modulo ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_sequencial ) )
			{
				$campos .= "{$gruda}ref_sequencial";
				$valores .= "{$gruda}'{$this->ref_sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_tipo_avaliacao ) )
			{
				$campos .= "{$gruda}ref_ref_cod_tipo_avaliacao";
				$valores .= "{$gruda}'{$this->ref_ref_cod_tipo_avaliacao}'";
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
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
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
			if( is_numeric( $this->nota ) )
			{
				$campos .= "{$gruda}nota";
				$valores .= "{$gruda}'{$this->nota}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_nota_aluno_seq");
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
		if( is_numeric( $this->cod_nota_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_sequencial ) )
			{
				$set .= "{$gruda}ref_sequencial = '{$this->ref_sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_tipo_avaliacao ) )
			{
				$set .= "{$gruda}ref_ref_cod_tipo_avaliacao = '{$this->ref_ref_cod_tipo_avaliacao}'";
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
			if( is_numeric( $this->nota ) )
			{
				$set .= "{$gruda}nota = '{$this->nota}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'" );
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
	function lista( $int_cod_nota_aluno = null, $int_ref_sequencial = null, $int_ref_ref_cod_tipo_avaliacao = null, $int_ref_cod_serie = null, $int_ref_cod_escola = null, $int_ref_cod_disciplina = null, $int_ref_cod_matricula = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_modulo = null, $int_ref_cod_curso_disciplina = null, $int_nota = null)
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_nota_aluno ) )
		{
			$filtros .= "{$whereAnd} cod_nota_aluno = '{$int_cod_nota_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_sequencial ) )
		{
			$filtros .= "{$whereAnd} ref_sequencial = '{$int_ref_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_tipo_avaliacao ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_tipo_avaliacao = '{$int_ref_ref_cod_tipo_avaliacao}'";
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
		if( is_numeric( $int_nota ) )
		{
			$filtros .= "{$whereAnd} nota = '{$int_nota}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );
	
//		echo "<!--{$sql}-->";
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
		if( is_numeric( $this->cod_nota_aluno ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'" );
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
		if( is_numeric( $this->cod_nota_aluno ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'" );
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
		if( is_numeric( $this->cod_nota_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'" );
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
	 * calcula a média do aluno $cod_matricula na disciplina $cod_disciplina
	 *
	 * @param int  $cod_matricula
	 * @param int  $cod_disciplina
	 * @param int  $qtd_modulos
	 * @param float $media_sem_exame caso a media das notas esteja abaixo da media nao realiza arredondamento da media
	 * @param  float $media_com_exame caso a nota seja de exame deve ser informado true para que esta nota seja multiplicada por 2 conforme regras da instituicao
	 *
	 * @return float
	 */
	function getMediaAluno($cod_matricula,$cod_disciplina,$cod_serie,$qtd_modulos,$media_sem_exame = false,$media_com_exame = false)
	{
		if( is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($qtd_modulos) && $qtd_modulos && is_numeric($cod_serie) && $cod_serie )
		{
			$db = new clsBanco();
			/**
			 * para calcular a nota do exame,
			 * esta nota e multiplicada por 2
			 * e dividido pela quantidade de
			 * modulos da materia.. esta media
			 * pode ser arredondada
			 */
			$nota_exame = 0;

			if($media_com_exame)
			{
				/**
				 * diminui em 1 o numero de modulos para
				 * o calculo do exame, uma vez que a nota do
				 * exame eh multiplicada por 2 ex: 4 modulos + 1 exame => 5 + 5.5 + 6 + 7 + (4 * 2) / 5 = nota exame
				 */

				$nota_exame = $db->CampoUnico("
				SELECT tav.valor * 2
				FROM pmieducar.nota_aluno na
				, pmieducar.tipo_avaliacao_valores tav
				WHERE na.ref_cod_matricula = '{$cod_matricula}'
				AND na.ref_cod_disciplina = '{$cod_disciplina}'
				AND na.ref_cod_serie = '{$cod_serie}'
				AND na.ativo = 1
				AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
				AND tav.sequencial = na.ref_sequencial
				AND na.modulo = '{$qtd_modulos}'
				");
				
				/**
				 * diminiu em um no numero de modulos
				 * jah que a nota do exame eh multiplicada
				 * por 2 entao esta nota sera somada com as restantes
				 * e o calculo prossegue normalmente
				 */
				$qtd_modulos_sem_exame = $qtd_modulos -1;
			}
			else
			{
				$qtd_modulos_sem_exame = $qtd_modulos;
			}
			
			$soma = $db->CampoUnico("
			SELECT SUM( tav.valor )
			FROM pmieducar.nota_aluno na
			, pmieducar.tipo_avaliacao_valores tav
			WHERE na.ref_cod_matricula = '{$cod_matricula}'
			AND na.ref_cod_disciplina = '{$cod_disciplina}'
			AND na.ref_cod_serie = '{$cod_serie}'
			AND na.ativo = 1
			AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
			AND tav.sequencial = na.ref_sequencial
			AND na.modulo <= '{$qtd_modulos_sem_exame}'
			GROUP BY ref_cod_disciplina
			");
			/**
			 * notas +  nota exame
			 */
			if($media_com_exame)
			{
				$soma += $nota_exame;

			}
			if( $soma !== false )
			{
				$tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL LIMIT 1");
				if ($media_com_exame)
				{
					$media = $soma / ($qtd_modulos+1);
				}
				else 
				{
					$media = $soma / $qtd_modulos;
				}
				/**
				 * @author Haissam
				 * @see 15-12-2006
				 * quando for dar as notas e for calcular a ultima
				 * ao fazer a media e essa nota estiver abaixo nao
				 * pode ser feito o arredondamento, somente se estiver
				 *  acima da media deixando o aluno em exame
				 */
				if($media_sem_exame && !$media_com_exame/*nota com exame pode ser arredondada*/ )
				{
					if($media < $media_sem_exame)
						return $media;
				}
				$objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
				$objTipoAvaliacaoValores->setLimite(1);
				$objTipoAvaliacaoValores->setOrderby("valor DESC");
				$lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao,null,null,null,$media,$media);
				if(is_array($lista))
				{
					foreach ($lista AS $valor)
					{
						return $valor["valor"];
					}
				}
			}
		}
		return false;
	}
	
	function getMediaAlunoExame($cod_matricula,$cod_disciplina,$cod_serie,$qtd_modulos)
	{
		if (is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($cod_serie) && is_numeric($qtd_modulos))
		{
			$sqlNotas = "SELECT 
							SUM( tav.valor )
						FROM 
							pmieducar.nota_aluno na
							, pmieducar.tipo_avaliacao_valores tav
						WHERE 
							na.ref_cod_matricula = '{$cod_matricula}'
							AND na.ref_cod_disciplina = '{$cod_disciplina}'
							AND na.ref_cod_serie = '{$cod_serie}'
							AND na.ativo = 1
							AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
							AND tav.sequencial = na.ref_sequencial
							AND na.modulo <= {$qtd_modulos}";
			$sqlExame = "SELECT 
							na.nota * 2 
						FROM 
							pmieducar.nota_aluno na
						WHERE 
							na.ref_cod_matricula = '{$cod_matricula}' 
							AND na.ref_cod_disciplina = '{$cod_disciplina}' 
							AND na.ref_cod_serie = '{$cod_serie}' 
							AND na.ativo = 1 
							AND na.modulo = {$qtd_modulos} + 1";
			$db = new clsBanco();
			$somaNotas = $db->CampoUnico($sqlNotas);
			$notaExame = $db->CampoUnico($sqlExame);
			$media = ($somaNotas + $notaExame)/($qtd_modulos+2);
			$tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL ORDER BY modulo LIMIT 1");
			if (is_numeric($tipo_avaliacao))
			{
				$objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
				$objTipoAvaliacaoValores->setLimite(1);
				$objTipoAvaliacaoValores->setOrderby("valor DESC");
				$lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao,null,null,null,$media,$media);
				$lista = array_shift($lista);
				return $lista["valor"];
			}
		}
		return false;
	}

	/**
	 * calcula a média especial do aluno $cod_matricula na disciplina $cod_disciplina
	 * calculo = (quantidade de disciplinas acima da media / quantidades de disciplinas) * 10 ) tem que ser maior que a media
	 * se for maior o aluno esta aprovado
	 * @param int  $cod_matricula
	 * @param int  $cod_disciplina
	 *
	 * @return boolean
	 */
	function getMediaEspecialAluno($cod_matricula,$cod_serie,$cod_escola,$qtd_modulos,$media_curso_sem_exame)
	{
		if( is_numeric($cod_matricula) && is_numeric($cod_escola) && $cod_escola && is_numeric($qtd_modulos) && $qtd_modulos && is_numeric($cod_serie) && $cod_serie && is_numeric($media_curso_sem_exame) )
		{
			$db = new clsBanco();

			$objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina();
			$listaEscolaSerieDisciplina = $objEscolaSerieDisciplina->lista($cod_serie,$cod_escola,null,1);

			$disciplinas_acima_media = 0;
			$total_disciplinas       = count($listaEscolaSerieDisciplina);
			if($listaEscolaSerieDisciplina)
			{
				foreach ($listaEscolaSerieDisciplina as $key => $disciplina)
				{
					$objNotaAluno = new clsPmieducarNotaAluno();
					$media = $objNotaAluno->getMediaAluno($cod_matricula,$disciplina["ref_cod_disciplina"],$disciplina["ref_ref_cod_serie"],$qtd_modulos);
					if( $media >= $media_curso_sem_exame )
					{
						//media acima da media incrementa o numero de disciplinas acima da media
						$disciplinas_acima_media++;
					}
				}

				$media_final = ($disciplinas_acima_media / $total_disciplinas) * 10;

				$tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' LIMIT 1");
				$objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
				$objTipoAvaliacaoValores->setLimite(1);
				$objTipoAvaliacaoValores->setOrderby("valor DESC");
				if($media_final)
				{
					$lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao,null,null,null,$media_final,$media_final);
					if(is_array($lista))
					{
						foreach ($lista AS $valor)
						{
							return $valor["valor"];
						}
					}
				}


				return false;
			}
			return false;
		}
	}

	/**
	 * retorna a quantidade de disciplinas que a matricula $cod_matricula pegou exame
	 *
	 * @param int $cod_matricula
	 * @param int $qtd_modulos_normais
	 * @param float $media
	 *
	 * return int
	 *
	 */
	function getQtdMateriasExame($cod_matricula,$qtd_modulos_normais,$media, $nao_arredondar_nota = false)
	{
		$exames = 0;
		if( is_numeric($cod_matricula) && is_numeric($qtd_modulos_normais) && is_numeric($media) )
		{
			$medias = $this->getMediasAluno($cod_matricula,$qtd_modulos_normais, $nao_arredondar_nota);

			if( is_array($medias) )
			{
				foreach ($medias as $value)
				{
					if( $value["media"] < $media )
					{
						$exames++;
					}
				}
			}
		}
		return $exames;
	}

	/**
	 * retorna a quantidade de disciplinas que a matricula $cod_matricula ja recebeu nota no exame
	 *
	 * @param int $cod_matricula
	 * @param int $qtd_modulos_normais
	 *
	 * return int
	 *
	 */
	function getQtdNotasExame($cod_matricula,$qtd_modulos_normais)
	{
		$db = new clsBanco();
		return $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ativo = 1 AND modulo > '{$qtd_modulos_normais}'");
	}

	/**
	 * calcula as médias do aluno $cod_matricula em todas as disciplinas, encontra
	 * os que estão abaixo da média ($media) e retorna as disciplinas
	 *
	 * @param int $cod_matricula
	 * @param int $qtd_modulos
	 * @param int $media
	 *
	 * @return array
	 */
	function getDisciplinasExameDoAluno($cod_matricula,$qtd_modulos_normais,$media,$nao_arredondar_nota = false)
	{
		$exames = array();
		if( is_numeric($cod_matricula) && is_numeric($qtd_modulos_normais) && is_numeric($media) )
		{
			$medias = $this->getMediasAluno($cod_matricula,$qtd_modulos_normais,$arredondar_nota);
			if( is_array($medias) )
			{
				foreach ($medias as $value)
				{
					if( $value["media"] < $media )
					{
						$exames[] = array( "cod_disciplina" => $value["cod_disciplina"], "cod_serie" => $value["cod_serie"] );
					}
				}
			}
		}
		return $exames;
	}

	/**
	 * calcula as médias do aluno $cod_matricula em todas as disciplinas
	 *
	 * @param int $cod_matricula
	 * @param int $qtd_modulos
	 *
	 * @return array
	 */
	function getMediasAluno($cod_matricula,$qtd_modulos, $nao_arredondar_nota = false)
	{
		$retorno = array();
		if( is_numeric($cod_matricula) && is_numeric($qtd_modulos) && $qtd_modulos )
		{
			$i = 0;

			$db = new clsBanco();
			$db2 = new clsBanco();
			$db->Consulta("
			SELECT na.ref_cod_disciplina, na.ref_cod_serie, SUM( tav.valor )
			FROM pmieducar.nota_aluno na
			, pmieducar.tipo_avaliacao_valores tav
			WHERE na.ref_cod_matricula = '{$cod_matricula}'
			AND na.ativo = 1
			AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
			AND tav.sequencial = na.ref_sequencial
			AND na.modulo <= '{$qtd_modulos}'
			GROUP BY ref_cod_disciplina, ref_cod_serie
			");
			while ($db->ProximoRegistro())
			{
				list($cod_disciplina,$cod_serie,$soma) = $db->Tupla();
				$retorno[$i]["cod_disciplina"] = $cod_disciplina;
				$retorno[$i]["cod_serie"] = $cod_serie;

				$tipo_avaliacao = $db2->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL LIMIT 1");

				$media = $soma / $qtd_modulos;
				if (!$nao_arredondar_nota)
				{
					$objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
					$objTipoAvaliacaoValores->setLimite(1);
					$objTipoAvaliacaoValores->setOrderby("valor DESC");
					$lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao,null,null,null,$media,$media);
					foreach ($lista AS $valor)
					{
						$media_valor = $valor["valor"];
					}
				}
				else 
				{
					$media_valor = $media;
				}
				$retorno[$i]["media"] = $media_valor;
				$i++;
			}

		}
		return $retorno;
	}

	/**
	 * calcula as médias dos alunos da turma $cod_turma em todas as disciplinas, encontra
	 * os que estão abaixo da média ($media) e retorna as matriculas
	 *
	 * @param int $cod_turma
	 * @param int $qtd_modulos
	 * @param int $media
	 *
	 * @return array
	 */
	function getAlunosExame($cod_turma,$qtd_modulos,$media,$ref_cod_disciplina = null)
	{
		$retorno = array();
		if( is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos )
		{
			if(is_numeric($ref_cod_disciplina))
			{
				$disciplina_exame = " AND na.ref_cod_disciplina = '{$ref_cod_disciplina}' ";
			}

			$db = new clsBanco();
			$db->Consulta("
			SELECT ref_cod_matricula, ref_cod_disciplina, total_notas
			FROM
			(
				SELECT na.ref_cod_matricula
				, na.ref_cod_disciplina
				, SUM( tav.valor ) AS total_notas
				, COUNT(0) AS qtd_modulos
				, ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
				FROM pmieducar.nota_aluno na
				, pmieducar.tipo_avaliacao_valores tav
				, pmieducar.v_matricula_matricula_turma mmt
				WHERE na.ref_cod_matricula = mmt.cod_matricula
				AND mmt.ref_cod_turma = '{$cod_turma}'
				AND na.ativo = 1
				AND mmt.ativo = 1
				AND mmt.aprovado = 3
				AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
				AND tav.sequencial = na.ref_sequencial
				$disciplina_exame
				GROUP BY na.ref_cod_disciplina, na.ref_cod_matricula
			) AS sub
			WHERE qtd_modulos = '{$qtd_modulos}'
			AND ( permite_exame = TRUE OR permite_exame IS NULL )
			");
			while ($db->ProximoRegistro())
			{
				list($cod_matricula,$cod_disciplina,$soma) = $db->Tupla();
				if( ! isset($retorno[$cod_matricula]) )
				{
					if( $soma / $qtd_modulos < $media )
					{
						$retorno[$cod_matricula] = $cod_matricula;
					}
				}
			}
		}
		return $retorno;
	}

	/**
	 * calcula as médias dos alunos da turma $cod_turma em todas as disciplinas, encontra
	 * os que estão abaixo da média ($media) e retorna as disciplinas
	 *
	 * @param int $cod_turma
	 * @param int $qtd_modulos
	 * @param int $media
	 *
	 * @return array
	 */
	function getDisciplinasExame($cod_turma,$qtd_modulos,$media,$verifica_aluno_possui_nota=false)
	{
		$retorno = array();
		if( is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos )
		{
			$db = new clsBanco();
			$db->Consulta("
			SELECT ref_cod_matricula,  ref_cod_disciplina, soma, ref_cod_serie
			FROM
			(
				SELECT na.ref_cod_matricula
				, na.ref_cod_disciplina
				, SUM( tav.valor ) AS soma
				, na.ref_cod_serie
				, COUNT(tav.valor) AS qtd_notas
				, ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
				FROM pmieducar.nota_aluno na
				, pmieducar.tipo_avaliacao_valores tav
				, pmieducar.v_matricula_matricula_turma mmt
				WHERE na.ref_cod_matricula = mmt.cod_matricula
				AND mmt.ref_cod_turma = '{$cod_turma}'
				AND na.ativo = 1
				AND mmt.ativo = 1
				AND mmt.aprovado = 3
				AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
				AND tav.sequencial = na.ref_sequencial
				GROUP BY na.ref_cod_disciplina, na.ref_cod_matricula, na.ref_cod_serie
			) AS sub1
			WHERE qtd_notas = '{$qtd_modulos}'
			AND ( permite_exame = TRUE OR permite_exame IS NULL )
			");
			while ($db->ProximoRegistro())
			{
				list($cod_matricula,$cod_disciplina,$soma,$cod_serie) = $db->Tupla();
				if( ! isset($retorno["{$cod_serie}_{$cod_disciplina}"]) )
				{
					if ($verifica_aluno_possui_nota)
					{
						$obj_nota_aluno = new clsPmieducarNotaAluno();
						$lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, null, null, $cod_disciplina, $cod_matricula, null, null, null, null, null, null, 1, $qtd_modulos+1);
						if (!$lst_nota_aluno)
						{
							if( $soma / $qtd_modulos < $media )
							{
								$retorno["{$cod_serie}_{$cod_disciplina}"] = array("cod_serie" => $cod_serie, "cod_disciplina" => $cod_disciplina );
							}
						}
					}
					else
					{ 
						if( $soma / $qtd_modulos < $media )
						{
							$retorno["{$cod_serie}_{$cod_disciplina}"] = array("cod_serie" => $cod_serie, "cod_disciplina" => $cod_disciplina );
						}
					}
				}
			}
		}
		return $retorno;
	}


	/**
	 * calcula as médias dos alunos da turma $cod_turma em uma disciplina especifica $cod_disciplina, encontra
	 * os que estão abaixo da média ($media) e retorna as matriculas
	 *
	 * @param int $cod_turma
	 * @param int $cod_disciplina
	 * @param int $qtd_modulos
	 * @param int $media
	 *
	 * @return array
	 */
	function getAlunosDisciplinaExame($cod_turma,$cod_disciplina,$qtd_modulos,$media)
	{
		$retorno = array();
		if( is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos )
		{
			$db = new clsBanco();
			$db->Consulta("
			SELECT ref_cod_matricula, soma
			FROM
			(
				SELECT na.ref_cod_matricula
				, SUM( tav.valor ) AS soma
				, COUNT(tav.valor) AS qtd_notas
				, ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
				FROM pmieducar.nota_aluno na
				, pmieducar.tipo_avaliacao_valores tav
				, pmieducar.v_matricula_matricula_turma mmt
				WHERE na.ref_cod_matricula = mmt.cod_matricula
				AND na.ref_cod_disciplina = '{$cod_disciplina}'
				AND mmt.ref_cod_turma = '{$cod_turma}'
				AND na.ativo = 1
				AND mmt.ativo = 1
				AND mmt.aprovado = 3
				AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
				AND tav.sequencial = na.ref_sequencial
				GROUP BY na.ref_cod_matricula
			) AS sub1
			WHERE qtd_notas = '{$qtd_modulos}'
			AND ( permite_exame = TRUE OR permite_exame IS NULL )
			");
			while ($db->ProximoRegistro())
			{
				list($cod_matricula,$soma) = $db->Tupla();
				if( ! isset($retorno[$cod_matricula]) )
				{
					if( $soma / $qtd_modulos < $media )
					{
						$retorno[$cod_matricula] = $cod_matricula;
					}
				}
			}
		}
		return $retorno;
	}

	/**
	 * Funcao com nome comprido e escrotissimo pra poder dizer o que ela faz...
	 * retorna quantas notas a matricula $cod_matricula ainda vai receber em disciplinas que apuram falta, no modulo $modulo
	 *
	 * @param int $cod_matricula
	 * @param int $cod_serie
	 * @param int $cod_turma
	 * @param int $modulo
	 *
	 * @return int
	 *
	 */
	function getQtdRestanteNotasAlunoNaoApuraFaltas($cod_matricula,$cod_serie,$cod_turma,$modulo,$ref_cod_escola)
	{
		if( is_numeric($cod_matricula) )
		{
			$db = new clsBanco();
			/*$total = $db->CampoUnico("
			SELECT COUNT(0)
			FROM pmieducar.disciplina_serie ds,
			pmieducar.disciplina d
			WHERE ref_cod_serie = '{$cod_serie}'
			  AND d.ativo  = 1
			  AND ds.ativo = 1
			  AND d.cod_disciplina = ds.ref_cod_disciplina

			");*/
			$total = $db->CampoUnico("
			SELECT COUNT(0)
			FROM pmieducar.escola_serie_disciplina ds,
			pmieducar.disciplina d
			WHERE ref_ref_cod_serie = '{$cod_serie}'
			  AND ref_ref_cod_escola = '{$ref_cod_escola}'
			  AND d.ativo  = 1
			  AND ds.ativo = 1
			  AND d.cod_disciplina = ds.ref_cod_disciplina

			");
//		echo "SELECT COUNT(0)
//			FROM pmieducar.escola_serie_disciplina ds,
//			pmieducar.disciplina d
//			WHERE ref_ref_cod_serie = '{$cod_serie}'
//			  AND ref_ref_cod_escola = '{$ref_cod_escola}'
//			  AND d.ativo  = 1
//			  AND ds.ativo = 1
//			  AND d.cod_disciplina = ds.ref_cod_disciplina<br><br><br>";
			
			//			AND d.apura_falta = 1
			/**
			 * para faltas globalizada considerar todas as disciplinas
			 */
			//AND d.apura_falta = 1

			// uma lista de disciplinas que apuram falta
			// exclui dessa lista todas as que o aluno ja recebeu nota nesse modulo
			$ja_recebidas = $db->CampoUnico("
				SELECT COUNT(0)
				FROM pmieducar.nota_aluno na
				, pmieducar.disciplina d
				, pmieducar.v_matricula_matricula_turma mmt
				WHERE na.ref_cod_matricula = '{$cod_matricula}'
				AND na.ref_cod_matricula = mmt.cod_matricula
				AND mmt.ref_cod_turma = '{$cod_turma}'
				AND na.ativo = 1
				AND mmt.ativo = 1
				AND na.ref_cod_disciplina = d.cod_disciplina
				AND na.ref_cod_serie = '{$cod_serie}'
				AND na.modulo = '{$modulo}'
			");

//			die("SELECT COUNT(0)
//				FROM pmieducar.nota_aluno na
//				, pmieducar.disciplina d
//				, pmieducar.v_matricula_matricula_turma mmt
//				WHERE na.ref_cod_matricula = '{$cod_matricula}'
//				AND na.ref_cod_matricula = mmt.cod_matricula
//				AND mmt.ref_cod_turma = '{$cod_turma}'
//				AND na.ativo = 1
//				AND mmt.ativo = 1
//				AND na.ref_cod_disciplina = d.cod_disciplina
//				AND na.ref_cod_serie = '{$cod_serie}'
//				AND na.modulo = '{$modulo}'");
			//				AND mmt.aprovado = 3
			//AND d.apura_falta = 1
			//retorna a qtd restante
						
			return $total - $ja_recebidas;
		}
		return false;
	}


	/*
		coisas do nagasava que ficaram aqui pq podem estar sendo usadas em algum lugar
	*/


	/**
	 * Retorna uma variável com o resultado
	 *
	 * @return int
	 */
	function retornaDiscMod( $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_cod_disciplina = null, $int_ref_ref_cod_turma = null, $int_ref_cod_turma = null, $int_ref_cod_matricula = null, $conta = false, $int_modulos = null )
	{
		if ( is_numeric( $int_ref_ref_cod_serie ) && is_numeric( $int_ref_ref_cod_escola ) && is_numeric( $int_cod_disciplina ) && is_numeric( $int_ref_ref_cod_turma ) && is_numeric( $int_ref_cod_turma ) && is_numeric( $int_modulos ) )
		{
			$db = new clsBanco();

			if ( $conta )
			{
				/*$sql = "SELECT MIN ( ( SELECT COUNT(0)
					        	   		 FROM pmieducar.nota_aluno na
						       		    WHERE na.ref_cod_matricula = mt.ref_cod_matricula
							 		      AND na.disc_ref_cod_turma = mt.ref_cod_turma
									      AND na.ref_ref_cod_turma = mt.ref_cod_turma
									      AND na.disc_ref_ref_cod_serie = {$int_ref_ref_cod_serie}
									      AND na.disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
									      AND na.ativo = 1
									      AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina} ) )
				  		  FROM pmieducar.matricula_turma mt
				 		 WHERE mt.ref_cod_turma = {$int_ref_cod_turma}";*/
				/*$sql = "SELECT MIN( ( SELECT COUNT(0)
							            FROM pmieducar.nota_aluno na2
							           WHERE na2.ref_cod_matricula 	     = mt.ref_cod_matricula
									     AND na2.disc_ref_cod_turma 	     = mt.ref_cod_turma
									     AND na2.ref_ref_cod_turma 	         = mt.ref_cod_turma
								  	     AND na2.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
									     AND na2.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
									     AND na2.ativo 			             = 1
									     AND na2.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina ) )
						  FROM pmieducar.matricula            m,
						       pmieducar.matricula_turma     mt,
						       pmieducar.nota_aluno          na
						 WHERE m.cod_matricula                = mt.ref_cod_matricula
						   AND na.ref_cod_matricula       = m.cod_matricula
						   AND na.disc_ref_ref_cod_serie      = $int_ref_ref_cod_serie
						   AND na.disc_ref_ref_cod_escola     = $int_ref_ref_cod_escola
						   AND na.ativo 	                  = 1
						   AND na.disc_ref_ref_cod_disciplina = $int_cod_disciplina
						   AND mt.ref_cod_turma               = $int_ref_cod_turma
						   AND NOT EXISTS ( SELECT 1
								      FROM pmieducar.dispensa_disciplina dd
								     WHERE dd.ref_ref_cod_turma 	       = na.disc_ref_cod_turma
								       AND dd.ref_cod_matricula       = na.ref_cod_matricula
								       AND dd.disc_ref_ref_cod_turma      = na.disc_ref_cod_turma
								       AND dd.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
								       AND dd.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
								       AND dd.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina )";*/
				/*$sql = "SELECT MIN( ( SELECT COUNT(0)
										FROM pmieducar.nota_aluno na
									   WHERE na.ref_cod_matricula 		= mt.ref_cod_matricula
										 AND na.disc_ref_cod_turma 			= mt.ref_cod_turma
										 AND na.ref_ref_cod_turma 			= mt.ref_cod_turma
										 AND na.disc_ref_ref_cod_serie 		= m.ref_ref_cod_serie
										 AND na.disc_ref_ref_cod_escola 	= m.ref_ref_cod_escola
										 AND na.ativo 			     		= 1
										 AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina} ) )
								  FROM pmieducar.matricula       m,
								       pmieducar.matricula_turma mt
								 WHERE mt.ref_cod_matricula = m.cod_matricula
								   AND mt.ref_cod_turma     = {$int_ref_cod_turma}
								   AND m.ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
								   AND m.ref_ref_cod_escola = {$int_ref_ref_cod_escola}
								   AND m.ativo				= 1";*/

				$sql = "SELECT MIN( ( SELECT DISTINCT CASE WHEN ( SELECT 1
			           									   FROM pmieducar.dispensa_disciplina dd
														  WHERE dd.ref_ref_cod_turma 	       = na.ref_ref_cod_turma
														    AND dd.ref_cod_matricula       = na.ref_cod_matricula
														    AND dd.disc_ref_ref_cod_turma      = na.disc_ref_cod_turma
														    AND dd.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
														    AND dd.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
														    AND dd.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina ) = 1
												  THEN {$int_modulos}
												  ELSE
													( SELECT COUNT(0)
													    FROM pmieducar.nota_aluno n
												       WHERE n.ref_cod_matricula       = na.ref_cod_matricula
													     AND n.disc_ref_cod_turma 	       = na.disc_ref_cod_turma
													     AND n.ref_ref_cod_turma 	       = na.ref_ref_cod_turma
													     AND n.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
													     AND n.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
													     AND n.ativo 		       		   = 1
													     AND n.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina )
												  END
										FROM pmieducar.nota_aluno na
									   WHERE na.ref_cod_matricula 	    = mt.ref_cod_matricula
										 AND na.disc_ref_cod_turma 	    	= mt.ref_cod_turma
										 AND na.ref_ref_cod_turma 	    	= mt.ref_cod_turma
										 AND na.disc_ref_ref_cod_serie 	    = m.ref_ref_cod_serie
										 AND na.disc_ref_ref_cod_escola     = m.ref_ref_cod_escola
										 AND na.ativo 			    		= 1
										 AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina} ) )
					      FROM pmieducar.matricula       m,
						       pmieducar.matricula_turma mt
						 WHERE mt.ref_cod_matricula = m.cod_matricula
						   AND mt.ref_cod_turma     = {$int_ref_cod_turma}
						   AND m.ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
						   AND m.ref_ref_cod_escola = {$int_ref_ref_cod_escola}
						   AND m.ativo		    	= 1";
			}
			else
			{
				$sql = "SELECT MIN( qtd )
						  FROM ( SELECT DISTINCT COUNT( na.cod_nota_aluno ) AS qtd
								   FROM pmieducar.nota_aluno na
								  WHERE na.disc_ref_ref_cod_serie 	   = {$int_ref_ref_cod_serie}
								    AND na.disc_ref_ref_cod_escola     = {$int_ref_ref_cod_escola}
								    AND na.disc_ref_cod_turma 	   	   = {$int_ref_ref_cod_turma}
								    AND na.ref_ref_cod_turma 	   	   = {$int_ref_cod_turma}
								    AND na.ativo 			   		   = 1
								    AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina}
									AND na.disc_ref_ref_cod_disciplina NOT IN ( SELECT dd.disc_ref_ref_cod_disciplina
																			      FROM pmieducar.dispensa_disciplina dd
																			     WHERE dd.ref_ref_cod_turma 	      = na.ref_ref_cod_turma
																				   AND dd.ref_cod_matricula       = na.ref_cod_matricula
																				   AND dd.disc_ref_ref_cod_turma      = na.disc_ref_cod_turma
																				   AND dd.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
																				   AND dd.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
																				   AND dd.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina )";

				if ( is_numeric( $int_ref_cod_matricula ) )
				{
					$sql .= " AND ref_cod_matricula = {$int_ref_cod_matricula}";
				}

				$sql .= " GROUP BY ref_cod_matricula ) AS subquery";
			}
			//echo "{$sql}<br><br>";

			return $db->CampoUnico( $sql );
		}
		return false;
	}

	/**
	 * Retorna uma lista com as médias filtradas conforme os parâmetros
	 *
	 * @return array
	 */
	function listaMedias( $int_disc_ref_ref_cod_serie = null, $int_disc_ref_ref_cod_escola = null, $int_disc_ref_cod_turma = null, $int_ref_ref_cod_turma = null, $int_qtd_modulos = null, $int_ref_cod_curso = null, $aprovado = false, $reprovado = false, $exame = false, $andamento = false )
	{
		if ( is_numeric( $int_disc_ref_ref_cod_serie ) && is_numeric( $int_disc_ref_ref_cod_escola ) && is_numeric( $int_disc_ref_cod_turma ) && is_numeric( $int_ref_ref_cod_turma ) && is_numeric( $int_qtd_modulos ) && is_numeric( $int_ref_cod_curso ) )
		{
			$sql = "SELECT ( SELECT DISTINCT tav2.valor
							   FROM pmieducar.tipo_avaliacao_valores tav2
							  WHERE tav2.ref_cod_tipo_avaliacao = ( SELECT DISTINCT na2.ref_ref_cod_tipo_avaliacao
																	  FROM pmieducar.nota_aluno na2
																	 WHERE na2.ref_cod_serie  = {$int_disc_ref_ref_cod_serie}
																	   AND na2.ref_cod_escola = {$int_disc_ref_ref_cod_escola} )
								AND tav2.valor_min <= ( SUM( tav.valor ) / ( {$int_qtd_modulos} ) )
								AND tav2.valor_max >= ( SUM( tav.valor ) / ( {$int_qtd_modulos} ) ) ) as media,
						   CASE WHEN ( SELECT falta_ch_globalizada
									     FROM pmieducar.curso
									    WHERE cod_curso = {$int_ref_cod_curso} ) = 0 THEN ( SELECT ( ( SUM( fa.faltas ) * c.hora_falta ) / d.carga_horaria ) * 100
																					          FROM pmieducar.falta_aluno fa,
																						      	   pmieducar.disciplina   d,
																						    	   pmieducar.curso	   	  c
																					         WHERE fa.ref_cod_matricula  = na.ref_cod_matricula
																							   AND fa.ref_cod_disciplina = na.disc_ref_ref_cod_disciplina
																							   AND fa.ref_cod_disciplina = d.cod_disciplina
																							   AND c.cod_curso 			 = {$int_ref_cod_curso}
																							   AND fa.ref_cod_serie      = {$int_disc_ref_ref_cod_serie}
																							   AND fa.ref_cod_escola     = {$int_disc_ref_ref_cod_escola}
																					      GROUP BY c.hora_falta,
																						    	   d.carga_horaria)
						        ELSE ( SELECT ( ( SUM( f.falta ) * c.hora_falta ) / c.carga_horaria ) * 100
							        	 FROM pmieducar.faltas 	  f,
								     		  pmieducar.curso	  c
							       		WHERE f.ref_cod_matricula = na.ref_cod_matricula
								 		  AND c.cod_curso 	      = {$int_ref_cod_curso}
							    GROUP BY c.hora_falta,
								     	 c.carga_horaria )
								 END as faltas,
						   na.ref_cod_matricula,
					       na.ref_cod_disciplina
					  FROM pmieducar.nota_aluno na,
					       pmieducar.tipo_avaliacao_valores tav
					 WHERE na.ref_ref_cod_tipo_avaliacao = tav.ref_cod_tipo_avaliacao
					   AND na.ref_sequencial 	      	 = tav.sequencial
					   AND na.ref_cod_serie     		 = {$int_disc_ref_ref_cod_serie}
					   AND na.ref_cod_escola    		 = {$int_disc_ref_ref_cod_escola}
					   AND na.ref_cod_matricula NOT IN ( SELECT m.cod_matricula
															   FROM pmieducar.matricula m
															  WHERE m.ref_ref_cod_escola = na.disc_ref_ref_cod_escola
															    AND m.ref_ref_cod_serie  = na.disc_ref_ref_cod_serie
															    AND m.ultima_matricula   = 1
															    AND m.ativo		      	 = 1";
			if ( $aprovado || $reprovado || $exame || $andamento )
			{
				$sql    .= " AND (";
				$conexao = "";

				if ( $aprovado )
				{
					$sql    .= " {$conexao} m.aprovado = 1";
					$conexao = "OR";
				}
				if ( $reprovado )
				{
					$sql    .= " {$conexao} m.aprovado = 2";
					$conexao = "OR";
				}
				if ( $exame )
				{
					$sql    .= " {$conexao} m.aprovado = 7";
					$conexao = "OR";
				}
				if ( $andamento )
				{
					$sql    .= " {$conexao} m.aprovado = 3";
					$conexao = "OR";
				}
				$sql .= " )";
			}
			$sql .= " )
					 GROUP BY na.ref_cod_matricula,
						   	  na.ref_cod_disciplina";

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
		}
		return false;
	}
	 /**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function todasNotas( $int_disc_ref_ref_cod_serie = null, $int_disc_ref_ref_cod_escola = null, $int_disc_ref_cod_turma = null, $int_ref_ref_cod_turma = null, $int_qtd_modulos = null, $int_ref_cod_matricula = null )
	{
		$db = new clsBanco();

		$sql = "SELECT CASE WHEN ( ( SELECT ( COUNT( * ) - ( SELECT COUNT( * )
														       FROM pmieducar.dispensa_disciplina
														      WHERE ref_ref_cod_turma 	    = {$int_ref_ref_cod_turma}
														        AND ref_cod_matricula   = {$int_ref_cod_matricula}
														        AND disc_ref_ref_cod_turma  = {$int_disc_ref_cod_turma}
														        AND disc_ref_ref_cod_serie  = {$int_disc_ref_ref_cod_serie}
														        AND disc_ref_ref_cod_escola = {$int_disc_ref_ref_cod_escola} ) ) * {$int_qtd_modulos}
								       FROM pmieducar.turma_disciplina
								      WHERE ref_cod_turma  = {$int_disc_ref_cod_turma}
								        AND ref_cod_escola = {$int_disc_ref_ref_cod_escola}
								        AND ref_cod_serie  = {$int_disc_ref_ref_cod_serie} ) <= ( SELECT COUNT( * )
																						            FROM pmieducar.nota_aluno
																						           WHERE disc_ref_ref_cod_serie  = {$int_disc_ref_ref_cod_serie}
																							 	  	 AND disc_ref_ref_cod_escola = {$int_disc_ref_ref_cod_escola}
																							 	  	 AND disc_ref_cod_turma	     = {$int_disc_ref_cod_turma}
																							 	  	 AND ref_ref_cod_turma	     = {$int_ref_ref_cod_turma}
																							 	  	 AND ref_cod_matricula   = {$int_ref_cod_matricula} ) ) THEN 'S'
				        ELSE 'N'
						 END AS terminou";

		return $db->CampoUnico( $sql );
	}

	/**
	 * Retorna uma variável com o resultado
	 *
	 * @return int
	 */
	function retornaDiscNota( $int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma, $int_ref_cod_matricula, $int_num_modulo )
	{
		$db = new clsBanco();
		$sql = "SELECT count( cod_nota_aluno )
				  FROM pmieducar.nota_aluno
				 WHERE ref_cod_serie  	 = {$int_ref_ref_cod_serie}
				   AND ref_cod_escola 	 = {$int_ref_ref_cod_escola}
				   AND ativo 		     = 1
				   AND ref_cod_matricula = {$int_ref_cod_matricula}";

		$qtd_nota = $db->CampoUnico( $sql );

		$sql = "SELECT ( ( SELECT COUNT(*)
			     		     FROM pmieducar.disciplina_serie
			    		    WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(*)
												    						 	   FROM pmieducar.dispensa_disciplina
																		          WHERE ref_cod_matricula = {$int_ref_cod_matricula}
																		      	    AND ref_cod_serie 	  = {$int_ref_ref_cod_serie}
																		      	    AND ref_cod_escola 	  = {$int_ref_ref_cod_escola} ) )";

		$qtd_disc = $db->CampoUnico( $sql );

		return ( ( $int_num_modulo > 1 ) ? ( $qtd_nota - ( ( $int_num_modulo - 1 ) * $qtd_disc ) ) : ( $qtd_nota ) );
	}

	/**
	 * Total de notas de uma turma
	 *
	 * @return int
	 */
	function retornaTotalNotas( $int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma )
	{
		$db  = new clsBanco();
		$sql = "SELECT COUNT(0)
				  FROM pmieducar.nota_aluno
				 WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
				   AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
				   AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
				   AND ref_ref_cod_turma       = {$int_ref_cod_turma}";

		$qtd_nota = $db->CampoUnico( $sql );

		return $qtd_nota;
	}

	/**
	 * Total de notas de uma turma
	 *
	 * @return int
	 */
	function retornaModuloAluno( $int_ref_cod_serie, $int_ref_cod_escola, $int_ref_cod_matricula )
	{
		if ( is_numeric( $int_ref_cod_serie ) && is_numeric( $int_ref_cod_escola ) && is_numeric( $int_ref_cod_matricula ) )
		{
			$db  = new clsBanco();
/*
			SQLs do nagasava

			$sql = "SELECT ( CEIL(( SELECT COUNT(0)
								 FROM pmieducar.nota_aluno
								WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
								  AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
								  AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
								  AND ref_cod_matricula   = {$int_ref_cod_matricula}
								  AND ref_ref_cod_turma       = {$int_ref_cod_turma}) / (( SELECT COUNT(*)
																					        FROM pmieducar.turma_disciplina
																					       WHERE ref_cod_turma  = {$int_ref_cod_turma}
																							 AND ref_cod_escola = {$int_ref_ref_cod_escola}
																							 AND ref_cod_serie  = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(*)
																																		      	   FROM pmieducar.dispensa_disciplina
																																		     	  WHERE ref_ref_cod_turma 	   = {$int_ref_cod_turma}
																																		       		AND ref_cod_matricula   = {$int_ref_cod_matricula}
																																		       		AND disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
																																		       		AND disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
																																		       		AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} )) ))";
*/
/*
			$sql = "SELECT ( CEIL( ( SELECT COUNT(0)
									   FROM pmieducar.nota_aluno
									  WHERE ref_cod_serie  	  = {$int_ref_cod_serie}
									    AND ref_cod_escola 	  = {$int_ref_cod_escola}
									    AND ref_cod_matricula = {$int_ref_cod_matricula} ) / ( ( SELECT COUNT(*)
																								   FROM pmieducar.disciplina_serie
																								  WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT(*)
																																			         FROM pmieducar.dispensa_disciplina
																																			        WHERE ref_cod_matricula = {$int_ref_cod_matricula}
																																			          AND ref_cod_serie     = {$int_ref_cod_serie}
																																			          AND ref_cod_escola    = {$int_ref_cod_escola} ) ) ) )";
*/
			// nova versao do romulo
			$sql = "
				SELECT COALESCE( MIN(total), 0 ) FROM
				(
					SELECT COUNT(0) AS total
					FROM pmieducar.nota_aluno
					WHERE ref_cod_serie				= '{$int_ref_cod_serie}'
					AND ref_cod_escola				= '{$int_ref_cod_escola}'
					AND ref_cod_matricula			= '{$int_ref_cod_matricula}'
					AND ativo 						= 1
					GROUP BY ref_cod_disciplina
				) AS sub1
			";

			$qtd_nota = $db->CampoUnico( $sql );

			return $qtd_nota;
		}
		return false;
	}


	/**
	 * Total de notas do aluno em determinada disciplina
	 *
	 * @return int
	 */
	function getQtdNotas( $int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_cod_disciplina = null, $int_ref_cod_matricula = null, $int_ref_cod_curso_disciplina = null )
	{
		if ( is_numeric($int_ref_cod_matricula) )
		{
			$db  = new clsBanco();
			$sql = "SELECT COUNT(cod_nota_aluno)
					FROM pmieducar.nota_aluno
					WHERE ref_cod_matricula = '{$int_ref_cod_matricula}'
					AND ativo = 1";

			if ($int_ref_cod_disciplina)
				$sql .= " AND ref_cod_disciplina = '{$int_ref_cod_disciplina}'";

			if ($int_ref_cod_escola)
				$sql .= " AND ref_cod_escola = '{$int_ref_cod_escola}'";

			if ($int_ref_cod_serie)
				$sql .= " AND ref_cod_serie = '{$int_ref_cod_serie}'";

			if ($int_ref_cod_curso_disciplina)
				$sql .= " AND ref_cod_curso_disciplina = '{$int_ref_cod_curso_disciplina}'";

			$qtd_nota = $db->CampoUnico( $sql );

			return $qtd_nota;
		}
		return false;
	}

	/**
	 * Maximo de notas em uma matricula
	 *
	 * @return int
	 */
	function getMaxNotas( $int_ref_cod_matricula )
	{
		if ( is_numeric($int_ref_cod_matricula) )
		{
			$db  = new clsBanco();
			$sql = "SELECT
						max(modulo)
					FROM
						pmieducar.nota_aluno
					WHERE
						ref_cod_matricula = '{$int_ref_cod_matricula}'
						AND ativo = 1";

			$max_nota = $db->CampoUnico( $sql );

			return $max_nota;
		}
		return false;
	}
	
	/**
	 * Funcao que retorna a ultima nota do modulo para as series que 
	 * a ultima nota define a situacao do aluno 
	 *
	 * @param int $cod_matricula
	 * @param int $cod_disciplina
	 * @param int $cod_serie
	 * @param int $ultimo_modulo
	 *
	 * @return int
	 *
	 */
	function getUltimaNotaModulo($cod_matricula, $cod_disciplina, $cod_serie, $ultimo_modulo) {
		if (is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($cod_serie) && is_numeric($ultimo_modulo))
		{
			$sql = "SELECT tav.valor
					FROM pmieducar.nota_aluno na
					, pmieducar.tipo_avaliacao_valores tav
					WHERE na.ref_cod_matricula = '{$cod_matricula}'
					AND na.ref_cod_disciplina = '{$cod_disciplina}'
					AND na.ref_cod_serie = '{$cod_serie}'
					AND na.ativo = 1
					AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
					AND tav.sequencial = na.ref_sequencial
					AND na.modulo = '{$ultimo_modulo}'";
			$db = new clsBanco();
			return $db->CampoUnico($sql);
		}
		return false;
	}
	
}
?>
