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


class clsSetor
{
	var $codSetor;
	var $refCodSetor;
	var $refCodPessoaExc;
	var $refCodPessoaCad;
	var $nmSetor;
	var $sglSetor;
	var $dataCadastro;
	var $dataExclusao;
	var $ativo;
	var $noPaco;
	var $endereco;
	var $tipo;
	var $refIdpesResp;
	var $nivel;

	 //$codSetor, $refCodSetor, $refCodPessoaExc, $refCodPessoaCad, $nmSetor, $sglSetor, $dataCadastro, $dataExclusao, $ativo, $nivel

	var $camposLista;
	var $todosCampos;

	var $tabela;

	var $_total;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsSetor( $intCodSetor = null, $intRefCodSetor = null, $intRefCodPessoaExc = null, $intRefCodPessoaCad = null, $strNmSetor = null, $strSglSetor = null, $strDataCadastro = null, $strDataExclusao = null, $intAtivo = null, $intNivel = null, $boolNoPaco = null, $strEndereco = null, $charTipo = null, $intRefIdpesResp = null )
	{
		if(is_numeric($intCodSetor))
		{
			$this->codSetor = $intCodSetor;
		}

		if(is_numeric($intRefCodSetor))
		{
			$objSetor = new clsSetor($intRefCodSetor);

			if($objSetor->detalhe())
			{
				$this->refCodSetor = $intRefCodSetor;
			}
		}

		if(is_numeric($intRefCodPessoaExc))
		{
			$objPessoa = new clsFuncionario($intRefCodPessoaExc);

			if($objPessoa->detalhe())
			{
				$this->refCodPessoaExc = $intRefCodPessoaExc;
			}
		}

		if(is_numeric($intRefCodPessoaCad))
		{
			$objPessoa = new clsFuncionario($intRefCodPessoaCad);

			if($objPessoa->detalhe())
			{
				$this->refCodPessoaCad= $intRefCodPessoaCad;
			}
		}

		if(is_string($strNmSetor))
		{
			$this->nmSetor = $strNmSetor;
		}

		if(is_string($strSglSetor))
		{
			$this->sglSetor = $strSglSetor;
		}

		if(is_string($strDataCadastro))
		{
			$this->dataCadastro = $strDataCadastro;
		}

		if(is_string($strDataExclusao))
		{
			$this->dataExclusao = $strDataExclusao;
		}

		if(is_numeric($intAtivo))
		{
			$this->ativo = $intAtivo;
		}

		if(is_numeric($intNivel))
		{
			$this->nivel = $intNivel;
		}

		if( ! is_null( $boolNoPaco ) )
		{
			if( $boolNoPaco )
			{
				$this->noPaco = 1;
			}
			else
			{
				$this->noPaco = 0;
			}
		}

		if( is_string( $strEndereco ) )
		{
			$this->endereco = $strEndereco;
		}

		if( is_string( $charTipo ) && strlen( $charTipo ) == 1 )
		{
			$this->tipo = $charTipo;
		}

		if( is_numeric( $intRefIdpesResp ) )
		{
			$objPessoaFisica = new clsFuncionario( $intRefIdpesResp );
			if( $objPessoaFisica->detalhe() )
			{
				$this->refIdpesResp = $intRefIdpesResp;
			}
		}

		$this->camposLista = $this->todosCampos = "cod_setor, ref_cod_setor, ref_cod_pessoa_exc, ref_cod_pessoa_cad, nm_setor, sgl_setor, data_cadastro, data_exclusao, ativo, nivel, ref_idpes_resp";

		$this->tabela = "pmidrh.setor";

	}

	/**
	 * Função retorna os niveis de um setor
	 *
	 * @return array
	 */
	function getNiveis( $intRefCodSetor )
	{
		$niveis = array();
		$db = new clsBanco();
		if( is_numeric( $intRefCodSetor ) )
		{
			do
			{
				$db->Consulta("SELECT cod_setor, ref_cod_setor, nivel FROM {$this->tabela} WHERE cod_setor='{$intRefCodSetor}'");
				if( $db->ProximoRegistro() )
				{
					list( $codSetor, $codPai, $nivel ) = $db->Tupla();
					$niveis[$nivel] = $codSetor;
				}
				$intRefCodSetor = $codPai;
			}while( ! is_null( $codPai ) );
		}

		return $niveis;
	}

