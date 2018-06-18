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
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Portabilis
 *
 * @since     Arquivo disponível desde a versão 1.1.0
 *
 * @version   $Id$
 */

/**
 * Portabilis_View_Helper_Inputs class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Portabilis
 *
 * @since     Classe disponível desde a versão 1.1.0
 *
 * @version   @@package_version@@
 */

class Portabilis_View_Helper_Inputs
{
    public function __construct($viewInstance)
    {
        $this->viewInstance = $viewInstance;
    }

    // dynamic inputs helper

    /* adiciona inputs de seleção dinamica ao formulário, recebendo diretamente as opcoes do input,
       sem necessidade de passar um array com um array de opções, ex:

       Ao invés de:
       $this->inputsHelper()->dynamic('instituicao', array('options' => array(required' => false)));

       Pode-se usar:
       $this->inputsHelper()->dynamic('instituicao', array(required' => false));

       Ou
       $this->inputsHelper()->dynamic('instituicao', array(), array('options' => array(required' => false)));

       Ou
       $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'pesquisaAluno'));
    */

    public function dynamic($helperNames, $inputOptions = [], $helperOptions = [])
    {
        $options = $this->mergeInputOptions($inputOptions, $helperOptions);

        if (! is_array($helperNames)) {
            $helperNames = [$helperNames];
        }

        foreach ($helperNames as $helperName) {
            $helperClassName = 'Portabilis_View_Helper_DynamicInput_' . ucfirst($helperName);
            $this->includeHelper($helperClassName);

            $helper = new $helperClassName($this->viewInstance, $this);
            $helper->$helperName($options);
        }
    }

    // input helpers

