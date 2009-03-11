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
* Criado em 07/07/2006 08:39 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAluno
{
	var $cod_aluno;
	//var $ref_cod_pessoa_educ;
	var $ref_cod_aluno_beneficio;
	var $ref_cod_religiao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $caminho_foto;
	var $analfabeto;
	var $nm_pai;
	var $nm_mae;
	var $tipo_responsavel;

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
	function clsPmieducarAluno( $cod_aluno = null,/* $ref_cod_pessoa_educ = null, */$ref_cod_aluno_beneficio = null, $ref_cod_religiao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_idpes = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $caminho_foto = null,$analfabeto = null, $nm_pai = null, $nm_mae = null, $tipo_responsavel = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}aluno";

		//$this->_campos_lista = $this->_todos_campos = "cod_aluno, ref_cod_pessoa_educ, ref_cod_aluno_beneficio, ref_cod_religiao, ref_usuario_exc, ref_usuario_cad, ref_idpes, data_cadastro, data_exclusao, ativo, caminho_foto";
		$this->_campos_lista = $this->_todos_campos = "cod_aluno, ref_cod_aluno_beneficio, ref_cod_religiao, ref_usuario_exc, ref_usuario_cad, ref_idpes, data_cadastro, data_exclusao, ativo, caminho_foto, analfabeto, nm_pai, nm_mae,tipo_responsavel";

	/*	if( is_numeric( $ref_cod_pessoa_educ ) )
		{
			if( class_exists( "clsPmieducarPessoaEduc" ) )
			{
				$tmp_obj = new clsPmieducarPessoaEduc( $ref_cod_pessoa_educ );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.pessoa_educ WHERE cod_pessoa_educ = '{$ref_cod_pessoa_educ}'" ) )
				{
					$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
				}
			}
		}
		*/
		if( is_numeric( $ref_cod_aluno_beneficio ) )
		{
			if( class_exists( "clsPmieducarAlunoBeneficio" ) )
			{
				$tmp_obj = new clsPmieducarAlunoBeneficio( $ref_cod_aluno_beneficio );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.aluno_beneficio WHERE cod_aluno_beneficio = '{$ref_cod_aluno_beneficio}'" ) )
				{
					$this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
				}
			}
		}elseif ($ref_cod_aluno_beneficio == "NULL"){

			$this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
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
		if( is_numeric( $ref_idpes ) )
		{
			if( class_exists( "clsCadastroFisica" ) )
			{
				$tmp_obj = new clsCadastroFisica( $ref_idpes );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_idpes}'" ) )
				{
					$this->ref_idpes = $ref_idpes;
				}
			}
		}

		if( is_numeric( $cod_aluno ) )
		{
			$this->cod_aluno = $cod_aluno;
		}
		if( is_numeric( $ref_cod_religiao )  || $ref_cod_aluno_beneficio == "NULL")
		{
			$this->ref_cod_religiao = $ref_cod_religiao;
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
		if( is_string( $caminho_foto ) )
		{
			$this->caminho_foto = $caminho_foto;
		}
		if( is_numeric( $analfabeto ) )
		{
			$this->analfabeto = $analfabeto;
		}
		if( is_string( $caminho_foto ) )
		{
			$this->caminho_foto = $caminho_foto;
		}
		if( is_string( $nm_pai ) )
		{
			$this->nm_pai = $nm_pai;
		}
		if( is_string( $nm_mae ) )
		{
			$this->nm_mae = $nm_mae;
		}
		if( is_string( $tipo_responsavel )  )
		{
			$this->tipo_responsavel = $tipo_responsavel;
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( (is_numeric( $this->ref_idpes )/* || is_numeric( $this->ref_cod_pessoa_educ )*/ ) /*&& is_numeric( $this->ref_cod_aluno_beneficio ) && is_numeric( $this->ref_cod_religiao )*/ )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			/*if( is_numeric( $this->ref_cod_pessoa_educ ) )
			{
				$campos .= "{$gruda}ref_cod_pessoa_educ";
				$valores .= "{$gruda}'{$this->ref_cod_pessoa_educ}'";
				$gruda = ", ";
			}*/
			if( is_numeric( $this->ref_cod_aluno_beneficio ) )
			{
				$campos .= "{$gruda}ref_cod_aluno_beneficio";
				$valores .= "{$gruda}'{$this->ref_cod_aluno_beneficio}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_religiao ) )
			{
				$campos .= "{$gruda}ref_cod_religiao";
				$valores .= "{$gruda}'{$this->ref_cod_religiao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idpes ) )
			{
				$campos .= "{$gruda}ref_idpes";
				$valores .= "{$gruda}'{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->analfabeto ) )
			{
				$campos .= "{$gruda}analfabeto";
				$valores .= "{$gruda}'{$this->analfabeto}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_string( $this->caminho_foto ) )
			{
				$campos .= "{$gruda}caminho_foto";
				$valores .= "{$gruda}'{$this->caminho_foto}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_pai ) && $this->nm_pai != "NULL")
			{
				$campos .= "{$gruda}nm_pai";
				$valores .= "{$gruda}'{$this->nm_pai}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_mae ) && $this->nm_mae != "NULL" )
			{
				$campos .= "{$gruda}nm_mae";
				$valores .= "{$gruda}'{$this->nm_mae}'";
				$gruda = ", ";
			}
			if( is_string( $this->tipo_responsavel ) && sizeof($this->tipo_responsavel) <= 1  )
			{
				$campos .= "{$gruda}tipo_responsavel";
				$valores .= "{$gruda}'{$this->tipo_responsavel}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_aluno_seq");
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
		if( is_numeric( $this->cod_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

		/*	if( is_numeric( $this->ref_cod_pessoa_educ ) )
			{
				$set .= "{$gruda}ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'";
				$gruda = ", ";
			}*/

			if( is_numeric( $this->ref_cod_aluno_beneficio ) || $this->ref_cod_aluno_beneficio == "NULL")
			{
				$set .= "{$gruda}ref_cod_aluno_beneficio = {$this->ref_cod_aluno_beneficio}";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_religiao ) || $this->ref_cod_religiao == "NULL")
			{
				$set .= "{$gruda}ref_cod_religiao = {$this->ref_cod_religiao}";
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
			if( is_numeric( $this->ref_idpes ) )
			{
				$set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
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
			if( is_string( $this->caminho_foto ) &&  $this->caminho_foto != "NULL")
			{
				$set .= "{$gruda}caminho_foto = '{$this->caminho_foto}'";
				$gruda = ", ";
			}elseif ($this->caminho_foto == "NULL"){
				$set .= "{$gruda}caminho_foto = {$this->caminho_foto}";
				$gruda = ", ";
			}
			if( is_numeric( $this->analfabeto ) )
			{
				$set .= "{$gruda}analfabeto = '{$this->analfabeto}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_pai ) && $this->nm_pai != "NULL")
			{
				$set .= "{$gruda}nm_pai = '{$this->nm_pai}'";
				$gruda = ", ";
			}
			elseif ($this->nm_pai == "NULL")
			{
				$set .= "{$gruda}nm_pai = NULL";
				$gruda = ", ";
			}

			if( is_string( $this->nm_mae ) && $this->nm_mae != "NULL" )
			{
				$set .= "{$gruda}nm_mae = '{$this->nm_mae}'";
				$gruda = ", ";
			}
			elseif ($this->nm_mae == "NULL")
			{
				$set .= "{$gruda}nm_mae = NULL";
				$gruda = ", ";
			}
			if( is_string( $this->tipo_responsavel ) && sizeof($this->tipo_responsavel) <= 1 )
			{
				$set .= "{$gruda}tipo_responsavel = '{$this->tipo_responsavel}'";
				$gruda = ", ";
			}
			elseif ($this->tipo_responsavel == '')
			{
				$set .= "{$gruda}tipo_responsavel = NULL";
				$gruda = ", ";
			}


			if( $set )
			{
				//echo  "UPDATE {$this->_tabela} SET $set WHERE cod_aluno = '{$this->cod_aluno}'";die;
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_aluno = '{$this->cod_aluno}'" );
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
	function lista( $int_cod_aluno = null,/* $int_ref_cod_pessoa_educ = null,*/ $int_ref_cod_aluno_beneficio = null, $int_ref_cod_religiao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_caminho_foto = null,$str_nome_aluno = null,$str_nome_responsavel = null, $int_cpf_responsavel = null, $int_analfabeto = null, $str_nm_pai = null, $str_nm_mae = null, $int_ref_cod_escola = null,$str_tipo_responsavel = null )
	{ //echo "$int_cod_aluno = null, $int_ref_cod_pessoa_educ = null, $int_ref_cod_aluno_beneficio = null, $int_ref_cod_religiao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_caminho_foto = null";die;

		$filtros = "";
		$this->resetCamposLista();

		/*$this->_campos_lista .= " ,(SELECT nome
									  FROM pmieducar.pessoa_educ
								     WHERE cod_pessoa_educ = ref_cod_pessoa_educ

									 UNION

								    SELECT nome
								      FROM cadastro.pessoa
						             WHERE idpes = ref_idpes
									) as nome_aluno";	*/

		$this->_campos_lista .= " ,(SELECT nome
								      FROM cadastro.pessoa
						             WHERE idpes = ref_idpes
									) as nome_aluno";


		/*$this->_campos_lista .= " , (SELECT ref_idpes_responsavel
									  FROM pmieducar.pessoa_educ
								     WHERE cod_pessoa_educ = ref_cod_pessoa_educ

									 UNION

								    SELECT idpes_responsavel
								      FROM cadastro.fisica
						             WHERE idpes = ref_idpes
								   )as ref_idpes_responsavel";*/

		/*$this->_campos_lista .= " , (SELECT COALESCE( idpes_responsavel, idpes_pai, idpes_mae)
								       FROM cadastro.fisica
						              WHERE idpes = ref_idpes
								    )as ref_idpes_responsavel";*/
		/*$this->_campos_lista .= " ,(SELECT nome
									  FROM cadastro.pessoa
								     WHERE pessoa.idpes = (SELECT ref_idpes_responsavel
															 FROM pmieducar.pessoa_educ
														    WHERE cod_pessoa_educ = ref_cod_pessoa_educ

															 UNION

														   SELECT idpes_responsavel
														     FROM cadastro.fisica
												            WHERE idpes = ref_idpes
														  )

									) as nome_responsavel";
*/
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_aluno ) )
		{
			$filtros .= "{$whereAnd} cod_aluno = '{$int_cod_aluno}'";
			$whereAnd = " AND ";
		}
		/*if( is_numeric( $int_ref_cod_pessoa_educ ) )
		{
			$filtros .= "{$whereAnd} ref_cod_pessoa_educ = '{$int_ref_cod_pessoa_educ}'";
			$whereAnd = " AND ";
		}*/
		if( is_numeric( $int_ref_cod_aluno_beneficio ) )
		{
			$filtros .= "{$whereAnd} ref_cod_aluno_beneficio = '{$int_ref_cod_aluno_beneficio}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_religiao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_religiao = '{$int_ref_cod_religiao}'";
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
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
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
		if( /*is_null( $int_ativo ) || */$int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_caminho_foto ) )
		{
			$filtros .= "{$whereAnd} caminho_foto LIKE '%{$str_caminho_foto}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_analfabeto ) )
		{
			$filtros .= "{$whereAnd} analfabeto = '{$int_analfabeto}'";
			$whereAnd = " AND ";
		}


		if( is_string( $str_nome_aluno ) )
		{

			$filtros .= "{$whereAnd} exists (SELECT 1
										       FROM cadastro.pessoa
								              WHERE cadastro.pessoa.idpes = ref_idpes
								               AND to_ascii(lower(nome)) like to_ascii(lower('%{$str_nome_aluno}%'))
										 	 )";
			$whereAnd = " AND ";
		}

		if( is_string( $str_nome_responsavel )  || is_numeric($int_cpf_responsavel))
		{

			$and_resp = "";

			if(is_string($str_nome_responsavel)){

				//$and_resp .= "and upper(to_ascii(responsavel.nome)) like upper(to_ascii('%$str_nome_responsavel%'))";
				$and_nome_pai_mae  = "OR upper(to_ascii(aluno.nm_pai)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'p')";
				$and_nome_pai_mae .= "OR upper(to_ascii(aluno.nm_mae)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'm')";
				$and_nome_resp = "	(upper(to_ascii(pai_mae.nome)) like upper(to_ascii('%$str_nome_responsavel%')))  AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
									  OR
									  (upper(to_ascii(pai_mae.nome)) like upper(to_ascii('%$str_nome_responsavel%') ))  AND (aluno.tipo_responsavel = 'p') AND pai_mae.idpes = fisica_aluno.idpes_pai";
				$and_resp = "AND";
			}

			if(is_numeric($int_cpf_responsavel)){

				$and_cpf_pai_mae = "and fisica_resp.cpf like '$int_cpf_responsavel'";
				//$and_resp = " {$and_resp} fisica_aluno.cpf like '$int_cpf_responsavel'";
			}

			$filtros .= "AND (exists(
							SELECT 1
							FROM cadastro.fisica fisica_resp
							     ,cadastro.fisica
							     ,cadastro.pessoa
							     ,cadastro.pessoa as responsavel
							where fisica.idpes_responsavel = fisica_resp.idpes
							and pessoa.idpes = fisica.idpes
							and responsavel.idpes = fisica.idpes_responsavel

							$and_cpf_pai_mae
							and aluno.ref_idpes = pessoa.idpes
							)
							$and_nome_pai_mae

						OR EXISTS ( SELECT 1 FROM
										cadastro.fisica as fisica_aluno
										 ,cadastro.pessoa as pai_mae
										 ,cadastro.fisica as fisica_pai_mae
									WHERE fisica_aluno.idpes = aluno.ref_idpes
									  AND (
											$and_nome_resp
											$and_resp
											(fisica_pai_mae.idpes = fisica_aluno.idpes_pai OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae) AND fisica_pai_mae.cpf like '$int_cpf_responsavel'
									  		)
									)
							)							";

//			$filtros .= "AND (exists(
//							SELECT 1
//							FROM cadastro.fisica fisica_resp
//							     ,cadastro.fisica
//							     ,cadastro.pessoa
//							     ,cadastro.pessoa as responsavel
//							where fisica.idpes_responsavel = fisica_resp.idpes
//							and pessoa.idpes = fisica.idpes
//							and responsavel.idpes = fisica.idpes_responsavel
//							{$and_resp}
//							and aluno.ref_idpes = pessoa.idpes
//							)
//							OR upper(to_ascii(aluno.nm_pai)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'p' OR aluno.tipo_responsavel IS NULL)
//							OR upper(to_ascii(aluno.nm_mae)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'm' OR aluno.tipo_responsavel IS NULL)
//						OR EXISTS ( SELECT 1 FROM
//										cadastro.fisica as fisica_aluno
//										 left outer join cadastro.pessoa as pessoa_mae on(pessoa_mae.idpes = fisica_aluno.idpes_pai)
//										 left outer join cadastro.pessoa as pessoa_pai on ( pessoa_pai.idpes = fisica_aluno.idpes_mae )
//									WHERE fisica_aluno.idpes = aluno.ref_idpes
//									  AND (
//									  (upper(to_ascii(pessoa_mae.nome)) like upper(to_ascii('%$str_nome_responsavel%')))  AND (aluno.tipo_responsavel = 'm' OR aluno.tipo_responsavel IS NULL)
//									  OR
//									  (upper(to_ascii(pessoa_pai.nome)) like upper(to_ascii('%$str_nome_responsavel%') ))  AND (aluno.tipo_responsavel = 'p' OR aluno.tipo_responsavel IS NULL)
//									  		)
//									)
//							)							";

			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_pai ) )
		{
			$filtros .= "{$whereAnd} to_ascii(lower(nm_pai)) nm_pai LIKE to_ascii(lower('%{$str_nm_pai}%'))";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_mae ) )
		{
			$filtros .= "{$whereAnd} to_ascii(lower(nm_mae)) LIKE to_ascii(lower('%{$str_nm_mae}%'))";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1 )";
			$whereAnd = " AND ";
		}
		if( is_numeric( $str_tipo_responsavel ) )
		{
			$filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		if(!$this->getOrderby())
			$this->setOrderby("nome_aluno");

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//die($sql);
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
		/*if($resultado["ref_idpes"]){
			$SELECT = "SELECT nome FROM cadastro.pessoa where idpes = {$resultado["ref_idpes"]}";
		}else{
			$SELECT = "SELECT nome FROM pmieducar.pessoa_educ where cod_pessoa_educ = {$resultado["ref_cod_pessoa_educ"]}";
		}
		*/
		//$resultado["nome_aluno"]  = $db->CampoUnico($SELECT);

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
	function lista2( $int_cod_aluno = null,/* $int_ref_cod_pessoa_educ = null,*/ $int_ref_cod_aluno_beneficio = null, $int_ref_cod_religiao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_caminho_foto = null,$str_nome_aluno = null,$str_nome_responsavel = null, $int_cpf_responsavel = null, $int_analfabeto = null, $str_nm_pai = null, $str_nm_mae = null, $int_ref_cod_escola = null,$str_tipo_responsavel = null, $data_nascimento = null, $str_nm_pai2 = null, $str_nm_mae2 = null, $str_nm_responsavel2 = null )
	{ 
		$filtros = "";
		$this->resetCamposLista();

		$this->_campos_lista .= " ,(SELECT nome
								      FROM cadastro.pessoa
						             WHERE idpes = ref_idpes
									) as nome_aluno";

		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_aluno ) )
		{
			$filtros .= "{$whereAnd} cod_aluno = '{$int_cod_aluno}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_aluno_beneficio ) )
		{
			$filtros .= "{$whereAnd} ref_cod_aluno_beneficio = '{$int_ref_cod_aluno_beneficio}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_religiao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_religiao = '{$int_ref_cod_religiao}'";
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
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
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
		if( /*is_null( $int_ativo ) || */$int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_caminho_foto ) )
		{
			$filtros .= "{$whereAnd} caminho_foto LIKE '%{$str_caminho_foto}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_analfabeto ) )
		{
			$filtros .= "{$whereAnd} analfabeto = '{$int_analfabeto}'";
			$whereAnd = " AND ";
		}


		if( is_string( $str_nome_aluno ) )
		{

			$filtros .= "{$whereAnd} exists (SELECT 1
										       FROM cadastro.pessoa
								              WHERE cadastro.pessoa.idpes = ref_idpes
								               AND to_ascii(lower(nome)) like to_ascii(lower('%{$str_nome_aluno}%'))
										 	 )";
			$whereAnd = " AND ";
		}

		if( is_string( $str_nome_responsavel )  || is_numeric($int_cpf_responsavel))
		{

			$and_resp = "";

			if(is_string($str_nome_responsavel)){

				//$and_resp .= "and upper(to_ascii(responsavel.nome)) like upper(to_ascii('%$str_nome_responsavel%'))";
				$and_nome_pai_mae  = "OR upper(to_ascii(aluno.nm_pai)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'p')";
				$and_nome_pai_mae .= "OR upper(to_ascii(aluno.nm_mae)) like upper(to_ascii('%$str_nome_responsavel%'))  AND (aluno.tipo_responsavel = 'm')";
				$and_nome_resp = "	(upper(to_ascii(pai_mae.nome)) like upper(to_ascii('%$str_nome_responsavel%')))  AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
									  OR
									  (upper(to_ascii(pai_mae.nome)) like upper(to_ascii('%$str_nome_responsavel%') ))  AND (aluno.tipo_responsavel = 'p') AND pai_mae.idpes = fisica_aluno.idpes_pai";
				$and_resp = "AND";
			}

			if(is_numeric($int_cpf_responsavel)){

				$and_cpf_pai_mae = "and fisica_resp.cpf like '$int_cpf_responsavel'";
				//$and_resp = " {$and_resp} fisica_aluno.cpf like '$int_cpf_responsavel'";
			}

			$filtros .= "AND (exists(
							SELECT 1
							FROM cadastro.fisica fisica_resp
							     ,cadastro.fisica
							     ,cadastro.pessoa
							     ,cadastro.pessoa as responsavel
							where fisica.idpes_responsavel = fisica_resp.idpes
							and pessoa.idpes = fisica.idpes
							and responsavel.idpes = fisica.idpes_responsavel

							$and_cpf_pai_mae
							and aluno.ref_idpes = pessoa.idpes
							)
							$and_nome_pai_mae

						OR EXISTS ( SELECT 1 FROM
										cadastro.fisica as fisica_aluno
										 ,cadastro.pessoa as pai_mae
										 ,cadastro.fisica as fisica_pai_mae
									WHERE fisica_aluno.idpes = aluno.ref_idpes
									  AND (
											$and_nome_resp
											$and_resp
											(fisica_pai_mae.idpes = fisica_aluno.idpes_pai OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae) AND fisica_pai_mae.cpf like '$int_cpf_responsavel'
									  		)
									)
							)							";

			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_pai ) )
		{
			$filtros .= "{$whereAnd} to_ascii(lower(nm_pai)) nm_pai LIKE to_ascii(lower('%{$str_nm_pai}%'))";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_mae ) )
		{
			$filtros .= "{$whereAnd} to_ascii(lower(nm_mae)) LIKE to_ascii(lower('%{$str_nm_mae}%'))";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1 )";
			$whereAnd = " AND ";
		}
		if( is_numeric( $str_tipo_responsavel ) )
		{
			$filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
			$whereAnd = " AND ";
		}
		//$data_nascimento = null, $str_nm_pai2 = null, $str_nm_mae2 = null, $str_nm_responsavel2 = null
		if (!empty($data_nascimento))
		{
			$filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM cadastro.fisica f WHERE f.idpes = ref_idpes AND to_char(data_nasc,'DD/MM/YYYY') = '{$data_nascimento}')";
			$whereAnd = " AND ";
		}
		if (!empty($str_nm_pai2) || !empty($str_nm_mae2) || !empty($str_nm_responsavel2))
		{
			$complemento_letf_outer = "";
			$complemento_where = "";
			$and_where = "";
			if (!empty($str_nm_pai2))
			{
				$complemento_sql .= " LEFT OUTER JOIN cadastro.pessoa AS pessoa_pai ON (pessoa_pai.idpes = f.idpes_pai)";
				$complemento_where .= "{$and_where} (nm_pai ILIKE ('%{$str_nm_pai2}%') OR pessoa_pai.nome ILIKE ('%{$str_nm_pai2}%'))";
				$and_where = " AND ";
			}
			if (!empty($str_nm_mae2))
			{
				$complemento_sql .= " LEFT OUTER JOIN cadastro.pessoa AS pessoa_mae ON (pessoa_mae.idpes = f.idpes_mae)";
				$complemento_where .= "{$and_where} (nm_mae ILIKE ('%{$str_nm_mae2}%') OR pessoa_mae.nome ILIKE ('%{$str_nm_mae2}%'))";
				$and_where = " AND ";
			}
			if (!empty($str_nm_responsavel2))
			{
				$complemento_sql .= " LEFT OUTER JOIN cadastro.pessoa AS pessoa_responsavel ON (pessoa_responsavel.idpes = f.idpes_responsavel)";
				$complemento_where .= "{$and_where} (pessoa_responsavel.nome ILIKE ('%{$str_nm_responsavel2}%'))";
				$and_where = " AND ";
			}
			$filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM cadastro.fisica f
											{$complemento_sql}
										WHERE
											f.idpes = ref_idpes
											AND ({$complemento_where})
									)";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		if(!$this->getOrderby())
			$this->setOrderby("nome_aluno");

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
//		die($sql);
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
		if( is_numeric( $this->cod_aluno ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		else if( is_numeric( $this->ref_idpes ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'" );
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
		if( is_numeric( $this->cod_aluno ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		else if( is_numeric( $this->ref_idpes ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'" );
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
	function existePessoa()
	{
		if(is_numeric( $this->ref_idpes ))
		{
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	function getResponsavelAluno()
	{
		if($this->cod_aluno)
		{
			$registro = $this->detalhe();

			$registro["nome_responsavel"] = null;
//echo $registro['tipo_responsavel']
			if($registro['tipo_responsavel'] == 'p'  || (!$registro["nome_responsavel"] && $registro['tipo_responsavel'] == null))
			{
				$obj_fisica= new clsFisica($registro["ref_idpes"]);
				$det_fisica_aluno = $obj_fisica->detalhe();
				if($det_fisica_aluno["idpes_pai"] )
				{
					$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_pai"] );
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$obj_fisica= new clsFisica($det_fisica_aluno["idpes_pai"]);
					$det_fisica = $obj_fisica->detalhe();
					$registro["nome_responsavel"] = $det_ref_idpes['nome'];

					if( $det_fisica["cpf"] )
						$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
				}

			}

			if($registro['tipo_responsavel'] == 'm' || ($registro["nome_responsavel"] == null && $registro['tipo_responsavel'] == null))
			{
				if(!$det_fisica_aluno)
				{
					$obj_fisica= new clsFisica($registro["ref_idpes"]);
					$det_fisica_aluno = $obj_fisica->detalhe();
				}

				if($det_fisica_aluno["idpes_mae"] )
				{
					$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_mae"] );
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$obj_fisica= new clsFisica($det_fisica_aluno["idpes_mae"]);
					$det_fisica = $obj_fisica->detalhe();
					$registro["nome_responsavel"] = $det_ref_idpes["nome"];

					if($det_fisica["cpf"])
						$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
				}
			}

			if($registro['tipo_responsavel'] == 'r' || ($registro["nome_responsavel"] == null && $registro['tipo_responsavel'] == null))
			{
				if(!$det_fisica_aluno)
				{
					$obj_fisica= new clsFisica($registro["ref_idpes"]);
					$det_fisica_aluno = $obj_fisica->detalhe();
				}

				if( $det_fisica_aluno["idpes_responsavel"] )
				{
					$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_responsavel"] );
					$obj_fisica = new clsFisica( $det_fisica_aluno["idpes_responsavel"] );
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$det_fisica = $obj_fisica->detalhe();
					$registro["nome_responsavel"] = $det_ref_idpes["nome"];
					if($det_fisica["cpf"])
						$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
				}
			}

			if(!$registro["nome_responsavel"])
			{
				if($registro['tipo_responsavel'] != null)
				{
					if($registro['tipo_responsavel'] == 'p')
						$registro["nome_responsavel"] = $registro["nm_pai"];
					else
						$registro["nome_responsavel"] = $registro["nm_mae"];
				}
				else
				{
					if($registro["nm_pai"])
						$registro["nome_responsavel"] = $registro["nm_pai"];
					else
						$registro["nome_responsavel"] = $registro["nm_mae"];
				}
			}

			return $registro;
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
		if( is_numeric( $this->cod_aluno ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'" );
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