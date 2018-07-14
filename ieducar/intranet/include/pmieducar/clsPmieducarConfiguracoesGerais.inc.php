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
require_once 'lib/Utils/SafeJson.php';

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
  var $active_on_ieducar;
  var $ieducar_image;
  var $ieducar_entity_name;
  var $ieducar_login_footer;
  var $ieducar_external_footer;
  var $ieducar_internal_footer;
  var $facebook_url;
  var $twitter_url;
  var $linkedin_url;
  var $ieducar_suspension_message;

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

  function __construct($ref_cod_instituicao = null, $campos = array()) {
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'configuracoes_gerais';

    $this->_campos_lista = $this->_todos_campos = 'ref_cod_instituicao, permite_relacionamento_posvendas,
        url_novo_educacao, mostrar_codigo_inep_aluno, justificativa_falta_documentacao_obrigatorio,
        tamanho_min_rede_estadual, modelo_boletim_professor, custom_labels, url_cadastro_usuario,
        active_on_ieducar, ieducar_image, ieducar_entity_name, ieducar_login_footer,
        ieducar_external_footer, ieducar_internal_footer, facebook_url, twitter_url, linkedin_url,
        ieducar_suspension_message ';

    if (!empty($campos['ref_cod_instituicao']) && is_numeric($campos['ref_cod_instituicao'])) {
      $this->ref_cod_instituicao = $campos['ref_cod_instituicao'];
    }

    if (!empty($campos['permite_relacionamento_posvendas']) && is_numeric($campos['permite_relacionamento_posvendas'])) {
      $this->permite_relacionamento_posvendas = $campos['permite_relacionamento_posvendas'];
    }

    if (!empty($campos['url_novo_educacao'])) {
      $this->url_novo_educacao = $campos['url_novo_educacao'];
    }

    if (!empty($campos['mostrar_codigo_inep_aluno']) && is_numeric($campos['mostrar_codigo_inep_aluno'])) {
        $this->mostrar_codigo_inep_aluno = $campos['mostrar_codigo_inep_aluno'];
    }

    if (!empty($campos['justificativa_falta_documentacao_obrigatorio']) && is_numeric($campos['justificativa_falta_documentacao_obrigatorio'])) {
       $this->justificativa_falta_documentacao_obrigatorio = $campos['justificativa_falta_documentacao_obrigatorio'];
    }

    if (!empty($campos['tamanho_min_rede_estadual'])) {
        $this->tamanho_min_rede_estadual = $campos['tamanho_min_rede_estadual'];
    }

    if (!empty($campos['modelo_boletim_professor']) && is_numeric($campos['modelo_boletim_professor'])) {
        $this->modelo_boletim_professor = $campos['modelo_boletim_professor'];
    }

    if (!empty($campos['custom_labels'])) {
        $this->custom_labels = $campos['custom_labels'];
    }

    if (!empty($campos['url_cadastro_usuario'])) {
        $this->url_cadastro_usuario = $campos['url_cadastro_usuario'];
    }

    if (isset($campos['active_on_ieducar']) && is_numeric($campos['active_on_ieducar'])) {
        $this->active_on_ieducar = $campos['active_on_ieducar'];
    }

    if (!empty($campos['ieducar_image'])) {
        $this->ieducar_image = $campos['ieducar_image'];
    }

    if (!empty($campos['ieducar_entity_name'])) {
        $this->ieducar_entity_name = $campos['ieducar_entity_name'];
    }

    if (!empty($campos['ieducar_login_footer'])) {
        $this->ieducar_login_footer = $campos['ieducar_login_footer'];
    }

    if (!empty($campos['ieducar_external_footer'])) {
        $this->ieducar_external_footer = $campos['ieducar_external_footer'];
    }

    if (!empty($campos['ieducar_internal_footer'])) {
        $this->ieducar_internal_footer = $campos['ieducar_internal_footer'];
    }

    if (!empty($campos['facebook_url'])) {
        $this->facebook_url = $campos['facebook_url'];
    }

    if (!empty($campos['twitter_url'])) {
        $this->twitter_url = $campos['twitter_url'];
    }

    if (!empty($campos['linkedin_url'])) {
        $this->linkedin_url = $campos['linkedin_url'];
    }

    if (!empty($campos['ieducar_suspension_message'])) {
        $this->ieducar_suspension_message = $campos['ieducar_suspension_message'];
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
        $customLabels = SafeJson::encode($this->custom_labels);
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

    if (is_numeric($this->active_on_ieducar)) {
        $set[] = "active_on_ieducar = '{$this->active_on_ieducar}'";
    }

    if (!empty($this->ieducar_image)) {
        $set[] = "ieducar_image = '{$this->ieducar_image}'";
    }

    if (!empty($this->ieducar_entity_name)) {
        $set[] = "ieducar_entity_name = '{$this->ieducar_entity_name}'";
    }

    if (!empty($this->ieducar_login_footer)) {
        $set[] = "ieducar_login_footer = '{$this->ieducar_login_footer}'";
    }

    if (!empty($this->ieducar_external_footer)) {
        $set[] = "ieducar_external_footer = '{$this->ieducar_external_footer}'";
    }

    if (!empty($this->ieducar_internal_footer)) {
        $set[] = "ieducar_internal_footer = '{$this->ieducar_internal_footer}'";
    }

    if (!empty($this->facebook_url)) {
        $set[] = "facebook_url = '{$this->facebook_url}'";
    }

    if (!empty($this->twitter_url)) {
        $set[] = "twitter_url = '{$this->twitter_url}'";
    }

    if (!empty($this->linkedin_url)) {
        $set[] = "linkedin_url = '{$this->linkedin_url}'";
    }

    if (!empty($this->ieducar_suspension_message)) {
        $set[] = "ieducar_suspension_message = '{$this->ieducar_suspension_message}'";
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