    public function input($helperName, $attrName, $inputOptions = [], $helperOptions = [])
    {
        $helperClassName = 'Portabilis_View_Helper_Input_' . ucfirst($helperName);

        $this->includeHelper($helperClassName);
        $helper = new $helperClassName($this->viewInstance, $this);
        $helper->$helperName($attrName, $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function text($attrNames, $inputOptions = [], $helperOptions = [])
    {
        if (! is_array($attrNames)) {
            $attrNames = [$attrNames];
        }

        foreach ($attrNames as $attrName) {
            $this->input('text', $attrName, $inputOptions, $helperOptions);
        }
    }

    public function numeric($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('numeric', $attrName, $inputOptions, $helperOptions);
    }

    public function integer($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('integer', $attrName, $inputOptions, $helperOptions);
    }

    public function select($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('select', $attrName, $inputOptions, $helperOptions);
    }

    public function search($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('search', $attrName, $inputOptions, $helperOptions);
    }

    public function hidden($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('hidden', $attrName, $inputOptions, $helperOptions);
    }

    public function checkbox($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('checkbox', $attrName, $inputOptions, $helperOptions);
    }

    public function date($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('date', $attrName, $inputOptions, $helperOptions);
    }

    public function textArea($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->input('textArea', $attrName, $inputOptions, $helperOptions);
    }

    public function booleanSelect($attrName, $inputOptions = [], $helperOptions = [])
    {
        $resources = [];
        if (isset($inputOptions['prompt'])) {
            $resources[''] = $inputOptions['prompt'];
        }
        $resources +=  [0 => 'Não', 1 => 'Sim'];
        $inputOptions['resources'] = $resources;
        $this->select($attrName, $inputOptions, $helperOptions);
    }

    // simple search input helper

    public function simpleSearch($objectName, $attrName, $inputOptions = [], $helperOptions = [])
    {
        $options = $this->mergeInputOptions($inputOptions, $helperOptions);

        $helperClassName = 'Portabilis_View_Helper_Input_SimpleSearch';
        $this->includeHelper($helperClassName);

        $helper = new $helperClassName($this->viewInstance, $this);
        $helper->simpleSearch($objectName, $attrName, $options);
    }

    // simple search resource input helper

    public function simpleSearchPessoa($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchPessoa', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchPais($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchPais', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchMunicipio($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchMunicipio', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchMatricula($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchMatricula', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchAluno($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchAluno', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchEmpresa($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchEmpresa', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchPessoaj($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchPessoaj', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchMotorista($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchMotorista', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchPonto($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchPonto', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchRota($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchRota', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchVeiculo($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchVeiculo', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchBairro($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchBairro', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchDistrito($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchDistrito', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchLogradouro($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchLogradouro', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchIes($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchIes', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchCursoSuperior($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchCursoSuperior', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchCartorioInep($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchCartorioInep', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchServidor($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchServidor', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchEscola($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchEscola', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchProjeto($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchProjeto', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchMenu($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchMenu', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchCliente($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchCliente', $attrName, $inputOptions, $helperOptions);
    }

    public function simpleSearchAcervo($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput('simpleSearchAcervo', $attrName, $inputOptions, $helperOptions);
    }

    // multiple search resource input helper

    /*public function multipleSearchDeficienciasAjax($attrName, $inputOptions = array(), $helperOptions = array()) {
      $this->multipleSearchResourceInput('multipleSearchDeficiencias', $attrName, $inputOptions, $helperOptions);
    }*/

    public function multipleSearchDeficiencias($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchDeficiencias', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchAssuntos($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchAssuntos', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchCategoriaObra($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchCategoriaObra', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchAutores($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchAutores', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchEtapacurso($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchEtapacurso', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchComponenteCurricular($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchComponenteCurricular', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchAreasConhecimento($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchAreasConhecimento', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchCursoAluno($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchCursoAluno', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchBeneficios($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchBeneficios', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchEscola($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchEscola', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchCurso($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchCurso', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchDocumentosAtestadoVaga($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchDocumentosAtestadoVaga', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchSerie($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchSerie', $attrName, $inputOptions, $helperOptions);
    }

    public function multipleSearchCustom($attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->multipleSearchResourceInput('multipleSearchCustom', $attrName, $inputOptions, $helperOptions);
    }

    /*public function multipleSearchAjaxAreasConhecimento($attrName, $inputOptions = array(), $helperOptions = array()) {
     $this->multipleSearchResourceInput('multipleSearchAjaxAreasConhecimento', $attrName, $inputOptions, $helperOptions);
  }*/

    // resource input helpers

    public function religiao($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('religiao', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function beneficio($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('beneficio', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function estadoCivil($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('estadoCivil', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function turmaTurno($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('turmaTurno', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function uf($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('uf', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    public function tipoLogradouro($inputOptions = [], $helperOptions = [])
    {
        $this->resourceInput('tipoLogradouro', $this->mergeInputOptions($inputOptions, $helperOptions));
    }

    // protected methods

    protected function resourceInput($helperName, $options = [])
    {
        $helperClassName = 'Portabilis_View_Helper_Input_Resource_' . ucfirst($helperName);

        $this->includeHelper($helperClassName);
        $helper = new $helperClassName($this->viewInstance, $this);
        $helper->$helperName($options);
    }

    protected function simpleSearchResourceInput($helperName, $attrName, $inputOptions = [], $helperOptions = [])
    {
        $options = $this->mergeInputOptions($inputOptions, $helperOptions);

        $helperClassName = 'Portabilis_View_Helper_Input_Resource_' . ucfirst($helperName);

        $this->includeHelper($helperClassName);

        $helper = new $helperClassName($this->viewInstance, $this);

        $helper->$helperName($attrName, $options);
    }

    protected function multipleSearchResourceInput($helperName, $attrName, $inputOptions = [], $helperOptions = [])
    {
        $this->simpleSearchResourceInput($helperName, $attrName, $inputOptions, $helperOptions);
    }

    protected function includeHelper($helperClassName)
    {
        $classPath       = str_replace('_', '/', $helperClassName) . '.php';

        // usado include_once para continuar execução script mesmo que o path inexista.
        include_once $classPath;

        if (! class_exists($helperClassName)) {
            throw new CoreExt_Exception("Class '$helperClassName' not found in path '$classPath'");
        }
    }

    protected function mergeInputOptions($inputOptions = [], $helperOptions = [])
    {
        if (! empty($inputOptions) && isset($helperOptions['options'])) {
            throw new Exception('Don\'t send $inputOptions and $helperOptions[\'options\'] at the same time!');
        }

        $defaultOptions = ['options' => $inputOptions];
        $options        = Portabilis_Array_Utils::merge($helperOptions, $defaultOptions);

        //foreach($helperOptions as $k => $v) {
        //  $options[$k] = $v;
        //}

        return $options;
    }
}
