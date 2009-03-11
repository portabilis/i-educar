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

class clsPessoaFisicaAlterado extends clsPessoaFj
{
	var $idpes;
	var $data_nasc;
	var $sexo;
	var $idpes_mae;
	var $idpes_pai;
	var $idpes_responsavel;
	var $idesco;
	var $ideciv;
	var $idpes_con;
	var $data_uniao;
	var $data_obito;
	var $nacionalidade;
	var $idpais_estrangeiro;
	var $data_chagada_brasil;
	var $idmun_nascimento;
	var $ultima_empresa;
	var $idocup;
	var $nome_mae;
	var $nome_pai;
	var $nome_conjuge;
	var $nome_responsavel;
	var $justificativa_provisorio;
	var $cpf;
	var $ref_cod_religiao;
	var $tipo_endereco;

	var $banco = 'pmi';
	var $schema_cadastro = "cadastro";

	function  clsPessoaFisicaAlterado($int_idpes = false, $numeric_cpf = false, $date_data_nasc = false, $str_sexo = false, $int_idpes_mae =false, $int_idpes_pai = false)
	{
		$this->idpes = $int_idpes;
		$this->cpf = $numeric_cpf;
	}


	function lista_simples($str_nome =false, $numeric_cpf =false, $inicio_limite=false, $qtd_registros = false, $str_orderBy = false, $int_ref_cod_sistema = false)
	{
		$whereAnd = "";
		$where = "";
		if( is_string( $str_nome ) && $str_nome != '' )
		{
			$str_nome = str_replace(" ", "%", $str_nome);
			$where .= "{$whereAnd} nome ILIKE '%{$str_nome}%' ";
			$whereAnd = " AND ";
		}

		if( is_string( $numeric_cpf ) )
		{
			$where .= "{$whereAnd} cpf ILIKE '%{$numeric_cpf}%' ";
		}

		if( is_numeric( $int_ref_cod_sistema ) )
		{
				$where .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR cpf is not null  )";
		}


		if( $inicio_limite !== false && $qtd_registros )
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}


		$orderBy = " ORDER BY ";
		if( $str_orderBy )
		{
			$orderBy .= "$str_orderBy ";
		}
		else
		{
			$orderBy .= "nome ";
		}
		if($where)
		{
			$where = "WHERE ".$where;
		}
		$db = new clsBanco($this->banco);
		
		$total = $db->UnicoCampo("Select count(0) FROM cadastro.fisica $where"); 
		
		$db->Consulta("Select idpes, nome, cpf FROM cadastro.v_pessoa_fisica $where $orderBy $limite");
		$resultado = array();
		while ($db->ProximoRegistro())
		{
			$tupla = $db->Tupla();
			$tupla['nome'] = transforma_minusculo($tupla['nome']);
			$tupla['total']= $total;
			$resultado[] = $tupla;
		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}


	function lista($str_nome =false, $numeric_cpf =false, $inicio_limite=false, $qtd_registros = false, $str_orderBy = false, $int_ref_cod_sistema = false, $int_idpes = false)
		{
			$whereAnd = "";
			$where = "";
			if( is_string( $str_nome ) && $str_nome != '' )
			{
				$str_nome = str_replace(" ", "%", $str_nome);
				$where .= "{$whereAnd} nome ILIKE '%{$str_nome}%' ";
				$whereAnd = " AND ";
			}

			if( is_string( $numeric_cpf ) )
			{
				$where .= "{$whereAnd} cpf ILIKE '%{$numeric_cpf}%' ";
				$whereAnd = " AND ";
			}
			if( is_numeric( $int_ref_cod_sistema ) )
			{
				$where .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR cpf is not null  )";
				$whereAnd = " AND ";
			}
			if( is_numeric( $int_idpes ) )
			{
				$where .= "{$whereAnd} idpes = '$int_idpes'";
				$whereAnd = " AND ";
			}
			
			if(is_numeric($this->tipo_endereco))
			{
				if($this->tipo_endereco == 1)
				{
					//interno
					$where .= "{$whereAnd} idpes IN (SELECT idpes FROM cadastro.endereco_pessoa)";
					$whereAnd = " AND ";
				}
				elseif ($this->tipo_endereco == 2)
				{
					//externo
					$where .= "{$whereAnd} idpes IN (SELECT idpes FROM cadastro.endereco_externo)";
					$whereAnd = " AND ";
				}
			}


			if( $inicio_limite !== false && $qtd_registros )
			{
				$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
			}


			$orderBy = " ORDER BY ";
			if( $str_orderBy )
			{
				$orderBy .= "$str_orderBy ";
			}
			else
			{
				$orderBy .= "nome ";
			}
			

			$db = new clsBanco();
			$dba = new clsBanco();
			
			
			if($where)
			{
				$where = "AND ".$where;
			}
			if(!$where)
			{
				$total = $db->CampoUnico("Select count(0) FROM cadastro.fisica WHERE (alterado != 'TRUE' OR alterado IS NULL) $where");
			}else 
			{
				$total = $db->CampoUnico("Select count(0) FROM cadastro.v_pessoa_fisica WHERE idpes IN (SELECT idpes FROM cadastro.fisica WHERE (alterado != 'TRUE' OR alterado IS NULL)) $where");
			}
			
			
