<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1);
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
* Criado em 10/08/2006 17:11 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsModulesComponenteCurricular
{
	var $id;
	var $instituicao_id;
	var $area_conhecimento_id;
	var $nome;
	var $abreviatura;
	var $tipo_base;
	var $codigo_educacenso;
	var $ordenamento;


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


  function __construct(){
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}componente_curricular";

    $this->_campos_lista = $this->_todos_campos = "cc.id, cc.instituicao_id, cc.area_conhecimento_id, cc.nome,
      cc.abreviatura, cc.tipo_base, cc.codigo_educacenso, cc.ordenamento";
  }

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $instituicao_id = null, $nome = null, $abreviatura = null, $tipo_base = null, $area_conhecimento_id = null)
	{
		$sql = "SELECT {$this->_campos_lista}, ac.nome as area_conhecimento
              FROM {$this->_tabela} cc
              INNER JOIN modules.area_conhecimento ac ON cc.area_conhecimento_id = ac.id ";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $instituicao_id ) )
		{
			$filtros .= "{$whereAnd} cc.instituicao_id = '{$instituicao_id}'";
			$whereAnd = " AND ";
		}
    if( is_string( $nome ) )
    {
      $filtros .= "{$whereAnd} cc.nome LIKE '%{$nome}%'";
      $whereAnd = " AND ";
    }
    if( is_string( $abreviatura ) )
    {
      $filtros .= "{$whereAnd} cc.abreviatura LIKE '%{$abreviatura}%'";
      $whereAnd = " AND ";
    }
		if( is_string( $nome ) )
		{
			$filtros .= "{$whereAnd} cc.nome LIKE '%{$nome}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $tipo_base ) )
		{
			$filtros .= "{$whereAnd} cc.tipo_base >= '{$tipo_base}'";
			$whereAnd = " AND ";
		}
    if( is_numeric( $area_conhecimento_id ) )
    {
      $filtros .= "{$whereAnd} cc.area_conhecimento_id = '{$area_conhecimento_id}'";
      $whereAnd = " AND ";
    }

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} cc {$filtros}" );

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

	function listaComponentesPorCurso( $instituicao_id = null, $curso = null)
	{
		$sql = "SELECT DISTINCT(mca.componente_curricular_id) AS id, cc.nome AS nome
                  FROM modules.componente_curricular_ano_escolar mca
                 INNER JOIN pmieducar.serie s ON (s.cod_serie = mca.ano_escolar_id)
                 INNER JOIN modules.componente_curricular cc ON (cc.id = mca.componente_curricular_id)";

		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $instituicao_id ) )
		{
			$filtros .= "{$whereAnd} cc.instituicao_id = '{$instituicao_id}'";
			$whereAnd = " AND ";
		}

	    if( is_numeric( $curso ) )
	    {
	      $filtros .= "{$whereAnd} s.ref_cod_curso = '{$curso}'";
	      $whereAnd = " AND ";
	    }

		$db = new clsBanco();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$db->Consulta( $sql );

		while($db->ProximoRegistro()) {
          $resultado[] = $db->Tupla();
        }
		if( count( $resultado ) )
		{
			return $resultado;
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