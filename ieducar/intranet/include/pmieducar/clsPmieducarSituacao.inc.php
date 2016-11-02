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
* Criado em 14/07/2006 17:42 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarSituacao
{
	var $cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_situacao;
	var $permite_emprestimo;
	var $descricao;
	var $situacao_padrao;
	var $situacao_emprestada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

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
	function clsPmieducarSituacao( $cod_situacao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_situacao = null, $permite_emprestimo = null, $descricao = null, $situacao_padrao = null, $situacao_emprestada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_biblioteca = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}situacao";

		$this->_campos_lista = $this->_todos_campos = "s.cod_situacao, s.ref_usuario_exc, s.ref_usuario_cad, s.nm_situacao, s.permite_emprestimo, s.descricao, s.situacao_padrao, s.situacao_emprestada, s.data_cadastro, s.data_exclusao, s.ativo, s.ref_cod_biblioteca";

		if( is_numeric( $ref_cod_biblioteca ) )
		{
			if( class_exists( "clsPmieducarBiblioteca" ) )
			{
				$tmp_obj = new clsPmieducarBiblioteca( $ref_cod_biblioteca );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_biblioteca = $ref_cod_biblioteca;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.biblioteca WHERE cod_biblioteca = '{$ref_cod_biblioteca}'" ) )
				{
					$this->ref_cod_biblioteca = $ref_cod_biblioteca;
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


		if( is_numeric( $cod_situacao ) )
		{
			$this->cod_situacao = $cod_situacao;
		}
		if( is_string( $nm_situacao ) )
		{
			$this->nm_situacao = $nm_situacao;
		}
		if( is_numeric( $permite_emprestimo ) )
		{
			$this->permite_emprestimo = $permite_emprestimo;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_numeric( $situacao_padrao ) )
		{
			$this->situacao_padrao = $situacao_padrao;
		}
		if( is_numeric( $situacao_emprestada ) )
		{
			$this->situacao_emprestada = $situacao_emprestada;
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

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_situacao ) && is_numeric( $this->permite_emprestimo ) && is_numeric( $this->situacao_padrao ) && is_numeric( $this->situacao_emprestada ) && is_numeric( $this->ref_cod_biblioteca ) )
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
			if( is_string( $this->nm_situacao ) )
			{
				$campos .= "{$gruda}nm_situacao";
				$valores .= "{$gruda}'{$this->nm_situacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->permite_emprestimo ) )
			{
				$campos .= "{$gruda}permite_emprestimo";
				$valores .= "{$gruda}'{$this->permite_emprestimo}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->situacao_padrao ) )
			{
				$campos .= "{$gruda}situacao_padrao";
				$valores .= "{$gruda}'{$this->situacao_padrao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->situacao_emprestada ) )
			{
				$campos .= "{$gruda}situacao_emprestada";
				$valores .= "{$gruda}'{$this->situacao_emprestada}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$campos .= "{$gruda}ref_cod_biblioteca";
				$valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_situacao_seq");
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
		if( is_numeric( $this->cod_situacao ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_string( $this->nm_situacao ) )
			{
				$set .= "{$gruda}nm_situacao = '{$this->nm_situacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->permite_emprestimo ) )
			{
				$set .= "{$gruda}permite_emprestimo = '{$this->permite_emprestimo}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->situacao_padrao ) )
			{
				$set .= "{$gruda}situacao_padrao = '{$this->situacao_padrao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->situacao_emprestada ) )
			{
				$set .= "{$gruda}situacao_emprestada = '{$this->situacao_emprestada}'";
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
			if( is_numeric( $this->ref_cod_biblioteca ) )
			{
				$set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_situacao = '{$this->cod_situacao}'" );
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
	function lista( $int_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_situacao = null, $int_permite_emprestimo = null, $str_descricao = null, $int_situacao_padrao = null, $int_situacao_emprestada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null )
	{
		$sql = "SELECT {$this->_campos_lista}, b.ref_cod_instituicao, b.ref_cod_escola FROM {$this->_tabela} s, {$this->_schema}biblioteca b";

		$whereAnd = " AND ";
		$filtros = " WHERE s.ref_cod_biblioteca = b.cod_biblioteca ";

		if( is_numeric( $int_cod_situacao ) )
		{
			$filtros .= "{$whereAnd} s.cod_situacao = '{$int_cod_situacao}'";
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
		if( is_string( $str_nm_situacao ) )
		{
			$filtros .= "{$whereAnd} s.nm_situacao LIKE '%{$str_nm_situacao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_permite_emprestimo ) )
		{
			$filtros .= "{$whereAnd} s.permite_emprestimo = '{$int_permite_emprestimo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} s.descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_situacao_padrao ) )
		{
			$filtros .= "{$whereAnd} s.situacao_padrao = '{$int_situacao_padrao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_situacao_emprestada ) )
		{
			$filtros .= "{$whereAnd} s.situacao_emprestada = '{$int_situacao_emprestada}'";
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
		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} s, {$this->_schema}biblioteca b {$filtros}" );

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
		if( is_numeric( $this->cod_situacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_situacao = '{$this->cod_situacao}'" );
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
		if( is_numeric( $this->cod_situacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_situacao = '{$this->cod_situacao}'" );
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
		if( is_numeric( $this->cod_situacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_situacao = '{$this->cod_situacao}'" );
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