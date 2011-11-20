<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja?								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P?blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja?			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  ?  software livre, voc? pode redistribu?-lo e/ou	 *
*	modific?-lo sob os termos da Licen?a P?blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers?o 2 da	 *
*	Licen?a   como  (a  seu  crit?rio)  qualquer  vers?o  mais  nova.	 *
*																		 *
*	Este programa  ? distribu?do na expectativa de ser ?til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl?cita de COMERCIALI-	 *
*	ZA??O  ou  de ADEQUA??O A QUALQUER PROP?SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen?a  P?blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc?  deve  ter  recebido uma c?pia da Licen?a P?blica Geral GNU	 *
*	junto  com  este  programa. Se n?o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Leandro Zimmer - TTC - Sistemas de informa??o - UFSC 2009/02 - Orientador: Jos? Eduardo De Lucca
*
* Criado em 21/00/2009 17:00
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsAlimentacaoNutricionistaEscola{
	var $dt_cadastro;
	var $ref_usuario;
	var $ref_escola;

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
	function clsAlimentacaoNutricionistaEscola()
	{
		$db = new clsBanco();
		$this->_schema = "alimentacao.";
		$this->_tabela = "{$this->_schema}usuario_escola";
		$this->_campo_order_by = "dt_cadastro desc";

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO {$this->_tabela} (ref_usuario,ref_escola,dt_cadastro) VALUES( '{$this->ref_usuario}','{$this->ref_escola}',{$this->dt_cadastro} )" );
		
		return true;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		return false;
	}
	
	/**
	 * Exclui os dados de um registro
	 *
	 * @return bool
	 */
	function exclui()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_usuario='{$this->ref_usuario}' AND ref_escola='{$this->ref_escola}'" );
		return true;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $ref_usuario = null, $ref_escola = null)
	{
		$sql = "SELECT dt_cadastro, ref_escola, ref_usuario FROM {$this->_tabela} ";

		$whereAnd = " WHERE ";

		if( is_numeric( $ref_usuario ) )
		{
			$filtros .= "{$whereAnd} ref_usuario = '{$ref_usuario}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $ref_escola ) )
		{
			$filtros .= "{$whereAnd} ref_escola = '{$ref_escola}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$resultado = array();

		$sql .= $filtros . $this->getOrderby();

		$db->Consulta( $sql );

		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla;
		}
		
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
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