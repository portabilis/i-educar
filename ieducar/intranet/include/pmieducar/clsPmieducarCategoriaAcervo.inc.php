<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
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
* Criado em 14/07/2006 09:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCategoriaAcervo
{
	var $id;
	var $descricao;
	var $observacoes;
	
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

	function clsPmieducarCategoriaAcervo($id = null, $descricao = null, $observacoes = null ){
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}categoria_obra";

		$this->_campos_lista = $this->_todos_campos = "id, descricao, observacoes";
		
		if(is_numeric($id)){
			$this->id = $id;
		}
		if(is_string($descricao)){
			$this->descricao = $descricao;
		}
		if(is_string($observacoes)){
			$this->observacoes = $observacoes;
		}
	}

	function deletaCategoriaDaObra($acervoId){
		$db = new clsBanco();
		$db->Consulta("DELETE FROM pmieducar.relacao_categoria_acervo WHERE ref_cod_acervo = {$acervoId}");
		return true;
	}

	function cadastraCategoriaParaObra($acervoId, $categoriaId){
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO pmieducar.relacao_categoria_acervo (ref_cod_acervo, categoria_id) VALUES ({$acervoId},{$categoriaId})");
		return true;
	}

	function listaCategoriasPorObra($acervoId){
		$db = new clsBanco();
		$db->Consulta("SELECT pmieducar.relacao_categoria_acervo.*,
							  categoria_obra.descricao as descricao
						 FROM pmieducar.acervo
				   INNER JOIN relacao_categoria_acervo ON (acervo.cod_acervo = relacao_categoria_acervo.ref_cod_acervo)
				   INNER JOIN categoria_obra on (relacao_categoria_acervo.categoria_id = categoria_obra.id)
					    WHERE acervo.cod_acervo = {$acervoId}");
		
		while($db->ProximoRegistro()) {
			$resultado[] = $db->Tupla();
		}		
		if(count($resultado)){
			return $resultado;
		}
		return false;
	}
}