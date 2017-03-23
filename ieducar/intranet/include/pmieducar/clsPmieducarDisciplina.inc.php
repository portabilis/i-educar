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
* Criado em 10/07/2006 08:48 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarDisciplina
{
	var $cod_disciplina;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $desc_disciplina;
	var $desc_resumida;
	var $abreviatura;
	var $carga_horaria;
	var $apura_falta;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_disciplina;
	var $ref_cod_curso;

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
	function clsPmieducarDisciplina( $cod_disciplina = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $desc_disciplina = null, $desc_resumida = null, $abreviatura = null, $carga_horaria = null, $apura_falta = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $nm_disciplina = null, $ref_cod_curso = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}disciplina";

		$this->_campos_lista = $this->_todos_campos = "d.cod_disciplina, d.ref_usuario_exc, d.ref_usuario_cad, d.desc_disciplina, d.desc_resumida, d.abreviatura, d.carga_horaria, d.apura_falta, d.data_cadastro, d.data_exclusao, d.ativo, d.nm_disciplina, d.ref_cod_curso";

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


		if( is_numeric( $cod_disciplina ) )
		{
			$this->cod_disciplina = $cod_disciplina;
		}
		if( is_string( $desc_disciplina ) )
		{
			$this->desc_disciplina = $desc_disciplina;
		}
		if( is_string( $desc_resumida ) )
		{
			$this->desc_resumida = $desc_resumida;
		}
		if( is_string( $abreviatura ) )
		{
			$this->abreviatura = $abreviatura;
		}
		if( is_numeric( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
		}
		if( is_numeric( $apura_falta ) )
		{
			$this->apura_falta = $apura_falta;
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
		if( is_string( $nm_disciplina ) )
		{
			$this->nm_disciplina = $nm_disciplina;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) /* && is_string( $this->desc_disciplina )*&& is_string( $this->desc_resumida ) */ && is_string( $this->abreviatura ) && is_numeric( $this->carga_horaria ) && is_string( $this->nm_disciplina ) )
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
			if( is_string( $this->desc_disciplina ) )
			{
				$campos .= "{$gruda}desc_disciplina";
				$valores .= "{$gruda}'{$this->desc_disciplina}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_resumida ) )
			{
				$campos .= "{$gruda}desc_resumida";
				$valores .= "{$gruda}'{$this->desc_resumida}'";
				$gruda = ", ";
			}
			if( is_string( $this->abreviatura ) )
			{
				$campos .= "{$gruda}abreviatura";
				$valores .= "{$gruda}'{$this->abreviatura}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->apura_falta ) )
			{
				$campos .= "{$gruda}apura_falta";
				$valores .= "{$gruda}'{$this->apura_falta}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_string( $this->nm_disciplina ) )
			{
				$campos .= "{$gruda}nm_disciplina";
				$valores .= "{$gruda}'{$this->nm_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$campos .= "{$gruda}ref_cod_curso";
				$valores .= "{$gruda}'{$this->ref_cod_curso}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_disciplina_seq");
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
		if( is_numeric( $this->cod_disciplina ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_string( $this->desc_disciplina ) )
			{
				$set .= "{$gruda}desc_disciplina = '{$this->desc_disciplina}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_resumida ) )
			{
				$set .= "{$gruda}desc_resumida = '{$this->desc_resumida}'";
				$gruda = ", ";
			}
			if( is_string( $this->abreviatura ) )
			{
				$set .= "{$gruda}abreviatura = '{$this->abreviatura}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->apura_falta ) )
			{
				$set .= "{$gruda}apura_falta = '{$this->apura_falta}'";
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
			if( is_string( $this->nm_disciplina ) )
			{
				$set .= "{$gruda}nm_disciplina = '{$this->nm_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_curso ) )
			{
				$set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_disciplina = '{$this->cod_disciplina}'" );
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
	function lista( $int_cod_disciplina = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_desc_disciplina = null, $str_desc_resumida = null, $str_abreviatura = null, $int_carga_horaria = null, $int_apura_falta = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_disciplina = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $arr_int_cod_disciplina = null )
	{
		$sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao FROM {$this->_tabela} d, {$this->_schema}curso c";

		$whereAnd = " AND ";
		$filtros = " WHERE d.ref_cod_curso = c.cod_curso ";

		if( is_numeric( $int_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} d.cod_disciplina = '{$int_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} d.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} d.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_desc_disciplina ) )
		{
			$filtros .= "{$whereAnd} d.desc_disciplina LIKE '%{$str_desc_disciplina}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_desc_resumida ) )
		{
			$filtros .= "{$whereAnd} d.desc_resumida LIKE '%{$str_desc_resumida}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_abreviatura ) )
		{
			$filtros .= "{$whereAnd} d.abreviatura LIKE '%{$str_abreviatura}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_carga_horaria ) )
		{
			$filtros .= "{$whereAnd} d.carga_horaria = '{$int_carga_horaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_apura_falta ) )
		{
			$filtros .= "{$whereAnd} d.apura_falta = '{$int_apura_falta}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} d.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} d.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} d.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} d.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} d.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} d.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_disciplina ) )
		{
			$filtros .= "{$whereAnd} d.nm_disciplina LIKE '%{$str_nm_disciplina}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} d.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric($int_ref_cod_instituicao))
		{
			$filtros .= "{$whereAnd} c.ref_cod_instituicao = '$int_ref_cod_instituicao'";
			$whereAnd = " AND ";
		}
		if( is_array($arr_int_cod_disciplina) && count($arr_int_cod_disciplina))
		{
			$filtros .= "{$whereAnd} d.cod_disciplina IN ( ";
			$gruda = "";
			foreach ($arr_int_cod_disciplina AS $value)
			{
				if( is_numeric($value) )
				{
					$filtros .= "{$gruda} $value";
					$gruda = ",";
				}
			}
			$filtros .= " )";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} d, {$this->_schema}curso c {$filtros}" );

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
		if( is_numeric( $this->cod_disciplina ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} d WHERE d.cod_disciplina = '{$this->cod_disciplina}'" );
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
		if( is_numeric( $this->cod_disciplina ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_disciplina = '{$this->cod_disciplina}'" );
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
		if( is_numeric( $this->cod_disciplina ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_disciplina = '{$this->cod_disciplina}'" );
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