<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itajaï¿½								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software Pï¿½blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaï¿½			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  ï¿½  software livre, vocï¿½ pode redistribuï¿½-lo e/ou	 *
*	modificï¿½-lo sob os termos da Licenï¿½a Pï¿½blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versï¿½o 2 da	 *
*	Licenï¿½a   como  (a  seu  critï¿½rio)  qualquer  versï¿½o  mais  nova.	 *
*																		 *
*	Este programa  ï¿½ distribuï¿½do na expectativa de ser ï¿½til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implï¿½cita de COMERCIALI-	 *
*	ZAï¿½ï¿½O  ou  de ADEQUAï¿½ï¿½O A QUALQUER PROPï¿½SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licenï¿½a  Pï¿½blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Vocï¿½  deve  ter  recebido uma cï¿½pia da Licenï¿½a Pï¿½blica Geral GNU	 *
*	junto  com  este  programa. Se nï¿½o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itajaï¿½
*
* Criado em 10/08/2006 17:11 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarMatricula
{
	var $cod_matricula;
	var $ref_cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ano;
	var $ultima_matricula;
	var $modulo;
	var $descricao_reclassificacao;
	var $matricula_reclassificacao;
	var $formando;
	var $ref_cod_curso;
	var $semestre;

	/**
	 * caso seja a primeira matricula do aluno
	 * marcar como true este atributo
	 * necessário para contabilizar como admitido por transferência
	 * no relatorio de movimentacao mensal
	 *
	 * @var bool
	 */

	var $matricula_transferencia;

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
	function clsPmieducarMatricula( $cod_matricula = null, $ref_cod_reserva_vaga = null, $ref_ref_cod_escola = null, $ref_ref_cod_serie = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_aluno = null, $aprovado = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ano = null, $ultima_matricula = null, $modulo = null,$formando = null,$descricao_reclassificacao = null,$matricula_reclassificacao = null, $ref_cod_curso = null, $matricula_transferencia = null, $semestre = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}matricula";

		$this->_campos_lista = $this->_todos_campos = "m.cod_matricula, m.ref_cod_reserva_vaga, m.ref_ref_cod_escola, m.ref_ref_cod_serie, m.ref_usuario_exc, m.ref_usuario_cad, m.ref_cod_aluno, m.aprovado, m.data_cadastro, m.data_exclusao, m.ativo, m.ano, m.ultima_matricula, m.modulo,formando,descricao_reclassificacao,matricula_reclassificacao, m.ref_cod_curso,m.matricula_transferencia,m.semestre";

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

		if( is_numeric( $ref_cod_reserva_vaga ) )
		{
			if( class_exists( "clsPmieducarReservaVaga" ) )
			{
				$tmp_obj = new clsPmieducarReservaVaga( $ref_cod_reserva_vaga );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.reserva_vaga WHERE cod_reserva_vaga = '{$ref_cod_reserva_vaga}'" ) )
				{
					$this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
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
		if( is_numeric( $ref_cod_curso ) )
		{
			if( class_exists( "clsPmieducarCurso" ) )
			{
				$tmp_obj = new clsPmieducarCurso( $ref_cod_curso );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_curso = $ref_cod_curso;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_curso = $ref_cod_curso;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.curso WHERE cod_curso = '{$ref_cod_curso}'" ) )
				{
					$this->ref_cod_curso = $ref_cod_curso;
				}
			}
		}


		if( is_numeric( $cod_matricula ) )
		{
			$this->cod_matricula = $cod_matricula;
		}
		if( is_numeric( $ref_ref_cod_escola ) )
		{
			$this->ref_ref_cod_escola = $ref_ref_cod_escola;
		}
		if( is_numeric( $ref_ref_cod_serie ) )
		{
			$this->ref_ref_cod_serie = $ref_ref_cod_serie;
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
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}
		if( is_numeric( $ultima_matricula ) )
		{
			$this->ultima_matricula = $ultima_matricula;
		}
		if( is_numeric( $modulo ) )
		{
			$this->modulo = $modulo;
		}
		if( is_numeric( $formando ) )
		{
			$this->formando = $formando;
		}
		if( is_string( $descricao_reclassificacao ) )
		{
			$this->descricao_reclassificacao = $descricao_reclassificacao;
		}
		if( is_numeric( $matricula_reclassificacao ) )
		{
			$this->matricula_reclassificacao = $matricula_reclassificacao;
		}
		if(dbBool($matricula_transferencia))
		{
			$this->matricula_transferencia = dbBool($matricula_transferencia) ? "t" : "f";
		}
		if (is_numeric($semestre))
		{
			$this->semestre = $semestre;	
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{

		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->aprovado ) && is_numeric( $this->ano ) && is_numeric( $this->ultima_matricula ) && is_numeric( $this->ref_cod_curso ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_reserva_vaga ) )
			{
				$campos .= "{$gruda}ref_cod_reserva_vaga";
				$valores .= "{$gruda}'{$this->ref_cod_reserva_vaga}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_aluno ) )
			{
				$campos .= "{$gruda}ref_cod_aluno";
				$valores .= "{$gruda}'{$this->ref_cod_aluno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aprovado ) )
			{
				$campos .= "{$gruda}aprovado";
				$valores .= "{$gruda}'{$this->aprovado}'";
				$gruda = ", ";
			}

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ultima_matricula ) )
			{
				$campos .= "{$gruda}ultima_matricula";
				$valores .= "{$gruda}'{$this->ultima_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->modulo ) )
			{
				$campos .= "{$gruda}modulo";
				$valores .= "{$gruda}'{$this->modulo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->formando ) )
			{
				$campos .= "{$gruda}formando";
				$valores .= "{$gruda}'{$this->formando}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_reclassificacao ) )
			{
				$campos .= "{$gruda}matricula_reclassificacao";
				$valores .= "{$gruda}'{$this->matricula_reclassificacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_reclassificacao ) )
			{
				$campos .= "{$gruda}descricao_reclassificacao";
				$valores .= "{$gruda}'{$this->descricao_reclassificacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$campos .= "{$gruda}ref_cod_curso";
				$valores .= "{$gruda}'{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			if( dbBool( $this->matricula_transferencia ) )
			{
				$campos .= "{$gruda}matricula_transferencia";
				$valores .= "{$gruda}'{$this->matricula_transferencia}'";
				$gruda = ", ";
			}
			if ( is_numeric($this->semestre) )
			{
				$campos .= "{$gruda}semestre";
				$valores .= "{$gruda}'{$this->semestre}'";
				$gruda = ", ";
			}
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_matricula_seq");
		}
		return false;
	}

	/**
	 * Passa o aluno para o proximo modulo
	 *
	 * @return bool
	 */
	function avancaModulo()
	{
		if( is_numeric( $this->cod_matricula ) && is_numeric( $this->ref_usuario_exc ) )
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->_tabela} SET modulo = modulo + 1, data_exclusao = NOW(), ref_usuario_exc = '{$this->ref_usuario_exc}' WHERE cod_matricula = '{$this->cod_matricula}'" );
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
		if( is_numeric( $this->cod_matricula ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_reserva_vaga ) )
			{
				$set .= "{$gruda}ref_cod_reserva_vaga = '{$this->ref_cod_reserva_vaga}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_serie ) )
			{
				$set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
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
			if( is_numeric( $this->ref_cod_aluno ) )
			{
				$set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aprovado ) )
			{
				$set .= "{$gruda}aprovado = '{$this->aprovado}'";
				$gruda = ", ";
			}
			/*if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}*/
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ultima_matricula ) )
			{
				$set .= "{$gruda}ultima_matricula = '{$this->ultima_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->modulo ) )
			{
				$set .= "{$gruda}modulo = '{$this->modulo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->formando ) )
			{
				$set .= "{$gruda}formando = '{$this->formando}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->matricula_reclassificacao ) )
			{
				$set .= "{$gruda}matricula_reclassificacao = '{$this->matricula_reclassificacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_reclassificacao ) )
			{
				$set .= "{$gruda}descricao_reclassificacao = '{$this->descricao_reclassificacao}'";
				$gruda = ", ";
			}
			if (is_numeric($this->semestre))
			{
				$set .= "{$gruda}semestre = '{$this->semestre}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_matricula = '{$this->cod_matricula}'" );
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
	function lista( $int_cod_matricula = null, $int_ref_cod_reserva_vaga = null, $int_ref_ref_cod_escola = null, $int_ref_ref_cod_serie = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_aluno = null, $int_aprovado = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ano = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ultima_matricula = null, $int_modulo = null,$int_padrao_ano_escolar = null,$int_analfabeto = null,$int_formando = null,$str_descricao_reclassificacao = null,$int_matricula_reclassificacao = null,$boo_com_deficiencia = null, $int_ref_cod_curso = null, $bool_curso_sem_avaliacao = null, $arr_int_cod_matricula = null, $int_mes_defasado = null,$boo_data_nasc = null, $boo_matricula_transferencia = null, $int_semestre = null, $int_ref_cod_turma = null )
	{
//		$join = "";
//		if(is_numeric($int_padrao_ano_escolar))
//		{
//			$join = ", {$this->_schema}curso c";
//			$where_join = "s.ref_cod_curso      = c.cod_curso AND c.padrao_ano_escolar = {$int_padrao_ano_escolar} AND ";
//		}
//		if(is_numeric($int_analfabeto))
//		{
//			$join .= ", {$this->_schema}aluno a";
//			$where_join .= "m.ref_cod_aluno     = a.cod_aluno AND";
//		}
		if($boo_data_nasc)
			$this->_campos_lista .= " ,(SELECT data_nasc
									      FROM cadastro.fisica
							             WHERE idpes = ref_idpes
										) as data_nasc";

		$sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, p.nome, a.cod_aluno, a.ref_idpes, c.cod_curso FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p ";
		//$filtros = "";

		//$whereAnd = " WHERE m.ref_ref_cod_serie = es.ref_cod_serie AND m.ref_ref_cod_escola = es.ref_cod_escola AND es.ref_cod_escola = e.cod_escola AND es.ref_cod_serie = s.cod_serie AND {$where_join} ";
		$whereAnd = " AND ";
		$filtros = " WHERE m.ref_cod_aluno = a.cod_aluno AND m.ref_cod_curso = c.cod_curso AND p.idpes = a.ref_idpes ";

		if( is_numeric( $int_cod_matricula ) )
		{
			$filtros .= "{$whereAnd} m.cod_matricula = '{$int_cod_matricula}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_reserva_vaga ) )
		{
			$filtros .= "{$whereAnd} m.ref_cod_reserva_vaga = '{$int_ref_cod_reserva_vaga}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} m.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} m.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_aluno ) )
		{
			$filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_aprovado ) )
		{
			$filtros .= "{$whereAnd} m.aprovado = '{$int_aprovado}'";
			$whereAnd = " AND ";
		}
		elseif (is_array($int_aprovado))
		{
			$int_aprovado = implode(",",$int_aprovado);
			$filtros .= "{$whereAnd} m.aprovado in ( {$int_aprovado})";
			$whereAnd = " AND ";
		}

		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} m.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} m.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} m.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} m.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( $int_ativo )
		{
			$filtros .= "{$whereAnd} m.ativo = '1'";
			$whereAnd = " AND ";
		}
		else if( !is_null($int_ativo) && is_numeric($int_ativo) )
		{
			$filtros .= "{$whereAnd} m.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} m.ano = '{$int_ano}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_padrao_ano_escolar ) )
		{
			$filtros .= "{$whereAnd} c.padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ultima_matricula ) )
		{
			$filtros .= "{$whereAnd} ultima_matricula = '{$int_ultima_matricula}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_modulo ) )
		{
			$filtros .= "{$whereAnd} m.modulo = '{$int_modulo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_analfabeto ) )
		{
			$filtros .= "{$whereAnd} a.analfabeto = '{$int_analfabeto}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_formando ) )
		{
			$filtros .= "{$whereAnd} a.formando = '{$int_formando}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_matricula_reclassificacao ) )
		{
			$filtros .= "{$whereAnd} m.matricula_reclassificacao = '{$int_matricula_reclassificacao}'";
			$whereAnd = " AND ";
		}
		if( dbBool( $boo_matricula_transferencia ) )
		{
			$boo_matricula_transferencia = dbBool($boo_matricula_transferencia) ? 't' : 'f';
			$filtros .= "{$whereAnd} m.matricula_transferencia = '{$boo_matricula_transferencia}'";
			$whereAnd = " AND ";
		}
		if( is_string( $int_matricula_reclassificacao ) )
		{
			$filtros .= "{$whereAnd} to_ascii(a.matricula_reclassificacao) like to_ascii('%{$int_matricula_reclassificacao}%')";
			$whereAnd = " AND ";
		}
		if( is_bool( $boo_com_deficiencia ) )
		{
			$not = $boo_com_deficiencia === true ? "" : "NOT";
			$filtros .= "{$whereAnd} $not EXISTS (SELECT 1 FROM cadastro.fisica_deficiencia fd, pmieducar.aluno a WHERE a.cod_aluno = m.ref_cod_aluno AND fd.ref_idpes    = a.ref_idpes)";
			$whereAnd = " AND ";
		}

		if (is_numeric($int_semestre)) 
		{
			$filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
			$whereAnd = " AND ";
		}
		
		if (is_numeric($int_ref_cod_turma))
		{
			$filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.matricula_turma mt WHERE mt.ativo = 1 AND mt.ref_cod_turma = {$int_ref_cod_turma} AND mt.ref_cod_matricula = m.cod_matricula)";
			$whereAnd = " AND ";
		}
//		elseif (is_bool($))
		
		if( is_array( $arr_int_cod_matricula ) && count($arr_int_cod_matricula) )
		{
			$filtros .= "{$whereAnd} cod_matricula IN (" . implode(",",$arr_int_cod_matricula) . ")";
			$whereAnd = " AND ";
		}

		if(is_numeric($int_mes_defasado))
		{
			$primeiroDiaDoMes = mktime(0,0,0,$int_mes_defasado,1,$int_ano);
			$NumeroDiasMes = date('t',$primeiroDiaDoMes);
			$ultimoDiaMes =date('d/m/Y',mktime(0,0,0,$int_mes_defasado,$NumeroDiasMes,$int_ano));
			$ultimoDiaMes = dataToBanco($ultimoDiaMes,false);

			$primeiroDiaDoMes = date('d/m/Y',$primeiroDiaDoMes);
			$primeiroDiaDoMes = dataToBanco($primeiroDiaDoMes,false);

			// Query anterior
/*			$filtroAux = "{$whereAnd} ( (aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
									   OR  (aprovado IN (1,2,3) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes')
									 )";*/
			
			$filtroAux = "{$whereAnd} ( (aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
											   OR  (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes')
											 )";			
					
						
/*			$diaAtual = date("d");
			$mesAtual = date("m");
			$anoAtual = date("Y");	*/

/*			$diaAtual = "$anoAtual-$mesAtual-$diaAtual 23:59:59";			
			
			if($int_ano == $anoAtual)
			{
				if($int_mes_defasado == $mesAtual)
				{
					$filtroAux = "{$whereAnd} ( (aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
											   OR  (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$diaAtual' AND m.data_exclusao <= '$ultimoDiaMes')
											 )";					
				} elseif($int_mes_defasado < $mesAtual) {
					$filtroAux = "{$whereAnd} ( (aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
											   OR  (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes')
											 )";						
				}
			}
*/			
			$filtros .= $filtroAux;
			
			$whereAnd = " AND ";
		}


		if( is_bool( $bool_curso_sem_avaliacao ))
		{
			$not =  ($bool_curso_sem_avaliacao  == true) ? "  " : " NOT ";
			$filtros .= "{$whereAnd} c.ref_cod_tipo_avaliacao IS $not NULL";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//echo($sql."<br>");
		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p {$filtros}" );		

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
		if( is_numeric( $this->cod_matricula ) )
		{
			$sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p WHERE m.cod_matricula = '{$this->cod_matricula}' AND a.cod_aluno = m.ref_cod_aluno AND p.idpes = a.ref_idpes ";
			if ( $this->ativo )
			{
				$sql .= " AND m.ativo = {$this->ativo}";
			}
			if ( $this->ultima_matricula )
			{
				$sql .= " AND m.ultima_matricula = {$this->ultima_matricula}";
			}
			$db = new clsBanco();
			$db->Consulta( $sql );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		if ( !$this->cod_matricula && is_numeric($this->ref_ref_cod_escola))
		{

			$sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p WHERE m.ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";

			$db = new clsBanco();
			$db->Consulta( $sql );
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
		if( is_numeric( $this->cod_matricula ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_matricula = '{$this->cod_matricula}'" );
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
		if( is_numeric( $this->cod_matricula ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_matricula = '{$this->cod_matricula}'" );
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

	function isSequencia( $origem, $destino )
	{
		$obj = new clsPmieducarSequenciaSerie();
		$sequencia = $obj->lista( $origem,null,null,null,null,null,null,null,1 );
		$achou = false;
		if( $sequencia )
		{
//			foreach ( $sequencia AS $lista )
//			{
//				echo "<pre>";print_r($lista)."<br>";
				do{
					if( $lista['ref_serie_origem'] == $destino )
					{
						$achou = true;
						break;
					}
					if( $lista['ref_serie_destino'] == $destino )
					{
						$achou = true;
						break;
					}

					$sequencia_ = $obj->lista( $lista['ref_serie_destino'],null,null,null,null,null,null,null,1 );
					if( !$lista )
					{
						$achou = false;
						break;
					}

				} while( $achou != false );
//			}
		}
		return $achou;
	}

	function getInicioSequencia()
	{
		$db = new clsBanco();
		$sql = "SELECT o.ref_serie_origem
				FROM pmieducar.sequencia_serie o
				WHERE NOT EXISTS
				(
					SELECT 1
		     		FROM pmieducar.sequencia_serie d
	            	WHERE o.ref_serie_origem = d.ref_serie_destino
	            )";

		$db->Consulta( $sql );

		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla;
		}
		return $resultado;
	}

	function getFimSequencia()
	{
		$db = new clsBanco();
		$sql = "SELECT o.ref_serie_destino
				FROM pmieducar.sequencia_serie o
				WHERE NOT EXISTS
				(
					SELECT 1
		     		FROM pmieducar.sequencia_serie d
	            	WHERE o.ref_serie_destino = d.ref_serie_origem
	            )";

		$db->Consulta( $sql );

		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla;
		}
		return $resultado;
	}

	 /**
	 * Retorna uma variavel com os dados de um registro
	 *
	 * @return array
	 */
	function numModulo( $int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma, $int_ref_ref_cod_matricula )
	{
		$db = new clsBanco();

		$sql = "SELECT CASE WHEN FLOOR( ( SELECT COUNT(*)
										    FROM pmieducar.nota_aluno
										   WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
										     AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
										     AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
										     AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
										     AND ref_ref_cod_turma       = {$int_ref_cod_turma} ) / ( ( SELECT COUNT(*)
																				     					  FROM pmieducar.disciplina_serie
																				    				     WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(0)
																																						        FROM pmieducar.dispensa_disciplina
																																						       WHERE ref_ref_cod_turma 	     = {$int_ref_cod_turma}
																																								 AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
																																								 AND disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
																																								 AND disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
																																								 AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} ) ) ) = 0
								    THEN 0
							        ELSE FLOOR( ( SELECT COUNT(*)
												    FROM pmieducar.nota_aluno
												   WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
												     AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
												     AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
												     AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
												     AND ref_ref_cod_turma       = {$int_ref_cod_turma} ) / ( ( SELECT COUNT(*)
																						     					  FROM pmieducar.disciplina_serie
																						    				     WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(0)
																																							            FROM pmieducar.dispensa_disciplina
																																							           WHERE ref_ref_cod_turma 	     = {$int_ref_cod_turma}
																																									     AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
																																									     AND disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
																																									     AND disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
																																									     AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} ) ) )
							        END";

		return $db->CampoUnico( $sql );
	}

	function aprova_matricula_andamento_curso_sem_avaliacao()
	{


		if(is_numeric($this->ref_ref_cod_escola) )
		{
			$db = new clsBanco();
			$consulta = "UPDATE {$this->_tabela} SET aprovado = 1 , ref_usuario_exc = {$this->ref_usuario_exc} , data_exclusao = NOW() WHERE ano = {$this->ano} AND ref_ref_cod_escola = {$this->ref_ref_cod_escola} AND exists (SELECT 1 FROM {$this->_schema}curso c WHERE c.cod_curso = ref_cod_curso AND c.ref_cod_tipo_avaliacao IS NULL )";

			$db->Consulta($consulta);
			return true;
		}
		return false;
	}

	function getTotalAlunosEscola($cod_escola,$cod_curso,$cod_serie, $ano = null, $semestre = null)
	{
		if(is_numeric($cod_escola) && is_numeric($cod_curso))
		{
			if (!is_numeric($ano))
			{
				$ano = date('Y');
			}
			if(is_numeric($cod_serie))
			{
				$where = " AND ref_ref_cod_serie = {$cod_serie} ";
			}
			if (is_numeric($semestre))
			{
				$where .= " AND semestre = {$semestre} ";
			}
			$select = "SELECT count(1) as total_alunos_serie
							  ,ref_ref_cod_serie as cod_serie
							  ,nm_serie
						  FROM pmieducar.matricula
						       ,pmieducar.serie
						 WHERE serie.cod_serie = ref_ref_cod_serie
						   AND ref_ref_cod_escola = {$cod_escola}
						   AND serie.ref_cod_curso = {$cod_curso}
						   AND ano = {$ano}
						   $where
						   AND ultima_matricula = 1
						   AND aprovado IN (1,2,3)
						   AND matricula.ativo = 1
						 GROUP BY ref_ref_cod_serie
						          ,ref_ref_cod_escola
						          ,nm_serie";

			$db= new clsBanco();
			$db->Consulta($select);
			$total_registros = $db->Num_Linhas();
			if(!$total_registros)
				return false;

			$resultados = array();
			$total = 0;
			while($db->ProximoRegistro())
			{
				$registro = $db->Tupla();
				$total += $registro['total_alunos_serie'];
				$resultados[$registro['cod_serie']] = $registro;
			}
			//$resultados[0]['_total'] = $total;


			$array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();

			$db = new clsBanco();

			foreach ($array_inicio_sequencias as $serie_inicio)
			{
				$serie_inicio = $serie_inicio[0];

				$seq_ini = $serie_inicio;
				$seq_correta = false;
				$series[$cod_serie] = $cod_serie;
				do
				{
					$sql = "SELECT o.ref_serie_origem
					               ,s.nm_serie
							       ,o.ref_serie_destino
							       ,s.ref_cod_curso as ref_cod_curso_origem
							       ,sd.ref_cod_curso as ref_cod_curso_destino
							  FROM pmieducar.sequencia_serie o
							       ,pmieducar.serie s
							       ,pmieducar.serie sd
							 WHERE s.cod_serie = o.ref_serie_origem
							   AND s.cod_serie = $seq_ini
					           AND sd.cod_serie = o.ref_serie_destino
							";

					$db->Consulta($sql);
					$db->ProximoRegistro();
					$tupla = $db->Tupla();
					$serie_origem = $tupla['ref_serie_origem'];

					//$curso_origem = $tupla['ref_cod_curso_origem'];
					//$curso_destino = $tupla['ref_cod_curso_destino'];
					$seq_ini = $serie_destino = $tupla['ref_serie_destino'];

					/*$obj_curso = new clsPmieducarCurso($curso_origem);
					$det_curso = $obj_curso->detalhe();
					$cursos[$curso_origem] = $det_curso['nm_curso'];

					$obj_curso = new clsPmieducarCurso($curso_destino);
					$det_curso = $obj_curso->detalhe();*/
					//$cursos[$curso_destino] = $det_curso['nm_curso'];

					$series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];


					/*if($cod_serie == $serie_origem)
						$seq_correta = true;*/

					$sql = "SELECT 1
							  FROM pmieducar.sequencia_serie s
							 WHERE s.ref_serie_origem = $seq_ini
						    ";
					$true = $db->CampoUnico($sql);

				}while($true);

				$obj_serie = new clsPmieducarSerie($serie_destino);
				$det_serie = $obj_serie->detalhe();


				if($cod_serie == $serie_destino)
					$seq_correta = true;

				if($seq_correta == false)
				{
					//$series = null; //array('' => 'Nï¿½o existem cursos/sï¿½ries para reclassificaï¿½ï¿½o');
				}else
				{
					//break;
				}
			}
			if($series)
			{
				$resultados2 = array();
				foreach ($series as $key => $serie)
				{
					if(key_exists($key,$resultados))
					{
						$resultados[$key]['_total'] = $total;
						$resultados2[] = $resultados[$key];
					}
				}
			}

			return $resultados2;
		}
		return false;
	}

	function getTotalAlunosIdadeSexoEscola($cod_escola,$cod_curso,$cod_serie, $ano = null, $semestre = null)
	{
		if(is_numeric($cod_escola) && is_numeric($cod_curso))
		{
			if (!is_numeric($ano))
			{
				$ano = date('Y');
			}
			if(is_numeric($cod_serie))
			{
				$where = " AND ref_ref_cod_serie = {$cod_serie} ";
			}
			if (is_numeric($semestre))
			{
				$where .= " AND m.semestre = {$semestre} ";
			}

			$select = "SELECT m.ref_ref_cod_serie as cod_serie
							  ,nm_serie
							   ,COUNT(1) as total_alunos_serie
						       , COALESCE ( EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )::text , '-' ) as idade
						       ,f.sexo
						  FROM pmieducar.aluno a
						       ,pmieducar.matricula m
						       ,cadastro.fisica f
						       ,pmieducar.serie
						 WHERE a.cod_aluno = m.ref_cod_aluno
						   AND a.ref_idpes = idpes
						   AND ref_ref_cod_serie = cod_serie
						   AND m.ref_ref_cod_escola = $cod_escola
						   AND ano = $ano
						   AND ultima_matricula = 1
						   AND aprovado IN ( 1,2,3)
						   AND m.ref_cod_curso = $cod_curso
						   $where
						GROUP BY m.ref_ref_cod_serie
							     ,nm_serie
						         ,EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )
						         ,f.sexo
						ORDER BY EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )
						         ,f.sexo";



			$db= new clsBanco();
			$db->Consulta($select);
			$total_registros = $db->Num_Linhas();
			if(!$total_registros)
				return false;

			$resultados = array();
			$total = 0;
			while($db->ProximoRegistro())
			{
				$registro = $db->Tupla();
				$total += $registro['total_alunos_serie'];
				$resultados[] = $registro;
			}
			//$resultados[0]['_total'] = $total;


			$array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();

			$db = new clsBanco();

			foreach ($array_inicio_sequencias as $serie_inicio)
			{
				$serie_inicio = $serie_inicio[0];

				$seq_ini = $serie_inicio;
				$seq_correta = false;
				$series[$cod_serie] = $cod_serie;
				do
				{
					$sql = "SELECT o.ref_serie_origem
					               ,s.nm_serie
							       ,o.ref_serie_destino
							       ,s.ref_cod_curso as ref_cod_curso_origem
							       ,sd.ref_cod_curso as ref_cod_curso_destino
							  FROM pmieducar.sequencia_serie o
							       ,pmieducar.serie s
							       ,pmieducar.serie sd
							 WHERE s.cod_serie = o.ref_serie_origem
							   AND s.cod_serie = $seq_ini
					           AND sd.cod_serie = o.ref_serie_destino
							";

					$db->Consulta($sql);
					$db->ProximoRegistro();
					$tupla = $db->Tupla();
					$serie_origem = $tupla['ref_serie_origem'];

					//$curso_origem = $tupla['ref_cod_curso_origem'];
					//$curso_destino = $tupla['ref_cod_curso_destino'];
					$seq_ini = $serie_destino = $tupla['ref_serie_destino'];

					/*$obj_curso = new clsPmieducarCurso($curso_origem);
					$det_curso = $obj_curso->detalhe();
					$cursos[$curso_origem] = $det_curso['nm_curso'];

					$obj_curso = new clsPmieducarCurso($curso_destino);
					$det_curso = $obj_curso->detalhe();*/
					//$cursos[$curso_destino] = $det_curso['nm_curso'];

					$series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];


					/*if($cod_serie == $serie_origem)
						$seq_correta = true;*/

					$sql = "SELECT 1
							  FROM pmieducar.sequencia_serie s
							 WHERE s.ref_serie_origem = $seq_ini
						    ";
					$true = $db->CampoUnico($sql);

				}while($true);

				$obj_serie = new clsPmieducarSerie($serie_destino);
				$det_serie = $obj_serie->detalhe();


				if($cod_serie == $serie_destino)
					$seq_correta = true;

				if($seq_correta == false)
				{
					//$series = null; //array('' => 'Nï¿½o existem cursos/sï¿½ries para reclassificaï¿½ï¿½o');
				}else
				{
					//break;
				}
			}
			if($series)
			{
				$resultados2 = array();
				foreach ($series as $key => $serie)
				{
					foreach ($resultados as $key2 => $resultado)
					{
						if($key == $resultado['cod_serie'])
						{
							$resultados[$key2]['_total'] = $total;
							$resultados2[] = $resultados[$key2];
							unset($resultados[$key2]);
						}
					}
				}
			}
			return $resultados2;
		}
		return false;
	}
}
?>