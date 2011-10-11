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
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';

class DiarioAjaxController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper'; #FIXME ? esta propriedade deveria ser diferente para outros atts ? ex Falta
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; #FIXME para que serve esta propriedade ? remover ?
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  protected function validatesPresenceOf(&$value, $name, $raiseExceptionOnEmpty = false, $msg = '')
  {
    if (! isset($value) || empty($value))
    {
      $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
      $this->appendMsg($msg);

      if ($raiseExceptionOnEmpty)
         throw new Exception($msg);

      return false;
    }
    return true;
  }

  protected function validatesValueInSetOf($value, $setExpectedValues, $name, $raiseExceptionOnError = false, $msg = '')
  {
    if (! in_array($value, $setExpectedValues))
    {
      $msg = empty($msg) ? "Valor recebido na variavel '$name' é invalido" : $msg;
      $this->appendMsg($msg);

      if ($raiseExceptionOnError)
         throw new Exception($msg);

      return false;
    }
    return true;
  }


  protected function requiresLogin($raiseExceptionOnEmpty)
  {
    #TODO verificar se usuário logado tem permissão para alterar / criar nota
    return $this->validatesPresenceOf($this->getSession()->id_pessoa, '', $raiseExceptionOnEmpty, 'Usuário deve estar logado');
  }

  protected function validatesPresenceOfInstituicaoId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->instituicao_id, 'instituicao_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfEscolaId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->escola_id, 'escola_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfCursoId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->curso_id, 'curso_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfSerieId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->serie_id, 'serie_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfTurmaId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->turma_id, 'turma_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfAnoEscolar($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->ano_escolar, 'ano_escolar', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfAlunoId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->aluno_id, 'aluno_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfMatriculaId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->matricula_id, 'matricula_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfEtapa($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->etapa, 'etapa', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfAttValue($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->att_value, 'att_value', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceAndValueInSetOfAtt($raiseExceptionOnError)
  {
    $result = $this->validatesPresenceOf($this->getRequest()->att, 'att', $raiseExceptionOnError);

    if ($result)
    {
      $expectedAtts = array('nota', 'nota_exame', 'falta', 'parecer', 'matriculas', 'opcoes_notas', 'opcoes_faltas');
      $result = $this->validatesValueInSetOf($this->getRequest()->att, $expectedAtts, 'att', $raiseExceptionOnEmpty);
    }
    return $result;
  }

  protected function validatesPresenceAndValueInSetOfOper($raiseExceptionOnError)
  {
    $result = $this->validatesPresenceOf($this->getRequest()->oper, 'oper', $raiseExceptionOnError);

    if ($result)
    {
      $expectedOpers = array('post', 'get', 'delete');
      $result = $this->validatesValueInSetOf($this->getRequest()->oper, $expectedOpers, 'oper', $raiseExceptionOnEmpty);
    }
    return $result;
  }

  /* esta funcao só pode ser chamada após setar $this->getService() */
  protected function validatesPresenceOfComponenteCurricularId($raiseExceptionOnEmpty)
  {
    return $this->validatesPresenceOf($this->getRequest()->componente_curricular_id, 'componente_curricular_id', $raiseExceptionOnEmpty);
  }

  protected function canAcceptRequest()
  {
    try {
      $this->requiresLogin(true);
      $this->validatesPresenceAndValueInSetOfAtt(true);
      $this->validatesPresenceAndValueInSetOfOper(true);
    }
    catch (Exception $e) {
      return false;
    }
    return true;
  }

  protected function canGetMatriculas()
  {
    try {
      $this->validatesPresenceOfInstituicaoId(true);
      $this->validatesPresenceOfEscolaId(true);
      $this->validatesPresenceOfCursoId(true);
      $this->validatesPresenceOfSerieId(true);
      $this->validatesPresenceOfTurmaId(true);
      $this->validatesPresenceOfAnoEscolar(true);
      $this->validatesPresenceOfComponenteCurricularId(true);
      $this->validatesPresenceOfEtapa(true);
    }
    catch (Exception $e) {
      return false;
    }
    return true;
  }


  protected function canPost()
  {
    try {
      $this->validatesPresenceOfEtapa(true);
      $this->validatesPresenceOfAttValue(true);
    }
    catch (Exception $e) {
      return false;
    }
    return true;
  }

  protected function canPostNota()
  {
    return $this->canPost();
  }


  protected function canPostFalta()
  {
    return $this->canPost();
  }


  protected function canPostParecer()
  {
    return $this->canPost();
  }


  protected function canDeleteNota()
  {
  }


  protected function canDeleteFalta()
  {
  }


  protected function canDeleteParecer()
  {
  }


  protected function deleteNota()
  {
  }


  protected function deleteFalta()
  {
  }


  protected function deleteParecer()
  {
  }


  protected function postNota()
  {
    if ($this->canPostNota() &&
        $this->setService() &&
        $this->validatesPresenceOfComponenteCurricularId(false))
    {
      $nota = new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => $this->getRequest()->componente_curricular_id,
        'nota' => urldecode($this->getRequest()->att_value),
        'etapa' => $this->getRequest()->etapa
      ));
      $this->getService()->addNota($nota);
      $this->saveService();
      $this->appendMsg('Nota alterada com sucesso.', 'notice');
    }
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
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa
    ));
  }


  protected function postFalta()
  {

    $canPost = $this->canPostFalta() && $this->setService();
    if ($canPost && $this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $canPost = $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost)
    {
      if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
        $falta = $this->getFaltaComponente();
      elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $falta = $this->getFaltaGeral();

      $this->getService()->addFalta($falta);
      $this->saveService();
      $this->appendMsg('Falta alterada com sucesso.', 'notice');
    }
  }


  protected function getParecerComponente()
  {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
              'componenteCurricular' => $this->getRequest()->componente_curricular_id,
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


  protected function postParecer()
  {
    $canPost = $this->canPostParecer() && $this->setService();
    if ($canPost && $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM)
    {
      $this->appendMsg("Não é possivel gravar o parecer descritivo, pois a regra de avaliação não utiliza parecer.");
    }
    elseif($this->getRequest()->etapa != 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL))
    {
      $canPost = false;
      $this->appendMsg("Não é possivel gravar o parecer descritivo, pois é esperado uma etapa 'An' e foi recebido a etapa '{$this->getRequest()->etapa}'.");
    }

    if ($canPost && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE))
    {
      $canPost = $this->validatesPresenceOfComponenteCurricularId(false);
      if ($canPost)
        $parecer = $this->getParecerComponente();
    }
    elseif ($canpost)
      $parecer = $this->getParecerGeral();

    if ($canPost)
    {
      $this->getService()->addParecer($parecer);
      $this->saveService();
      $this->appendMsg('Parecer alterado com sucesso.', 'notice');
    }
  }


  protected function getEtapaParecer()
  {
    if($this->getRequest()->etapa != 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL))
    {
      $this->appendMsg("Ignorado etapa recebida '{$this->getRequest()->etapa}', utilizando 'An' pois a regra de avaliação é anual.", 'notice');
      return 'An';
    }
    else
      return $this->getRequest()->etapa;
  }


  protected function getSituacaoMatricula($raiseExceptionOnErrors = true, $appendMsgOnErrors = true)
  {
    $service = $this->getService($raiseExceptionOnErrors, $appendMsgOnErrors);
    $situacao = 'Situação não recuperada';
    if ($service)
    {
      try {
        $situacao = App_Model_MatriculaSituacao::getInstance()->getValue($service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->getRequest()->componente_curricular_id]->situacao);
      }
      catch (Exception $e) {
        $this->appendMsg("Erro ao recuperar situação matricula: " . $e->getMessage());
      }
    }
    return utf8_encode($situacao);
  }


  protected function getMatriculas()
  {
    $etapaParecer = null;
    $matriculas = array();
    if ($this->canGetMatriculas())
    {
      $alunos = new clsPmieducarMatriculaTurma();
      $alunos->setOrderby('nome');

      #FIXME pega só a ultima matricula ?
      #FIXME revisao todos parametros repassados, bool_escola_andamento passar false ?
      $alunos = $alunos->lista(
        $this->getRequest()->matricula_id,
        $this->getRequest()->turma_id,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        $this->getRequest()->serie_id,
        $this->getRequest()->curso_id,
        $this->getRequest()->escola_id,
        $this->getRequest()->instituicao_id,
        $this->getRequest()->aluno_id,
        NULL,
        NULL,
        NULL,
        NULL,
        $this->getRequest()->ano_escolar,
        NULL,
        TRUE,
        NULL,
        NULL,
        TRUE,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      if (! is_array($alunos))
        $alunos = array();

      $requiredFields = array(
        array('matricula_id', 'ref_cod_matricula'), 
        array('nome', 'nome_aluno'),
      );

      foreach($alunos as $aluno)
      {
        $matricula = array();
        $this->setService($matriculaId = $aluno['ref_cod_matricula']);

        //confere a etapa apenas uma vez, para não repetir a mesma mensagem varias vezes
        if (is_null($etapaParecer))
          $etapaParecer = $this->getEtapaParecer();

        $matricula['situacao'] = $this->getSituacaoMatricula($raiseExceptionOnErrors = false);
        $matricula['nota_atual'] = $this->getNotaAtual();
        $matricula['falta_atual'] = $this->getFaltaAtual();
        $matricula['parecer_atual'] = $this->getParecerAtual($etapaParecer);

        foreach($requiredFields as $f)
          $matricula[$f[0]] = $aluno[$f[1]];

        $matriculas[] = $matricula;
      }
    }
    return $matriculas;
  }

  protected function getNotaAtual()
  {
    $nota = urldecode($this->service->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa)->nota);
    return str_replace(',', '.', $nota);
  }



  protected function getFaltaAtual()
  {
    if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      return $service->getFalta($this->getRequest()->componente_curricular_id, $this->getRequest()->componente_curricular_id)->etapa;
    elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
      return $this->service->getFalta($this->getRequest()->etapa)->quantidade;
  }


  protected function getParecerAtual($etapa)
  {
    if ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
      $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE)
    {
      return utf8_encode($this->service->getParecerDescritivo($etapa, $this->getRequest()->componente_curricular_id));
    }
    else
      return utf8_encode($this->service->getParecerDescritivo($this->getRequest()->etapa));
  }


  protected function getOpcoesFaltas()
  {
    $opcoes = array();
    foreach (range(0, 100, 1) as $f)
      $opcoes[$f] = $f;
    return $opcoes;
  }


  protected function canGetOpcoesNotas()
  {
    return $this->validatesPresenceOfMatriculaId(false);
  }  


  protected function getOpcoesNotas()
  {
    $opcoes = array();
    if ($this->canGetOpcoesNotas() && $this->setService())
    {
      $tabela = $this->getService()->getRegra()->tabelaArredondamento->findTabelaValor();
      foreach ($tabela as $item)
      {
        if ($this->getService()->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA)
          $opcoes[(string) $item->nome] = (string) $item->nome;
        else
          $opcoes[(string) $item->valorMaximo] = $item->nome . ' (' . $item->descricao .  ')';
      }
    }
    return $opcoes;
  }


  protected function saveService()
  {
    try {
      $this->getService()->save();   
    }
    catch (CoreExt_Service_Exception $e)
    {
      //excecoes ignoradas :( servico lanca excecoes de alertas, que não são exatamente errors.
      error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }

  protected function getService($raiseExceptionOnErrors = false, $appendMsgOnErrors = true)
  {
    if (isset($this->service) && ! is_null($this->service))
      return $this->service;

    $msg = 'Erro ao recuperar serviço boletim: serviço não definido.';
    if($appendMsgOnErrors)
      $this->appendMsg($msg);

    if ($raiseExceptionOnErrors)
      throw new Exception($msg);

    return null;
  }

  protected function canSetService($validatesPresenceOfMatriculaId = true)
  {
    try {
      $this->requiresLogin(true);
      if ($validatesPresenceOfMatriculaId)
        $this->validatesPresenceOfMatriculaId(true);
    }
    catch (Exception $e) {
      return false;
    }
    return true;
  }

  protected function setService($matriculaId = null)
  {
    if ($this->canSetService($validatesPresenceOfMatriculaId = is_null($matriculaId)))
    {
      try {

        if (! $matriculaId)
          $matriculaId = $this->getRequest()->matricula_id;

        $this->service = new Avaliacao_Service_Boletim(array(
            'matricula' => $matriculaId,
            'usuario'   => $this->getSession()->id_pessoa
        ));

      return true;
      }
      catch (Exception $e) {
        $this->appendMsg('Exception ao instanciar serviço boletim: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);
      }
    }
    return false;
  }


  public function Gerar()
  {

    $this->msgs = array();
    $this->response = array();

    if ($this->canAcceptRequest())
    {
      try {
        if ($this->getRequest()->oper == 'get')
        {
          if ($this->getRequest()->att == 'matriculas')
          {
            $matriculas = $this->getMatriculas();          
            $this->appendResponse('matriculas', $matriculas);
          }
          if ($this->getRequest()->att == 'opcoes_notas')
          {
            $opcoesNotas = $this->getOpcoesNotas();
            $this->appendResponse('opcoes_notas', $opcoesNotas);
          }
          if ($this->getRequest()->att == 'opcoes_faltas')
          {
            $opcoesFaltas = $this->getOpcoesFaltas();
            $this->appendResponse('opcoes_faltas', $opcoesFaltas);
          }
        }
        elseif ($this->getRequest()->oper == 'post')
        {
          if ($this->getRequest()->att == 'nota' || $this->getRequest()->att == 'nota_exame')
            $this->postNota();

          elseif ($this->getRequest()->att == 'falta')
            $this->postFalta();

          elseif ($this->getRequest()->att == 'parecer')
            $this->postParecer();        
        }
        elseif ($this->getRequest()->oper == 'delete')
        {
          #TODO delete
        }
      }
      catch (Exception $e) {
        $this->appendMsg('Exception: ' . $e->getMessage(), $type = 'error', $encodeToUtf8 = true);
      }
    }
    echo $this->prepareResponse();
  }

  protected function appendResponse($name, $value)
  {
    $this->response[$name] = $value;
  }

  protected function prepareResponse()
  {
    $msgs = array();
    $this->appendResponse('att', isset($this->getRequest()->att) ? $this->getRequest()->att : '');

    if (isset($this->getRequest()->matricula_id) && 
              $this->getRequest()->oper != 'get' && 
              $this->getRequest()->att !== 'matriculas')
    {
      $this->appendResponse('matricula', $this->getRequest()->matricula_id);
      $this->appendResponse('situacao', $this->getSituacaoMatricula($raiseExceptionOnErrors = false, $appendMsgOnErrors = false));
    }

    foreach($this->msgs as $m)
      $msgs[] = array('msg' => $m['msg'], 'type' => $m['type']);
    $this->appendResponse('msgs', $msgs);

    echo json_encode($this->response);
  }

  protected function appendMsg($msg, $type="error", $encodeToUtf8 = false)
  {
    if ($encodeToUtf8)
      $msg = utf8_encode($msg);

    error_log("$type msg: '$msg'");
    $this->msgs[] = array('msg' => $msg, 'type' => $type);
  }

  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header('Content-type: application/json');
    $instance->Gerar();
  }
}
