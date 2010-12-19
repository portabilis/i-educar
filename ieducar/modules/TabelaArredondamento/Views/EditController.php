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
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'TabelaArredondamento_Model_TabelaDataMapper';
  protected $_titulo            = 'Cadastro de tabela de arredondamento de notas';
  protected $_processoAp        = 949;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label' => 'Instituição',
      'help'  => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Um nome para a tabela. Exemplo: "<em>Tabela genérica de conceitos</em>".'
    ),
    'tipoNota' => array(
      'label'  => 'Tipo de nota',
      'help'   => ''
    ),
    'valor_nome' => array(
      'label'  => 'Rótulo da nota:',
      'help'   => 'Exemplos: A, B, C (conceituais)<br />
                  <b>6,5<b>, <b>7,5<b> (numéricas)'
    ),
    'valor_descricao' => array(
      'label'  => '<span style="padding-left: 10px"></span>Descrição:',
      'help'   => 'Exemplos: Bom, Regular, Em Processo.'
    ),
    'valor_valor_minimo' => array(
      'label'  => '<span style="padding-left: 10px"></span>Valor mínimo:',
      'help'   => 'O valor numérico mínimo da nota.'
    ),
    'valor_valor_maximo' => array(
      'label'  => '<span style="padding-left: 10px"></span>Valor máximo:',
      'help'   => 'O valor numérico máximo da nota.'
    )
  );

  /**
   * Array de instâncias TabelaArredondamento_Model_TabelaValor.
   * @var array
   */
  protected $_valores = array();

  /**
   * Setter.
   * @param array $valores
   * @return Core_Controller_Page_Abstract Provê interface fluída
   */
  protected function _setValores(array $valores = array())
  {
    foreach ($valores as $key => $valor) {
      $this->_valores[$valor->id] = $valor;
    }
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  protected function _getValores()
  {
    return $this->_valores;
  }

  /**
   * Getter
   * @param int $id
   * @return TabelaArredondamento_Model_TabelaValor
   */
  protected function _getValor($id)
  {
    return isset($this->_valores[$id]) ? $this->_valores[$id] : NULL;
  }

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   * @todo Interação com a API está errada. Isso já é feito em _initNovo()
   *   na superclasse. VER.
   */
  protected function _preConstruct()
  {
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
      $this->_setValores($this->getDataMapper()->findTabelaValor($this->getEntity()));
    }
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituição
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      40, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Tipo de nota
    $notaTipoValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
    $notaTipos = $notaTipoValor->getEnums();
    unset($notaTipos[RegraAvaliacao_Model_Nota_TipoValor::NENHUM]);
    $this->campoRadio('tipoNota', $this->_getLabel('tipoNota'), $notaTipos,
      $this->getEntity()->get('tipoNota'), '', $this->_getHelp('tipoNota'));

    // Parte condicional
    if (!$this->getEntity()->isNew()) {
      // Quebra
      $this->campoQuebra();

      // Ajuda
      $help = 'Caso seja necessário adicionar mais notas, '
            . 'salve o formulário. Automaticamente 3 campos '
            . 'novos ficarão disponíveis.<br /><br />';

      $this->campoRotulo('__help1', '<strong>Notas para arredondamento</strong><br />', $help, FALSE, '', '');

      // Cria campos para a postagem de notas
      $valores = $this->getDataMapper()->findTabelaValor($this->getEntity());

      for ($i = 0, $loop = count($valores); $i < ($loop == 0 ? 5 : $loop + 3); $i++) {
        $valorNota = $valores[$i];

        $valor_label        = sprintf("valor[label][%d]", $i);
        $valor_id           = sprintf("valor[id][%d]", $i);
        $valor_nome         = sprintf("valor[nome][%d]", $i);
        $valor_descricao    = sprintf("valor[descricao][%d]", $i);
        $valor_valor_minimo = sprintf("valor[valor_minimo][%d]", $i);
        $valor_valor_maximo = sprintf("valor[valor_maximo][%d]", $i);

        $this->campoRotulo($valor_label, 'Arredondamento ' . ($i + 1),
          $this->_getLabel(''), TRUE);

        // Id
        $this->campoOculto($valor_id, $valorNota->id);

        // Nome
        $this->campoTexto($valor_nome, $this->_getLabel('valor_nome'),
          $valorNota->nome, 5, 5, FALSE, FALSE, TRUE, $this->_getHelp('valor_nome'));

        // Descrição (se conceitual)
        if (RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL == $this->getEntity()->get('tipoNota')) {
          $this->campoTexto($valor_descricao, $this->_getLabel('valor_descricao'),
            $valorNota->descricao, 15, 25, FALSE, FALSE, TRUE,
            $this->_getHelp('valor_descricao'));
        }

        // Valor mínimo
        $this->campoTexto($valor_valor_minimo, $this->_getLabel('valor_valor_minimo'),
          $valorNota->valorMinimo, 6, 6, FALSE, FALSE, TRUE,
          $this->_getHelp('valor_valor_minimo'));

        // Valor máximo
        $this->campoTexto($valor_valor_maximo, $this->_getLabel('valor_valor_maximo'),
          $valorNota->valorMaximo, 6, 6, FALSE, FALSE, FALSE,
          $this->_getHelp('valor_valor_maximo'));
      }

      // Quebra
      $this->campoQuebra();
    }
  }

  protected function _save()
  {
    // Verifica pela existência do field identity
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
      $entity = $this->getEntity();
    }

    // Se existir, chama _save() do parent
    if (!isset($entity)) {
      return parent::_save();
    }

    // Processa os dados da requisição, apenas os valores para a tabela de valores.
    $valores = $this->getRequest()->valor;

    // A contagem usa um dos índices do formulário, senão ia contar sempre 4.
    $loop    = count($valores['id']);

    // Array de objetos a persistir
    $insert  = array();

    // Cria um array de objetos a persistir
    for ($i = 0; $i < $loop; $i++) {
      $id = $valores['id'][$i];

      // Não atribui a instância de $entity senão não teria sucesso em verificar
      // se a instância é isNull().
      $data = array(
        'id' => $id,
        'nome' => $valores['nome'][$i],
        'descricao' => $valores['descricao'][$i],
        'valorMinimo' => $valores['valor_minimo'][$i],
        'valorMaximo' => $valores['valor_maximo'][$i]
      );

      // Se a instância já existir, use-a para garantir UPDATE
      if (NULL != ($instance = $this->_getValor($id))) {
        $insert[$id] = $instance->setOptions($data);
      }
      else {
        $instance = new TabelaArredondamento_Model_TabelaValor($data);
        if (!$instance->isNull()) {
          $insert['new_' . $i] = $instance;
        }
      }
    }

    // Persiste
    foreach ($insert as $tabelaValor) {
      // Atribui uma tabela de arredondamento a instância de tabela valor
      $tabelaValor->tabelaArredondamento = $entity;

      // Se não tiver nome, passa para o próximo
      if ($tabelaValor->isValid()) {
        $this->getDataMapper()->getTabelaValorDataMapper()->save($tabelaValor);
      }
      else {
        $this->mensagem = 'Erro no formulário';
        return FALSE;
      }
    }

    return TRUE;
  }
}