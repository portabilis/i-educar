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
 * Cria e configura um front controller para encaminhar as requisições para
 * page controllers especializados no diretório modules/.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Modules
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once '../includes/bootstrap.php';
require_once 'include/clsBanco.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/Controller/Request.php';
require_once 'CoreExt/Controller/Front.php';
require_once 'CoreExt/DataMapper.php';

// Objeto de requisição
$request = new CoreExt_Controller_Request();

// Helper de URL. Auxilia para criar uma URL no formato http://www.example.org/module
$url = CoreExt_View_Helper_UrlHelper::getInstance();
$url = $url->url($request->get('REQUEST_URI'), array('components' => CoreExt_View_Helper_UrlHelper::URL_HOST));

// Configura o baseurl da request
$request->setBaseurl(sprintf('%s/module', $url));

// Configura o DataMapper para usar uma instância de clsBanco com fetch de resultados
// usando o tipo FETCH_ASSOC
CoreExt_DataMapper::setDefaultDbAdapter(new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC)));

// Inicia o Front Controller
$frontController = CoreExt_Controller_Front::getInstance();
$frontController->setRequest($request);

// Configura o caminho aonde os módulos estão instalados
$frontController->setOptions(
  array('basepath' => PROJECT_ROOT . DS . 'modules')
);
$frontController->dispatch();

// Resultado
print $frontController->getViewContents();