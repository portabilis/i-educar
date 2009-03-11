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

class clsPessoaFj
{
	// Vari�veis da Pessoa
	var $idpes;
	var $nome;
	var $idpes_cad;
	var $data_cad;
	var $url;
	var $tipo;
	var $idpes_rev;
	var $data_rev;
	var $situacao;
	var $origem_gravacao;
	var $email;
	var $data_nasc;
	// Vari�veis de Endereco
	var $bairro;
	var $idbai;
	var $logradouro;
	var $idlog;
	var $idtlog;
	var $cidade;
	var $idmun;
	var $sigla_uf;
	var $pais;
	var $complemento;
	var $reside_desde;
	var $letra;
	var $numero;
	var $cep;
	var $bloco;
	var $apartamento;
	var $andar;
	// Vari�veis de Telefone
	var $ddd_1;
	var $fone_1;
	var $ddd_2;
	var $fone_2;
	var $ddd_fax;
	var $fone_fax;
	var $ddd_mov;
	var $fone_mov;
	//Variaveis de documentacao
	var $rg;
	var $cpf;

	var $_total;

	var $banco = 'gestao_homolog';
	var $schema_cadastro = "cadastro";
	var $tabela_pessoa = "pessoa";


	function  clsPessoaFj($int_idpes = false )
	{
		$this->idpes = $int_idpes;
	}


	function lista($str_nome = false, $inicio_limite = false, $qtd_registros = false, $str_orderBy = false, $arrayint_idisin=false, $arrayint_idnotin = false, $str_tipo_pessoa = false )
	{
		$objPessoa = new clsPessoa_();
		$listaPessoa = $objPessoa->lista($str_nome,$inicio_limite,$qtd_registros,$str_orderBy,$arrayint_idisin,$arrayint_idnotin,$str_tipo_pessoa);
		if(count($listaPessoa) > 0)
		{
			return $listaPessoa;
		}
		return false;
	}

	/*function lista_rapida($idpes = null, $nome = null, $id_federal = null, $inicio_limite = null, $limite = null, $str_tipo_pessoa = null, $str_order_by = null, $int_ref_cod_sistema = null)
	{
		$db = new clsBanco();

		$filtros = "";
		$filtroTipo = "";
		$whereAnd = " WHERE ";
		$outros_filtros = false;
		$filtro_cnpj = false;

		if( is_string( $nome ) && $nome != "")
		{
			$filtros .= "{$whereAnd} (nome ILIKE '%{$nome}%')";
			$whereAnd = " AND ";
			$outros_filtros =true;
		}
		
		if( is_numeric( $id_federal ) )
		{
			$filtros .= "{$whereAnd} id_federal LIKE '%{$id_federal}'";
			$whereAnd = " AND ";
			$filtro_cnpj =true;
		}
		

		if( is_numeric( $idpes ) )
		{
			$filtros .= "{$whereAnd} idpes = '{$idpes}'";
			$whereAnd = " AND ";
			$outros_filtros =true;
		}

		if( is_numeric( $int_ref_cod_sistema ) )
		{
			$filtro_sistema = true;
			$filtros .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR id_federal is not null)";
			$whereAnd = " AND ";
		}

		if ( is_string( $str_tipo_pessoa ) ) {
			$filtroTipo .= " AND tipo  = '{$str_tipo_pessoa}' ";
			$outros_filtros =true;
		}

		if ( is_string( $str_order_by ) ) {
			$order = "ORDER BY $str_order_by";
		}

		$limit = "";
		if(is_numeric($inicio_limite) && is_numeric($limite))
		{
			$limit = "LIMIT $limite OFFSET $inicio_limite";
		}

		if($filtro_sistema && $outros_filtros == false || $filtro_cnpj  )
		{
			$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.v_pessoafj_count $filtros");
		}else
		{
			$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.v_pessoa_fj $filtros");
		}

		$db->Consulta("SELECT idpes, nome, ref_cod_sistema, fantasia, tipo , id_federal as cpf, id_federal as cnpj, id_federal FROM cadastro.v_pessoa_fj $filtros $order $limit ");
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["_total"] = $this->_total;
			$resultado[] = $tupla;
		}

		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}*/