//			$db->Consulta("Select idpes, nome, url,'F' as tipo, email, cpf FROM cadastro.v_pessoa_fisica $where $orderBy $limite");
			//$where = " AND ".$where; 
			$db->Consulta("Select idpes, nome, url,'F' as tipo, email, cpf FROM cadastro.v_pessoa_fisica WHERE idpes IN (SELECT idpes FROM cadastro.fisica WHERE (alterado != 'TRUE' OR alterado IS NULL)) $where $orderBy $limite");
			
			$resultado = array();
			while ($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$tupla['nome'] = transforma_minusculo($tupla['nome']);
				$tupla['total']= $total;

				$tupla['alterado'] = $tupla['alterado'];
				
				$dba->Consulta("Select ddd_1, fone_1, ddd_2, fone_2, ddd_mov, fone_mov, ddd_fax, fone_fax from cadastro.v_fone_pessoa where idpes ={$tupla['idpes']}");
				if($dba->ProximoRegistro())
				{
					$tupla_fone = $dba->Tupla();
				}else 
				{
					$tupla_fone = "";
				}
				
				$tupla['ddd_1'] = $tupla_fone['ddd_1'];
				$tupla['fone_1'] = $tupla_fone['fone_1'];
				$tupla['ddd_2'] = $tupla_fone['ddd_2'];
				$tupla['fone_2'] = $tupla_fone['fone_2'];
				$tupla['ddd_mov'] = $tupla_fone['ddd_mov'];
				$tupla['fone_mov'] = $tupla_fone['fone_mov'];
				$tupla['ddd_fax'] = $tupla_fone['ddd_fax'];
				$tupla['fone_fax'] = $tupla_fone['fone_fax'];
				
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
		if($this->idpes)
		{
			$tupla = parent::detalhe();

			$objFisica = new clsFisica($this->idpes);
			$detalhe_fisica = $objFisica->detalhe();
			if($detalhe_fisica)
			{
				$this->data_nasc  = $detalhe_fisica['data_nasc'];
				$this->sexo = $detalhe_fisica['sexo'];
				$this->idpes_mae = $detalhe_fisica['idpes_mae'];
				$this->idpes_pai = $detalhe_fisica['idpes_pai'];
				$this->idpes_responsavel = $detalhe_fisica['idpes_responsavel'];
				$this->idesco = $detalhe_fisica['idesco'];
				$this->ideciv = $detalhe_fisica['ideciv'];
				$this->idpes_con = $detalhe_fisica['idpes_con'];
				$this->data_uniao = $detalhe_fisica['data_uniao'];
				$this->data_obito = $detalhe_fisica['data_obito'];
				$this->nacionalidade = $detalhe_fisica['nacionalidade'];
				$this->idpais_estrangeiro = $detalhe_fisica['idpais_estrangeiro'];
				$this->data_chagada_brasil = $detalhe_fisica['data_chagada_brasil'];
				$this->idmun_nascimento = $detalhe_fisica['idmun_nascimento'];
				$this->ultima_empresa = $detalhe_fisica['ultima_empresa'];
				$this->idocup = $detalhe_fisica['idocup'];
				$this->nome_mae = $detalhe_fisica['nome_mae'];
				$this->nome_pai = $detalhe_fisica['nome_pai'];
				$this->nome_conjuge = $detalhe_fisica['nome_conjuge'];
				$this->nome_responsavel = $detalhe_fisica['nome_responsavel'];
				$this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
				$this->cpf = $detalhe_fisica['cpf'];
				$this->ref_cod_religiao = $detalhe_fisica['ref_cod_religiao'];
				
				$tupla['idpes' ]= $this->idpes;
				$tupla[] = &$tupla['idpes' ];
				$tupla['cpf' ]= $this->cpf;
				$tupla[] = &$tupla['cpf'];
				$tupla['ref_cod_religiao' ]= $this->ref_cod_religiao;
				$tupla[] = &$tupla['ref_cod_religiao'];
				$tupla['data_nasc'] = $this->data_nasc;
				$tupla[] = &$tupla['data_nasc'];
				$tupla['sexo'] = $this->sexo;
				$tupla[] = &$tupla['sexo'];
				$tupla['idpes_mae'] = $this->idpes_mae;
				$tupla[] = &$tupla['idpes_mae'];
				$tupla['idpes_pai'] = $this->idpes_pai;
				$tupla[] = &$tupla['idpes_pai'];
				$tupla['idpes_responsavel'] = $this->idpes_responsavel;
				$tupla[] = &$tupla['idpes_responsavel'];
				$tupla['idesco'] = $this->idesco;
				$tupla[] = &$tupla['idesco'];
				$tupla['ideciv'] = $this->ideciv;
				$tupla[] = &$tupla['ideciv'];
				$tupla['idpes_con'] = $this->idpes_con;
				$tupla[] = &$tupla['idpes_con'];
				$tupla['data_uniao'] = $this->data_uniao;
				$tupla[] = &$tupla['data_uniao'];
				$tupla['data_obito'] = $this->data_obito;
				$tupla[] = &$tupla['data_obito'];
				$tupla['nacionalidade'] = $this->nacionalidade;
				$tupla[] = &$tupla['nacionalidade'];
				$tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
				$tupla[] = &$tupla['idpais_estrangeiro'];
				$tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
				$tupla[] = &$tupla['data_chagada_brasil'];
				$tupla['idmun_nascimento'] = $this->idmun_nascimento;
				$tupla[] = &$tupla['idmun_nascimento'];
				$tupla['ultima_empresa'] = $this->ultima_empresa;
				$tupla[] = &$tupla['ultima_empresa'];
				$tupla['idocup'] = $this->idocup;
				$tupla[] = &$tupla['idocup'];
				$tupla['nome_mae'] = $this->nome_mae;
				$tupla[] = &$tupla['nome_mae'];
				$tupla['nome_pai'] = $this->nome_pai;
				$tupla[] = &$tupla['nome_pai'];
				$tupla['nome_conjuge'] = $this->nome_conjuge;
				$tupla[] = &$tupla['nome_conjuge'];
				$tupla['nome_responsavel'] = $this->nome_responsavel;
				$tupla[] = &$tupla['nome_responsavel'];
				$tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
				$tupla[] = &$tupla['justificativa_provisorio'];

				return $tupla;
			}
		}
		elseif ( $this->cpf )
		{


				$tupla = parent::detalhe();
				
				$objFisica = new clsFisica();
				$lista = $objFisica->lista(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,$this->cpf);
				$this->idpes = $lista[0]['idpes'];
			
			if( $this->idpes)
			{
				$objFisica = new clsFisica($this->idpes);
				$detalhe_fisica = $objFisica->detalhe();
				if($detalhe_fisica)
				{
					$this->data_nasc  = $detalhe_fisica['data_nasc'];
					$this->sexo = $detalhe_fisica['sexo'];
					$this->idpes_mae = $detalhe_fisica['idpes_mae'];
					$this->idpes_pai = $detalhe_fisica['idpes_pai'];
					$this->idpes_responsavel = $detalhe_fisica['idpes_responsavel'];
					$this->idesco = $detalhe_fisica['idesco'];
					$this->ideciv = $detalhe_fisica['ideciv'];
					$this->idpes_con = $detalhe_fisica['idpes_con'];
					$this->data_uniao = $detalhe_fisica['data_uniao'];
					$this->data_obito = $detalhe_fisica['data_obito'];
					$this->nacionalidade = $detalhe_fisica['nacionalidade'];
					$this->idpais_estrangeiro = $detalhe_fisica['idpais_estrangeiro'];
					$this->data_chagada_brasil = $detalhe_fisica['data_chagada_brasil'];
					$this->idmun_nascimento = $detalhe_fisica['idmun_nascimento'];
					$this->ultima_empresa = $detalhe_fisica['ultima_empresa'];
					$this->idocup = $detalhe_fisica['idocup'];
					$this->nome_mae = $detalhe_fisica['nome_mae'];
					$this->nome_pai = $detalhe_fisica['nome_pai'];
					$this->nome_conjuge = $detalhe_fisica['nome_conjuge'];
					$this->nome_responsavel = $detalhe_fisica['nome_responsavel'];
					$this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
					$this->cpf = $detalhe_fisica['cpf'];
					
					$tupla['idpes' ]= $this->idpes;
					$tupla[] = &$tupla['idpes' ];
					$tupla['cpf' ]= $this->cpf;
					$tupla[] = &$tupla['cpf' ];
					$tupla['data_nasc'] = $this->data_nasc;
					$tupla[] = &$tupla['data_nasc'];
					$tupla['sexo'] = $this->sexo;
					$tupla[] = &$tupla['sexo'];
					$tupla['idpes_mae'] = $this->idpes_mae;
					$tupla[] = &$tupla['idpes_mae'];
					$tupla['idpes_pai'] = $this->idpes_pai;
					$tupla[] = &$tupla['idpes_pai'];
					$tupla['idpes_responsavel'] = $this->idpes_responsavel;
					$tupla[] = &$tupla['idpes_responsavel'];
					$tupla['idesco'] = $this->idesco;
					$tupla[] = &$tupla['idesco'];
					$tupla['ideciv'] = $this->ideciv;
					$tupla[] = &$tupla['ideciv'];
					$tupla['idpes_con'] = $this->idpes_con;
					$tupla[] = &$tupla['idpes_con'];
					$tupla['data_uniao'] = $this->data_uniao;
					$tupla[] = &$tupla['data_uniao'];
					$tupla['data_obito'] = $this->data_obito;
					$tupla[] = &$tupla['data_obito'];
					$tupla['nacionalidade'] = $this->nacionalidade;
					$tupla[] = &$tupla['nacionalidade'];
					$tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
					$tupla[] = &$tupla['idpais_estrangeiro'];
					$tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
					$tupla[] = &$tupla['data_chagada_brasil'];
					$tupla['idmun_nascimento'] = $this->idmun_nascimento;
					$tupla[] = &$tupla['idmun_nascimento'];
					$tupla['ultima_empresa'] = $this->ultima_empresa;
					$tupla[] = &$tupla['ultima_empresa'];
					$tupla['idocup'] = $this->idocup;
					$tupla[] = &$tupla['idocup'];
					$tupla['nome_mae'] = $this->nome_mae;
					$tupla[] = &$tupla['nome_mae'];
					$tupla['nome_pai'] = $this->nome_pai;
					$tupla[] = &$tupla['nome_pai'];
					$tupla['nome_conjuge'] = $this->nome_conjuge;
					$tupla[] = &$tupla['nome_conjuge'];
					$tupla['nome_responsavel'] = $this->nome_responsavel;
					$tupla[] = &$tupla['nome_responsavel'];
					$tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
					$tupla[] = &$tupla['justificativa_provisorio'];

					return $tupla;
				}
			}
		}
		return false;
	}


	function queryRapida($int_idpes)
	{
		$this->idpes = $int_idpes;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] = &$resultado[$pos];
			$pos++;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function queryRapidaCPF( $int_cpf )
	{
		$this->cpf = $int_cpf + 0;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] = &$resultado[$pos];
			$pos++;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function excluir()
	{
		if($this->idpes)
		{
			$db = new clsBanco();

			$obj = new clsFuncionario($this->idpes);
			if(!$obj->detalhe())
			{
				$db->Consulta("Delete FROM cadastro.fone_pessoa WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.fisica WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.documento WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.endereco_pessoa WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.endereco_externo WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.documento WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.documento WHERE idpes = $this->idpes");
				$db->Consulta("Delete FROM cadastro.pessoa WHERE idpes = $this->idpes");
			}

		}
	}
	function setTipoEndereco($endereco)
	{
		if(is_numeric($endereco))
		{
			$this->tipo_endereco = $endereco;
		}
	}

}
?>