	/**
	 * Função retorna o nome completo (com os niveis acima) de um setor
	 *
	 * @return string
	 */
	function getNomeFamilia( $intRefCodSetor, $separador = " &gt; ", $sigla = false )
	{
		$campo = ( $sigla ) ? "sgl_setor":"nm_setor";
		$nome = array();
		$niveis = $this->getNiveis( $intRefCodSetor );
		for ( $i = 0; $i < count( $niveis ); $i++ )
		{
			$objSetor = new clsSetor($niveis[$i]);
			$det = $objSetor->detalhe();
			$nome[] = $det[$campo];
		}
		return implode( $separador, $nome );
	}

	/**
	 * Função que retorna os setores abaixo
	 *
	 * @param int $ref_cod_setor
	 * @param int $status_ativo [opcional]
	 *
	 * @return array
	 */
	function getNiveisParaBaixo($ref_cod_setor, $status_ativo = null)
	{
		if(is_numeric($status_ativo))
		{
			$ativo = "AND ativo = '$status_ativo'";
		}
		$db = new clsBanco();
		$db->Consulta("SELECT cod_setor
					   FROM pmidrh.setor
					   WHERE ref_cod_setor = '{$ref_cod_setor}'
					   $ativo
					   UNION
					   SELECT cod_setor
					   FROM pmidrh.setor
					   WHERE ref_cod_setor IN ( SELECT cod_setor
					   							FROM pmidrh.setor
					   							WHERE ref_cod_setor = '{$ref_cod_setor}'
					   							$ativo
					   						  )
					   $ativo
					   UNION
					   SELECT cod_setor
					   FROM pmidrh.setor
					   WHERE ref_cod_setor IN ( SELECT cod_setor
					   							FROM pmidrh.setor WHERE ref_cod_setor IN ( SELECT cod_setor
					   																	   FROM pmidrh.setor
					   																	   WHERE ref_cod_setor = '{$ref_cod_setor}'
					   																	   $ativo
					   																	 )
	   																	 $ativo
					   						   )
					   $ativo
					   UNION
					   SELECT cod_setor
 					   FROM pmidrh.setor
 					   WHERE ref_cod_setor IN ( SELECT cod_setor
 					   							FROM pmidrh.setor
 					   							WHERE ref_cod_setor IN ( SELECT cod_setor
 					   													 FROM pmidrh.setor
 					   													 WHERE ref_cod_setor IN ( SELECT cod_setor
 					   													 						  FROM pmidrh.setor
 					   													 						  WHERE ref_cod_setor = '{$ref_cod_setor}'
 					   													 						  $ativo
 					   													 						)
												 						$ativo
 					   													)
											   $ativo
 					   						  )
   					  $ativo
 					   						  ");

		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla[0];
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
    }

	/**
	 * Função que indica o número de níveis de um determinado setor
	 *
	 * @return bool
	 */
	function contaNiveis($intRefCodSetor)
	{
		$niveis = 0;
		$db = new clsBanco();
		$db->Consulta("SELECT ref_cod_setor FROM {$this->tabela} WHERE cod_setor='{$intRefCodSetor}'");

		if( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();

			if(!is_null($tupla["ref_cod_setor"]))
			{
				$niveis++;
				while(!is_null($tupla["ref_cod_setor"]) || $niveis<5)
				{
					$db->Consulta("SELECT ref_cod_setor FROM {$this->tabela} WHERE cod_setor='{$tupla["ref_cod_setor"]}'");
					if( $db->ProximoRegistro() )
					{
						$tupla = $db->Tupla();
						$niveis++;
					}
					else
					{
						return false;
					}
				}
			}

			return $niveis;
		}

		return false;
	}

	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificações de campos obrigatorios para inserção
		//echo "{$this->refCodPessoaCad} && {$this->nmSetor} && {$this->sglSetor}";
		if( $this->refCodPessoaCad && $this->nmSetor && $this->sglSetor )
		{
			$campos = "";
			$valores= "";
			$nivel = 0;

			if($this->refCodSetor)
			{
				$campos .= ", ref_cod_setor";
				$valores .= ", {$this->refCodSetor}";

				$nivel = $this->getNiveis($this->refCodSetor);

				$nivel = $nivel ? count($nivel) : 1;
			}
			if( is_numeric( $this->noPaco ) )
			{
				$campos .= ", no_paco";
				$valores .= ", '{$this->noPaco}'";
			}
			if( is_string( $this->endereco ) )
			{
				$campos .= ", endereco";
				$valores .= ", '{$this->endereco}'";
			}
			if( is_string( $this->tipo ) )
			{
				$campos .= ", tipo";
				$valores .= ", '{$this->tipo}'";
			}
			if( is_numeric( $this->refIdpesResp ) )
			{
				$campos .= ", ref_idpes_resp";
				$valores .= ", '{$this->refCodPessoaCad}'";
			}

			if($nivel < 5)
			{
				$db->Consulta("INSERT INTO {$this->tabela} ( ref_cod_pessoa_cad, nm_setor, sgl_setor, data_cadastro, nivel$campos ) VALUES ( '{$this->refCodPessoaCad}', '{$this->nmSetor}', '{$this->sglSetor}', NOW(), '{$nivel}'$valores )");
				return $db->InsertId("pmidrh.setor_cod_setor_seq");
			}
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
		if( $this->codSetor && $this->refCodPessoaCad && $this->nmSetor && $this->sglSetor && $this->ativo)
		{
			$setVir = "";
			$nivel = 0;

			if($this->refCodSetor)
			{
				$setVir.= ", ref_cod_setor={$this->refCodSetor}";

				$nivel = $this->getNiveis($this->refCodSetor);

				$nivel = $nivel ? count($nivel) : 1;
			}
			if( is_numeric( $this->noPaco ) )
			{
				$setVir= ", no_paco = '{$this->noPaco}'";
			}
			if( is_string( $this->endereco ) )
			{
				$setVir= ", endereco = '{$this->endereco}'";
			}
			if( is_string( $this->tipo ) )
			{
				$setVir= ", tipo = '{$this->tipo}'";
			}
			if( is_numeric( $this->refIdpesResp ) )
			{
				$setVir= ", ref_idpes_resp = '{$this->refIdpesResp}'";
			}

			if($nivel < 5)
			{
				$db = new clsBanco();
				//echo "UPDATE {$this->tabela} SET ref_cod_pessoa_cad='{$this->refCodPessoaCad}', nm_setor='{$this->nmSetor}', sgl_setor='{$this->sglSetor}', data_cadastro=NOW(),ativo='{$this->ativo}', nivel='{$nivel}'$setVir WHERE cod_setor='{$this->codSetor}'"; die();
				$db->Consulta( "UPDATE {$this->tabela} SET ref_cod_pessoa_exc='{$this->refCodPessoaCad}', nm_setor='{$this->nmSetor}', sgl_setor='{$this->sglSetor}', data_cadastro=NOW(),ativo='{$this->ativo}', nivel='{$nivel}'$setVir WHERE cod_setor='{$this->codSetor}'");
				return true;
			}
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
		// verifica se existe um ID definido para delecao
		if( $this->codSetor && $this->refCodPessoaExc)
		{
			$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setCamposLista($strCampos)
	{
		$this->camposLista = $strCampos;
	}


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
	function lista( $intRefCodSetor = null, $intRefCodPessoaExc = null, $intRefCodPessoaCad = null, $strNmSetor = null, $strSglSetor = null, $strDataCadastroIni = null, $strDataCadastroFim = null, $strDataExclusaoIni = null, $strDataExclusaoFim = null, $intAtivo = 1, $intNivel = null, $intLimiteIni = null, $intLimiteQtd = null, $strOrderBy = null, $intCodSetor = null, $boolNoPaco = null, $strEndereco = null, $charTipo = null, $intRefIdpesResp = null ,$strNotIn = null,$intCodSetor_ = null)
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";

		if( is_numeric( $intCodSetor_) )
		{
			$where .= " $and cod_setor = '{$intCodSetor_}'";
			$and = " AND ";
		}

		if( is_numeric( $intRefCodSetor) )
		{
			$where .= " $and ref_cod_setor = '{$intRefCodSetor}'";
			$and = " AND ";
		}

		if( is_numeric( $intRefCodPessoaExc) )
		{
			$where .= " $and ref_cod_pessoa_exc = '$intRefCodPessoaExc'";
			$and = " AND ";
		}

		if( is_numeric( $intRefCodPessoaCad) )
		{
			$where .= " $and ref_cod_pessoa_cad = '$intRefCodPessoaCad'";
			$and = " AND ";
		}

		if( is_string( $strNmSetor) )
		{
			$where .= " $and nm_setor ILIKE '%{$strNmSetor}%' ";
			$and = " AND ";
		}

		if( is_string( $strSglSetor) )
		{
			$where .= " $and sgl_setor ILIKE '%{$strSglSetor}%' ";
			$and = " AND ";
		}

		if( is_string( $strDataCadastroIni) )
		{
			$where .= " $and data_cadastro >= '$strDataCadastroIni' ";
			$and = " AND ";
		}

		if( is_string( $strDataCadastroFim) )
		{
			$where .= " $and data_cadastro <= '$strDataCadastroFim'";
			$and = " AND ";
		}

		if( is_string( $strDataExclusaoIni) )
		{
			$where .= " $and data_exclusao >= '$strDataExclusaoIni' ";
			$and = " AND ";
		}

		if( is_string( $strDataExclusaoFim) )
		{
			$where .= " $and data_cadastro <= '$strDataExclusaoFim'";
			$and = " AND ";
		}

		if( is_numeric( $intAtivo) )
		{
			$where .= " $and ativo = '$intAtivo'";
			$and = " AND ";
		}
		else
		{
			$where .= " $and ativo = '1'";
			$and = " AND ";
		}


		if( is_numeric( $intNivel) )
		{
			$where .= " $and nivel = '$intNivel'";
			$and = " AND ";
		}


		if( ! is_null( $boolNoPaco ) )
		{
			if( $boolNoPaco )
			{
				$where .= " $and no_paco = '1'";
			}
			else
			{
				$where .= " $and no_paco = '0'";
			}
			$and = " AND ";
		}
		if( is_string( $charTipo ) )
		{
			$where .= " $and tipo = '$charTipo'";
			$and = " AND ";
		}
		if( is_string( $strEndereco ) )
		{
			$where .= " $and endereco ILIKE '%$strEndereco%'";
			$and = " AND ";
		}
		if( is_numeric( $intRefIdpesResp ) )
		{
			$where .= " $and ref_idpes_resp = '$intRefIdpesResp'";
			$and = " AND ";
		}

		if( !empty( $strNotIn ))
		{
			$where .= " $and cod_setor not in({$strNotIn})";
			$and = " AND ";
		}

		$orderBy = " ORDER BY nm_setor";
		if( is_string( $strOrderBy ))
		{
			$orderBy = "ORDER BY $strOrderBy";
		}

		if($where)
		{
			$where = " WHERE $where";
		}

		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}else{
			$limit = $this->getLimite();
		}
		$db = new clsBanco();
		$this->_total = $total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );

		//echo "SELECT ".$this->camposLista." FROM {$this->tabela} $where $orderBy $limit";die;
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
		if( is_numeric( $this->codSetor ) )
		{
			$db = new clsBanco();
			//echo "SELECT {$this->todosCampos} FROM {$this->tabela} WHERE  cod_setor='{$this->codSetor}'";
			$db->Consulta( "SELECT {$this->todosCampos} FROM {$this->tabela} WHERE  cod_setor='{$this->codSetor}'" );
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
