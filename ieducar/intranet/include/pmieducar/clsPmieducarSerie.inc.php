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
* Criado em 03/07/2006 11:07 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarSerie
{
	var $cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_curso;
	var $nm_serie;
	var $etapa_curso;
	var $concluinte;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $intervalo;

	var $idade_inicial;
	var $idade_final;
	var $media_especial;
	var $ultima_nota_define;

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
	function clsPmieducarSerie( $cod_serie = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_curso = null, $nm_serie = null, $etapa_curso = null, $concluinte = null, $carga_horaria = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $intervalo = null, $idade_inicial = null, $idade_final = null, $media_especial = null, $ultima_nota_define = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}serie";

		$this->_campos_lista = $this->_todos_campos = "s.cod_serie, s.ref_usuario_exc, s.ref_usuario_cad, s.ref_cod_curso, s.nm_serie, s.etapa_curso, s.concluinte, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.intervalo, s.idade_inicial, s.idade_final,media_especial, ultima_nota_define ";

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


		if( is_numeric( $cod_serie ) )
		{
			$this->cod_serie = $cod_serie;
		}
		if( is_string( $nm_serie ) )
		{
			$this->nm_serie = $nm_serie;
		}
		if( is_numeric( $etapa_curso ) )
		{
			$this->etapa_curso = $etapa_curso;
		}
		if( is_numeric( $concluinte ) )
		{
			$this->concluinte = $concluinte;
		}
		if( is_numeric( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
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
		if( is_numeric( $intervalo ) )
		{
			$this->intervalo = $intervalo;
		}
		if( is_numeric( $idade_inicial ) )
		{
			$this->idade_inicial = $idade_inicial;
		}
		if( is_numeric( $idade_final ) )
		{
			$this->idade_final = $idade_final;
		}

		$this->media_especial = dbBool($media_especial) ? "true" : "false";
		$this->ultima_nota_define = dbBool($ultima_nota_define) ? "true" : "false";

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_curso ) && is_string( $this->nm_serie ) && is_numeric( $this->etapa_curso ) && is_numeric( $this->concluinte ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->intervalo ) )
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
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$campos .= "{$gruda}ref_cod_curso";
				$valores .= "{$gruda}'{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_serie ) )
			{
				$campos .= "{$gruda}nm_serie";
				$valores .= "{$gruda}'{$this->nm_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->etapa_curso ) )
			{
				$campos .= "{$gruda}etapa_curso";
				$valores .= "{$gruda}'{$this->etapa_curso}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->concluinte ) )
			{
				$campos .= "{$gruda}concluinte";
				$valores .= "{$gruda}'{$this->concluinte}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idade_inicial ) )
			{
				$campos .= "{$gruda}idade_inicial";
				$valores .= "{$gruda}'{$this->idade_inicial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idade_final ) )
			{
				$campos .= "{$gruda}idade_final";
				$valores .= "{$gruda}'{$this->idade_final}'";
				$gruda = ", ";
			}

			$campos .= "{$gruda}media_especial";
			$valores .= "{$gruda}".(dbBool($this->media_especial) ? "true" : "false");
			$gruda = ", ";
			
			$campos .= "{$gruda}ultima_nota_define";
			$valores .= "{$gruda}".(dbBool($this->ultima_nota_define) ? "true" : "false");
			$gruda = ", ";

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->intervalo ) )
			{
				$campos .= "{$gruda}intervalo";
				$valores .= "{$gruda}'{$this->intervalo}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_serie_seq");
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
		if( is_numeric( $this->cod_serie ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_serie ) )
			{
				$set .= "{$gruda}nm_serie = '{$this->nm_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->etapa_curso ) )
			{
				$set .= "{$gruda}etapa_curso = '{$this->etapa_curso}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->concluinte ) )
			{
				$set .= "{$gruda}concluinte = '{$this->concluinte}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
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
			if( is_numeric( $this->intervalo ) )
			{
				$set .= "{$gruda}intervalo = '{$this->intervalo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idade_inicial ) )
			{
				$set .= "{$gruda}idade_inicial = '{$this->idade_inicial}'";
				$gruda = ", ";
			}
			else{
				$set .= "{$gruda}idade_inicial = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->idade_final ) )
			{
				$set .= "{$gruda}idade_final = '{$this->idade_final}'";
				$gruda = ", ";
			}else{
				$set .= "{$gruda}idade_final = NULL";
				$gruda = ", ";
			}

			$set .= "{$gruda}media_especial = ".(dbBool($this->media_especial) ? "true" : "false");
			$gruda = ", ";
			
			$set .= "{$gruda}ultima_nota_define = ".(dbBool($this->ultima_nota_define) ? "true" : "false");
			$gruda = ", ";

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_serie = '{$this->cod_serie}'" );
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
	function lista( $int_cod_serie = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_curso = null, $str_nm_serie = null, $int_etapa_curso = null, $int_concluinte = null, $int_carga_horaria = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null, $int_intervalo = null,$int_idade_inicial = null,$int_idade_final = null,$int_ref_cod_escola = null, $bool_media_especial = null )
	{
		$sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao FROM {$this->_tabela} s, {$this->_schema}curso c";

		$whereAnd = " AND ";
		$filtros = " WHERE s.ref_cod_curso = c.cod_curso";

		if( is_numeric( $int_cod_serie ) )
		{
			$filtros .= "{$whereAnd} s.cod_serie = '{$int_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} s.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} s.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_serie ) )
		{
			$filtros .= "{$whereAnd} s.nm_serie LIKE '%{$str_nm_serie}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_etapa_curso ) )
		{
			$filtros .= "{$whereAnd} s.etapa_curso = '{$int_etapa_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_concluinte ) )
		{
			$filtros .= "{$whereAnd} s.concluinte = '{$int_concluinte}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_carga_horaria ) )
		{
			$filtros .= "{$whereAnd} s.carga_horaria = '{$int_carga_horaria}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} s.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} s.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric($int_ref_cod_instituicao))
		{
			$filtros .= "{$whereAnd} c.ref_cod_instituicao = '$int_ref_cod_instituicao'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_intervalo ) )
		{
			$filtros .= "{$whereAnd} intervalo = '{$int_intervalo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idade_inicial ) )
		{
			$filtros .= "{$whereAnd} idade_inicial = '{$int_idade_inicial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idade_final) )
		{
			$filtros .= "{$whereAnd} idade_final= '{$int_idade_final}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola) )
		{
			$filtros .= "{$whereAnd} EXISTS ( SELECT 1 FROM pmieducar.escola_serie es WHERE s.cod_serie = es.ref_cod_serie AND es.ref_cod_escola = '{$int_ref_cod_escola}') ";
			$whereAnd = " AND ";
		}
		if( dbBool( $bool_media_especial) )
		{
			$filtros .= "{$whereAnd}  ".(dbBool($bool_media_especial) ? "true" : "false");
			$whereAnd = " AND ";
		}
		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} s, {$this->_schema}curso c {$filtros}" );

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
		if( is_numeric( $this->cod_serie ) && is_numeric( $this->ref_cod_curso ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}' AND s.ref_cod_curso = '{$this->ref_cod_curso}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		elseif( is_numeric( $this->cod_serie ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}'" );
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
		if( is_numeric( $this->cod_serie ) )
		{
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_serie = '{$this->cod_serie}'" );
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
		if( is_numeric( $this->cod_serie ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_serie = '{$this->cod_serie}'" );
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

	function getNotEscolaSerie( $ref_cod_curso, $ref_cod_escola )
	{
		$db = new clsBanco();
		$sql = "SELECT *
				FROM pmieducar.serie s
				WHERE s.ref_cod_curso = '{$ref_cod_curso}'
				AND s.cod_serie NOT IN
				(
					SELECT es.ref_cod_serie
					FROM pmieducar.escola_serie es
					WHERE es.ref_cod_escola = '{$ref_cod_escola}'
				)";

		$db->Consulta( $sql );

		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla;
		}
		return $resultado;
	}

}
?>