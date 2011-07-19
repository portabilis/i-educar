<?php
  //error_reporting(E_ALL);
  //ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'include/portabilis_utils.php';

class DiarioAjaxController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper'; #FIXME ? esta propriedade deveria ser diferente para outros atts ? ex Falta
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; #FIXME para que serve esta propriedade ? remover ?
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  protected function _preConstruct()
  {
    // Id do usuário na session
    //$this->user = $this->getSession()->id_pessoa;

    $user = new User();

    if (! $user->isLoggedIn())#TODO verificar se usuário logado tem permissão para alterar / criar nota
      die('not authorized');
    elseif (! isset($this->getRequest()->att))
      die('invalid att');
    else
    {
      $this->service = new Avaliacao_Service_Boletim(array(
          'matricula' => $this->getRequest()->matricula,
          'usuario'   => $user->userId
      ));
    }
  }

  #TODO implementar uma funcao para cada att, setFalta, setNota, setParecer
  public function Gerar()
  {

    if ($this->getRequest()->att == 'nota')
    {
      $nota = new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => $this->getRequest()->componente_curricular,
        'nota' => urldecode($this->getRequest()->att_value),
        'etapa' => $this->getRequest()->etapa
      ));
      $this->service->addNota($nota);
    }
    elseif ($this->getRequest()->att == 'falta')
    {
      $quantidade = 0 < $this->getRequest()->att_value ? (int) $this->getRequest()->att_value : 0;

      if ($this->service->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      {
        $falta = new Avaliacao_Model_FaltaComponente(array(
          'componenteCurricular' => $this->getRequest()->componente_curricular,
          'quantidade' => $quantidade,
          'etapa' => $this->getRequest()->etapa
        ));
      }
      elseif ($this->service->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
      {
        $falta = new Avaliacao_Model_FaltaGeral(array(
          'quantidade' => $quantidade,
          'etapa' => $this->getRequest()->etapa
          ));
        }
        $this->service->addFalta($falta);
    }
    elseif ($this->getRequest()->att == 'parecer')
    {
      if ($this->service->getRegra()->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM)
      {
        if ($this->service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
          $this->service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE)
        {
          $parecer = new Avaliacao_Model_ParecerDescritivoComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular,
            'parecer'  => addslashes($this->getRequest()->att_value),
            'etapa'  => $this->getRequest()->etapa
          ));
        }

        else
        {
          $parecer = new Avaliacao_Model_ParecerDescritivoGeral(array(
            'parecer' => addslashes($this->getRequest()->att_value),
            'etapa'   => $this->getRequest()->etapa
          ));
        }
      $this->service->addParecer($parecer);
      }
      else
        die('regra de avaliação não utiliza parecer');
    }
    else
      die('invalid att');

    try {
      $this->service->save();
    }
    catch (CoreExtservice_Exception $e) {
      // Ok. Não pode promover por se tratar de progressão manual ou por estar em andamento
    }
    catch (Exception $e) {
      $result = 0;
    }
    $result = 1;

    $situacao = App_Model_MatriculaSituacao::getInstance()->getValue(
      $this->service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->getRequest()->componente_curricular]->situacao);
    $attCurrentValue = $this->getRequest()->att_value;
    $_att = $this->getRequest()->att;
    $_matricula = $this->getRequest()->matricula;

    echo "<?xml version='1.0' encoding='ISO-8859-1' ?>
    <status>
    <success>$result</success>
    <matricula>$_matricula</matricula>
    <att>$_att</att>
    <situacao>$situacao</situacao>
    </status>";
  }

  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header("Content-type: text/xml");
    #TODO implementar uma funcao para cada att, setFalta, setNota, setParecer
    $instance->Gerar();
  }
}

