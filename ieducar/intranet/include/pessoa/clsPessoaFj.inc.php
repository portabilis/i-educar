<?php

/**
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
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';

/**
 * clsPessoaFj class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPessoaFj
{
  /**
   * Atributos de clsPessoa(Fisica|Juridica).
   */
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

  /**
   * Atributos de endereço.
   */
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

  /**
   * Atributos de endereço.
   */
  var $ddd_1;
  var $fone_1;
  var $ddd_2;
  var $fone_2;
  var $ddd_fax;
  var $fone_fax;
  var $ddd_mov;
  var $fone_mov;

  /**
   * Atributos de documentos.
   */
  var $rg;
  var $cpf;

  var $_total;

  var $banco           = 'gestao_homolog';
  var $schema_cadastro = 'cadastro';
  var $tabela_pessoa   = 'pessoa';

  /**
   * Construtor.
   */
  function  clsPessoaFj($int_idpes = FALSE)
  {
    $this->idpes = $int_idpes;
  }

  function lista($str_nome = FALSE, $inicio_limite = FALSE, $qtd_registros = FALSE,
    $str_orderBy = FALSE, $arrayint_idisin = FALSE, $arrayint_idnotin = FALSE,
    $str_tipo_pessoa = FALSE)
  {
    $objPessoa = new clsPessoa_();

    $listaPessoa = $objPessoa->lista($str_nome, $inicio_limite, $qtd_registros,
      $str_orderBy, $arrayint_idisin, $arrayint_idnotin, $str_tipo_pessoa);

    if (count($listaPessoa) > 0) {
      return $listaPessoa;
    }

    return FALSE;
  }

  function lista_rapida($idpes = NULL, $nome = NULL, $id_federal = NULL,
    $inicio_limite = NULL, $limite = NULL, $str_tipo_pessoa = NULL,
    $str_order_by = NULL, $int_ref_cod_sistema = NULL)
  {
    $db = new clsBanco();

    $filtros        = '';
    $filtroTipo     = '';
    $whereAnd       = ' WHERE ';
    $outros_filtros = FALSE;
    $filtro_cnpj    = FALSE;

    if (is_string($nome) && $nome != '') {
      $filtros       .= "{$whereAnd} (nome ILIKE '%{$nome}%')";
      $whereAnd       = ' AND ';
      $outros_filtros = TRUE;
    }

    if (is_numeric($idpes)) {
      $filtros       .= "{$whereAnd} idpes = '{$idpes}'";
      $whereAnd       = ' AND ';
      $outros_filtros = TRUE;
    }

    if (is_numeric($int_ref_cod_sistema)) {
      $filtro_sistema = TRUE;
      $filtros       .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR id_federal is not null)";
      $whereAnd       = ' AND ';
    }

    if (is_numeric($id_federal)) {
      $db2 = new clsBanco();

      $sql = sprintf(
        'SELECT idpes FROM cadastro.fisica WHERE cpf LIKE \'%%%s%%\'', $id_federal
      );

      $db2->Consulta();

      $array_idpes = NULL;

      while ($db2->ProximoRegistro()) {
        list($id_pes)  = $db2->Tupla();
        $array_idpes[] = $id_pes;
      }

      $sql = sprintf(
        'SELECT idpes FROM cadastro.juridica WHERE cnpj LIKE \'%%%s%%\'', $id_federal
      );

      $db2->Consulta($sql);

      while ($db2->ProximoRegistro()) {
        list($id_pes)  = $db2->Tupla();
        $array_idpes[] = $id_pes;
      }

      if (is_array($array_idpes)) {
        $array_idpes      = implode(', ', $array_idpes);
        $filtros         .= "{$whereAnd} idpes IN ($array_idpes)";
        $whereAnd         = ' AND ';
        $filtro_idfederal = TRUE;
      }
      else {
        return FALSE;
      }
    }

    if (is_string($str_tipo_pessoa)) {
      $filtroTipo    .= " AND tipo  = '{$str_tipo_pessoa}' ";
      $outros_filtros = TRUE;
    }

    if (is_string($str_order_by)) {
      $order = "ORDER BY $str_order_by";
    }

    $limit = '';

    if (is_numeric($inicio_limite) && is_numeric($limite)) {
      $limit = "LIMIT $limite OFFSET $inicio_limite";
    }

    if ($filtro_idfederal) {
      $this->_total = $db->CampoUnico(
        sprintf('SELECT COUNT(0) FROM cadastro.v_pessoa_fj %s', $filtros)
      );
    }
    else {
      if ($filtro_sistema && $outros_filtros == FALSE || $filtro_cnpj) {
        $this->_total = $db->CampoUnico(
          sprintf('SELECT COUNT(0) FROM cadastro.v_pessoafj_count %s', $filtros)
        );
      }
      else {
        $this->_total = $db->CampoUnico(
          sprintf('SELECT COUNT(0) FROM cadastro.v_pessoa_fj %s', $filtros)
        );
      }
    }

    $sql = sprintf('
      SELECT
        idpes,
        nome,
        ref_cod_sistema,
        fantasia,
        tipo,
        id_federal AS cpf,
        id_federal AS cnpj,
        id_federal
      FROM
        cadastro.v_pessoa_fj
        %s
        %s
        %s',
      $filtros, $order, $limit
    );

    $db->Consulta($sql);

    while ($db->ProximoRegistro()) {
      $tupla           = $db->Tupla();
      $tupla['_total'] = $this->_total;
      $resultado[]     = $tupla;
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro
   * @return array
   */
  function detalhe()
  {
    if ($this->idpes) {
      $objPessoa     = new clsPessoa_($this->idpes);
      $detalhePessoa = $objPessoa->detalhe();

      $objEndereco     = new clsEndereco($this->idpes);
      $detalheEndereco = $objEndereco->detalhe();

      if ($detalheEndereco) {
        $this->bairro           = $detalheEndereco['bairro'];
        $this->logradouro       = $detalheEndereco['logradouro'];
        $this->sigla_uf         = $detalheEndereco['sigla_uf'];
        $this->cidade           = $detalheEndereco['cidade'];
        $this->reside_desde     = $detalheEndereco['reside_desde'];
        $this->idtlog           = $detalheEndereco['idtlog'];
        $this->complemento      = $detalheEndereco['complemento'];
        $this->numero           = $detalheEndereco['numero'];
        $this->letra            = $detalheEndereco['letra'];
        $this->idlog            = $detalheEndereco['idlog'];
        $this->idbai            = $detalheEndereco['idbai'];
        $this->cep              = $detalheEndereco['cep'];
        $this->apartamento      = $detalheEndereco['apartamento'];
        $this->bloco            = $detalheEndereco['bloco'];
        $this->andar            = $detalheEndereco['andar'];
        $this->zona_localizacao = $detalheEndereco['zona_localizacao'];

        $detalhePessoa['bairro']       = $this->bairro;
        $detalhePessoa['logradouro']   = $this->logradouro;
        $detalhePessoa['sigla_uf']     = $this->sigla_uf;
        $detalhePessoa['cidade']       = $this->cidade;
        $detalhePessoa['reside_desde'] = $this->reside_desde;
        $detalhePessoa['idtlog']       = $this->idtlog;
        $detalhePessoa['complemento']  = $this->complemento;
        $detalhePessoa['numero']       = $this->numero;
        $detalhePessoa['letra']        = $this->letra;
        $detalhePessoa['idbai']        = $this->idbai;
        $detalhePessoa['cep']          = $this->cep;
        $detalhePessoa['idlog']        = $this->idlog;
      }

      $obj_fisica     = new clsFisica($this->idpes);
      $detalhe_fisica = $obj_fisica->detalhe();

      if ($detalhe_fisica) {
        $detalhePessoa['cpf'] = $detalhe_fisica['cpf'];

        $this->cpf       = $detalhe_fisica['cpf'];
        $this->data_nasc = $detalhe_fisica['data_nasc'];

        if ($this->data_nasc) {
          $detalhePessoa['data_nasc'] = $this->data_nasc;
        }
      }

      $objFone   = new clsPessoaTelefone();
      $listaFone = $objFone->lista($this->idpes);

      if ($listaFone) {
        foreach ($listaFone as $fone) {
          if($fone['tipo'] == 1) {
            $detalhePessoa['ddd_1']  = $fone['ddd'];
            $detalhePessoa[]         = & $detalhePessoa['ddd_1'];
            $detalhePessoa['fone_1'] = $fone['fone'];
            $detalhePessoa[]         = & $detalhePessoa['fone_1'];

            $this->ddd_1  = $fone['ddd'];
            $this->fone_1 = $fone['fone'];
          }

          if ($fone['tipo'] == 2) {
            $detalhePessoa['ddd_2']  = $fone['ddd'];
            $detalhePessoa[]         = & $detalhePessoa['ddd_2'];
            $detalhePessoa['fone_2'] = $fone['fone'];
            $detalhePessoa[]         = & $detalhePessoa['fone_2'];

            $this->ddd_2  = $fone['ddd'];
            $this->fone_2 = $fone['fone'];
          }

          if ($fone['tipo'] == 3) {
            $detalhePessoa['ddd_mov']  = $fone['ddd'];
            $detalhePessoa[]           = & $detalhePessoa['ddd_mov'];
            $detalhePessoa['fone_mov'] = $fone['fone'];
            $detalhePessoa[]           = & $detalhePessoa['fone_mov'];

            $this->ddd_mov  = $fone['ddd'];
            $this->fone_mov = $fone['fone'];
          }

          if ($fone['tipo'] == 4) {
            $detalhePessoa['ddd_fax']  = $fone['ddd'];
            $detalhePessoa[]           = & $detalhePessoa['ddd_fax'];
            $detalhePessoa['fone_fax'] = $fone['fone'];
            $detalhePessoa[]           = & $detalhePessoa['fone_fax'];

            $this->ddd_fax  = $fone['ddd'];
            $this->fone_fax = $fone['fone'];
          }
        }
      }

      $obj_documento = new clsDocumento($this->idpes);
      $documentos = $obj_documento->detalhe();

      if (is_array($documentos)) {
        if ($documentos['rg']) {
          $detalhePessoa['rg'] = $documentos['rg'];
          $detalhePessoa[]     = & $detalhePessoa['rg'];

          $this->rg = $documentos['rg'];
        }
      }

      $this->idpes           = $detalhePessoa['idpes'];
      $this->nome            = $detalhePessoa['nome'];
      $this->idpes_cad       = $detalhePessoa['idpes_cad'];
      $this->data_cad        = $detalhePessoa['data_cad'];
      $this->url             = $detalhePessoa['url'];
      $this->tipo            = $detalhePessoa['tipo'];
      $this->idpes_rev       = $detalhePessoa['idpes_rev'];
      $this->data_rev        = $detalhePessoa['data_rev'];
      $this->situacao        = $detalhePessoa['situacao'];
      $this->origem_gravacao = $detalhePessoa['origem_gravacao'];
      $this->email           = $detalhePessoa['email'];

      return $detalhePessoa;
    }

    return FALSE;
  }

  function queryRapida($int_idpes)
  {
    $this->idpes = $int_idpes;

    $this->detalhe();

    $resultado = array();
    $pos = 0;

    for ($i = 1; $i< func_num_args(); $i++) {
      $campo             = func_get_arg($i);
      $resultado[$pos]   = ($this->$campo) ? $this->$campo : '';
      $resultado[$campo] = & $resultado[$pos];

      $pos++;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }
}