	function lista_rapida($idpes = null, $nome = null, $id_federal = null, $inicio_limite = null, $limite = null, $str_tipo_pessoa = null, $str_order_by = null, $int_ref_cod_sistema = null)
	{
		$db = new clsBanco();

		$filtros = "";
		$filtroTipo = "";
		$whereAnd = " WHERE ";
		$outros_filtros = false;
		$filtro_cnpj = false;

		if( is_string( $nome ) && $nome != "")
		{
			$filtros .= "{$whereAnd} (nome ILIKE '%{$nome}%')";
			$whereAnd = " AND ";
			$outros_filtros =true;
		}
		
		if( is_numeric( $idpes ) )
		{
			$filtros .= "{$whereAnd} idpes = '{$idpes}'";
			$whereAnd = " AND ";
			$outros_filtros =true;
		}

		if( is_numeric( $int_ref_cod_sistema ) )
		{
			$filtro_sistema = true;
			$filtros .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR id_federal is not null)";
			$whereAnd = " AND ";
		}

		if( is_numeric( $id_federal ) )
		{
			$db2 = new clsBanco();
			$db2->Consulta("SELECT idpes FROM cadastro.fisica WHERE cpf LIKE '%{$id_federal}%'");
			
			$array_idpes = null;
			while ($db2->ProximoRegistro())
			{
				list($id_pes) = $db2->Tupla();
				$array_idpes[] = $id_pes;
			}
			
			$db2->Consulta("SELECT idpes FROM cadastro.juridica WHERE cnpj LIKE '%{$id_federal}%'");
			while ($db2->ProximoRegistro())
			{
				list($id_pes) = $db2->Tupla();
				$array_idpes[] = $id_pes;
			}
			
			if(is_array($array_idpes))
			{
				$array_idpes = implode(", ", $array_idpes);
				$filtros .= "{$whereAnd} idpes IN ($array_idpes)";
				$whereAnd = " AND ";
				$filtro_idfederal = true;
			}
			else 
			{
				return false;
			}
		}
		
		if ( is_string( $str_tipo_pessoa ) ) {
			$filtroTipo .= " AND tipo  = '{$str_tipo_pessoa}' ";
			$outros_filtros =true;
		}

		if ( is_string( $str_order_by ) ) {
			$order = "ORDER BY $str_order_by";
		}

		$limit = "";
		if(is_numeric($inicio_limite) && is_numeric($limite))
		{
			$limit = "LIMIT $limite OFFSET $inicio_limite";
		}

		if($filtro_idfederal)
		{
			$this->_total =	$db->CampoUnico("SELECT COUNT(0) FROM cadastro.v_pessoa_fj $filtros");
		}
		else 
		{
			if($filtro_sistema && $outros_filtros == false || $filtro_cnpj  )
			{
				$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.v_pessoafj_count $filtros");
			}else
			{
				$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.v_pessoa_fj $filtros");
			}
		}

		echo "<!-- SELECT idpes, nome, ref_cod_sistema, fantasia, tipo , id_federal as cpf, id_federal as cnpj, id_federal FROM cadastro.v_pessoa_fj $filtros $order $limit -->";
		
