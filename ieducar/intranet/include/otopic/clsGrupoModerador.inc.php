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


class clsGrupoModerador
{
	var $ref_ref_cod_pessoa_fj;
	var $ref_cod_grupos;
	var $ref_pessoa_exc;
	var $ref_pessoa_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $camposLista;
	var $todosCampos;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsGrupoModerador( $int_ref_ref_cod_pessoa_fj = false, $int_ref_cod_grupos = false, $int_ref_pessoa_cad = false, $int_ref_pessoa_exc = false, $int_ativo = false )
	{
		if(is_numeric($int_ref_ref_cod_pessoa_fj))
		{
			$objFuncionario = new clsFuncionario($int_ref_ref_cod_pessoa_fj);
			
			if($objFuncionario->detalhe())
			{
				$this->ref_ref_cod_pessoa_fj = $int_ref_ref_cod_pessoa_fj;
			}
		}
		
		if(is_numeric($int_ref_cod_grupos))
		{
			$objGrupos = new clsGrupos($int_ref_cod_grupos);
			if($objGrupos->detalhe())
			{
			   $this->ref_cod_grupos = $int_ref_cod_grupos;
			}
		}
		
		if(is_numeric($int_ref_pessoa_cad))
		{
			$objFuncionario = new clsFuncionario($int_ref_pessoa_cad);
			if($objFuncionario->detalhe())
			{
			   $this->ref_pessoa_cad = $int_ref_pessoa_cad;
			}
		}
		
		if(is_numeric($int_ref_pessoa_exc))
		{
			$objFuncionario = new clsFuncionario($int_ref_pessoa_exc);
			if($objFuncionario->detalhe())
			{
			   $this->ref_pessoa_exc = $int_ref_pessoa_exc;
			}
		}
		
		if(is_numeric($int_ativo))
		{
			$this->ativo = $int_ativo;
		}
		
		$this->camposLista = $this->todosCampos = "ref_ref_cod_pessoa_fj, ref_cod_grupos, ref_pessoa_exc, ref_pessoa_cad, data_cadastro, data_exclusao, ativo";
		
		$this->tabela = "pmiotopic.grupomoderador";
		
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificações de campos obrigatorios para inserï¿½ï¿½o
		if( $this->ref_ref_cod_pessoa_fj && $this->ref_cod_grupos && $this->ref_pessoa_cad)
		{
			$campos = "";
			$valores= "";
			
			
			$db->Consulta("INSERT INTO {$this->tabela} ( ref_ref_cod_pessoa_fj, ref_cod_grupos, data_cadastro, ref_pessoa_cad $campos ) VALUES ( '$this->ref_ref_cod_pessoa_fj', '{$this->ref_cod_grupos}', NOW(), '{$this->ref_pessoa_cad}' $valores )");
			//return $db->InsertId("");
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
		if( $this->ref_ref_cod_pessoa_fj && $this->ref_cod_grupos && $this->ref_pessoa_cad && $this->ativo)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET  ref_pessoa_cad = '{$this->ref_pessoa_cad}', data_cadastro= NOW(), ativo = '{$this->ativo}' WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_grupos = '{$this->ref_cod_grupos}'");
			return true;
		} 
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui( )
	{
		// verifica se existe um ID definido para delecao
		if( $this->ref_ref_cod_pessoa_fj  && $this->ref_cod_grupos  && $this->ref_pessoa_exc)
		{
			$this->detalhe();
			$this->ativo++;
			
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->tabela} SET ref_pessoa_exc = '$this->ref_pessoa_exc', data_exclusao = NOW(), ativo = '$this->ativo' WHERE ref_ref_cod_pessoa_fj = '$this->ref_ref_cod_pessoa_fj' AND ref_cod_grupos = '$this->ref_cod_grupos'");
					
			return true;
		}
		return false;
	}
	
	/**
	 * Remove todos os registros
	 *
	 * @return bool
	 */
	function excluiTodos()
	{
		// verifica se existe um ID definido para delecao
		if( $this->ref_cod_grupos && $this->ref_pessoa_exc  )
		{
				$db = new clsBanco();
				$this->detalhe();
				$this->ativo++;
				$db->Consulta("UPDATE $this->tabela SET ativo='2', data_exclusao=NOW(), ref_pessoa_exc = '$this->ref_pessoa_exc' WHERE ref_cod_grupos = '{$this->ref_cod_grupos}' ");
				return true;
		}
		return false;
	}
	
	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setCamposLista($str_campos)
	{
		$this->camposLista = $str_campos;
	}
	
	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetCamposLista()
	{
		$this->camposLista = $this->todosCampos;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_idpes = false, $int_ref_cod_grupos = false, $str_data_cadastro_ini = false, $str_data_cadastro_fim = false, $str_data_exclusao_ini = false, $str_data_exclusao_fim = false, $int_ativo = 1, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if( is_numeric( $int_ref_idpes) )
		{
			$where .= " $and ref_ref_cod_pessoa_fj = '$int_ref_idpes'";
			$and = " AND ";
		}		
		
		if( is_numeric( $int_ref_cod_grupos) )
		{
			$where .= " $and ref_cod_grupos = '$int_ref_cod_grupos'";
			$and = " AND ";
		}		
				
		if( is_string( $str_data_cadastro_ini) )
		{
			$where .= " $and data_cadastro >= '$str_data_cadastro_ini' ";
			$and = " AND ";
		}	
		
		if( is_string( $str_data_cadastro_fim) )
		{
			$where .= " $and data_cadastro <= '$str_data_cadastro_fim' ";
			$and = " AND ";
		}
		
		if( is_string( $str_data_exclusao_ini) )
		{
			$where .= " $and data_exclusao >= '$str_data_exclusao_ini'";
			$and = " AND ";
		}
		if( is_string( $str_data_exclusao_fim) )
		{
			$where .= " $and data_exclusao >= '$str_data_exclusao_fim'";
			$and = " AND ";
		}
		
		if( is_numeric( $int_ativo) )
		{
			$where .= " $and ativo = '$int_ativo'";
			$and = " AND ";
		}
		
		$orderBy = "";
		if( is_string( $str_order_by))
		{
			$orderBy = "ORDER BY $str_order_by";
		}
		
		if($where)
		{
			$where = " WHERE $where";
		}
		
		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		
		$db->Consulta( "SELECT ".$this->camposLista." FROM {$this->tabela} $where $orderBy $limit" );
		
		$resultado = array();
		$countCampos = count( explode( ",", $this->camposLista ) );
		
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			
			if($countCampos > 1 )
			{
				$tupla["total"] = $total;
				$resultado[] = $tupla;
			}
			else 
			{
				$resultado[] = $tupla["$this->camposLista"];
			}
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
		if( $this->ref_ref_cod_pessoa_fj && $this->ref_cod_grupos )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_ref_cod_pessoa_fj, ref_cod_grupos, ref_pessoa_exc, ref_pessoa_cad, data_cadastro, data_exclusao, ativo  FROM {$this->tabela} WHERE  ref_ref_cod_pessoa_fj = '$this->ref_ref_cod_pessoa_fj' AND ref_cod_grupos = '$this->ref_cod_grupos'  " );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				
				$this->ativo = $tupla['ativo'];
				
				return $tupla;
			}
		}
		return false;
	}
}
?>
