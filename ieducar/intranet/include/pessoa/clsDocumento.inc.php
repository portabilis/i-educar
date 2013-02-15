<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");


/**
 * clsDocumento class.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pessoa
 * @since       Classe disponível desde a versão 1.0.0
 * @version     $Id$
 */
class clsDocumento
{
	var $idpes;
	var $rg;
	var $data_exp_rg;
	var $sigla_uf_exp_rg;
	var $tipo_cert_civil;
	var $num_termo;
	var $num_livro;
	var $num_folha;
	var $data_emissao_cert_civil;
	var $sigla_uf_cert_civil;
	var $cartorio_cert_civil;
	var $num_cart_trabalho;
	var $serie_cart_trabalho;
	var $data_emissao_cart_trabalho;
	var $sigla_uf_cart_trabalho;
	var $num_tit_eleitor;
	var $zona_tit_eleitor;
	var $secao_tit_eleitor;
	var $idorg_exp_rg;
	var $certidao_nascimento;

	var $tabela;
	var $schema = "cadastro";

	/**
	 * Construtor
	 *
	 * @return Object:clsDocumento
	 */
	function clsDocumento( $int_idpes = false, $int_rg = false, $str_data_exp_rg = false, $str_sigla_uf_exp_rg = false, $int_tipo_cert_civil = false, $int_num_termo = false, $int_num_livro = false, $int_num_folha = false, $str_data_emissao_cert_civil = false, $str_sigla_uf_cert_civil = false, $str_cartorio_cert_civil = false, $int_num_cart_trabalho = false, $int_serie_cart_trabalho = false, $str_data_emissao_cart_trabalho = false, $str_sigla_uf_cart_trabalho = false, $int_num_tit_eleitor = false, $int_zona_tit_eleitor = false, $int_secao_tit_eleitor = false, $int_idorg_exp_rg = false, $str_certidao_nascimento = null)
	{
		$objPessoa = new clsFisica($int_idpes);
		if($objPessoa->detalhe())
		{
			$this->idpes= $int_idpes;
		}

		$this->rg = $int_rg;
		$this->data_exp_rg = $str_data_exp_rg;
		$objUj = new clsUf($str_sigla_uf_exp_rg);
		if($objUj->detalhe())
		{
			$this->sigla_uf_exp_rg = $str_sigla_uf_exp_rg;
		}
		$this->tipo_cert_civil = $int_tipo_cert_civil;
		$this->num_termo = $int_num_termo;
		$this->num_livro = $int_num_livro;
		$this->num_folha = $int_num_folha;
		$this->data_emissao_cert_civil = $str_data_emissao_cert_civil;
		$objUj = new clsUf($str_sigla_uf_cert_civil);
		if($objUj->detalhe())
		{
			$this->sigla_uf_cert_civil= $str_sigla_uf_cert_civil;
		}

		$this->cartorio_cert_civil = $str_cartorio_cert_civil;
		$this->num_cart_trabalho = $int_num_cart_trabalho;
		$this->serie_cart_trabalho = $int_serie_cart_trabalho;
		$this->data_emissao_cart_trabalho = $str_data_emissao_cart_trabalho;

		$objUj = new clsUf($str_sigla_uf_cart_trabalho);
		if($objUj->detalhe())
		{
			$this->sigla_uf_cart_trabalho= $str_sigla_uf_cart_trabalho;
		}

		$this->num_tit_eleitor = $int_num_tit_eleitor;
		$this->zona_tit_eleitor = $int_zona_tit_eleitor;
		$this->secao_tit_eleitor = $int_secao_tit_eleitor;

		$objOrgEmisRg = new clsOrgaoEmissorRg($int_idorg_exp_rg);
		if ($objOrgEmisRg->detalhe())
		{
			$this->idorg_exp_rg = $int_idorg_exp_rg;
		}

    $this->certidao_nascimento = $str_certidao_nascimento;

		$this->tabela = "documento";
	}

	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();

