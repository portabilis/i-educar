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
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");

class clsOrgaoEmissorRg
{
	var $idorg_rg;
	var $sigla;
	var $descricao;
	var $situacao;
	
	var $tabela;
	var $schema = "cadastro";

	/**
	 * Construtor
	 *
	 * @return Object:clsOrgaoEmissorRg
	 */
	function clsOrgaoEmissorRg( $int_idorg_rg = false, $str_sigla = false, $str_descricao = false, $str_situacao = false )
	{
		$this->idorg_rg = $int_idorg_rg;
		$this->sigla= $str_sigla;
		$this->descricao = $str_descricao;
		$this->situacao= $str_situacao;
		
		$this->tabela = "orgao_emissor_rg";
	}
	
	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificacoes de campos obrigatorios para insercao
		if( is_string($this->sigla) && is_string($this->descricao) && is_string($this->situacao) )
		{
			$campos = "";
			$values = "";
			
			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( sigla, descricao, situacao$campos ) VALUES ( '{$this->sigla}', '{$this->descricao}', '{$this->situacao}'$values " );

			return true;
		}
		return false;
	}
	
	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{
		// verifica campos obrigatorios para edicao
		if( is_string($this->sigla) && is_string($this->descricao) && is_string($this->situacao) )
		{
			$set = "SET sigla = '{$this->sigla}', descricao = '{$this->descricao}', idnum = '{$this->situacao}' ";
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idorg_rg = '$this->idorg_rg'" );

			return true;
		}
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui()
	{
		if(is_numeric($this->idorg_rg))
		{
			$objDocumento = new clsDocumento(false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->idorg_rg);
			if(!count($objDocumento->lista(false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->idorg_rg )))
			{
				$db = new clsBanco();
				//$db->Consulta( "DELETE FROM {$this->schema}.{$this->tabela} WHERE idorg_br = '$this->idorg_rg'" );
			
				return true;
			}
		}
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_sigla = false, $str_descricao = false, $str_situacao = false,$int_limite_ini = 0, $int_limite_qtd = 20, $str_orderBy = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_sigla ) )
		{
			$where .= "{$whereAnd}sigla LIKE '%$str_sigla%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$where .= "{$whereAnd}descricao LIKE '%$str_descricao%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_situacao ) )
		{
			$where .= "{$whereAnd}situacao LIKE '%$str_situacao%'";
			$whereAnd = " AND ";
		}
		
		if($str_orderBy)
		{
			$orderBy = "ORDER BY $str_orderBy";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT idorg_rg, sigla, descricao, situacao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();

			$tupla["total"] = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	} 
	
	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->idorg_rg)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idorg_rg, sigla, descricao, situacao FROM {$this->schema}.{$this->tabela} WHERE idorg_rg='{$this->idorg_rg}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idorg_rg = $tupla["idorg_rg"];
				$this->sigla = $tupla["sigla"];
				$this->descricao = $tupla["descricao"];
				$this->situacao = $tupla["situacao"];
				
				return $tupla;
			}
		}
		return false;
	}
}
?>
