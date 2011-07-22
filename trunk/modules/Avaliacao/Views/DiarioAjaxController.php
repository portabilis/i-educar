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

  #TODO implementar uma funcao para cada att, setFalta, setNota, setParecer
  public function Gerar()
  {

    #error_log("New DiarioAjax request: id_pessoa [{$this->getSession()->id_pessoa}] att [{$this->getRequest()->att}] matricula [{$this->getRequest()->matricula}] componente_curricular [{$this->getRequest()->componente_curricular}] att_value [{$this->getRequest()->att_value}] att_value encoded [" . urldecode($this->getRequest()->att_value) . "] etapa [{$this->getRequest()->etapa}]");

    $this->msgs = array();

    if (! $this->getSession()->id_pessoa)#TODO verificar se usuário logado tem permissão para alterar / criar nota
      $this->appendMsg("Usuario nao logado");
    elseif (! isset($this->getRequest()->att))
      $this->appendMsg("É necessario receber um atributo 'att'");
    else
    {
      try {
        $this->service = new Avaliacao_Service_Boletim(array(
            'matricula' => $this->getRequest()->matricula,
            'usuario'   => $this->getSession()->id_pessoa
        ));
      }
      catch (Exception $e) {
        $this->AppendMsg('Exception: ' . $e->getMessage(), $decode_to_utf8  = False);
      }
    }

    if (count($this->msgs) < 1)
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
          $this->appendMsg("Não é gravar parecer descritivo, pois a regra de avaliação não utiliza parecer.");        
      }
      else
        $this->AppendMsg("Valor recebido do atributo 'att' inválido");

      try {
        $this->service->save();
      }
      catch (CoreExt_Service_Exception $e) {
        // Ok. Não pode promover por se tratar de progressão manual ou por estar em andamento
        #error_log('CoreExt_Service_Exception: ' . $e->getMessage(), $decode_to_utf8  = False);
      }
      catch (Exception $e) {
        $this->AppendMsg('Exception: ' . $e->getMessage(), $decode_to_utf8  = False);
      }

      $situacao = App_Model_MatriculaSituacao::getInstance()->getValue(
        $this->service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->getRequest()->componente_curricular]->situacao);
      $attCurrentValue = $this->getRequest()->att_value;
      $_att = $this->getRequest()->att;
    }


    $_matricula = $this->getRequest()->matricula;

    echo "<?xml version='1.0' encoding='ISO-8859-1' ?>
    <status>
    <errors>{$this->msgsToXml('error')}</errors>
    <matricula>$_matricula</matricula>
    <att>$_att</att>
    <situacao>$situacao</situacao>
    </status>";
  }

  function appendMsg($msg, $decode_to_utf8 = True)
  {
    if ($decode_to_utf8)
      $msg = utf8_decode($msg);
    $this->msgs[] = $msg;
    #error_log($msg);
  }

  function msgsToXml($tag = 'msg')
  {
    $x = "";
    foreach($this->msgs as $m)
      $x .= "<$tag>$m</$tag>";
    return $x;
  }

  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header("Content-type: text/xml");
    #TODO implementar uma funcao para cada att, setFalta, setNota, setParecer
    $instance->Gerar();
  }
}
