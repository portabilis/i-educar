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

class clsPmieducarEscolaSerieDisciplina
{
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_disciplina;
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
	function clsPmieducarEscolaSerieDisciplina( $ref_ref_cod_serie = null, $ref_ref_cod_escola = null, $ref_cod_disciplina = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}escola_serie_disciplina";

		$this->_campos_lista = $this->_todos_campos = "ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina";

		if( is_numeric( $ref_cod_disciplina ) )
		{
			if( class_exists( "clsPmieducarDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarDisciplina( $ref_cod_disciplina );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.disciplina WHERE cod_disciplina = '{$ref_cod_disciplina}'" ) )
				{
					$this->ref_cod_disciplina = $ref_cod_disciplina;
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
		}else
		{
			$this->ref_ref_cod_serie = $ref_ref_cod_serie;
		}

		if(is_numeric($ativo))
			$this->ativo = $ativo;



	}


	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

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
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


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
		if( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'" );
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
	function lista( $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_disciplina = null, $int_ativo = null, $boo_nome_disc = false )
	{

		$whereAnd = " WHERE ";

		if($boo_nome_disc)
		{
			$join = ",pmieducar.disciplina"	;
			$whereAnd = " WHERE ref_cod_disciplina = cod_disciplina AND disciplina.ativo = 1 AND ";
			$campos = ",disciplina.nm_disciplina";
		}

		$sql = "SELECT {$this->_campos_lista}{$campos} FROM {$this->_tabela}{$join}";
		$filtros = "";

		if( is_numeric( $int_ref_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ativo ) )
		{
			$filtros .= "{$whereAnd} escola_serie_disciplina.ativo = '{$int_ativo}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

//		echo $sql;

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela}{$join} {$filtros}" );

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
		if( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'" );
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
		if( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'" );
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
		if( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{
			//	delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'" );
			return true;
		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a um tipo de avaliacao
	 */
	function  excluirTodos()
	{
		if ( is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) )
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} SET ativo = '0' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'" );
			return true;
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

	function diferente( $disciplinas )
	{
		if( is_array( $disciplinas ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) )
		{
			$disciplina_in= '';
			$conc = '';
			foreach ( $disciplinas AS $disciplina )
			{
				for ($i = 0; $i < sizeof($disciplina) ; $i++)
				{
					$disciplina_in .= "{$conc}{$disciplina[$i]}";
					$conc = ",";
				}
			}

			$db = new clsBanco();
//			echo "SELECT ref_cod_disciplina FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina not in ({$disciplina_in})";
			$db->Consulta("SELECT ref_cod_disciplina FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina not in ({$disciplina_in})");

			$resultado = array();

			while ( $db->ProximoRegistro() )
			{
				$resultado[] = $db->Tupla();
			}
			return $resultado;
		}
		return false;
	}

	function eh_usado( $disciplina )
	{
		if( is_numeric( $disciplina ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_ref_cod_escola ) )
		{
			$db = new clsBanco();
			$resultado = $db->CampoUnico("SELECT 1
						 	FROM pmieducar.turma_disciplina td
							WHERE td.ref_cod_disciplina = {$disciplina}
								AND td.ref_cod_escola = {$this->ref_ref_cod_escola}
								AND td.ref_cod_serie = {$this->ref_ref_cod_serie}

							UNION

							SELECT 1
						 	FROM pmieducar.disciplina_disciplina_topico ddt
							WHERE ddt.ref_ref_cod_disciplina = {$disciplina}
								AND ddt.ref_ref_ref_cod_escola = {$this->ref_ref_cod_escola}
								AND ddt.ref_ref_ref_cod_serie = {$this->ref_ref_cod_serie}");

//			echo $sql = "SELECT 1
//						 	FROM pmieducar.turma_disciplina td
//							WHERE td.ref_cod_disciplina = {$disciplina}
//								AND td.ref_cod_escola = {$this->ref_ref_cod_escola}
//								AND td.ref_cod_serie = {$this->ref_ref_cod_serie}
//
//							UNION
//
//							SELECT 1
//						 	FROM pmieducar.disciplina_disciplina_topico ddt
//							WHERE ddt.ref_ref_cod_disciplina = {$disciplina}
//								AND ddt.ref_ref_ref_cod_escola = {$this->ref_ref_cod_escola}
//								AND ddt.ref_ref_ref_cod_serie = {$this->ref_ref_cod_serie}";

			return $resultado;
		}
		return false;
	}

	function setAtivoDisciplinaSerie($ativo)
	{
		if(is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_ref_cod_serie) && is_numeric($ativo))
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->_tabela} set ativo = '$ativo' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_disciplina ='{$this->ref_cod_disciplina}'");
			return true;
		}
		return false;
	}

	function desativarDisciplinasSerie()
	{
		if(is_numeric($this->ref_ref_cod_serie))
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->_tabela} set ativo = '0' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'");
			return true;
		}
		return false;
	}
}
?>