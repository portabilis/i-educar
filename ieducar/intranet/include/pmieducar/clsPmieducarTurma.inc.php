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
* @author Prefeitura Municipal de Itajaï¿½
*
* Criado em 02/08/2006 08:42 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarTurma
{
	var $cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_infra_predio_comodo;
	var $nm_turma;
	var $sgl_turma;
	var $max_aluno;
	var $multiseriada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_turma_tipo;
	var $hora_inicial;
	var $hora_final;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	var $ref_cod_regente;
  	var $ref_cod_instituicao_regente;

  	var $ref_cod_instituicao;
  	var $ref_cod_curso;

  	var $ref_ref_cod_serie_mult;
    var $ref_ref_cod_escola_mult;
    var $visivel;
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
	function clsPmieducarTurma( $cod_turma = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_ref_cod_serie = null, $ref_ref_cod_escola = null, $ref_cod_infra_predio_comodo = null, $nm_turma = null, $sgl_turma = null, $max_aluno = null, $multiseriada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_turma_tipo = null, $hora_inicial = null, $hora_final = null, $hora_inicio_intervalo = null, $hora_fim_intervalo = null, $ref_cod_regente = null, $ref_cod_instituicao_regente = null, $ref_cod_instituicao = null, $ref_cod_curso = null, $ref_ref_cod_serie_mult = null, $ref_ref_cod_escola_mult = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null)
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}turma";

		$this->_campos_lista = $this->_todos_campos = "t.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie, t.ref_ref_cod_escola, t.ref_cod_infra_predio_comodo, t.nm_turma, t.sgl_turma, t.max_aluno, t.multiseriada, t.data_cadastro, t.data_exclusao, t.ativo, t.ref_cod_turma_tipo, t.hora_inicial, t.hora_final, t.hora_inicio_intervalo, t.hora_fim_intervalo, t.ref_cod_regente, t.ref_cod_instituicao_regente,t.ref_cod_instituicao, t.ref_cod_curso, t.ref_ref_cod_serie_mult, t.ref_ref_cod_escola_mult, t.visivel, t.turma_turno_id, t.tipo_boletim, t.ano";

		if( is_numeric( $ref_cod_turma_tipo ) )
		{
			if( class_exists( "clsPmieducarTurmaTipo" ) )
			{
				$tmp_obj = new clsPmieducarTurmaTipo( $ref_cod_turma_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_turma_tipo = $ref_cod_turma_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_turma_tipo = $ref_cod_turma_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.turma_tipo WHERE cod_turma_tipo = '{$ref_cod_turma_tipo}'" ) )
				{
					$this->ref_cod_turma_tipo = $ref_cod_turma_tipo;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_escola ) && is_numeric( $ref_ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarEscolaSerie" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerie( $ref_ref_cod_escola, $ref_ref_cod_serie );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
						$this->ref_ref_cod_serie = $ref_ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_escola = $ref_ref_cod_escola;
						$this->ref_ref_cod_serie = $ref_ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$ref_ref_cod_escola}' AND ref_cod_serie = '{$ref_ref_cod_serie}'" ) )
				{
					$this->ref_ref_cod_escola = $ref_ref_cod_escola;
					$this->ref_ref_cod_serie = $ref_ref_cod_serie;
				}
			}
		}
		if( is_numeric( $ref_cod_infra_predio_comodo ) )
		{
			if( class_exists( "clsPmieducarInfraPredioComodo" ) )
			{
				$tmp_obj = new clsPmieducarInfraPredioComodo( $ref_cod_infra_predio_comodo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_infra_predio_comodo = $ref_cod_infra_predio_comodo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_infra_predio_comodo = $ref_cod_infra_predio_comodo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.infra_predio_comodo WHERE cod_infra_predio_comodo = '{$ref_cod_infra_predio_comodo}'" ) )
				{
					$this->ref_cod_infra_predio_comodo = $ref_cod_infra_predio_comodo;
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

		if( is_numeric( $ref_cod_regente ) && is_numeric( $ref_cod_instituicao_regente ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor($ref_cod_regente,null,null,null,null,null,null,null,$ref_cod_instituicao_regente);
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_regente = $ref_cod_regente;
						$this->ref_cod_instituicao_regente = $ref_cod_instituicao_regente;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_regente = $ref_cod_regente;
						$this->ref_cod_instituicao_regente = $ref_cod_instituicao_regente;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE ref_cod_regente = '{$ref_cod_regente}' AND ref_cod_instituicao_regente = '{$ref_cod_instituicao_regente}'" ) )
				{
						$this->ref_cod_regente = $ref_cod_regente;
						$this->ref_cod_instituicao_regente = $ref_cod_instituicao_regente;
				}
			}
		}

		if( is_numeric( $cod_turma ) )
		{
			$this->cod_turma = $cod_turma;
		}
		if( is_string( $nm_turma ) )
		{
			$this->nm_turma = $nm_turma;
		}
		if( is_string( $sgl_turma ) )
		{
			$this->sgl_turma = $sgl_turma;
		}
		if( is_numeric( $max_aluno ) )
		{
			$this->max_aluno = $max_aluno;
		}
		if( is_numeric( $multiseriada ) )
		{
			$this->multiseriada = $multiseriada;
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
		if( ( $hora_inicial ) )
		{
			$this->hora_inicial = $hora_inicial;
		}
		if( ( $hora_final ) )
		{
			$this->hora_final = $hora_final;
		}
		if( ( $hora_inicio_intervalo ) )
		{
			$this->hora_inicio_intervalo = $hora_inicio_intervalo;
		}
		if( ( $hora_fim_intervalo ) )
		{
			$this->hora_fim_intervalo = $hora_fim_intervalo;
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

		if( is_numeric( $ref_ref_cod_escola_mult ) && is_numeric( $ref_ref_cod_serie_mult ) )
		{
			if( class_exists( "clsPmieducarEscolaSerie" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerie( $ref_ref_cod_escola_mult, $ref_ref_cod_serie_mult );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_escola_mult = $ref_ref_cod_escola_mult;
						$this->ref_ref_cod_serie_mult = $ref_ref_cod_serie_mult;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_escola_mult = $ref_ref_cod_escola_mult;
						$this->ref_ref_cod_serie_mult = $ref_ref_cod_serie_mult;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$ref_ref_cod_escola_mult}' AND ref_cod_serie = '{$ref_ref_cod_serie_mult}'" ) )
				{
					$this->ref_ref_cod_escola_mult = $ref_ref_cod_escola_mult;
					$this->ref_ref_cod_serie_mult = $ref_ref_cod_serie_mult;
				}
			}
		}
		if (is_bool($visivel))
		{
			$this->visivel = dbBool($visivel);
		}

    $this->turma_turno_id = $turma_turno_id;
    $this->tipo_boletim   = $tipo_boletim;
    $this->ano            = $ano;
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) /*&& is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo )*/ && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo ) )
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
			if( is_numeric( $this->ref_ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_predio_comodo ) )
			{
				$campos .= "{$gruda}ref_cod_infra_predio_comodo";
				$valores .= "{$gruda}'{$this->ref_cod_infra_predio_comodo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_turma ) )
			{
				$campos .= "{$gruda}nm_turma";
				$valores .= "{$gruda}'{$this->nm_turma}'";
				$gruda = ", ";
			}
			if( is_string( $this->sgl_turma ) )
			{
				$campos .= "{$gruda}sgl_turma";
				$valores .= "{$gruda}'{$this->sgl_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->max_aluno ) )
			{
				$campos .= "{$gruda}max_aluno";
				$valores .= "{$gruda}'{$this->max_aluno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->multiseriada ) )
			{
				$campos .= "{$gruda}multiseriada";
				$valores .= "{$gruda}'{$this->multiseriada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_regente ) )
			{
				$campos .= "{$gruda}ref_cod_regente";
				$valores .= "{$gruda}'{$this->ref_cod_regente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_regente ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao_regente";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao_regente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$campos .= "{$gruda}ref_cod_curso";
				$valores .= "{$gruda}'{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_turma_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_turma_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_turma_tipo}'";
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
			if( ( $this->hora_inicio_intervalo ) )
			{
				$campos .= "{$gruda}hora_inicio_intervalo";
				$valores .= "{$gruda}'{$this->hora_inicio_intervalo}'";
				$gruda = ", ";
			}
			if( ( $this->hora_fim_intervalo ) )
			{
				$campos .= "{$gruda}hora_fim_intervalo";
				$valores .= "{$gruda}'{$this->hora_fim_intervalo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola_mult ) )
			{
				$campos .= "{$gruda}ref_ref_cod_escola_mult";
				$valores .= "{$gruda}'{$this->ref_ref_cod_escola_mult}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_serie_mult ) )
			{
				$campos .= "{$gruda}ref_ref_cod_serie_mult";
				$valores .= "{$gruda}'{$this->ref_ref_cod_serie_mult}'";
				$gruda = ", ";
			}
			$this->visivel = dbBool($this->visivel) ? "TRUE" : "FALSE";
			$campos .= "{$gruda}visivel";
			$valores .= "{$gruda}'{$this->visivel}'";
			$gruda = ", ";

			if(is_numeric($this->turma_turno_id)){
				$campos  .= "{$gruda}turma_turno_id";
				$valores .= "{$gruda}'{$this->turma_turno_id}'";
				$gruda    = ", ";
			}

			if(is_numeric($this->tipo_boletim)){
				$campos  .= "{$gruda}tipo_boletim";
				$valores .= "{$gruda}'{$this->tipo_boletim}'";
				$gruda    = ", ";
			}

			if(is_numeric($this->ano)){
				$campos  .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda    = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_turma_seq");
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
		if( is_numeric( $this->cod_turma ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_numeric( $this->ref_ref_cod_serie ) )
			{
				$set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_predio_comodo ) )
			{
				$set .= "{$gruda}ref_cod_infra_predio_comodo = '{$this->ref_cod_infra_predio_comodo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_turma ) )
			{
				$set .= "{$gruda}nm_turma = '{$this->nm_turma}'";
				$gruda = ", ";
			}
			if( is_string( $this->sgl_turma ) )
			{
				$set .= "{$gruda}sgl_turma = '{$this->sgl_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->max_aluno ) )
			{
				$set .= "{$gruda}max_aluno = '{$this->max_aluno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->multiseriada ) )
			{
				$set .= "{$gruda}multiseriada = '{$this->multiseriada}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}multiseriada = '0'";
				$gruda = ", ";
			}

			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_regente ) )
			{
				$set .= "{$gruda}ref_cod_regente = '{$this->ref_cod_regente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_regente ) )
			{
				$set .= "{$gruda}ref_cod_instituicao_regente = '{$this->ref_cod_instituicao_regente}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_turma_tipo ) )
			{
				$set .= "{$gruda}ref_cod_turma_tipo = '{$this->ref_cod_turma_tipo}'";
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
			if( ( $this->hora_inicio_intervalo ) )
			{
				$set .= "{$gruda}hora_inicio_intervalo = '{$this->hora_inicio_intervalo}'";
				$gruda = ", ";
			}
			if( ( $this->hora_fim_intervalo ) )
			{
				$set .= "{$gruda}hora_fim_intervalo = '{$this->hora_fim_intervalo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_escola_mult ) )
			{
				$set .= "{$gruda}ref_ref_cod_escola_mult = '{$this->ref_ref_cod_escola_mult}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}ref_ref_cod_escola_mult = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_serie_mult ) )
			{
				$set .= "{$gruda}ref_ref_cod_serie_mult = '{$this->ref_ref_cod_serie_mult}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}ref_ref_cod_serie_mult = NULL";
				$gruda = ", ";
			}
			if (dbBool($this->visivel))
			{
				$set .= "{$gruda}visivel = TRUE";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}visivel = FALSE";
				$gruda = ", ";
			}

			if(is_numeric($this->turma_turno_id)) {
				$set  .= "{$gruda}turma_turno_id = '{$this->turma_turno_id}'";
				$gruda = ", ";
			}
			else {
				$set  .= "{$gruda}turma_turno_id = NULL";
				$gruda = ", ";
			}

			if(is_numeric($this->tipo_boletim)) {
				$set  .= "{$gruda}tipo_boletim = '{$this->tipo_boletim}'";
				$gruda = ", ";
			}
			else {
				$set  .= "{$gruda}tipo_boletim = NULL";
				$gruda = ", ";
			}

			if(is_numeric($this->ano)) {
				$set  .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			else {
				$set  .= "{$gruda}ano = NULL";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_turma = '{$this->cod_turma}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna os alunos matriculados nessa turma
	 *
	 * @return bool
	 */
	function matriculados()
	{
		$retorno = array();
		if( is_numeric( $this->cod_turma ) )
		{
			$db = new clsBanco();

			$db->Consulta("SELECT cod_matricula FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->cod_turma}' AND ativo = 1 AND aprovado = 3");
			if( $db->numLinhas() )
			{
				while ( $db->ProximoRegistro() )
				{
					list($matricula) = $db->Tupla();
					$retorno[$matricula] = $matricula;
				}
				return $retorno;
			}
		}
		return false;
	}

	/**
	 * Retorna o menor modulo dos alunos matriculados nessa turma
	 *
	 * @return bool
	 */
	function moduloMinimo()
	{
		if( is_numeric( $this->cod_turma ) )
		{
			$db = new clsBanco();

			// verifica se ainda existe alguem no primeiro modulo
			$modulo = $db->CampoUnico("
				SELECT COALESCE(MIN(modulo),1) AS modulo
				FROM pmieducar.v_matricula_matricula_turma
				WHERE ref_cod_turma = '{$this->cod_turma}'
				AND aprovado = 3 AND ativo = 1
			");
			return $modulo;
		}
	}

	/**
	 * Retorna as disciplinas do menor modulo
	 *
	 * @return bool
	 */
	function moduloMinimoDisciplina()
	{
		if( is_numeric( $this->cod_turma ) )
		{
			$disciplinas = array();
			$modulo = $this->moduloMinimo();

			$db = new clsBanco();
			$db->Consulta("SELECT ref_ref_cod_serie, ref_ref_cod_escola, multiseriada, ref_ref_cod_serie_mult, ref_ref_cod_escola_mult FROM pmieducar.turma WHERE cod_turma = '{$this->cod_turma}'");

			$db->ProximoRegistro();
			list($cod_serie,$cod_escola,$multiseriada,$cod_serie_mult,$cod_escola_mult) = $db->Tupla();

			// ve se existe alguem que nao tem nenhuma nota nesse modulo
			$todas_disciplinas = $db->CampoUnico("
			SELECT 1 FROM
			(
				SELECT cod_matricula
				, ( SELECT COUNT(0) FROM pmieducar.nota_aluno n 			WHERE n.ativo = 1 AND n.ref_cod_matricula = cod_matricula AND n.modulo = '{$modulo}' )
				+ ( SELECT COUNT(0) FROM pmieducar.dispensa_disciplina d 	WHERE d.ativo = 1 AND d.ref_cod_matricula = cod_matricula ) AS notas
				FROM pmieducar.v_matricula_matricula_turma
				WHERE ativo = 1
				AND aprovado = 3
				AND ref_cod_turma = '{$this->cod_turma}'
			) AS sub1
			WHERE notas = 0
			");

			if( $todas_disciplinas )
			{
				// existe um aluno que nao tem nenhuma nota nesse modulo
				if( $multiseriada )
				{
					$aluno_normal = false;
					$aluno_multi = false;
					$filtro = "";

					// ve se tem algum aluno cadastrado na serie normal
					$db->Consulta("SELECT 1 FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->cod_turma}' AND ref_cod_escola = '{$cod_escola}' AND ref_cod_serie = '{$cod_serie}' AND aprovado = 3 AND ativo = 1 ");
					if ($db->ProximoRegistro())
					{
						$aluno_normal = true;
					}

					if(is_numeric($cod_serie_mult))
					{
						// ve se tem algum aluno na serie alterantiva
						$db->Consulta("SELECT 1 FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->cod_turma}' AND ref_cod_escola = '{$cod_escola}' AND ref_cod_serie = '{$cod_serie_mult}' AND aprovado = 3 AND ativo = 1 ");
						if ($db->ProximoRegistro())
						{
							$aluno_multi = true;
						}
					}

					// monta o filtro de acordo com os alunos na serie normal ou alternativa
					if ($aluno_normal || $aluno_multi)
					{
						if ($aluno_normal)
						{
							if($aluno_multi)
							{
								$filtro = " AND ( ref_ref_cod_serie = '{$cod_serie}' OR ref_ref_cod_serie = '{$cod_serie_mult}' )";
							}
							else
							{
								$filtro = " AND ref_ref_cod_serie = '{$cod_serie}'";
							}
						}
						else
						{
							$filtro = " AND ref_ref_cod_serie = '{$cod_serie_mult}'";
						}
					}

					$db->Consulta("SELECT ref_cod_disciplina, ref_ref_cod_serie FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_escola = '{$cod_escola}' {$filtro} AND ativo = 1");
				}
				else
				{
					// nao eh multi-seriada
					$db->Consulta("SELECT ref_cod_disciplina, ref_ref_cod_serie FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_escola = '{$cod_escola}' AND ref_ref_cod_serie = '{$cod_serie}' AND ativo = 1");
				}
			}
			else
			{
				// todos os alunos tem pelo menos uma nota, vamos ver quais as disciplinas que estao faltando
				$qtd_alunos = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->cod_turma}' AND ref_cod_serie = '{$cod_serie}' AND aprovado = 3 AND ativo = 1");
				if( $multiseriada )
				{
					$qtd_alunos_mult = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.v_matricula_matricula_turma WHERE ref_cod_turma = '{$this->cod_turma}' AND ref_cod_serie = '{$cod_serie_mult}' AND aprovado = 3 AND ativo = 1");
//					encontra as disciplinas que ainda precisam receber nota
					$sql = "
					(
						SELECT ref_cod_disciplina, serie FROM
						(
							SELECT ds.ref_cod_disciplina, {$cod_serie} AS serie
							, ( SELECT COUNT(0) FROM pmieducar.dispensa_disciplina dd, pmieducar.v_matricula_matricula_turma mmt WHERE dd.ativo = 1 AND dd.ref_cod_disciplina = ds.ref_cod_disciplina AND mmt.cod_matricula = dd.ref_cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola}' AND mmt.ref_cod_serie = '{$cod_serie}' ) AS dispensas
							, ( SELECT COUNT(0) FROM pmieducar.nota_aluno na, pmieducar.v_matricula_matricula_turma mmt WHERE na.ativo = 1 AND na.ref_cod_disciplina = ds.ref_cod_disciplina AND na.modulo = '{$modulo}' AND na.ref_cod_matricula = mmt.cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola}' AND mmt.ref_cod_serie = '{$cod_serie}' ) AS notas
							FROM pmieducar.escola_serie_disciplina ds WHERE ds.ativo = 1 AND ref_ref_cod_serie = '{$cod_serie}' AND ref_ref_cod_escola = '{$cod_escola}'
						) AS sub1 WHERE dispensas + notas < $qtd_alunos
					)
					UNION
					(
						SELECT ref_cod_disciplina, serie FROM
						(
							SELECT ds.ref_cod_disciplina, {$cod_serie_mult} AS serie
							, ( SELECT COUNT(0) FROM pmieducar.dispensa_disciplina dd, pmieducar.v_matricula_matricula_turma mmt WHERE dd.ativo = 1 AND dd.ref_cod_disciplina = ds.ref_cod_disciplina AND mmt.cod_matricula = dd.ref_cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola_mult}' AND mmt.ref_cod_serie = '{$cod_serie_mult}' ) AS dispensas
							, ( SELECT COUNT(0) FROM pmieducar.nota_aluno na, pmieducar.v_matricula_matricula_turma mmt WHERE na.ativo = 1 AND na.ref_cod_disciplina = ds.ref_cod_disciplina AND na.modulo = '{$modulo}' AND na.ref_cod_matricula = mmt.cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola_mult}' AND mmt.ref_cod_serie = '{$cod_serie_mult}' ) AS notas
							FROM pmieducar.escola_serie_disciplina ds WHERE ds.ativo = 1 AND ref_ref_cod_serie = '{$cod_serie_mult}' AND ref_ref_cod_escola = '{$cod_escola_mult}'
						) AS sub2 WHERE dispensas + notas < $qtd_alunos_mult
					)
					";

				}
				else
				{
					$sql = "
					SELECT ref_cod_disciplina, serie FROM
					(
						SELECT ds.ref_cod_disciplina, {$cod_serie} AS serie
						, ( SELECT COUNT(0) FROM pmieducar.dispensa_disciplina dd, pmieducar.v_matricula_matricula_turma mmt WHERE dd.ativo = 1 AND dd.ref_cod_disciplina = ds.ref_cod_disciplina AND mmt.cod_matricula = dd.ref_cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola}' AND mmt.ref_cod_serie = '{$cod_serie}' ) AS dispensas
						, ( SELECT COUNT(0) FROM pmieducar.nota_aluno na, pmieducar.v_matricula_matricula_turma mmt WHERE na.ativo = 1 AND na.ref_cod_disciplina = ds.ref_cod_disciplina AND na.modulo = '{$modulo}' AND na.ref_cod_matricula = mmt.cod_matricula AND mmt.ativo = 1 AND mmt.aprovado = 3 AND mmt.ref_cod_turma = '{$this->cod_turma}' AND mmt.ref_cod_escola = '{$cod_escola}' AND mmt.ref_cod_serie = '{$cod_serie}' ) AS notas
						FROM pmieducar.escola_serie_disciplina ds WHERE ds.ativo = 1 AND ref_ref_cod_serie = '{$cod_serie}' AND ref_ref_cod_escola = '{$cod_escola}'
					) AS sub1 WHERE dispensas + notas < $qtd_alunos
					";
				}
				$db->Consulta($sql);
			}
			while( $db->ProximoRegistro() )
			{
				list($cod_disciplina,$cod_serie)=$db->Tupla();
				$disciplinas[] = array( "cod_disciplina" => $cod_disciplina, "cod_serie" => $cod_serie );
			}
			return $disciplinas;
		}
		return false;
	}

	/**
	 * Retorna as disciplinas do exame
	 *
	 * @return bool
	 */
	function moduloExameDisciplina($verifica_aluno_possui_nota = false)
	{
		if( is_numeric( $this->cod_turma ) )
		{
			$cod_curso = $this->getCurso();
			$objCurso = new clsPmieducarCurso($cod_curso);
			$detCurso = $objCurso->detalhe();

			$modulos = $this->maxModulos();
			$objNotaAluno = new clsPmieducarNotaAluno();
			return $objNotaAluno->getDisciplinasExame($this->cod_turma,$modulos,$detCurso["media"], $verifica_aluno_possui_nota);
		}
		return false;
	}

	/**
	 * Retorna os alunos do exame
	 *
	 * @return bool
	 */
	function moduloExameAlunos($cod_disciplina_exame = null)
	{
		if( is_numeric( $this->cod_turma ) )
		{
			$cod_curso = $this->getCurso();
			$objCurso = new clsPmieducarCurso($cod_curso);
			$detCurso = $objCurso->detalhe();

			$modulos = $this->maxModulos();
			$objNotaAluno = new clsPmieducarNotaAluno();
			return $objNotaAluno->getAlunosExame($this->cod_turma,$modulos,$detCurso["media"],$cod_disciplina_exame);
		}
		return false;
	}

	/**
	 * encontra as matriculas dessa turma que ainda nao receberam nota da disciplina $cod_disciplina da serie $cod_serie no modulo $modulo
	 *
	 * @param int $cod_disciplina
	 * @param int $cod_serie
	 * @param int $modulo
	 * @return array
	 */
	function matriculados_modulo_disciplina_sem_nota( $cod_disciplina, $cod_serie, $modulo )
	{
		$matriculas = array();

		$db = new clsBanco();
		$db->Consulta("
		SELECT cod_matricula FROM pmieducar.v_matricula_matricula_turma
		WHERE ref_cod_turma = '{$this->cod_turma}'
		AND aprovado = 3
		AND ativo = 1
		AND cod_matricula NOT IN (
			SELECT ref_cod_matricula
			FROM pmieducar.nota_aluno
			WHERE ref_cod_disciplina = '{$cod_disciplina}'
			AND ref_cod_serie = '{$cod_serie}'
			AND modulo = '{$modulo}'
			AND ativo = 1
		)
		AND cod_matricula NOT IN (
			SELECT ref_cod_matricula FROM pmieducar.dispensa_disciplina WHERE ref_cod_disciplina = '{$cod_disciplina}' AND ativo = 1
		)
		");

		if( $db->numLinhas() )
		{
			while ( $db->ProximoRegistro() )
			{
				list($matricula) = $db->Tupla();
				$matriculas[$matricula] = $matricula;
			}
		}
		return $matriculas;
	}

	/**
	 * volta o maior modulo comum (antes do exame) permitido nessa turma
	 *
	 * @return unknown
	 */
	function maxModulos()
	{
		if( is_numeric($this->cod_turma) )
		{
			$db = new clsBanco();
			// ve se o curso segue o padrao escolar
			$padrao = $db->CampoUnico("SELECT c.padrao_ano_escolar FROM {$this->_schema}curso c, {$this->_tabela} t WHERE cod_turma = {$this->cod_turma} AND c.cod_curso = t.ref_cod_curso AND c.ativo = 1 AND t.ativo = 1");
			if( $padrao )
			{
				// segue o padrao
				$cod_escola = $db->CampoUnico("SELECT ref_ref_cod_escola FROM {$this->_tabela} WHERE cod_turma = {$this->cod_turma} AND ativo = 1");
				$ano = $db->CampoUnico("SELECT COALESCE(MAX(ano),0) FROM {$this->_schema}escola_ano_letivo WHERE ref_cod_escola = {$cod_escola} AND andamento = 1 AND ativo = 1");
				if( $ano )
				{
					return $db->CampoUnico("SELECT COALESCE(MAX(sequencial),0) FROM {$this->_schema}ano_letivo_modulo WHERE ref_ref_cod_escola = {$cod_escola} AND ref_ano = {$ano}");
				}
			}
			else
			{
				// nao segue o padrao escolar
				return $db->CampoUnico("SELECT COALESCE(MAX(sequencial),0) FROM {$this->_schema}turma_modulo WHERE ref_cod_turma = {$this->cod_turma}");
			}
		}
		return false;
	}

	function getCurso()
	{
		if( is_numeric($this->cod_turma) )
		{
			$db = new clsBanco();
			$db->Consulta("SELECT ref_cod_curso, ref_ref_cod_serie FROM {$this->_tabela} WHERE cod_turma = '{$this->cod_turma}'");
			$db->ProximoRegistro();
			list($cod_curso,$cod_serie) = $db->Tupla();
			if( is_numeric($cod_curso) )
			{
				return $cod_curso;
			}

			if(is_numeric($cod_serie))
			{
				$db->Consulta("SELECT ref_cod_curso FROM {$this->_schema}serie WHERE cod_serie = '{$cod_serie}'");
				$db->ProximoRegistro();
				list($cod_curso) = $db->Tupla();
				if( is_numeric($cod_curso) )
				{
					return $cod_curso;
				}
			}

			return false;
		}
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_infra_predio_comodo = null, $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, $bool_verifica_serie_multiseriada = false, $bool_tem_alunos_aguardando_nota = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null)
	{

		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} t";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_turma ) )
		{
			$filtros .= "{$whereAnd} t.cod_turma = '{$int_cod_turma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			if($bool_verifica_serie_multiseriada == true)
			{
				$mult = " OR  t.ref_ref_cod_serie_mult = '{$int_ref_ref_cod_serie}' ";
			}

			$filtros .= "{$whereAnd} ( t.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}' $mult )";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			/*if($bool_verifica_serie_multiseriada === true)
			{
				$mult = " OR  t.ref_ref_cod_escola_mult = '{$int_ref_ref_cod_escola}' ";
			}*/

			$filtros .= "{$whereAnd} ( t.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}' )";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_infra_predio_comodo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_infra_predio_comodo = '{$int_ref_cod_infra_predio_comodo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_turma ) )
		{
			$filtros .= "{$whereAnd} t.nm_turma LIKE '%{$str_nm_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sgl_turma ) )
		{
			$filtros .= "{$whereAnd} t.sgl_turma LIKE '%{$str_sgl_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_max_aluno ) )
		{
			$filtros .= "{$whereAnd} t.max_aluno = '{$int_max_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_multiseriada ) )
		{
			$filtros .= "{$whereAnd} t.multiseriada = '{$int_multiseriada}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} t.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_turma_tipo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_turma_tipo = '{$int_ref_cod_turma_tipo}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
			$whereAnd = " AND ";
		}
	/*	if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}*/
		if( is_numeric( $int_ref_cod_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_regente = '{$int_ref_cod_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao_regente = '{$int_ref_cod_instituicao_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ))
		{
			$filtros .= "{$whereAnd} t.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola_mult ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_escola_mult = '{$int_ref_ref_cod_escola_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie_mult ))
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_serie_mult = '{$int_ref_ref_cod_serie_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric($int_qtd_min_alunos_matriculados) )
		{
			$filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.matricula_turma WHERE ref_cod_turma = t.cod_turma) >= '{$int_qtd_min_alunos_matriculados}' ";
			$whereAnd = " AND ";
		}

		if (is_bool($bool_tem_alunos_aguardando_nota) )
		{
			if ($bool_tem_alunos_aguardando_nota)
			{
				$filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.v_matricula_matricula_turma mmt WHERE mmt.ref_cod_turma = t.cod_turma AND mmt.aprovado = 3 AND mmt.ativo = 1 ) > 0 ";
				$whereAnd = " AND ";
			}
		}
		if (is_bool($visivel))
		{
			if ($visivel)
			{
				$filtros .= "{$whereAnd} t.visivel = TRUE";
				$whereAnd = " AND ";
			}
			else
			{
				$filtros .= "{$whereAnd} t.visivel = FALSE";
				$whereAnd = " AND ";
			}
		}
		elseif (is_array($visivel) && count($visivel))
		{
			$filtros .= "{$whereAnd} t.visivel IN (".implode(",", $visivel).")";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.visivel = TRUE";
			$whereAnd = " AND ";
		}

		if( is_numeric( $turma_turno_id ) ) {
			$filtros .= "{$whereAnd} t.turma_turno_id = '{$turma_turno_id}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $tipo_boletim ) ) {
			$filtros .= "{$whereAnd} t.tipo_boletim = '{$tipo_boletim}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $ano ) ) {
			$filtros .= "{$whereAnd} t.ano = '{$ano}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} t, {$this->_schema}escola_serie es, {$this->_schema}serie s, {$this->_schema}escola e {$filtros}" );
	 	$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} t {$filtros}" );

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
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista2( $int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_infra_predio_comodo = null, $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null )
	{

		/*$nm_escola = "(
	SELECT c.nm_escola AS nm_escola
	FROM  pmieducar.escola_complemento c
	WHERE c.ref_cod_escola = t.ref_ref_cod_escola
AND e.cod_escola = t.ref_ref_cod_escola

UNION
	SELECT j.fantasia AS nm_escola
	FROM  cadastro.juridica j
	WHERE j.idpes = e.ref_idpes
and  e.cod_escola = t.ref_ref_cod_escola
					)  AS nm_escola ";a*/
		$sql = "SELECT {$this->_campos_lista},c.nm_curso,s.nm_serie,i.nm_instituicao FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c, {$this->_schema}instituicao i ";
		$filtros = "";

		$whereAnd = " WHERE t.ref_cod_curso = c.cod_curso AND c.ref_cod_instituicao = i.cod_instituicao AND ";

		if( is_numeric( $int_cod_turma ) )
		{
			$filtros .= "{$whereAnd} t.cod_turma = '{$int_cod_turma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_infra_predio_comodo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_infra_predio_comodo = '{$int_ref_cod_infra_predio_comodo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_turma ) )
		{
			$filtros .= "{$whereAnd} t.nm_turma LIKE '%{$str_nm_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sgl_turma ) )
		{
			$filtros .= "{$whereAnd} t.sgl_turma LIKE '%{$str_sgl_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_max_aluno ) )
		{
			$filtros .= "{$whereAnd} t.max_aluno = '{$int_max_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_multiseriada ) )
		{
			$filtros .= "{$whereAnd} t.multiseriada = '{$int_multiseriada}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} t.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_turma_tipo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_turma_tipo = '{$int_ref_cod_turma_tipo}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
			$whereAnd = " AND ";
		}
	/*	if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}*/
		if( is_numeric( $int_ref_cod_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_regente = '{$int_ref_cod_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao_regente = '{$int_ref_cod_instituicao_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola_mult ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_escola_mult = '{$int_ref_ref_cod_escola_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie_mult ) )
		{
			$filtros .= "{$whereAnd} t.int_ref_ref_cod_serie_mult = '{$int_ref_ref_cod_serie_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric($int_qtd_min_alunos_matriculados) )
		{
			$filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.matricula_turma WHERE ref_cod_turma = t.cod_turma) >= '{$int_qtd_min_alunos_matriculados}' ";
			$whereAnd = " AND ";
		}
		if (is_bool($visivel))
		{
			if ($visivel)
			{
				$filtros .= "{$whereAnd} t.visivel = TRUE";
				$whereAnd = " AND ";
			}
			else
			{
				$filtros .= "{$whereAnd} t.visivel = FALSE";
				$whereAnd = " AND ";
			}
		}
		elseif (is_array($visivel) && count($visivel))
		{
			$filtros .= "{$whereAnd} t.visivel IN (".implode(",", $visivel).")";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.visivel = TRUE";
			$whereAnd = " AND ";
		}

		if( is_numeric( $turma_turno_id ) ) {
			$filtros .= "{$whereAnd} t.turma_turno_id = '{$turma_turno_id}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $tipo_boletim ) ) {
			$filtros .= "{$whereAnd} t.tipo_boletim = '{$tipo_boletim}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $ano ) ) {
			$filtros .= "{$whereAnd} t.ano = '{$ano}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//		echo "<!--{$sql}-->";

//		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} t, {$this->_schema}escola_serie es, {$this->_schema}serie s, {$this->_schema}escola e {$filtros}" );
	 	$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c , {$this->_schema}instituicao i {$filtros}" );

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
	 * Retorna uma lista filtrados de acordo com os parametros
	 * (Modificação da lista2, agora trazendo somente turmas do ano atual)
	 * @return array
	 */
	function lista3( $int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_infra_predio_comodo = null, $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null )
	{


		$sql = "SELECT {$this->_campos_lista},c.nm_curso,s.nm_serie,i.nm_instituicao FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c, {$this->_schema}instituicao i ";
		$filtros = "";

		$whereAnd = " WHERE t.ref_cod_curso = c.cod_curso AND c.ref_cod_instituicao = i.cod_instituicao AND ";

		if( is_numeric( $int_cod_turma ) )
		{
			$filtros .= "{$whereAnd} t.cod_turma = '{$int_cod_turma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} t.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_infra_predio_comodo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_infra_predio_comodo = '{$int_ref_cod_infra_predio_comodo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_turma ) )
		{
			$filtros .= "{$whereAnd} t.nm_turma LIKE '%{$str_nm_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sgl_turma ) )
		{
			$filtros .= "{$whereAnd} t.sgl_turma LIKE '%{$str_sgl_turma}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_max_aluno ) )
		{
			$filtros .= "{$whereAnd} t.max_aluno = '{$int_max_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_multiseriada ) )
		{
			$filtros .= "{$whereAnd} t.multiseriada = '{$int_multiseriada}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} t.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} t.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_turma_tipo ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_turma_tipo = '{$int_ref_cod_turma_tipo}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} t.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_regente = '{$int_ref_cod_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_regente ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao_regente = '{$int_ref_cod_instituicao_regente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} t.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola_mult ) )
		{
			$filtros .= "{$whereAnd} t.ref_ref_cod_escola_mult = '{$int_ref_ref_cod_escola_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_serie_mult ) )
		{
			$filtros .= "{$whereAnd} t.int_ref_ref_cod_serie_mult = '{$int_ref_ref_cod_serie_mult}'";
			$whereAnd = " AND ";
		}
		if( is_numeric($int_qtd_min_alunos_matriculados) )
		{
			$filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.matricula_turma WHERE ref_cod_turma = t.cod_turma) >= '{$int_qtd_min_alunos_matriculados}' ";
			$whereAnd = " AND ";
		}
		if (is_bool($visivel))
		{
			if ($visivel)
			{
				$filtros .= "{$whereAnd} t.visivel = TRUE";
				$whereAnd = " AND ";
			}
			else
			{
				$filtros .= "{$whereAnd} t.visivel = FALSE";
				$whereAnd = " AND ";
			}
		}
		elseif (is_array($visivel) && count($visivel))
		{
			$filtros .= "{$whereAnd} t.visivel IN (".implode(",", $visivel).")";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} t.visivel = TRUE";
			$whereAnd = " AND ";
		}

		if( is_numeric( $turma_turno_id ) ) {
			$filtros .= "{$whereAnd} t.turma_turno_id = '{$turma_turno_id}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $tipo_boletim ) ) {
			$filtros .= "{$whereAnd} t.tipo_boletim = '{$tipo_boletim}'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $ano ) ) {
			$filtros .= "{$whereAnd} t.ano = '{$ano}'";
			$whereAnd = " AND ";
		}

		$filtros .= "{$whereAnd} (ano = (SELECT max(ano)
					  FROM pmieducar.escola_ano_letivo mat	        
					  WHERE ativo = 1 and mat.andamento = 1) or ((t.ano is null) AND (select 1 from pmieducar.matricula_turma 
					  where ativo = 1 and date_part('year',data_cadastro) = (SELECT max(ano)
					  FROM pmieducar.escola_ano_letivo
					  WHERE ativo = 1 and andamento = 1) and t.cod_turma = ref_cod_turma limit 1) is not null))";

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c , {$this->_schema}instituicao i {$filtros}" );


		$db->Consulta( $sql);

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
		if( is_numeric( $this->cod_turma ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} t WHERE t.cod_turma = '{$this->cod_turma}'" );
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
		if( is_numeric( $this->cod_turma ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_turma = '{$this->cod_turma}'" );
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
		if( is_numeric( $this->cod_turma ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_turma = '{$this->cod_turma}'" );
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
