<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\PersonHasPlace;
use App\Models\Place;
use App\Models\State;
use iEducar\Modules\Educacenso\RunMigrations;

ini_set("max_execution_time", 0);
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
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesProfessorTurma.inc.php';

/**
 * @author    Caroline Salib Canto <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Importação educacenso');
    $this->processoAp = 9998849;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $arquivo;

  function Inicializar()
  {
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998849, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $this->breadcrumb('Importação educacenso', [
        url('intranet/educar_educacenso_index.php') => 'Educacenso',
    ]);

    $this->titulo = "Nova importação";

    return 'Editar';
  }

  function Gerar()
  {
      $resources = [
          null => 'Selecione',
          '2019' => '2019',
          '2020' => '2020',
      ];
      $options = [
          'label' => 'Ano',
          'resources' => $resources,
          'value' => $this->ano,
      ];
      $this->inputsHelper()->select('ano', $options);

    $this->campoArquivo('arquivo', 'Arquivo', $this->arquivo,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Somente arquivos com formato txt serão aceitos</span>');

    $this->nome_url_sucesso = "Importar";

      Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Importacao.js');
  }

  function Novo()
  {
      return;
  }

  function Editar()
  {
      return;
  }
  
}
// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
