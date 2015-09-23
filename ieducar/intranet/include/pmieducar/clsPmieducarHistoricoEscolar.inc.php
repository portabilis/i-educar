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

class clsPmieducarHistoricoEscolar
{
	var $ref_cod_aluno;
	var $sequencial;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $carga_horaria;
	var $dias_letivos;
	var $escola;
	var $escola_cidade;
	var $escola_uf;
	var $observacao;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $faltas_globalizadas;
	var $frequencia;

	var $ref_cod_instituicao;
	var $nm_serie;
	var $origem;
	var $extra_curricular;
	var $ref_cod_matricula;

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
	function clsPmieducarHistoricoEscolar( $ref_cod_aluno = null, $sequencial = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_serie = null, $ano = null, $carga_horaria = null, $dias_letivos = null, $escola = null, $escola_cidade = null, $escola_uf = null, $observacao = null, $aprovado = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $faltas_globalizadas = null, $ref_cod_instituicao = null, $origem = null, $extra_curricular = null, $ref_cod_matricula = null, $frequencia = null, $registro = null, $livro = null, $folha = null, $nm_curso = null, $historico_grade_curso_id = null, $aceleracao = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}historico_escolar";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_aluno, sequencial, ref_usuario_exc, ref_usuario_cad, ano, carga_horaria, dias_letivos, escola, escola_cidade, escola_uf, observacao, aprovado, data_cadastro, data_exclusao, ativo, faltas_globalizadas, ref_cod_instituicao, nm_serie, origem, extra_curricular, ref_cod_matricula, frequencia, registro, livro, folha, nm_curso, historico_grade_curso_id, aceleracao";

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
		if( is_numeric( $ref_cod_aluno ) )
		{
			if( class_exists( "clsPmieducarAluno" ) )
			{
				$tmp_obj = new clsPmieducarAluno( $ref_cod_aluno );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_aluno = $ref_cod_aluno;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_aluno = $ref_cod_aluno;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.aluno WHERE cod_aluno = '{$ref_cod_aluno}'" ) )
				{
					$this->ref_cod_aluno = $ref_cod_aluno;
				}
			}
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

		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}
		if( is_numeric( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
		}
		if( is_numeric( $dias_letivos ) )
		{
			$this->dias_letivos = $dias_letivos;
		}
		if( is_string( $escola ) )
		{
			$this->escola = $escola;
		}
		if( is_string( $escola_cidade ) )
		{
			$this->escola_cidade = $escola_cidade;
		}
		if( is_string( $escola_uf ) )
		{
			$this->escola_uf = $escola_uf;
		}
		if( is_string( $observacao ) )
		{
			$this->observacao = $observacao;
		}
		if( is_numeric( $aprovado ) )
		{
			$this->aprovado = $aprovado;
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
		if( is_numeric( $faltas_globalizadas ) || $faltas_globalizadas == 'NULL')
		{
			$this->faltas_globalizadas = $faltas_globalizadas;
		}
		if( is_numeric( $origem ) )
		{
			$this->origem = $origem;
		}
		if( is_numeric( $extra_curricular ) )
		{
			$this->extra_curricular = $extra_curricular;
		}
		if( is_string( $nm_serie ) )
		{
			$this->nm_serie = $nm_serie;
		}
		if( is_numeric( $frequencia ) )
		{
			$this->frequencia = $frequencia;
		}

    $this->registro = $registro;
    $this->livro = $livro;
    $this->folha = $folha;
    $this->nm_curso = $nm_curso;
    $this->historico_grade_curso_id = $historico_grade_curso_id;
    $this->aceleracao = $aceleracao;
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_serie ) && is_numeric( $this->ano ) && is_numeric( $this->carga_horaria ) && is_string( $this->escola ) && is_string( $this->escola_cidade ) && is_numeric( $this->aprovado ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->frequencia))
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_aluno ) )
			{
				$campos .= "{$gruda}ref_cod_aluno";
				$valores .= "{$gruda}'{$this->ref_cod_aluno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_serie ) )
			{
				$campos .= "{$gruda}nm_serie";
				$valores .= "{$gruda}'{$this->nm_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->origem ) )
			{
				$campos .= "{$gruda}origem";
				$valores .= "{$gruda}'{$this->origem}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->extra_curricular ) )
			{
				$campos .= "{$gruda}extra_curricular";
				$valores .= "{$gruda}'{$this->extra_curricular}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$campos .= "{$gruda}ref_cod_matricula";
				$valores .= "{$gruda}'{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias_letivos ) )
			{
				$campos .= "{$gruda}dias_letivos";
				$valores .= "{$gruda}'{$this->dias_letivos}'";
				$gruda = ", ";
			}
			if( is_string( $this->escola ) )
			{
				$campos .= "{$gruda}escola";
				$valores .= "{$gruda}'{$this->escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->escola_cidade ) )
			{
				$campos .= "{$gruda}escola_cidade";
				$valores .= "{$gruda}'{$this->escola_cidade}'";
				$gruda = ", ";
			}
			if( is_string($this->escola_uf) )
			{
				$campos .= "{$gruda}escola_uf";
				$valores .= "{$gruda}'{$this->escola_uf}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$campos .= "{$gruda}observacao";
				$valores .= "{$gruda}'{$this->observacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aprovado ) )
			{
				$campos .= "{$gruda}aprovado";
				$valores .= "{$gruda}'{$this->aprovado}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->faltas_globalizadas ) )
			{
				$campos .= "{$gruda}faltas_globalizadas";
				$valores .= "{$gruda}'{$this->faltas_globalizadas}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->frequencia ) )
			{
				$campos .= "{$gruda}frequencia";
				$valores .= "{$gruda}'{$this->frequencia}'";
				$gruda = ", ";
			}

			if( is_string( $this->registro ))
			{
				$campos .= "{$gruda}registro";
				$valores .= "{$gruda}'{$this->registro}'";
				$gruda = ", ";
			}

			if( is_string( $this->livro ))
			{
				$campos .= "{$gruda}livro";
				$valores .= "{$gruda}'{$this->livro}'";
				$gruda = ", ";
			}

			if( is_string( $this->folha ))
			{
				$campos .= "{$gruda}folha";
				$valores .= "{$gruda}'{$this->folha}'";
				$gruda = ", ";
			}

			if( is_string( $this->nm_curso ))
			{
				$campos .= "{$gruda}nm_curso";
				$valores .= "{$gruda}'{$this->nm_curso}'";
				$gruda = ", ";
			}

			if( is_numeric( $this->historico_grade_curso_id ))
			{
				$campos .= "{$gruda}historico_grade_curso_id";
				$valores .= "{$gruda}'{$this->historico_grade_curso_id}'";
				$gruda = ", ";
			}

      if (is_numeric($aceleracao)) {
				$campos .= "{$gruda}aceleracao";
				$valores .= "{$gruda}'{$this->aceleracao}'";
				$gruda = ", ";
      }

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";

			$sequencial = $db->campoUnico("SELECT COALESCE( MAX(sequencial), 0 ) + 1 FROM {$this->_tabela} WHERE ref_cod_aluno = {$this->ref_cod_aluno}" );

			$db->Consulta( "INSERT INTO {$this->_tabela} ( sequencial, $campos ) VALUES( $sequencial, $valores )" );
//			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $sequencial;
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
		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_string( $this->nm_serie ) )
			{
				$set .= "{$gruda}nm_serie = '{$this->nm_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->origem ) )
			{
				$set .= "{$gruda}origem = '{$this->origem}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->extra_curricular ) )
			{
				$set .= "{$gruda}extra_curricular = '{$this->extra_curricular}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias_letivos ) )
			{
				$set .= "{$gruda}dias_letivos = '{$this->dias_letivos}'";
				$gruda = ", ";
			}
			if( is_string( $this->escola ) )
			{
				$set .= "{$gruda}escola = '{$this->escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->escola_cidade ) )
			{
				$set .= "{$gruda}escola_cidade = '{$this->escola_cidade}'";
				$gruda = ", ";
			}
			if( is_string( $this->escola_uf ) )
			{
				$set .= "{$gruda}escola_uf = '{$this->escola_uf}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$set .= "{$gruda}observacao = '{$this->observacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aprovado ) )
			{
				$set .= "{$gruda}aprovado = '{$this->aprovado}'";
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
			if( is_numeric( $this->frequencia ) )
			{
				$set .= "{$gruda}frequencia = '{$this->frequencia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->faltas_globalizadas ) )
			{
				$set .= "{$gruda}faltas_globalizadas = '{$this->faltas_globalizadas}'";
				$gruda = ", ";
			}
			elseif ($this->faltas_globalizadas == 'NULL')
			{
				$set .= "{$gruda}faltas_globalizadas = NULL";
				$gruda = ", ";
			}

			if( is_string( $this->registro))
			{
				$set .= "{$gruda}registro = '{$this->registro}'";
				$gruda = ", ";
			}

			if( is_string( $this->livro))
			{
				$set .= "{$gruda}livro = '{$this->livro}'";
				$gruda = ", ";
			}

			if( is_string( $this->folha))
			{
				$set .= "{$gruda}folha = '{$this->folha}'";
				$gruda = ", ";
			}

			if( is_string( $this->nm_curso))
			{
				$set .= "{$gruda}nm_curso = '{$this->nm_curso}'";
				$gruda = ", ";
			}

			if( is_numeric( $this->historico_grade_curso_id))
			{
				$set .= "{$gruda}historico_grade_curso_id = '{$this->historico_grade_curso_id}'";
				$gruda = ", ";
			}

			if( is_numeric( $this->aceleracao))
			{
				$set .= "{$gruda}aceleracao = '{$this->aceleracao}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'" );
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
	function lista( $int_ref_cod_aluno = null, $int_sequencial = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_serie = null, $int_ano = null, $int_carga_horaria = null, $int_dias_letivos = null, $str_escola = null, $str_escola_cidade = null, $str_escola_uf = null, $str_observacao = null, $int_aprovado = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_faltas_globalizadas = null, $int_ref_cod_instituicao = null, $int_origem = null, $int_extra_curricular = null, $int_ref_cod_matricula = null, $int_frequencia = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_aluno ) )
		{
			$filtros .= "{$whereAnd} ref_cod_aluno = '{$int_ref_cod_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
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
		if( is_string( $str_nm_serie ) )
		{
			$filtros .= "{$whereAnd} nm_serie = '{$str_nm_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_origem ) )
		{
			$filtros .= "{$whereAnd} origem = '{$int_origem}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_extra_curricular ) )
		{
			$filtros .= "{$whereAnd} extra_curricular = '{$int_extra_curricular}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_matricula ) )
		{
			$filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} ano = '{$int_ano}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_carga_horaria ) )
		{
			$filtros .= "{$whereAnd} carga_horaria = '{$int_carga_horaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dias_letivos ) )
		{
			$filtros .= "{$whereAnd} dias_letivos = '{$int_dias_letivos}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_escola ) )
		{
			$filtros .= "{$whereAnd} escola LIKE '%{$str_escola}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_escola_cidade ) )
		{
			$filtros .= "{$whereAnd} escola_cidade LIKE '%{$str_escola_cidade}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_escola_uf ) )
		{
			$filtros .= "{$whereAnd} escola_uf LIKE '%{$str_escola_uf}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_observacao ) )
		{
			$filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_aprovado ) )
		{
			$filtros .= "{$whereAnd} aprovado = '{$int_aprovado}'";
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
		if (!is_null( $int_ativo ))
		{
			if( /*is_null( $int_ativo ) ||*/ $int_ativo )
			{
				$filtros .= "{$whereAnd} ativo = '1'";
				$whereAnd = " AND ";
			}
			else
			{
				$filtros .= "{$whereAnd} ativo = '0'";
				$whereAnd = " AND ";
			}
		}
		if( is_numeric( $int_faltas_globalizadas ) )
		{
			$filtros .= "{$whereAnd} faltas_globalizadas = '{$int_faltas_globalizadas}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_frequencia ) )
		{
			$filtros .= "{$whereAnd} frequencia = '{$int_frequencia}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

//		echo $sql;

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
		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'" );
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

	function getMaxSequencial( $ref_cod_aluno )
	{
		if( is_numeric( $ref_cod_aluno ) )
		{
			$db = new clsBanco();
			$sequencial = $db->campoUnico("SELECT COALESCE( MAX(sequencial), 0 ) FROM {$this->_tabela} WHERE ref_cod_aluno = {$ref_cod_aluno}" );
			return $sequencial;
		}
		return false;
	}

}
?>
