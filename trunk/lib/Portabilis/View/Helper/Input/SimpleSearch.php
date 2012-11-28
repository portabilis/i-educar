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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/Core.php';


/**
 * Portabilis_View_Helper_DynamicInput_BibliotecaPesquisaObra class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_SimpleSearch extends Portabilis_View_Helper_Input_Core {

  public function simpleSearch($objectName, $attrName, $options = array()) {
    /* #TODO adicionar opção 'dependsOn' => array()
             e via js resetar campos pesquisa quando mudar alguma dependencia e
             antes de efetuar pesquisa validar se dependencias estão com valores informados.
    */

    $defaultOptions = array('options'            => array(),
                            'searchPath'         => "/module/Api/" . ucwords($objectName) . "?oper=get&resource={$objectName}-search",
                            'addHiddenInput'     => false,
                            'hiddenInputOptions' => array());

    $options = $this->mergeOptions($options, $defaultOptions);

    if ($options['addHiddenInput']) {
      if ($attrName == 'id') {
        throw new CoreExt_Exception("When \$addHiddenInput is true the \$attrName (of the visible input) " .
                                    "must be different than 'id', because the hidden input will use it.");
      }

      $this->inputsHelper()->hiddenInput($objectName, 'id', $options['hiddenInputOptions']);
    }

    $this->inputsHelper()->textInput($objectName, $attrName, $options['options']);

    // search js

    $js = "
      var simpleSearch = {

        /* API returns result as {value : label, value2, label2}
           but jquery autocomplete, expects [{value : label}, {value : label}]
        */
        fixResult :  function(result) {
          var fixed = [];

          \$j.each(result, function(value, label) {
            fixed.push({ value : value, label : label})
          });

          return fixed;
        },

        handleSearch : function(dataResponse, response) {
          handleMessages(dataResponse['msgs']);

          if (dataResponse.result) {
            response(simpleSearch.fixResult(dataResponse.result));
          }
        },

        search : function(request, response) {
          \$j.get('" . $options['searchPath'] . "', { query : request.term }, function(dataResponse) {
            simpleSearch.handleSearch(dataResponse, response);
          });
        },

        handleSelect : function(event, ui) {
          \$j(this).val(ui.item.label)
          \$j('#" . $objectName . "_id').val(ui.item.value);

          return false;
        },
      }

      var autocompleteOptions = {
        source    : simpleSearch.search,
        minLength : 1,
        autoFocus : true,
        select    : simpleSearch.handleSelect
      }

      \$j('#{$objectName}_{$attrName}').autocomplete(autocompleteOptions);
    ";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js);
  }
}
?>