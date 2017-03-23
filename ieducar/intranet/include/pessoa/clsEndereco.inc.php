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
require_once 'include/Geral.inc.php';

/**
 * clsEndereco class.
 *
 * Possui API de busca por endereço de pessoa através da view
 * "cadastro.v_endereco".
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsEndereco
{
  var $idpes;
  var $tipo;
  var $idtlog;
  var $logradouro;
  var $idlog;
  var $numero;
  var $letra;
  var $complemento;
  var $bairro;
  var $idbai;
  var $cep;
  var $cidade;
  var $idmun;
  var $sigla_uf;
  var $reside_desde;
  var $bloco;
  var $apartamento;
  var $andar;
  var $zona_localizacao;

  /**
   * Construtor.
   * @param int $idpes
   */
  function clsEndereco($idpes = FALSE)
  {
    $this->idpes = $idpes;
  }

  /**
   * Retorna o endereço da pessoa cadastrada (tabela cadastro.endereco_pessoa
   * ou cadastro.endereco_externo) como array associativo.
   * @return array|FALSE caso não haja um endereço cadastrado.
   */
  function detalhe()
  {
    if ($this->idpes) {
      $db = new clsBanco();

      $sql = sprintf('SELECT
                cep, idlog, numero, letra, complemento, idbai, bloco, andar,
                apartamento, logradouro, bairro, cidade, sigla_uf, idtlog,
                zona_localizacao
              FROM
                cadastro.v_endereco
              WHERE
                idpes = %d', $this->idpes);

      $db->Consulta($sql);

      if ($db->ProximoRegistro()) {
        $tupla                  = $db->Tupla();
        $this->bairro           = $tupla['bairro'];
        $this->idbai            = $tupla['idbai'];
        $this->cidade           = $tupla['cidade'];
        $this->sigla_uf         = $tupla['sigla_uf'];
        $this->complemento      = $tupla['complemento'];
        $this->bloco            = $tupla['bloco'];
        $this->apartamento      = $tupla['apartamento'];
        $this->andar            = $tupla['andar'];
        $this->letra            = $tupla['letra'];
        $this->numero           = $tupla['numero'];
        $this->logradouro       = $tupla['logradouro'];
        $this->idlog            = $tupla['idlog'];
        $this->idtlog           = $tupla['idtlog'];
        $this->cep              = $tupla['cep'];
        $this->zona_localizacao = $tupla['zona_localizacao'];

        return $tupla;
      }
    }

    return FALSE;
  }

  function edita()
  {
  }
}