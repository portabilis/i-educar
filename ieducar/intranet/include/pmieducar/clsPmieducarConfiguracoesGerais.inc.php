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
  var $url_novo_educacao;
  var $mostrar_codigo_inep_aluno;
  var $justificativa_falta_documentacao_obrigatorio;
  var $tamanho_min_rede_estadual;
  var $modelo_boletim_professor;
  var $custom_labels;
  var $url_cadastro_usuario;

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

  function __construct(
      $ref_cod_instituicao = null,
      $permite_relacionamento_posvendas = null,
      $url_novo_educacao = null,
      $mostrar_codigo_inep_aluno = null,
      $justificativa_falta_documentacao_obrigatorio = null,
      $tamanho_min_rede_estadual = null,
      $modelo_boletim_professor = null,
      $custom_labels = null,
      $url_cadastro_usuario = null
  ) {
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'configuracoes_gerais';

      $this->_campos_lista = $this->_todos_campos = 'ref_cod_instituicao, permite_relacionamento_posvendas, url_novo_educacao, mostrar_codigo_inep_aluno, justificativa_falta_documentacao_obrigatorio, tamanho_min_rede_estadual, modelo_boletim_professor, custom_labels, url_cadastro_usuario ';

    if (is_numeric($ref_cod_instituicao)) {
      $this->ref_cod_instituicao = $ref_cod_instituicao;
    }

    if (is_numeric($permite_relacionamento_posvendas)) {
      $this->permite_relacionamento_posvendas = $permite_relacionamento_posvendas;
    }

    if (!empty($url_novo_educacao)) {
      $this->url_novo_educacao = $url_novo_educacao;
    }

    if (is_numeric($mostrar_codigo_inep_aluno)) {
        $this->mostrar_codigo_inep_aluno = $mostrar_codigo_inep_aluno;
    }

    if (is_numeric($justificativa_falta_documentacao_obrigatorio)) {
       $this->justificativa_falta_documentacao_obrigatorio = $justificativa_falta_documentacao_obrigatorio;
    }

    $this->tamanho_min_rede_estadual = $tamanho_min_rede_estadual;

    if (is_numeric($modelo_boletim_professor)) {
        $this->modelo_boletim_professor = $modelo_boletim_professor;
    }

    if (!empty($custom_labels)) {
        $this->custom_labels = $custom_labels;
    }

      if (!empty($url_cadastro_usuario)) {
          $this->url_cadastro_usuario = $url_cadastro_usuario;
      }
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    $db = new clsBanco();
    $set = array();

    if (is_numeric($this->permite_relacionamento_posvendas)) {
      $set[] = "permite_relacionamento_posvendas = '{$this->permite_relacionamento_posvendas}'";
    }

    if (is_numeric($this->ref_cod_instituicao)) {
      $ref_cod_instituicao = $this->ref_cod_instituicao;
    } else {
      $ref_cod_instituicao = $this->getUltimaInstituicaoAtiva();
    }

    if (!empty($this->url_novo_educacao)) {
      $set[] = "url_novo_educacao = '{$this->url_novo_educacao}'";
    }

    if (is_array($this->custom_labels)) {
        $customLabels = json_encode($this->custom_labels);
        $set[] = "custom_labels = '{$customLabels}'";
    }

    if (is_numeric($this->mostrar_codigo_inep_aluno)) {
        $set[] = "mostrar_codigo_inep_aluno = '{$this->mostrar_codigo_inep_aluno}'";
    }

    if (is_numeric($this->justificativa_falta_documentacao_obrigatorio)) {
        $set[] = "justificativa_falta_documentacao_obrigatorio = '{$this->justificativa_falta_documentacao_obrigatorio}'";
    }

    if ($this->tamanho_min_rede_estadual == '') {
        $this->tamanho_min_rede_estadual = 'NULL';
    }

    $set[] = "tamanho_min_rede_estadual = {$this->tamanho_min_rede_estadual}";

    if (is_numeric($this->modelo_boletim_professor)) {
        $set[] = "modelo_boletim_professor = '{$this->modelo_boletim_professor}'";
    }

    if (!empty($this->url_cadastro_usuario)) {
        $set[] = "url_cadastro_usuario = '{$this->url_cadastro_usuario}'";
    }

    if (!empty($set)) {
      $set = join(', ', $set);
      $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_instituicao = '{$ref_cod_instituicao}'");

      return true;
    }

    return false;
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
    $record = $db->Tupla();

    if (!empty($record['custom_labels'])) {
        $record['custom_labels'] = json_decode($record['custom_labels'], true);
    }

    return $record;
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
