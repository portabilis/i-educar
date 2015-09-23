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
* Criado em 26/06/2006 16:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAvaliacao
{
	var $cod_avaliacao;
	var $disc_ref_ref_cod_serie;
	var $disc_ref_ref_cod_escola;
	var $disc_ref_ref_cod_disciplina;
	var $disc_ref_ref_cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $titulo;
	var $descricao;
	var $aplicada;
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
	function clsPmieducarAvaliacao( $cod_avaliacao = null, $disc_ref_ref_cod_serie = null, $disc_ref_ref_cod_escola = null, $disc_ref_ref_cod_disciplina = null, $disc_ref_ref_cod_turma = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $titulo = null, $descricao = null, $aplicada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}avaliacao";

		$this->_campos_lista = $this->_todos_campos = "cod_avaliacao, disc_ref_ref_cod_serie, disc_ref_ref_cod_escola, disc_ref_ref_cod_disciplina, disc_ref_ref_cod_turma, ref_usuario_exc, ref_usuario_cad, titulo, descricao, aplicada, data_cadastro, data_exclusao, ativo";
		
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
		if( is_numeric( $disc_ref_ref_cod_turma ) && is_numeric( $disc_ref_ref_cod_disciplina ) && is_numeric( $disc_ref_ref_cod_escola ) && is_numeric( $disc_ref_ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarTurmaDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarTurmaDisciplina( $disc_ref_ref_cod_turma, $disc_ref_ref_cod_disciplina, $disc_ref_ref_cod_escola, $disc_ref_ref_cod_serie );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->disc_ref_ref_cod_turma = $disc_ref_ref_cod_turma;
						$this->disc_ref_ref_cod_disciplina = $disc_ref_ref_cod_disciplina;
						$this->disc_ref_ref_cod_escola = $disc_ref_ref_cod_escola;
						$this->disc_ref_ref_cod_serie = $disc_ref_ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->disc_ref_ref_cod_turma = $disc_ref_ref_cod_turma;
						$this->disc_ref_ref_cod_disciplina = $disc_ref_ref_cod_disciplina;
						$this->disc_ref_ref_cod_escola = $disc_ref_ref_cod_escola;
						$this->disc_ref_ref_cod_serie = $disc_ref_ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.turma_disciplina WHERE ref_cod_turma = '{$disc_ref_ref_cod_turma}' AND ref_cod_disciplina = '{$disc_ref_ref_cod_disciplina}' AND ref_cod_escola = '{$disc_ref_ref_cod_escola}' AND ref_cod_serie = '{$disc_ref_ref_cod_serie}'" ) )
				{
					$this->disc_ref_ref_cod_turma = $disc_ref_ref_cod_turma;
					$this->disc_ref_ref_cod_disciplina = $disc_ref_ref_cod_disciplina;
					$this->disc_ref_ref_cod_escola = $disc_ref_ref_cod_escola;
					$this->disc_ref_ref_cod_serie = $disc_ref_ref_cod_serie;
				}
			}
		}

		
		if( is_numeric( $cod_avaliacao ) )
		{
			$this->cod_avaliacao = $cod_avaliacao;
		}
		if( is_string( $titulo ) )
		{
			$this->titulo = $titulo;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_numeric( $aplicada ) )
		{
			$this->aplicada = $aplicada;
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
		if( is_numeric( $this->disc_ref_ref_cod_serie ) && is_numeric( $this->disc_ref_ref_cod_escola ) && is_numeric( $this->disc_ref_ref_cod_disciplina ) && is_numeric( $this->disc_ref_ref_cod_turma ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->titulo ) && is_numeric( $this->aplicada ) )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->disc_ref_ref_cod_serie ) )
			{
				$campos .= "{$gruda}disc_ref_ref_cod_serie";
				$valores .= "{$gruda}'{$this->disc_ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_escola ) )
			{
				$campos .= "{$gruda}disc_ref_ref_cod_escola";
				$valores .= "{$gruda}'{$this->disc_ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}disc_ref_ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->disc_ref_ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_turma ) )
			{
				$campos .= "{$gruda}disc_ref_ref_cod_turma";
				$valores .= "{$gruda}'{$this->disc_ref_ref_cod_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$campos .= "{$gruda}titulo";
				$valores .= "{$gruda}'{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aplicada ) )
			{
				$campos .= "{$gruda}aplicada";
				$valores .= "{$gruda}'{$this->aplicada}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_avaliacao_seq");
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
		if( is_numeric( $this->cod_avaliacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->disc_ref_ref_cod_serie ) )
			{
				$set .= "{$gruda}disc_ref_ref_cod_serie = '{$this->disc_ref_ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_escola ) )
			{
				$set .= "{$gruda}disc_ref_ref_cod_escola = '{$this->disc_ref_ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_disciplina ) )
			{
				$set .= "{$gruda}disc_ref_ref_cod_disciplina = '{$this->disc_ref_ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->disc_ref_ref_cod_turma ) )
			{
				$set .= "{$gruda}disc_ref_ref_cod_turma = '{$this->disc_ref_ref_cod_turma}'";
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
			if( is_string( $this->titulo ) )
			{
				$set .= "{$gruda}titulo = '{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->aplicada ) )
			{
				$set .= "{$gruda}aplicada = '{$this->aplicada}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_avaliacao = '{$this->cod_avaliacao}'" );
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
	function lista( $int_cod_avaliacao = null, $int_disc_ref_ref_cod_serie = null, $int_disc_ref_ref_cod_escola = null, $int_disc_ref_ref_cod_disciplina = null, $int_disc_ref_ref_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_titulo = null, $str_descricao = null, $int_aplicada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_cod_avaliacao ) )
		{
			$filtros .= "{$whereAnd} cod_avaliacao = '{$int_cod_avaliacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_disc_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} disc_ref_ref_cod_serie = '{$int_disc_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_disc_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} disc_ref_ref_cod_escola = '{$int_disc_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_disc_ref_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} disc_ref_ref_cod_disciplina = '{$int_disc_ref_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_disc_ref_ref_cod_turma ) )
		{
			$filtros .= "{$whereAnd} disc_ref_ref_cod_turma = '{$int_disc_ref_ref_cod_turma}'";
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
		if( is_string( $str_titulo ) )
		{
			$filtros .= "{$whereAnd} titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_aplicada ) )
		{
			$filtros .= "{$whereAnd} aplicada = '{$int_aplicada}'";
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
		if( is_numeric( $this->cod_avaliacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_avaliacao = '{$this->cod_avaliacao}'" );
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
		if( is_numeric( $this->cod_avaliacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_avaliacao = '{$this->cod_avaliacao}'" );
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
		if( is_numeric( $this->cod_avaliacao ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_avaliacao = '{$this->cod_avaliacao}'" );
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