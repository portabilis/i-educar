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
/***
* @author Prefeitura Municipal de Itajaí
*
* Criado em 23/06/2006 08:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarEscola
{
	var $cod_escola;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_cod_instituicao;
	var $ref_cod_escola_localizacao;
	var $ref_cod_escola_rede_ensino;
	var $ref_idpes;
	var $sigla;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

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
	function clsPmieducarEscola( $cod_escola = null, $ref_usuario_cad = null, $ref_usuario_exc = null, $ref_cod_instituicao = null, $ref_cod_escola_localizacao = null, $ref_cod_escola_rede_ensino = null, $ref_idpes = null, $sigla = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}escola";

		$this->_campos_lista = $this->_todos_campos = "e.cod_escola, e.ref_usuario_cad, e.ref_usuario_exc, e.ref_cod_instituicao, e.ref_cod_escola_localizacao, e.ref_cod_escola_rede_ensino, e.ref_idpes, e.sigla, e.data_cadastro, e.data_exclusao, e.ativo";

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
		if( is_numeric( $ref_cod_escola_localizacao ) )
		{
			if( class_exists( "clsPmieducarEscolaLocalizacao" ) )
			{
				$tmp_obj = new clsPmieducarEscolaLocalizacao( $ref_cod_escola_localizacao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_localizacao WHERE cod_escola_localizacao = '{$ref_cod_escola_localizacao}'" ) )
				{
					$this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
				}
			}
		}
		if( is_numeric( $ref_cod_escola_rede_ensino ) )
		{
			if( class_exists( "clsPmieducarEscolaRedeEnsino" ) )
			{
				$tmp_obj = new clsPmieducarEscolaRedeEnsino( $ref_cod_escola_rede_ensino );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_rede_ensino WHERE cod_escola_rede_ensino = '{$ref_cod_escola_rede_ensino}'" ) )
				{
					$this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
				}
			}
		}
		if( is_numeric( $ref_idpes ) )
		{
			if( class_exists( "clsCadastroJuridica" ) )
			{
				$tmp_obj = new clsCadastroJuridica( $ref_idpes );
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
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.juridica WHERE idpes = '{$ref_idpes}'" ) )
				{
					$this->ref_idpes = $ref_idpes;
				}
			}
		}


		if( is_numeric( $cod_escola ) )
		{
			$this->cod_escola = $cod_escola;
		}
		if( is_string( $sigla ) )
		{
			$this->sigla = $sigla;
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
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola_localizacao ) && is_numeric( $this->ref_cod_escola_rede_ensino ) && is_string( $this->sigla ) )
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
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$campos .= "{$gruda}ref_usuario_exc";
				$valores .= "{$gruda}'{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola_localizacao ) )
			{
				$campos .= "{$gruda}ref_cod_escola_localizacao";
				$valores .= "{$gruda}'{$this->ref_cod_escola_localizacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola_rede_ensino ) )
			{
				$campos .= "{$gruda}ref_cod_escola_rede_ensino";
				$valores .= "{$gruda}'{$this->ref_cod_escola_rede_ensino}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idpes ) )
			{
				$campos .= "{$gruda}ref_idpes";
				$valores .= "{$gruda}'{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_string( $this->sigla ) )
			{
				$campos .= "{$gruda}sigla";
				$valores .= "{$gruda}'{$this->sigla}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId("{$this->_tabela}_cod_escola_seq");
		}
		else
		{
			echo "<br><br>is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola_localizacao ) && is_numeric( $this->ref_cod_escola_rede_ensino ) && is_string( $this->sigla )";
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
		if( is_numeric( $this->cod_escola ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola_localizacao ) )
			{
				$set .= "{$gruda}ref_cod_escola_localizacao = '{$this->ref_cod_escola_localizacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola_rede_ensino ) )
			{
				$set .= "{$gruda}ref_cod_escola_rede_ensino = '{$this->ref_cod_escola_rede_ensino}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idpes ) )
			{
				$set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_string( $this->sigla ) )
			{
				$set .= "{$gruda}sigla = '{$this->sigla}'";
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

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_escola = '{$this->cod_escola}'" );
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
  public function lista($int_cod_escola = NULL, $int_ref_usuario_cad = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_cod_instituicao = NULL,
    $int_ref_cod_escola_localizacao = NULL, $int_ref_cod_escola_rede_ensino = NULL,
    $int_ref_idpes = NULL, $str_sigla = NULL, $date_data_cadastro = NULL,
    $date_data_exclusao = NULL, $int_ativo = NULL, $str_nome = NULL,
    $escola_sem_avaliacao = NULL)
  {

    $sql = "
      SELECT * FROM
      (
        SELECT j.fantasia AS nome, {$this->_campos_lista}, 1 AS tipo_cadastro
          FROM {$this->_tabela} e, cadastro.juridica j
          WHERE e.ref_idpes = j.idpes
        UNION
        SELECT c.nm_escola AS nome, {$this->_campos_lista}, 2 AS tipo_cadastro
          FROM {$this->_tabela} e, pmieducar.escola_complemento c
          WHERE e.cod_escola = c.ref_cod_escola
      ) AS sub";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_cod_escola)) {
			$filtros .= "{$whereAnd} cod_escola = '{$int_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola_localizacao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola_localizacao = '{$int_ref_cod_escola_localizacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola_rede_ensino ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola_rede_ensino = '{$int_ref_cod_escola_rede_ensino}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sigla ) )
		{
			$filtros .= "{$whereAnd} sigla LIKE '%{$str_sigla}%'";
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
		if( is_numeric( $int_ativo ) )
		{
			$filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
			$whereAnd = " AND ";
		}
		if (is_bool($escola_sem_avaliacao))
		{
			if (dbBool($escola_sem_avaliacao))
			{
				$filtros .= "{$whereAnd} NOT EXISTS (SELECT 1 FROM pmieducar.escola_curso ec, pmieducar.curso c WHERE
												ec.ref_cod_escola = cod_escola
												AND ec.ref_cod_curso = c.cod_curso
												AND ec.ativo = 1 AND c.ativo = 1
												AND c.ref_cod_tipo_avaliacao IS NOT NULL)";
			}
			else
			{
				$filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.escola_curso ec, pmieducar.curso c WHERE
												ec.ref_cod_escola = cod_escola
												AND ec.ref_cod_curso = c.cod_curso
												AND ec.ativo = 1 AND c.ativo = 1
												AND c.ref_cod_tipo_avaliacao IS NOT NULL)";
			}
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();
		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$db->Consulta("
				SELECT COUNT(0) FROM
				(
					SELECT j.fantasia AS nome, {$this->_campos_lista}, 1 AS tipo_cadastro
					FROM {$this->_tabela} e, cadastro.juridica j
					WHERE e.ref_idpes = j.idpes
				UNION
					SELECT c.nm_escola AS nome, {$this->_campos_lista}, 2 AS tipo_cadastro
					FROM {$this->_tabela} e, pmieducar.escola_complemento c
					WHERE e.cod_escola = c.ref_cod_escola
				) AS sub
				{$filtros}
		");
		$db->ProximoRegistro();
		list( $this->_total ) = $db->Tupla();
		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
				$this->_total = count( $tupla);
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
		if( is_numeric( $this->cod_escola ) )
		{
			$db = new clsBanco();
			$db->Consulta( "
				SELECT * FROM
				(
					SELECT c.nm_escola AS nome, {$this->_todos_campos}, 2 AS tipo_cadastro
					FROM {$this->_tabela} e, pmieducar.escola_complemento c
					WHERE e.cod_escola = c.ref_cod_escola

				UNION

					SELECT j.fantasia AS nome, {$this->_todos_campos}, 1 AS tipo_cadastro
					FROM {$this->_tabela} e, cadastro.juridica j
					WHERE e.ref_idpes = j.idpes


				) AS sub WHERE cod_escola = '{$this->cod_escola}'"
			);
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
		if( is_numeric( $this->cod_escola ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_escola = '{$this->cod_escola}'" );
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
		if( is_numeric( $this->cod_escola ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_escola = '{$this->cod_escola}'" );
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