		if(is_numeric($this->idpes))
		{
			$campos = "";
			$values = "";

			if( is_numeric( $this->rg ) and (!empty($this->rg)) )
			{
				$campos .= ", rg";
				$values .= ", '{$this->rg}'";
			}
			if( is_string( $this->data_exp_rg ) and (!empty($this->data_exp_rg)))
			{
				$campos .= ", data_exp_rg";
				$values .= ", '{$this->data_exp_rg}'";
			}
			if( is_string( $this->sigla_uf_exp_rg ) and (!empty($this->sigla_uf_exp_rg)))
			{
				$campos .= ", sigla_uf_exp_rg";
				$values .= ", '{$this->sigla_uf_exp_rg}'";
			}
			if( is_string( $this->tipo_cert_civil ) and (!empty($this->tipo_cert_civil)))
			{
				$campos .= ", tipo_cert_civil";
				$values .= ", '{$this->tipo_cert_civil}'";
			}
			if( is_numeric( $this->num_termo ) and (!empty($this->num_termo)))
			{
				$campos .= ", num_termo";
				$values .= ", '{$this->num_termo}'";
			}
			if( is_string( $this->num_livro ) and (!empty($this->num_livro)))
			{
				$campos .= ", num_livro";
				$values .= ", '{$this->num_livro}'";
			}
			if( is_numeric( $this->num_folha ) and (!empty($this->num_folha)))
			{
				$campos .= ", num_folha";
				$values .= ", '{$this->num_folha}'";
			}
			if( is_string( $this->data_emissao_cert_civil ) and (!empty($this->data_emissao_cert_civil)))
			{
				$campos .= ", data_emissao_cert_civil";
				$values .= ", '{$this->data_emissao_cert_civil}'";
			}
			if( is_string( $this->sigla_uf_cert_civil ) and (!empty($this->sigla_uf_cert_civil)))
			{
				$campos .= ", sigla_uf_cert_civil";
				$values .= ", '{$this->sigla_uf_cert_civil}'";
			}
			if( is_string( $this->cartorio_cert_civil ) and (!empty($this->cartorio_cert_civil)))
			{
				$campos .= ", cartorio_cert_civil";
				$values .= ", '{$this->cartorio_cert_civil}'";
			}
			if( is_numeric( $this->num_cart_trabalho ) and (!empty($this->num_cart_trabalho)))
			{
				$campos .= ", num_cart_trabalho";
				$values .= ", '{$this->num_cart_trabalho}'";
			}
			if( is_numeric( $this->serie_cart_trabalho ) and (!empty($this->serie_cart_trabalho)))
			{
				$campos .= ", serie_cart_trabalho";
				$values .= ", '{$this->serie_cart_trabalho}'";
			}
			if( is_string( $this->data_emissao_cart_trabalho ) and (!empty($this->data_emissao_cart_trabalho)))
			{
				$campos .= ", data_emissao_cart_trabalho";
				$values .= ", '{$this->data_emissao_cart_trabalho}'";
			}
			if( is_string( $this->sigla_uf_cart_trabalho ) and (!empty($this->sigla_uf_cart_trabalho)))
			{
				$campos .= ", sigla_uf_cart_trabalho";
				$values .= ", '{$this->sigla_uf_cart_trabalho}'";
			}
			if( is_numeric( $this->num_tit_eleitor ) and (!empty($this->num_tit_eleitor)))
			{
				$campos .= ", num_tit_eleitor";
				$values .= ", '{$this->num_tit_eleitor}'";
			}
			if( is_numeric( $this->zona_tit_eleitor ) and (!empty($this->zona_tit_eleitor)))
			{
				$campos .= ", zona_tit_eleitor";
				$values .= ", '{$this->zona_tit_eleitor}'";
			}
			if( is_numeric( $this->secao_tit_eleitor ) and (!empty($this->secao_tit_eleitor)))
			{
				$campos .= ", secao_tit_eleitor";
				$values .= ", '{$this->secao_tit_eleitor}'";
			}
			if( is_numeric( $this->idorg_exp_rg ) and (!empty($this->idorg_exp_rg)))
			{
				$campos .= ", idorg_exp_rg";
				$values .= ", '{$this->idorg_exp_rg}'";
			}
			if( is_string( $this->certidao_nascimento ) and (!empty($this->certidao_nascimento)))
			{
				$campos .= ", certidao_nascimento";
				$values .= ", '{$this->certidao_nascimento}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idpes , origem_gravacao, idsis_cad, data_cad, operacao $campos ) VALUES ( '{$this->idpes}', 'M', 17, NOW(), 'I' $values )" );

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
		$set = "";
		$gruda = "SET ";
		//die($this->rg."<-");
		if( is_numeric( $this->rg ) and (!empty($this->rg)))
		{	//die("aki");
			$set = "SET rg = '{$this->rg}'";
			$gruda = ", ";
		}else
		{
			$set .= $gruda."rg = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->data_exp_rg ) and (!empty($this->data_exp_rg)))
		{
			$set .= $gruda."data_exp_rg = '{$this->data_exp_rg}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."data_exp_rg = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->sigla_uf_exp_rg ) and (!empty($this->sigla_uf_exp_rg)))
		{
			$set .= $gruda."sigla_uf_exp_rg = '{$this->sigla_uf_exp_rg}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."sigla_uf_exp_rg = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->tipo_cert_civil ) and (!empty($this->tipo_cert_civil)))
		{
			$set .= $gruda."tipo_cert_civil = '{$this->tipo_cert_civil}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."tipo_cert_civil = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->num_termo ) and (!empty($this->num_termo)))
		{
			$set .= $gruda."num_termo = '{$this->num_termo}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."num_termo = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->num_livro ) and (!empty($this->num_livro)))
		{
			$set .= $gruda."num_livro = '{$this->num_livro}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."num_livro = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->num_folha ) and (!empty($this->num_folha)))
		{
			$set .= $gruda."num_folha = '{$this->num_folha}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."num_folha = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->data_emissao_cert_civil ) and (!empty($this->data_emissao_cert_civil)))
		{
			$set .= $gruda."data_emissao_cert_civil = '{$this->data_emissao_cert_civil}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."data_emissao_cert_civil = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->sigla_uf_cert_civil ) and (!empty($this->sigla_uf_cert_civil)))
		{
			$set .= $gruda."sigla_uf_cert_civil = '{$this->sigla_uf_cert_civil}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."sigla_uf_cert_civil = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->cartorio_cert_civil ) and (!empty($this->cartorio_cert_civil)))
		{
			$set .= $gruda."cartorio_cert_civil = '{$this->cartorio_cert_civil}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."cartorio_cert_civil = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->num_cart_trabalho ) and (!empty($this->num_cart_trabalho)))
		{
			$set .= $gruda."num_cart_trabalho = '{$this->num_cart_trabalho}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."num_cart_trabalho = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->serie_cart_trabalho ) and (!empty($this->serie_cart_trabalho)))
		{
			$set .= $gruda."serie_cart_trabalho = '{$this->serie_cart_trabalho}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."serie_cart_trabalho = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->data_emissao_cart_trabalho ) and (!empty($this->data_emissao_cart_trabalho)))
		{
			$set .= $gruda."data_emissao_cart_trabalho = '{$this->data_emissao_cart_trabalho}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."data_emissao_cart_trabalho = NULL";
			$gruda = ", ";
		}

		if( is_string( $this->sigla_uf_cart_trabalho ) and (!empty($this->sigla_uf_cart_trabalho)))
		{
			$set .= $gruda."sigla_uf_cart_trabalho = '{$this->sigla_uf_cart_trabalho}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."sigla_uf_cart_trabalho = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->num_tit_eleitor ) and (!empty($this->num_tit_eleitor)))
		{
			$set .= $gruda."num_tit_eleitor = '{$this->num_tit_eleitor}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."num_tit_eleitor = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->zona_tit_eleitor ) and (!empty($this->zona_tit_eleitor)))
		{
			$set .= $gruda."zona_tit_eleitor = '{$this->zona_tit_eleitor}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."zona_tit_eleitor = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->secao_tit_eleitor ) and (!empty($this->secao_tit_eleitor)))
		{
			$set .= $gruda."secao_tit_eleitor = '{$this->secao_tit_eleitor}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."secao_tit_eleitor = NULL";
			$gruda = ", ";
		}

		if( is_numeric( $this->idorg_exp_rg ) and (!empty($this->idorg_exp_rg)))
		{
			$set .= $gruda."idorg_exp_rg = '{$this->idorg_exp_rg}'";
			$gruda = ", ";
		}
		else
		{
			$set .= $gruda."idorg_exp_rg = NULL";
			$gruda = ", ";
		}


		if( ! is_null( $this->certidao_nascimento ))
		{
			$set .= $gruda."certidao_nascimento = '{$this->certidao_nascimento}'";
			$gruda = ", ";
		}

		if($set)
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idpes = '$this->idpes'" );
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
		if(is_numeric($this->idpes))
		{
			$db = new clsBanco();
			//$db->Consulta( "DELETE FROM {$this->schema}.{$this->tabela} WHERE idpes = '$this->idpes'" );

			return true;
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_rg = false, $str_data_exp_rg = false, $str_sigla_uf_exp_rg = false, $int_tipo_cert_civil = false, $int_num_termo = false, $int_num_livro = false, $int_num_folha = false, $str_data_emissao_cert_civil = false, $str_sigla_uf_cert_civil = false, $str_cartorio_cert_civil = false, $int_num_cart_trabalho = false, $int_serie_cart_trabalho = false, $str_data_emissao_cart_trabalho = false, $str_sigla_uf_cart_trabalho = false, $int_num_tit_eleitor = false, $int_zona_tit_eleitor = false, $int_secao_tit_eleitor = false, $int_idorg_exp_rg = false, $int_limite_ini=0, $int_limite_qtd=20, $str_orderBy = false, $int_idpes = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";

		if( is_string( $int_idpes ) )
		{
			$where .= "{$whereAnd}idpes IN ({$int_idpes})";
			$whereAnd = " AND ";
		}

		if( is_numeric( $this->rg ) )
		{
			$where .= "{$whereAnd}rg = '$int_rg'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->data_exp_rg ) )
		{
			$where .= "{$whereAnd}data_exp_rg LIKE '%$str_data_exp_rg%'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->sigla_uf_exp_rg ) )
		{
			$where .= "{$whereAnd}sigla_uf_exp_rg LIKE '%$str_sigla_uf_exp_rg%'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->tipo_cert_civil ) )
		{
			$where .= "{$whereAnd}tipo_cert_civil LIKE '%$str_tipo_cert_civil%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->num_termo ) )
		{
			$where .= "{$whereAnd}num_termo = '$int_num_termo'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->num_livro ) )
		{
			$where .= "{$whereAnd}num_livro = '$int_num_livro'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->num_folha ) )
		{
			$where .= "{$whereAnd}num_folha = '$intnum_folha'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->data_emissao_cert_civil ) )
		{
			$where .= "{$whereAnd}data_emissao_cert_civil LIKE '$str_data_emissao_cert_civil'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->sigla_uf_cert_civil ) )
		{
			$where .= "{$whereAnd}sigla_uf_cert_civil LIKE '$str_sigla_uf_cert_civil'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->cartorio_cert_civil ) )
		{
			$where .= "{$whereAnd}cartorio_cert_civil LIKE '$str_cartorio_cert_civil'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->num_cart_trabalho ) )
		{
			$where .= "{$whereAnd}num_cart_trabalho = '$int_num_cart_trabalho'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->serie_cart_trabalho ) )
		{
			$where .= "{$whereAnd}serie_cart_trabalho = '$int_serie_cart_trabalho'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->data_emissao_cart_trabalho ) )
		{
			$where .= "{$whereAnd}data_emissao_cart_trabalho LIKE '$str_data_emissao_cart_trabalho'";
			$whereAnd = " AND ";
		}
		if( is_string( $this->sigla_uf_cart_trabalho ) )
		{
			$where .= "{$whereAnd}sigla_uf_cart_trabalho LIKE '$str_sigla_uf_cart_trabalho'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->num_tit_eleitor ) )
		{
			$where .= "{$whereAnd}num_tit_eleitor = '$int_num_tit_eleitor'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->zona_tit_eleitor ) )
		{
			$where .= "{$whereAnd}zona_tit_eleitor = '$int_zona_tit_eleitor'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->secao_tit_eleitor ) )
		{
			$where .= "{$whereAnd}secao_tit_eleitor = '$int_secao_tit_eleitor'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $this->idorg_exp_rg ) )
		{
			$where .= "{$whereAnd}idorg_exp_rg = '$int_idorg_exp_rg'";
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
		$db->Consulta( "SELECT * FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();

			$tupla["idpes"] = $tupla["idpes"];
			$tupla["idorg_exp_rg"] = $tupla["idorg_exp_rg"];
			$tupla["sigla_uf_cart_trabalho"] = $tupla["sigla_uf_cart_trabalho"];
			$tupla["sigla_uf_cert_civil"] = $tupla["sigla_uf_cert_civil"];
			$tupla["sigla_uf_exp_rg"] = $tupla["sigla_uf_exp_rg"];

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

		$objPessoa = new clsFisica($this->idpes);
		if($objPessoa->detalhe())
		{
			$db = new clsBanco();
			$db->Consulta("SELECT rg, data_exp_rg, sigla_uf_exp_rg, tipo_cert_civil, num_termo, num_livro, num_folha, data_emissao_cert_civil, sigla_uf_cert_civil, cartorio_cert_civil, num_cart_trabalho, serie_cart_trabalho, data_emissao_cart_trabalho, sigla_uf_cart_trabalho, num_tit_eleitor, zona_tit_eleitor, secao_tit_eleitor, idorg_exp_rg, certidao_nascimento FROM {$this->schema}.{$this->tabela} WHERE idpes = '{$this->idpes}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$this->rg = $tupla["rg"];
				$this->data_exp_rg = $tupla["data_exp_rg"];
				$this->tipo_cert_civil = $tupla["tipo_cert_civil"];
				$this->num_termo = $tupla["num_termo"];
				$this->num_livro = $tupla["num_livro"];
				$this->num_folha = $tupla["num_folha"];
				$this->data_emissao_cert_civil = $tupla["data_emissao_cert_civil"];
				$this->cartorio_cert_civil = $tupla["cartorio_cert_civil"];
				$this->num_cart_trabalho = $tupla["num_cart_trabalho"];
				$this->serie_cart_trabalho = $tupla["serie_cart_trabalho"];
				$this->data_emissao_cart_trabalho = $tupla["data_emissao_cart_trabalho"];
				$this->num_tit_eleitor = $tupla["num_tit_eleitor"];
				$this->zona_tit_eleitor = $tupla["zona_tit_eleitor"];
				$this->secao_tit_eleitor = $tupla["secao_tit_eleitor"];
				$this->certidao_nascimento = $tupla["certidao_nascimento"];

				$tupla["idpes"] = $tupla["idpes"];
				$tupla["idorg_exp_rg"] = $tupla["idorg_exp_rg"];
				$tupla["sigla_uf_cart_trabalho"] = $tupla["sigla_uf_cart_trabalho"];
				$tupla["sigla_uf_cert_civil"] = $tupla["sigla_uf_cert_civil"];
				$tupla["sigla_uf_exp_rg"] = $tupla["sigla_uf_exp_rg"];

				return $tupla;
			}
		}
		return false;
	}
}
?>
