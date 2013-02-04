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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'ComponenteCurricular/Model/Componente.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';

/**
 * AnoController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class AnoController extends Core_Controller_Page_EditController
{
  protected $_dataMapper = 'ComponenteCurricular_Model_AnoEscolarDataMapper';
  protected $_titulo     = 'Configuração de ano escolar';
  protected $_processoAp = 946;
  protected $_formMap    = array();

  /**
   * Array de instâncias ComponenteCurricular_Model_AnoEscolar.
   * @var array
   */
  protected $_entries = array();

  /**
   * Setter.
   * @param array $entries
   * @return Core_Controller_Page Provê interface fluída
   */
  public function setEntries(array $entries = array())
  {
    foreach ($entries as $entry) {
      $this->_entries[$entry->anoEscolar] = $entry;
    }
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  public function getEntries()
  {
    return $this->_entries;
  }

  /**
   * Getter.
   * @param int $id
   * @return ComponenteCurricular_Model_AnoEscolar
   */
  public function getEntry($id)
  {
    return $this->_entries[$id];
  }

  /**
   * Verifica se uma instância ComponenteCurricular_Model_AnoEscolar identificada
   * por $id existe.
   * @param int $id
   * @return bool
   */
  public function hasEntry($id)
  {
    if (isset($this->_entries[$id])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Retorna um array associativo de séries com código de curso como chave.
   * @return array
   */
  protected function _getSeriesAgrupadasPorCurso()
  {
    $series = App_Model_IedFinder::getSeries($this->getEntity()->instituicao);
    $cursos = array();

    foreach ($series as $id => $nome) {
      $serie    = App_Model_IedFinder::getSerie($id);
      $codCurso = $serie['ref_cod_curso'];

      $cursos[$codCurso][$id] = $nome;
    }

    return $cursos;
  }

  /**
   * Retorna o nome de um curso.
   * @param int $id
   * @return string
   */
  protected function _getCursoNome($id)
  {
    return App_Model_IedFinder::getCurso($id);
  }

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   */
  public function _preConstruct()
  {
    // Popula array de disciplinas selecionadas
    $this->setOptions(array('edit_success_params' => array('id' => $this->getRequest()->cid)));
    $this->setEntries($this->getDataMapper()->findAll(array(),
      array('componenteCurricular' => $this->getRequest()->cid)));

    // Configura ação cancelar
    $this->setOptions(array('url_cancelar' => array(
      'path' => 'view', 'options' => array(
        'query' => array('id' => $this->getRequest()->cid)
      )
    )));
  }

  /**
   * @see Core_Controller_Page_EditController#_initNovo()
   */
  protected function _initNovo()
  {
    if (!isset($this->getRequest()->cid)) {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance());
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @see Core_Controller_Page_EditController#_initEditar()
   */
  protected function _initEditar()
  {
   try {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance(array('componenteCurricular' => $this->getRequest()->cid)));
    } catch(Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }
    return TRUE;
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('cid', $this->getEntity()->get('componenteCurricular'));

    // Cursos
    $cursos = $this->_getSeriesAgrupadasPorCurso();

    // Cria a matriz de checkboxes
    foreach ($cursos as $key => $curso) {
      $this->campoRotulo($key, $this->_getCursoNome($key), '', FALSE, '', '');
      foreach ($curso as $c => $serie) {
        $this->campoCheck('ano_escolar['.$c.']', '', $this->hasEntry($c), $serie, FALSE);

        $valor = $this->hasEntry($c) ? $this->getEntry($c)->cargaHoraria : NULL;
        $this->campoTexto('carga_horaria['.$c.']', 'Carga horária',
          $valor, 5, 5, FALSE, FALSE,
          FALSE);
      }
      $this->campoQuebra();
    }
  }

  /**
   * @see Core_Controller_Page_EditController#_save()
   */
  protected function _save()
  {
    $data = $insert = $delete = $intersect = array();

    // O id de componente_curricular será igual ao id da request
    if ($cid = $this->getRequest()->cid) {
      $data['componenteCurricular'] = $cid;
    }

    // Cria um array de Entity geradas pela requisição
    foreach ($this->getRequest()->ano_escolar as $key => $val) {
      $data['anoEscolar'] = $key;
      $data['cargaHoraria'] = $this->getRequest()->carga_horaria[$key];
      $insert[$key] = $this->getDataMapper()->createNewEntityInstance($data);
    }

    // Cria um array de chaves da Entity AnoEscolar para remover
    $entries = $this->getEntries();
    $delete = array_diff(array_keys($entries), array_keys($insert));

    // Cria um array de chaves da Entity AnoEscolar para evitar inserir novamente
    $intersect = array_intersect(array_keys($entries), array_keys($insert));

    // Registros a apagar
    foreach ($delete as $id)
    {
      $this->getDataMapper()->delete($entries[$id]);
    }

    // Registros a inserir
    foreach ($insert as $key => $entity) {
      // Se o registro já existe, passa para o próximo
      if (FALSE !== array_search($key, $intersect)) {
        $entity->markOld();
      }

      try {
        $this->getDataMapper()->save($entity);
      }
      catch (Exception $e) {
        $this->mensagem = 'Erro no preenchimento do formulário.';
        return FALSE;
      }
    }

    return TRUE;
  }
}