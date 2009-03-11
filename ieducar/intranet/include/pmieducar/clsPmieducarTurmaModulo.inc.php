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
* Criado em 02/08/2006 14:41 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarTurmaModulo
{
	var $ref_cod_turma;
	var $ref_cod_modulo;
	var $sequencial;
	var $data_inicio;
	var $data_fim;

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
	function clsPmieducarTurmaModulo( $ref_cod_turma = null, $ref_cod_modulo = null, $sequencial = null, $data_inicio = null, $data_fim = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}turma_modulo";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_turma, ref_cod_modulo, sequencial, data_inicio, data_fim";

		if( is_numeric( $ref_cod_modulo ) )
		{
			if( class_exists( "clsPmieducarModulo" ) )
			{
				$tmp_obj = new clsPmieducarModulo( $ref_cod_modulo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_modulo = $ref_cod_modulo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_modulo = $ref_cod_modulo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.modulo WHERE cod_modulo = '{$ref_cod_modulo}'" ) )
				{
					$this->ref_cod_modulo = $ref_cod_modulo;
				}
			}
		}
		if( is_numeric( $ref_cod_turma ) )
		{
			if( class_exists( "clsPmieducarTurma" ) )
			{
				$tmp_obj = new clsPmieducarTurma( $ref_cod_turma );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_turma = $ref_cod_turma;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_turma = $ref_cod_turma;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.turma WHERE cod_turma = '{$ref_cod_turma}'" ) )
				{
					$this->ref_cod_turma = $ref_cod_turma;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $data_inicio ) )
		{
			$this->data_inicio = $data_inicio;
		}
		if( is_string( $data_fim ) )
		{
			$this->data_fim = $data_fim;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_cod_modulo ) && is_numeric( $this->sequencial ) && is_string( $this->data_inicio ) && is_string( $this->data_fim ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_turma ) )
			{
				$campos .= "{$gruda}ref_cod_turma";
				$valores .= "{$gruda}'{$this->ref_cod_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_modulo ) )
			{
				$campos .= "{$gruda}ref_cod_modulo";
				$valores .= "{$gruda}'{$this->ref_cod_modulo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inicio ) )
			{
				$campos .= "{$gruda}data_inicio";
				$valores .= "{$gruda}'{$this->data_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_fim ) )
			{
				$campos .= "{$gruda}data_fim";
				$valores .= "{$gruda}'{$this->data_fim}'";
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
		if( is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_cod_modulo ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->data_inicio ) )
			{
				$set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_fim ) )
			{
				$set .= "{$gruda}data_fim = '{$this->data_fim}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_modulo = '{$this->ref_cod_modulo}' AND sequencial = '{$this->sequencial}'" );
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
	function lista( $int_ref_cod_turma = null, $int_ref_cod_modulo = null, $int_sequencial = null, $date_data_inicio_ini = null, $date_data_inicio_fim = null, $date_data_fim_ini = null, $date_data_fim_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_turma ) )
		{
			$filtros .= "{$whereAnd} ref_cod_turma = '{$int_ref_cod_turma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_modulo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_modulo = '{$int_ref_cod_modulo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_ini ) )
		{
			$filtros .= "{$whereAnd} data_inicio >= '{$date_data_inicio_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_fim ) )
		{
			$filtros .= "{$whereAnd} data_inicio <= '{$date_data_inicio_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_fim_ini ) )
		{
			$filtros .= "{$whereAnd} data_fim >= '{$date_data_fim_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_fim_fim ) )
		{
			$filtros .= "{$whereAnd} data_fim <= '{$date_data_fim_fim}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

//		echo  "<!-- $sql -->";
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
		if( is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_cod_modulo ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_modulo = '{$this->ref_cod_modulo}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_cod_modulo ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_modulo = '{$this->ref_cod_modulo}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_cod_modulo ) && is_numeric( $this->sequencial ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_modulo = '{$this->ref_cod_modulo}' AND sequencial = '{$this->sequencial}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a uma turma
	 */
	function  excluirTodos( $ref_cod_turma = null )
	{
		if ( is_numeric( $ref_cod_turma ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_turma = '{$ref_cod_turma}'" );
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

	/**
	 * Retorna um caracter indicando se o modulo encerrou
	 *
	 * @return array
	 */
	function numModulo( $int_ref_sequencial, $int_disc_ref_ref_cod_serie, $int_disc_ref_ref_cod_escola, $arr_disc_ref_ref_cod_disciplina, $int_disc_ref_cod_turma, $int_ref_ref_cod_turma )
	{
		if ( is_numeric( $int_disc_ref_ref_cod_serie ) && is_numeric( $int_disc_ref_ref_cod_escola ) && is_array( $arr_disc_ref_ref_cod_disciplina ) && is_numeric( $int_disc_ref_cod_turma ) && is_numeric( $int_ref_ref_cod_turma ) )
		{
			$db  = new clsBanco();
			$plus = "";
			$sql  = "SELECT( ";

			foreach ( $arr_disc_ref_ref_cod_disciplina as $cod )
			{
				$sql .= "{$plus}( SELECT COUNT( cod_nota_aluno ) / ( ( SELECT CASE WHEN COUNT( 0 ) = 0
																				THEN 1
																				ELSE COUNT( 0 )
																			END
																	FROM pmieducar.disciplina_serie
																   WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT( ref_ref_cod_matricula )
																													  FROM pmieducar.dispensa_disciplina
																													 WHERE ref_cod_serie      = {$int_disc_ref_ref_cod_serie}
																													   AND ref_cod_escola     = {$int_disc_ref_ref_cod_escola}
																													   AND ref_cod_disciplina = {$cod} ) )
									FROM pmieducar.nota_aluno
								   WHERE ref_cod_serie      = {$int_disc_ref_ref_cod_serie}
									 AND ref_cod_escola     = {$int_disc_ref_ref_cod_escola}
									 AND ativo     	        = 1
									 AND ref_cod_disciplina = {$cod} )";
				$plus = " + ";
			}
			$sql .= " ) / ( SELECT COUNT( ref_cod_disciplina )
							  FROM pmieducar.turma_disciplina
							 WHERE ref_cod_turma  = {$int_ref_ref_cod_turma}
							   AND ref_cod_escola = {$int_disc_ref_ref_cod_escola}
							   AND ref_cod_serie  = {$int_disc_ref_ref_cod_serie} )";

			$resultado = $db->CampoUnico( $sql );
			if ( is_string( $resultado ) )
				return $resultado;
			else
				return 'N';
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retorna uma variavel com os dados de um registro
	 *
	 * @return array
	 */
	function fimAno( $int_ref_cod_turma, $int_qtd_modulo, $int_ref_cod_escola, $int_ref_cod_serie )
	{
		if ( is_numeric( $int_ref_cod_turma ) && is_numeric( $int_qtd_modulo ) && is_numeric( $int_ref_cod_escola ) && is_numeric( $int_ref_cod_serie ) )
		{

			$db = new clsBanco();

			$sql = "SELECT CASE WHEN ( SELECT COUNT(0)
										 FROM pmieducar.matricula_turma
										WHERE ref_cod_turma = {$int_ref_cod_turma} ) > ( SELECT COUNT( DISTINCT ref_ref_cod_matricula )
																						   FROM pmieducar.nota_aluno
																						  WHERE ref_cod_escola = {$int_ref_cod_escola}
																							AND ref_cod_serie  = {$int_ref_cod_serie} )
								THEN 'N'
								WHEN ( SELECT MIN( modulo )
										 FROM ( SELECT ( COUNT(0) / ( ( SELECT COUNT(0)
																		  FROM pmieducar.disciplina_serie
																		 WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT(0)
												    																		FROM pmieducar.dispensa_disciplina
																														   WHERE ref_cod_matricula = {$int_ref_cod_matricula}
																															 AND ref_cod_serie 	  = {$int_ref_cod_serie}
																															 AND ref_cod_escola 	  = {$int_ref_cod_escola} ) ) ) AS modulo
												  FROM pmieducar.nota_aluno na
												 WHERE ref_cod_escola = {$int_ref_cod_escola}
												   AND ref_cod_serie  = {$int_ref_cod_serie}
											  GROUP BY ref_ref_cod_matricula ) AS subquery1 ) <> ( SELECT MAX(modulo)
											  														 FROM ( SELECT ( COUNT(0) / ( ( SELECT COUNT(0)
							  																										  FROM pmieducar.disciplina_serie
							  																										 WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT(0)
																									  																					FROM pmieducar.dispensa_disciplina dd
																									  																				   WHERE na.ref_cod_matricula = dd.ref_ref_cod_matricula ) ) ) AS modulo
																											  FROM pmieducar.nota_aluno na
																											 WHERE ref_ref_cod_turma = {$int_ref_cod_turma}
																										  GROUP BY ref_ref_cod_matricula ) AS subquery2 )
								     AND ( SELECT MAX(modulo)
								     		 FROM ( SELECT ( COUNT(0) / ( ( SELECT COUNT(0)
								     		 								  FROM pmieducar.disciplina_serie
								     		 								 WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT(0)
								     		 								 													FROM pmieducar.dispensa_disciplina dd
								     		 								 												   WHERE na.ref_cod_matricula = dd.ref_ref_cod_matricula ) ) ) AS modulo
								     		 		  FROM pmieducar.nota_aluno na
								     		 		 WHERE ref_ref_cod_turma = {$int_ref_cod_turma}
								     		 	  GROUP BY ref_ref_cod_matricula ) AS subquery2 ) <= {$int_qtd_modulo}
								THEN 'N'
								WHEN ( SELECT MIN(modulo)
										 FROM ( SELECT ( COUNT(0) / ( ( SELECT COUNT(0)
										 								  FROM pmieducar.disciplina_serie
										 								 WHERE ref_cod_serie = {$int_ref_cod_serie} ) - ( SELECT COUNT(0)
										 								 													FROM pmieducar.dispensa_disciplina dd
										 								 												   WHERE na.ref_cod_matricula = dd.ref_ref_cod_matricula ) ) ) AS modulo
										 		  FROM pmieducar.nota_aluno na
										 		 WHERE ref_cod_escola = {$int_ref_cod_escola}
												   AND ref_cod_serie  = {$int_ref_cod_serie}
										 	  GROUP BY ref_cod_matricula ) AS subquery1 ) = {$int_qtd_modulo}
								THEN 'S'
								ELSE 'N'
								 END AS modulo";

			return $db->CampoUnico( $sql );
		}
		else
		{
			return false;
		}
	}
}
/*SELECT qtd, dis, qtd / dis AS divisao
  FROM ( SELECT COUNT(0) AS qtd,
                ( SELECT COUNT(0)
		    FROM pmieducar.disciplina_serie ds
		   WHERE ds.ref_cod_serie = 17 ) AS dis,
		( SELECT COUNT(0)
		    FROM pmieducar.dispensa_disciplina dd
		   WHERE dd.ref_ref_cod_turma = 5
		     AND dd.ref_ref_cod_matricula = na.ref_ref_cod_matricula
		     AND dd.disc
		) AS dis
	FROM pmieducar.nota_aluno na
	WHERE
		na.disc_ref_ref_cod_serie = 17 AND
		na.disc_ref_ref_cod_escola = 10 AND
		na.disc_ref_cod_turma = 5 AND
		na.ref_ref_cod_turma = 5
	GROUP BY na.ref_ref_cod_matricula
) AS sub1 */
?>