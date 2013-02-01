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
* Criado em 30/06/2006 09:04 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAlunoCMF
{


	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;


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
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarAlunoCMF( )
	{


	}


	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $nome_aluno = null, $cpf_aluno = null, $nome_responsavel = null, $cpf_responsavel = null, $cod_sistema = 1)
	{


		$where_aluno       = "";
		$where_responsavel = "";
		$where_sistema = "";

		if(is_numeric($cod_sistema))
		{
			//$where_sistema .= "AND (fisica_aluno.cpf is not null OR fisica_aluno.ref_cod_sistema = {$cod_sistema} )";
			$where_sistema .= "AND (cpf_aluno.cpf is not null OR cpf_aluno.ref_cod_sistema = {$cod_sistema} )";
		}else{
			$where_sistema .= "cpf_aluno is not null";
		}

		if(is_string($nome_aluno))
		{
			$table_join =",cadastro.pessoa       pessoa_aluno";
			$where_join ="AND cpf_aluno.idpes    = pessoa_aluno.idpes";
			$where_aluno .= "AND to_ascii(lower(pessoa_aluno.nome)) like  to_ascii(lower('%{$nome_aluno}%')) ";
		}
		if(is_numeric($cpf_aluno))
		{
			$where_aluno .= "AND cpf_aluno.cpf like '%{$cpf_aluno}%' ";
		}


		if(is_string($nome_responsavel))
		{
			$where_responsavel .= "AND to_ascii(lower(pessoa_resp.nome)) like  to_ascii(lower('%{$nome_responsavel}%')) ";
		}
		if(is_numeric($cpf_responsavel))
		{
			$where_responsavel .= "AND cpf_resp.cpf like '%{$cpf_responsavel}%' ";
		}
		if(!empty($where_responsavel))
		{
			$where_responsavel = " AND EXISTS (SELECT 1
												 FROM cadastro.pessoa       pessoa_resp
												      ,cadastro.fisica  cpf_resp
												      ,cadastro.fisica	   fisica_resp
										        WHERE cpf_resp.idpes    = pessoa_resp.idpes
											 	  AND pessoa_resp.idpes = fisica_resp.idpes
									              AND fisica_aluno.idpes_responsavel = pessoa_resp.idpes
												  {$where_responsavel}
											 	  AND cpf_resp.cpf is not null
				              		         )";
		}

		$campos_select = "SELECT pessoa_aluno.idpes as cod_aluno
				      ,pessoa_aluno.nome as nome_aluno
				      ,lower(trim(to_ascii(pessoa_aluno.nome))) as nome_ascii
				      ,cpf_aluno.cpf as cpf_aluno
				      ,cpf_aluno.idpes_responsavel as idpes_responsavel";


		$sql = "
				 FROM cadastro.pessoa       pessoa_aluno
				      ,cadastro.fisica      cpf_aluno
				WHERE cpf_aluno.idpes    = pessoa_aluno.idpes
				  AND cpf_aluno.cpf is not null
				  {$where_sistema}
				  {$where_aluno}
				  {$where_responsavel}";

		$sql_count = "
				 FROM cadastro.fisica      cpf_aluno
				     $table_join
				WHERE cpf_aluno.cpf is not null
				  $where_join
				  {$where_sistema}
				  {$where_aluno}
				  {$where_responsavel}";

/*		$sql = "SELECT pessoa_aluno.idpes as cod_aluno
				      ,pessoa_aluno.nome as nome_aluno
				      ,lower(trim(to_ascii(pessoa_aluno.nome))) as nome_ascii
				      ,cpf_aluno.cpf as cpf_aluno
				      ,fisica_aluno.idpes_responsavel as idpes_responsavel
				 FROM cadastro.pessoa       pessoa_aluno
				      ,cadastro.fisica      cpf_aluno
				      ,cadastro.fisica	    fisica_aluno
				WHERE cpf_aluno.idpes    = pessoa_aluno.idpes
				  AND pessoa_aluno.idpes = fisica_aluno.idpes
				  AND cpf_aluno.cpf is not null
				  {$where_sistema}
				  {$where_aluno}
				  {$where_responsavel}";*/

		$db = new clsBanco();
		//@session_start();
//	if($_SESSION['id_pessoa'] == 21317)
	//		$this->_total = $total = $db->CampoUnico("SELECT COUNT(1) FROM cadastro.fisica cpf_aluno WHERE cpf_aluno.cpf is not null AND (cpf_aluno is not null OR cpf_aluno.ref_cod_sistema = 1 )");
		//else

		$this->_total = $total = $db->CampoUnico("SELECT COUNT(1) {$sql_count}");


		$db->Consulta( "{$campos_select} {$sql} ORDER BY nome_aluno ".$this->getLimite() );

		$resultado = array();

		if($total >= 1)
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$resultado[] = array("nome_aluno" => $tupla["nome_aluno"], "cpf_aluno" => $tupla["cpf_aluno"],"cod_aluno" => $tupla["cod_aluno"], "idpes_responsavel" => $tupla["idpes_responsavel"]);
			}

			return $resultado;
		}




		return false;
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


}
?>