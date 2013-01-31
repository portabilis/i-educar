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

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';


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
class Portabilis_View_Helper_DynamicInput_BibliotecaPesquisaObra extends Portabilis_View_Helper_DynamicInput_Core {

  protected function getAcervoId($id = null) {
    if (! $id && $this->viewInstance->ref_cod_acervo)
      $id = $this->viewInstance->ref_cod_acervo;

    return $id;
  }


  protected function getObra($id) {
    if (! $id)
      $id = $this->getAcervoId($id);

    // chama finder somente se possuir id, senão ocorrerá exception
    $obra = empty($id) ? null : App_Model_IedFinder::getBibliotecaObra($this->getBibliotecaId(), $id);

    return $obra;
  }


  public function bibliotecaPesquisaObra($options = array()) {
    $defaultOptions = array('id' => null, 'options' => array(), 'hiddenInputOptions' => array());
    $options        = $this->mergeOptions($options, $defaultOptions);

    $inputHint  = "<img border='0' onclick='pesquisaObra();' id='lupa_pesquisa_obra' name='lupa_pesquisa_obra' src='imagens/lupa.png' />";

    // se não recuperar obra, deixa titulo em branco
    $obra       = $this->getObra($options['id']);
    $tituloObra = $obra ? $obra['titulo'] : '';

    $defaultInputOptions = array('id'         => 'titulo_obra',
                                 'label'      => 'Obra',
                                 'value'      => $tituloObra,
                                 'size'       => '30',
                                 'max_length' => '255',
                                 'required'   => true,
                                 'expressao'  => false,
                                 'inline'     => false,
                                 'label_hint' => '',
                                 'input_hint' => $inputHint,
                                 'callback'   => '',
                                 'event'      => 'onKeyUp',
                                 'disabled'   => true);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoTexto'), $inputOptions);

    // hidden input
    $defaultHiddenInputOptions = array('id'    => 'ref_cod_acervo',
                                       'value' => $this->getAcervoId($options['id']));

    $hiddenInputOptions = $this->mergeOptions($options['hiddenInputOptions'], $defaultHiddenInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoOculto'), $hiddenInputOptions);

    // Ao selecionar obra, na pesquisa de obra é setado o value deste elemento
    $this->viewInstance->campoOculto("cod_biblioteca", "");

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
      var resetObra = function(){
        $("#ref_cod_acervo").val("");
        $("#titulo_obra").val("");
      }

      $("#ref_cod_biblioteca").change(resetObra);', true);

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
      function pesquisaObra() {

        var additionalFields = getElementFor("biblioteca");
        var exceptFields     = getElementFor("titulo_obra");

        if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {
  	      var bibliotecaId = getElementFor("biblioteca").val();

          pesquisa_valores_popless("educar_pesquisa_obra_lst.php?campo1=ref_cod_acervo&campo2=titulo_obra&campo3="+bibliotecaId)
        }
      }');
  }
}