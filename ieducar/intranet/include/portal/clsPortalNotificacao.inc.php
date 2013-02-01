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
* Criado em 02/10/2006 16:22 pelo gerador automatico de classes
*/

//require_once( "include/portal/geral.inc.php" );

class clsPortalNotificacao
{
	var $cod_notificacao;
	var $ref_cod_funcionario;
	var $titulo;
	var $conteudo;
	var $data_hora_ativa;
	var $url;
	var $visualizacoes;

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
	 * @param integer cod_notificacao
	 * @param integer ref_cod_funcionario
	 * @param string titulo
	 * @param string conteudo
	 * @param string data_hora_ativa
	 * @param string url
	 * @param integer visualizacoes
	 *
	 * @return object
	 */
	function clsPortalNotificacao( $cod_notificacao = null, $ref_cod_funcionario = null, $titulo = null, $conteudo = null, $data_hora_ativa = null, $url = null, $visualizacoes = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}notificacao";

		$this->_campos_lista = $this->_todos_campos = "cod_notificacao, ref_cod_funcionario, titulo, conteudo, data_hora_ativa, url, visualizacoes";

		if( is_numeric( $ref_cod_funcionario ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_cod_funcionario );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_funcionario = $ref_cod_funcionario;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_funcionario = $ref_cod_funcionario;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_cod_funcionario}'" ) )
				{
					$this->ref_cod_funcionario = $ref_cod_funcionario;
				}
			}
		}


		if( is_numeric( $cod_notificacao ) )
		{
			$this->cod_notificacao = $cod_notificacao;
		}
		if( is_string( $titulo ) )
		{
			$this->titulo = $titulo;
		}
		if( is_string( $conteudo ) )
		{
			$this->conteudo = $conteudo;
		}
		if( is_string( $data_hora_ativa ) )
		{
			$this->data_hora_ativa = $data_hora_ativa;
		}
		if( is_string( $url ) )
		{
			$this->url = $url;
		}
		if( is_numeric( $visualizacoes ) )
		{
			$this->visualizacoes = $visualizacoes;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_funcionario ) && is_numeric( $this->visualizacoes ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_funcionario ) )
			{
				$campos .= "{$gruda}ref_cod_funcionario";
				$valores .= "{$gruda}'{$this->ref_cod_funcionario}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$campos .= "{$gruda}titulo";
				$valores .= "{$gruda}'{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->conteudo ) )
			{
				$campos .= "{$gruda}conteudo";
				$valores .= "{$gruda}'{$this->conteudo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_hora_ativa ) )
			{
				$campos .= "{$gruda}data_hora_ativa";
				$valores .= "{$gruda}'{$this->data_hora_ativa}'";
				$gruda = ", ";
			}
			if( is_string( $this->url ) )
			{
				$campos .= "{$gruda}url";
				$valores .= "{$gruda}'{$this->url}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->visualizacoes ) )
			{
				$campos .= "{$gruda}visualizacoes";
				$valores .= "{$gruda}'{$this->visualizacoes}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
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

		$db = new clsBanco();
		$set = "";

			if( is_numeric( $this->cod_notificacao ) )
			{
				$set .= "{$gruda}cod_notificacao = '{$this->cod_notificacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario ) )
			{
				$set .= "{$gruda}ref_cod_funcionario = '{$this->ref_cod_funcionario}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$set .= "{$gruda}titulo = '{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->conteudo ) )
			{
				$set .= "{$gruda}conteudo = '{$this->conteudo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_hora_ativa ) )
			{
				$set .= "{$gruda}data_hora_ativa = '{$this->data_hora_ativa}'";
				$gruda = ", ";
			}
			if( is_string( $this->url ) )
			{
				$set .= "{$gruda}url = '{$this->url}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->visualizacoes ) )
			{
				$set .= "{$gruda}visualizacoes = '{$this->visualizacoes}'";
				$gruda = ", ";
			}


		if( $set )
		{
			$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_notificacao = '{$this->cod_notificacao}'" );
			return true;
		}

		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_cod_notificacao
	 * @param integer int_ref_cod_funcionario
	 * @param string str_titulo
	 * @param string str_conteudo
	 * @param string date_data_hora_ativa_ini
	 * @param string date_data_hora_ativa_fim
	 * @param string str_url
	 * @param integer int_visualizacoes
	 *
	 * @return array
	 */
	function lista( $int_cod_notificacao = null, $int_ref_cod_funcionario = null, $str_titulo = null, $str_conteudo = null, $date_data_hora_ativa_ini = null, $date_data_hora_ativa_fim = null, $str_url = null, $int_visualizacoes = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_notificacao ) )
		{
			$filtros .= "{$whereAnd} cod_notificacao = '{$int_cod_notificacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_funcionario ) )
		{
			$filtros .= "{$whereAnd} ref_cod_funcionario = '{$int_ref_cod_funcionario}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_titulo ) )
		{
			$filtros .= "{$whereAnd} titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_conteudo ) )
		{
			$filtros .= "{$whereAnd} conteudo LIKE '%{$str_conteudo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_hora_ativa_ini ) )
		{
			$filtros .= "{$whereAnd} data_hora_ativa >= '{$date_data_hora_ativa_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_hora_ativa_fim ) )
		{
			$filtros .= "{$whereAnd} data_hora_ativa <= '{$date_data_hora_ativa_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_url ) )
		{
			$filtros .= "{$whereAnd} url LIKE '%{$str_url}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_visualizacoes ) )
		{
			$filtros .= "{$whereAnd} visualizacoes = '{$int_visualizacoes}'";
			$whereAnd = " AND ";
		}


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
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric($this->cod_notificacao))
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_notificacao = '{$this->cod_notificacao}'" );
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
		if( is_numeric($this->cod_notificacao))
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_notificacao = '{$this->cod_notificacao}'" );
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

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_notificacao = '{$this->cod_notificacao}'" );
		return true;
		*/



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