<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBanco.inc.php");

class clsPessoaTelefone
{
	var $idpes;
	var $ddd;
	var $fone;
	var $tipo;
	var $idpes_cad;
	var $idpes_rev;
	
	
	var $banco = 'gestao_homolog';
	var $schema_cadastro = "cadastro";
	var $tabela_telefone = "fone_pessoa";
	
	
	function  clsPessoaTelefone($int_idpes = false, $int_tipo = false, $str_fone=false, $str_ddd=false, $idpes_cad = false, $idpes_rev = false)
	{
		$this->idpes = $int_idpes;
		$this->ddd   = $str_ddd;
		$this->fone  = $str_fone;
		$this->tipo  = $int_tipo;
		$this->idpes_cad = $idpes_cad ? $idpes_cad : $_SESSION['id_pessoa'];
		$this->idpes_rev = $idpes_rev ? $idpes_rev : $_SESSION['id_pessoa'];
		
	}
	
	function cadastra()
	{
		// Cadastro do telefone da pessoa na tabela fone_pessoa
		if($this->idpes && $this->tipo && $this->idpes_cad )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->schema_cadastro}.{$this->tabela_telefone} WHERE idpes = '$this->idpes' AND tipo = '$this->tipo'" );
			// Verifica se ja existe um telefone desse tipo cadastrado para essa pessoa
			if( ! $db->Num_Linhas() )
			{
				// nao tem, cadastra 1 novo
				if( $this->ddd && $this->fone )
				{
					$db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela_telefone} (idpes, tipo, ddd, fone,origem_gravacao, idsis_cad, data_cad, operacao, idpes_cad) VALUES ('$this->idpes', '$this->tipo', '$this->ddd', '$this->fone','M', 17, NOW(), 'I', '$this->idpes_cad')");
					return true;
				}
			}
			else 
			{
				// jah tem, edita
				$this->edita();
				return true;
			}
		}
		
		return false;
	}	
	
	
	function edita()
	{
		// Cadastro do telefone da pessoa na tabela fone_pessoa
		if($this->idpes && $this->tipo && $this->idpes_rev)
		{
			$set = false;
			$gruda = "";
			if($this->ddd)
			{
				$set = "ddd = '$this->ddd'";
				$gruda = ", ";
			}
			if($this->fone)
			{
				$set .= "$gruda fone = '$this->fone'";
				$gruda = ", ";
			}	
			if($this->idpes_rev)
			{
				$set .= "$gruda idpes_rev = '$this->idpes_rev'";
				$gruda = ", ";
			}
			if($set && $this->ddd != "" && $this->fone != "" )
			{
				$db = new clsBanco();
				$db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela_telefone} SET $set WHERE idpes = $this->idpes AND tipo = $this->tipo");
				return true;
			}
			else 
			{
				if( $this->ddd == "" && $this->fone == "" )
				{
					$this->exclui();
				}
			}
		}
		return false;
	}
	
	
	function exclui()
	{
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes AND tipo = $this->tipo");
			return true;
		}
		return false;
	}
	
	function excluiTodos()
	{
		// exclui todos os telefones da pessoa, nao importa o tipo
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes");
			return true;
		}
		return false;
	}

	function lista($int_idpes = false, $str_ordenacao = false, $int_inicio_limite = false, $int_qtd_registros = false, $int_ddd = false, $int_fone = false )
	{
		$whereAnd = "WHERE ";
		$where = "";
		if(  is_numeric($int_idpes))
		{
			$where .= "{$whereAnd}idpes = '$int_idpes'";
			$whereAnd = " AND ";
		}
		elseif (is_string($int_idpes))	
		{
			$where .= "{$whereAnd}idpes IN ($int_idpes)";
			$whereAnd = " AND ";
		}
			
		if( is_string( $str_tipo_pessoa ) )
		{
			$where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
			$whereAnd = " AND ";
		}
		
		if(is_numeric($int_ddd))
		{
			$where .= "{$whereAnd}ddd = '$int_ddd' ";
			$whereAnd = " AND ";
		}
		
		if(is_numeric($int_fone))
		{
			$where .= "{$whereAnd}fone = '$int_fone' ";
			$whereAnd = " AND ";
		}
		
		
		if( $int_inicio_limite !== false && $int_qtd_registros)
		{
			$limite = "LIMIT $int_qtd_registros OFFSET $int_inicio_limite ";
		}
		
		
		if( $str_orderBy )
		{
			$orderBy = " ORDERY BY $str_orderBy ";
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM $this->schema_cadastro.$this->tabela_telefone $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		
		$db = new clsBanco($this->banco);
		$db = new clsBanco();
		
		$db->Consulta("SELECT idpes, tipo, ddd, fone FROM $this->schema_cadastro.$this->tabela_telefone $where $orderBy $limite");
		$resultado = array();
		while ($db->ProximoRegistro()) 
		{
			$tupla = $db->Tupla();
			$tupla["total"] = $total;
			$resultado[] = $tupla;
				
		}		
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}
	
	function detalhe()
	{
		if($this->idpes && $this->tipo)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes AND tipo = '$this->tipo' " );
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		
		elseif($this->idpes && !$this->tipo)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes " );
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		return false;
	}
	
}
?>