		$db->Consulta("SELECT idpes, nome, ref_cod_sistema, fantasia, tipo , id_federal as cpf, id_federal as cnpj, id_federal FROM cadastro.v_pessoa_fj $filtros $order $limit ");
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["_total"] = $this->_total;
			$resultado[] = $tupla;
		}

		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
	
	function detalhe()
	{
		if($this->idpes)
		{
			$objPessoa = new clsPessoa_($this->idpes);
			$detalhePessoa = $objPessoa->detalhe();

			$objEndereco = new clsEndereco($this->idpes);
			$detalheEndereco = $objEndereco->detalhe();


			if($detalheEndereco)
			{
				$this->bairro = $detalheEndereco['bairro'];
				$this->logradouro = $detalheEndereco['logradouro'];
				$this->sigla_uf = $detalheEndereco['sigla_uf'];
				$this->cidade = $detalheEndereco['cidade'];
				$this->reside_desde = $detalheEndereco['reside_desde'];
				$this->idtlog = $detalheEndereco['idtlog'];
				$this->complemento = $detalheEndereco['complemento'];
				$this->numero = $detalheEndereco['numero'];
				$this->letra = $detalheEndereco['letra'];
				$this->idlog = $detalheEndereco['idlog'];
				$this->idbai = $detalheEndereco['idbai'];
				$this->cep = $detalheEndereco['cep'];
				$this->apartamento = $detalheEndereco['apartamento'];
				$this->bloco = $detalheEndereco['bloco'];
				$this->andar = $detalheEndereco['andar'];

				$detalhePessoa['bairro'] = $this->bairro;
				$detalhePessoa['logradouro'] = $this->logradouro;
				$detalhePessoa['sigla_uf'] = $this->sigla_uf;
				$detalhePessoa['cidade'] = $this->cidade;
				$detalhePessoa['reside_desde'] = $this->reside_desde;
				$detalhePessoa['idtlog'] = $this->idtlog;
				$detalhePessoa['complemento'] = $this->complemento;
				$detalhePessoa['numero'] = $this->numero;
				$detalhePessoa['letra'] = $this->letra;
				$detalhePessoa['idbai'] = $this->idbai;
				$detalhePessoa['cep'] = $this->cep;
				$detalhePessoa['idlog'] = $this->idlog;

			}

			$obj_fisica =  new clsFisica($this->idpes);
			$detalhe_fisica = $obj_fisica->detalhe();
			if( $detalhe_fisica )
			{
				$detalhePessoa['cpf'] = $detalhe_fisica['cpf'];
				$this->cpf = $detalhe_fisica['cpf'];
				$this->data_nasc = $detalhe_fisica['data_nasc'];
				if($this->data_nasc)
				{
					$detalhePessoa['data_nasc'] = $this->data_nasc;
				}
			}
			$objFone = new clsPessoaTelefone();
			$listaFone = $objFone->lista($this->idpes);
			if($listaFone)
			{
				foreach ($listaFone as $fone) {
					if($fone['tipo'] == 1)
					{
						$detalhePessoa['ddd_1'] = $fone['ddd'];
						$detalhePessoa[] = &$detalhePessoa['ddd_1'];
						$detalhePessoa['fone_1'] = $fone['fone'];
						$detalhePessoa[] = &$detalhePessoa['fone_1'];
						$this->ddd_1 = $fone['ddd'];
						$this->fone_1 = $fone['fone'];
					}
					if($fone['tipo'] == 2)
					{
						$detalhePessoa['ddd_2'] = $fone['ddd'];
						$detalhePessoa[] = &$detalhePessoa['ddd_2'];
						$detalhePessoa['fone_2'] = $fone['fone'];
						$detalhePessoa[] = &$detalhePessoa['fone_2'];
						$this->ddd_2 = $fone['ddd'];
						$this->fone_2 = $fone['fone'];
					}
					if($fone['tipo'] == 3)
					{
						$detalhePessoa['ddd_mov'] = $fone['ddd'];
						$detalhePessoa[''] = &$detalhePessoa['ddd_mov'];
						$detalhePessoa['fone_mov'] = $fone['fone'];
						$detalhePessoa[] = &$detalhePessoa['fone_mov'];
						$this->ddd_mov = $fone['ddd'];
						$this->fone_mov = $fone['fone'];
					}
					if($fone['tipo'] == 4)
					{
						$detalhePessoa['ddd_fax'] = $fone['ddd'];
						$detalhePessoa[] = &$detalhePessoa['ddd_fax'];
						$detalhePessoa['fone_fax'] = $fone['fone'];
						$detalhePessoa[] = &$detalhePessoa['fone_fax'];
						$this->ddd_fax = $fone['ddd'];
						$this->fone_fax = $fone['fone'];
					}
				}
			}

			$obj_documento = new clsDocumento($this->idpes);
			$documentos = $obj_documento->detalhe();


			if(is_array($documentos))
			{
				if($documentos['rg'])
				{
					$detalhePessoa['rg'] = $documentos['rg'];
					$detalhePessoa[] = &$detalhePessoa['rg'];
					$this->rg = $documentos['rg'];

				}
			}
			$this->idpes = $detalhePessoa['idpes'];
			$this->nome = $detalhePessoa['nome'];
			$this->idpes_cad = $detalhePessoa['idpes_cad'];
			$this->data_cad = $detalhePessoa['data_cad'];
			$this->url = $detalhePessoa['url'];
			$this->tipo = $detalhePessoa['tipo'];
			$this->idpes_rev = $detalhePessoa['idpes_rev'];
			$this->data_rev = $detalhePessoa['data_rev'];
			$this->situacao = $detalhePessoa['situacao'];
			$this->origem_gravacao = $detalhePessoa['origem_gravacao'];
			$this->email = $detalhePessoa['email'];
			return $detalhePessoa;

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


}
?>
