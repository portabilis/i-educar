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
require_once ("include/otopic/otopicGeral.inc.php");


class clsFuncionarioSu
{
	var $ref_ref_cod_pessoa_fj;
	
	var $schema = "pmiotopic";
	var $tabela = "funcionario_su";

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsFuncionarioSu( $int_ref_ref_cod_pessoa_fj = false )
	{
		if(is_numeric($int_ref_ref_cod_pessoa_fj))
		{
			$this->ref_ref_cod_pessoa_fj = $int_ref_ref_cod_pessoa_fj;
		}
	}
	
	/**
	 * Funï¿½ï¿½o que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificações de campos obrigatorios para inserï¿½ï¿½o
		if( $this->ref_ref_cod_pessoa_fj )
		{
			$db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( ref_ref_cod_pessoa_fj ) VALUES ( '$this->ref_ref_cod_pessoa_fj' )");
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
		$db = new clsBanco();
		$db->Consulta("DELETE FROM $this->schema.$this->tabela ");
	}
	
	
	function detalhe()
	{
		if($this->ref_ref_cod_pessoa_fj)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_ref_cod_pessoa_fj FROM {$this->schema}.{$this->tabela} WHERE ref_ref_cod_pessoa_fj = $this->ref_ref_cod_pessoa_fj" );
			$resultado = array();
			if ( $db->ProximoRegistro() ) 
			{
				$tupla = $db->Tupla();
				return  $tupla;
			}
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_limite_ini = false, $int_limite_qtd = false)
	{
		
		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} " );
		$db->Consulta( "SELECT ref_ref_cod_pessoa_fj FROM {$this->schema}.{$this->tabela} $limit" );
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
	
}
?>
