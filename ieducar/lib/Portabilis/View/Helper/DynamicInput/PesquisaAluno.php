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
 * Portabilis_View_Helper_DynamicInput_PesquisaAluno class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_PesquisaAluno extends Portabilis_View_Helper_DynamicInput_Core {

  protected function inputValue($id = null) {
    if (! $id && $this->viewInstance->ref_cod_aluno)
      $id = $this->viewInstance->ref_cod_aluno;

    return $id;
  }


  protected function getResource($id) {
    if (! $id)
      $id = $this->inputValue($id);

    // chama finder somente se possuir id, senão ocorrerá exception
    $resource = empty($id) ? null : App_Model_IedFinder::getAluno($this->getEscolaId(), $id);

    return $resource;
  }


  public function pesquisaAluno($options = array()) {
    $defaultOptions = array('id' => null, 'options' => array(), 'filterByEscola' => false);
    $options        = $this->mergeOptions($options, $defaultOptions);

    $inputHint  = "<img border='0' onclick='pesquisaAluno();' id='lupa_pesquisa_aluno' name='lupa_pesquisa_aluno' src='imagens/lupa.png' />";

    // se não recuperar recurso, deixa resourceLabel em branco
    $resource      = $this->getResource($options['id']);
    $resourceLabel = $resource ? $resource['nome_aluno'] : '';

    $defaultInputOptions = array('id'         => 'nm_aluno',
                                 'label'      => 'Aluno',
                                 'value'      => $resourceLabel,
                                 'size'       => '30',
                                 'max_length'  => '255',
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

    $this->viewInstance->campoOculto("ref_cod_aluno", $this->inputValue($options['id']));

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
      var resetAluno = function(){
        $("#ref_cod_aluno").val("");
        $("#nm_aluno").val("");
      }

      $("#ref_cod_escola").change(resetAluno);', true);

    if ($options['filterByEscola']) {
      $js = 'function pesquisaAluno() {
          var additionalFields = [document.getElementById("ref_cod_escola")];
          var exceptFields     = [document.getElementById("nm_aluno")];

          if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {

    	      var escolaId = document.getElementById("ref_cod_escola").value;
            pesquisa_valores_popless("/intranet/educar_pesquisa_aluno.php?ref_cod_escola="+escolaId);
          }
        }';
    }

    else {
      $js = 'function pesquisaAluno() {
          var exceptFields     = [document.getElementById("nm_aluno")];

          if (validatesPresenseOfValueInRequiredFields([], exceptFields)) {
            pesquisa_valores_popless("/intranet/educar_pesquisa_aluno.php");
          }
        }';
    }

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js);
  }
}