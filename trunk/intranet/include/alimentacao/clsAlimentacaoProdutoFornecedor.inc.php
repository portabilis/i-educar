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

class clsAlimentacaoProdutoFornecedor{
	var $idpf;
	var $ref_produto;
	var $ref_fornecedor;
	var $agri_familiar;
	var $ano;
	var $mes_inicio;
	var $mes_fim;
	var $pesoouvolume_un;
	var $preco_un;

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
	function clsAlimentacaoProdutoFornecedor()
	{
		$db = new clsBanco();
		$this->_schema = "alimentacao.";
		$this->_tabela = "{$this->_schema}produto_fornecedor";
		$this->_campo_order_by = "nm_produto";

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO {$this->_tabela} (ref_produto,ref_fornecedor,agri_familiar,ano,mes_inicio,mes_fim,pesoouvolume_un,preco_un) VALUES( '{$this->ref_produto}','{$this->ref_fornecedor}','{$this->agri_familiar}','{$this->ano}',{$this->mes_inicio},'{$this->mes_fim}','{$this->pesoouvolume_un}','{$this->preco_un}' )" );
		 //return $db->InsertId( "{$this->_tabela}_idcar_seq");
		return true;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE {$this->_tabela} SET ref_produto='{$this->ref_produto}',ref_fornecedor='{$this->ref_fornecedor}',agri_familiar='{$this->agri_familiar}',ano='{$this->ano}',mes_inicio='{$this->mes_inicio}',mes_fim='{$this->mes_fim}',pesoouvolume_un='{$this->pesoouvolume_un}',preco_un='{$this->preco_un}' WHERE idpf='{$this->idpf}'" );
		return true;
	}

/**
	 * Exclui os dados de um registro
	 *
	 * @return bool
	 */
	function exclui()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE idpf='{$this->idpf}'" );
		return true;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $idpf = null, $ref_produto = null, $ref_fornecedor = null)
	{
		$sql = "SELECT pf.idpf, pf.ref_produto,pf.ref_fornecedor,pf.agri_familiar,pf.ano,pf.mes_inicio,pf.mes_fim,pf.pesoouvolume_un,pf.preco_un, p.nm_produto, pu.unidade, j.fantasia FROM {$this->_tabela} pf, alimentacao.produto p, alimentacao.produto_unidade pu, cadastro.juridica j";

		$whereAnd = " WHERE ";
		$filtros .= "{$whereAnd} pf.ref_produto = p.idpro";
		$whereAnd = " AND ";
		$filtros .= "{$whereAnd} p.ref_produto_unidade = pu.idpu";
		$whereAnd = " AND ";
		$filtros .= "{$whereAnd} pf.ref_fornecedor = j.idpes";

		if( is_numeric( $idpf ) )
		{
			$filtros .= "{$whereAnd} pf.idpf = '{$idpf}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $ref_produto ) )
		{
			$filtros .= "{$whereAnd} pf.ref_produto = '{$ref_produto}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $ref_fornecedor ) )
		{
			$filtros .= "{$whereAnd} pf.ref_fornecedor = '{$ref_fornecedor}'";
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
	
	function getMes($pMes)
	{
		if($pMes>=1 && $pMes<=12)
		{
			$meses = $this->getArrayMes();
			return $meses[$pMes];
		}
		return "";
	}
	
	function getArrayMes()
	{
		return array("1"=>"Janeiro","2"=>"Fevereiro","3"=>"Março","4"=>"Abril","5"=>"Maio","6"=>"Junho","7"=>"Julho","8"=>"Agosto","9"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro");
	}
	
	function getAgri($pValue)
	{
		$agri = array("Não","Sim");
		return  $agri[$pValue];
	}

}
?>