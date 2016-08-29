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
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarConfiguracoesGerais class.
 *
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarConfiguracoesGerais
{
  var $ref_cod_instituicao;
  var $permite_relacionamento_posvendas;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Define o campo para ser usado como padrão de agrupamento no método lista().
   * @var string
   */
  var $_campo_group_by;

  /**
   * Construtor.
   */
  function clsPmieducarConfiguracoesGerais($ref_cod_instituicao, $permite_relacionamento_posvendas)
  {
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'configuracoes_gerais';

    $this->_campos_lista = $this->_todos_campos = 'ref_cod_instituicao, permite_relacionamento_posvendas ';

    if (is_numeric($ref_cod_instituicao)) {
      $this->ref_cod_instituicao = $ref_cod_instituicao;
    }
    if (is_numeric($permite_relacionamento_posvendas)) {
      $this->permite_relacionamento_posvendas = $permite_relacionamento_posvendas;
    }
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    $db = new clsBanco();
    $set = '';
    if (is_numeric($this->permite_relacionamento_posvendas)) {
      $set .= "{$gruda}permite_relacionamento_posvendas = '{$this->permite_relacionamento_posvendas}'";
      $gruda = ', ';
    }

    if (is_numeric($this->ref_cod_instituicao)) {
      $ref_cod_instituicao = $this->ref_cod_instituicao;
    } else {
      $ref_cod_instituicao = $this->getUltimaInstituicaoAtiva();
    }

    if ($set) {
      $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_instituicao = '{$ref_cod_instituicao}'");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->ref_cod_instituicao)) {
      $ref_cod_instituicao = $this->ref_cod_instituicao;
    } else {
      $ref_cod_instituicao = $this->getUltimaInstituicaoAtiva();
    }

    $db = new clsBanco();
    $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$ref_cod_instituicao}'");
    $db->ProximoRegistro();
    return $db->Tupla();

    return FALSE;
  }

  function getUltimaInstituicaoAtiva() {
    $db = new clsBanco();
    $db->Consulta("SELECT cod_instituicao
                     FROM pmieducar.instituicao
                    WHERE ativo = 1
                    ORDER BY cod_instituicao DESC LIMIT 1");
    $db->ProximoRegistro();
    $instituicao = $db->Tupla();
    return $instituicao[0];
  }

}