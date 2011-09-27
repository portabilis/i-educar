<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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

class DiarioAjaxController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper'; #FIXME ? esta propriedade deveria ser diferente para outros atts ? ex Falta
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; #FIXME para que serve esta propriedade ? remover ?
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  protected function validadeParams()
  {
  
    $expectedAtts = array('nota', 'nota_exame', 'falta', 'parecer');
    $msgError = '';

    #TODO verificar se usuário logado tem permissão para alterar / criar nota
    if (! $this->getSession()->id_pessoa)
    {
      $msgError = "Usuário não logado";
    }
    elseif(! isset($this->getRequest()->att))
    {
      $msgError = "É necessario receber um atributo 'att'";
    }
    elseif(! in_array($this->getRequest()->att, $expectedAtts))
    {
      $msgError = "Valor recebido inválido para o atributo 'att'";
    }

    if($msgError)
    {
      $this->AppendMsg($msgError);
      return false;
    }
    return true; 
  }


  protected function setService()
  {
    try
    {
      $this->service = new Avaliacao_Service_Boletim(array(
          'matricula' => $this->getRequest()->matricula,
          'usuario'   => $this->getSession()->id_pessoa
      ));
    }
    catch (Exception $e)
    {
      $this->AppendMsg('Exception: ' . $e->getMessage(), $decode_to_utf8  = False);
      return false;
    }
    return true;
  }


  protected function changeNota()
  {

    #TODO se nota for vaziu, deletar

    $nota = new Avaliacao_Model_NotaComponente(array(
      'componenteCurricular' => $this->getRequest()->componente_curricular,
      'nota' => urldecode($this->getRequest()->att_value),
      'etapa' => $this->getRequest()->etapa
    ));
    $this->service->addNota($nota);
  }


  protected function getQuantidadeFalta()
  {
    $quantidade = (int) $this->getRequest()->att_value;

    if ($quantidade < 0)
      $quantidade = 0;
    
    return $quantidade;
  }


  protected function getFaltaGeral()
  {
    return new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => $this->getQuantidadeFalta(),
        'etapa' => $this->getRequest()->etapa
        ));
  }


  protected function getFaltaComponente()
  {

    return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa
          ));
  }


  protected function changeFalta()
  {

    #TODO se falta for vaziu, deletar

    if ($this->service->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $falta = $this->getFaltaComponente();
    elseif ($this->service->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
      $falta = $this->getFaltaGeral();

    $this->service->addFalta($falta);
  }


  protected function getParecerComponente()
  {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
              'componenteCurricular' => $this->getRequest()->componente_curricular,
              'parecer'  => addslashes($this->getRequest()->att_value),
              'etapa'  => $this->getRequest()->etapa
            ));
  }


  protected function getParecerGeral()
  {
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer' => addslashes($this->getRequest()->att_value),
              'etapa'   => $this->getRequest()->etapa
            ));
  }


  protected function changeParecer()
  {

    #TODO se nota for vaziu, deletar

    if ($this->service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM)
    {
      $this->appendMsg("Não é gravar parecer descritivo, pois a regra de avaliação não utiliza parecer.");
    }
    else
    {    
      if ($this->service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
        $this->service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE)
      {
        $parecer = $this->getParecerComponente();
      }
      else
      {
        $parecer = $this->getParecerGeral();
      }

      $this->service->addParecer($parecer);
    } 
  }


  public function Gerar()
  {

    #error_log("New DiarioAjax request: id_pessoa [{$this->getSession()->id_pessoa}] att [{$this->getRequest()->att}] matricula [{$this->getRequest()->matricula}] componente_curricular [{$this->getRequest()->componente_curricular}] att_value [{$this->getRequest()->att_value}] att_value encoded [" . urldecode($this->getRequest()->att_value) . "] etapa [{$this->getRequest()->etapa}]");

    $this->msgs = array();

    try {
      if ($this->validadeParams() && $this->setService())
      {
        if ($this->getRequest()->att == 'nota' || $this->getRequest()->att == 'nota_exame')
          $this->changeNota();
        elseif ($this->getRequest()->att == 'falta')
          $this->changeFalta();
        elseif ($this->getRequest()->att == 'parecer')
          $this->changeParecer();      

        $this->service->save();
      }
    }
    catch (CoreExt_Service_Exception $e)
    {
      //excecoes ignoradas :( servico lanca excecoes de alertas, que não são exatamente errors.
      error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
    catch (Exception $e) {
      $this->AppendMsg('Exception: ' . $e->getMessage(), $decode_to_utf8  = False);
    }

    $situacao = App_Model_MatriculaSituacao::getInstance()->getValue($this->service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->getRequest()->componente_curricular]->situacao);    
    $_matricula = $this->getRequest()->matricula;

    echo "<?xml version='1.0' encoding='ISO-8859-1' ?>
    <status>
    <errors>{$this->msgsToXml('error')}</errors>
    <matricula>$_matricula</matricula>
    <att>{$this->getRequest()->att}</att>
    <situacao>$situacao</situacao>
    </status>";
  }

  protected function appendMsg($msg, $decode_to_utf8 = True)
  {
    if ($decode_to_utf8)
      $msg = utf8_decode($msg);
    $this->msgs[] = $msg;
    error_log($msg);
  }

  protected function msgsToXml($tag = 'msg')
  {
    $x = "";
    foreach($this->msgs as $m)
      $x .= "<$tag>$m</$tag>";
    return $x;
  }

  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header("Content-type: text/xml");
    $instance->Gerar();
  }
}
