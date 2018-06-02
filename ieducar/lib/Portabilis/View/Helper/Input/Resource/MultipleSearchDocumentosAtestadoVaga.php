<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
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
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';


/**
 * Portabilis_View_Helper_Input_MultipleSearchComponenteCurricular class.
 *
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     ?
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_MultipleSearchDocumentosAtestadoVaga extends Portabilis_View_Helper_Input_MultipleSearch {

  protected function getOptions($resources) {

    if (empty($resources)) {
      $resources = array('certidao_nasci'         => 'Certid&atilde;o de nascimento e/ou carteira de identidade',
                         'comprovante_resi'       => 'Comprovante de resid&ecirc;ncia',
                         'foto_3_4'               => 'Foto 3/4',
                         'historico_escola'       => 'Hist&oacute;rico escolar original',
                         'atestado_frequencia'    => 'Atestado de frequ&ecirc;ncia original',
                         'atestado_transferencia' => 'Atestado de Transfer&ecirc;ncia',
                         'decla_vacina'           => 'Declara&ccedil;&atilde;o de vacina da unidade de sa&uacute;de original',
                         'carteira_sus'           => 'Carteira do SUS',
                         'cartao_bolsa_fami'      => 'C&oacute;pia do cart&atilde;o bolsa fam&iacute;lia',
                         'rg_aluno_pai'           => 'C&oacute;pia do RG (aluno e pai)',
                         'cpf_aluno_pai'          => 'C&oacute;pia do CPF (aluno e pai)',
                         'tit_eleitor'            => 'T&iacute;tulo de eleitor do respons&aacute;vel',
                         'doc_nis'                => 'N&uacute;mero de Identifica&ccedil;&atilde;o Social - NIS'
                         );
    }

    return $this->insertOption(null, '', $resources);
  }
  public function multipleSearchDocumentosAtestadoVaga($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'documentos',
                            'apiController' => '',
                            'apiResource'   => '');

    $options                         = $this->mergeOptions($options, $defaultOptions);

    $options['options']['resources'] = $this->getOptions($options['options']['resources']);

    $this->placeholderJs($options);

    parent::multipleSearch($options['objectName'], $attrName, $options);
  }

  protected function placeholderJs($options) {
    $optionsVarName = "multipleSearch" . Portabilis_String_Utils::camelize($options['objectName']) . "Options";
    $js             = "if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
                       $optionsVarName.placeholder = safeUtf8Decode('Selecione os componentes');";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
  }
}