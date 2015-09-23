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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'include/clsCadastro.inc.php';
require_once 'Core/Controller/Page/Validatable.php';
require_once 'App/Model/NivelAcesso.php';

/**
 * Core_Controller_Page_EditController abstract class.
 *
 * Provê um page controller padrão para páginas de edição e criação de registros.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Documentar a API
 * @todo      Definir o atributo $_formMap que é diferente do atributo
 *            semelhante dos outros controllers (view|list)
 * @todo      Documentar as opções new_success e edit_success
 * @version   @@package_version@@
 */
abstract class Core_Controller_Page_EditController
  extends clsCadastro
  implements Core_Controller_Page_Validatable
{
  /**
   * Array associativo de um elemento de formulário, usado para a definição
   * de labels, nome de campos e definição de qual campo foi invalidado por
   * CoreExt_DataMapper::isValid().
   *
   * @var array
   */
  protected $_formMap = array();

  /**
   * Determina se "Cadastrar" ou "Atualizar" são ações disponíveis na interface.
   * @var bool
   */
  protected $_saveOption = FALSE;

  /**
   * Determina se "Excluir" é uma ação disponível na interface.
   * @var bool
   */
  protected $_deleteOption = FALSE;

  /**
   * Determina o nível de acesso necessário para as ações de Cadastro/Exclusão.
   * @var int
   */
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;

  /**
   * Determina um caminho para redirecionar o usuário caso seus privilégios de
   * acesso sejam insuficientes.
   * @var string
   */
  protected $_nivelAcessoInsuficiente = NULL;

  /**
   * @var clsPermissoes
   */
  protected $_clsPermissoes = NULL;

  /**
   * Chama o construtor da superclasse para atribuir $tipoacao do $_POST.
   */
  public function __construct()
  {
    $this->setDataMapper($this->getDataMapper());

    // Adiciona novos itens de configuração
    $this->_options = $this->_options + array(
      'save_action'               => $this->_saveOption,
      'delete_action'             => $this->_deleteOption,
      'nivel_acesso'              => $this->_nivelAcessoOption,
      'nivel_acesso_insuficiente' => $this->_nivelAcessoInsuficiente
    );

    // Configura botões padrão
    if (0 < $this->getRequest()->id) {
      $this->setOptions(array(
        'url_cancelar' => array(
          'path'    => 'view',
          'options' => array('query' => array('id' => $this->getRequest()->id))
        )
      ));
    }

    $this->_preConstruct();
    parent::__construct();
    $this->_postConstruct();
  }

  /**
   * Subclasses podem sobrescrever esse método para executar operações antes
   * da chamada ao construtor de clsCadastro().
   */
  protected function _preConstruct()
  {
  }

  /**
   * Subclasses podem sobrescrever esse método para executar operações após
   * a chamada ao construtor de clsCadastro().
   */
  protected function _postConstruct()
  {
  }

  /**
   * Retorna um label de um item de formulário.
   * @param string $key
   * @return string
   */
  protected function _getLabel($key)
  {
    return $this->_formMap[$key]['label'];
  }

  /**
   * Retorna uma string de ajuda para um item de formulário.
   * @param string $key
   * @return string
   */
  protected function _getHelp($key)
  {
    return $this->_formMap[$key]['help'];
  }

  /**
   * Retorna o atributo de CoreExt_Entity para recuperar o valor de um item
   * de formulário.
   * @param string $key
   * @return mixed
   */
  protected function _getEntity($key)
  {
    return $this->_formMap[$key]['entity'];
  }

  /**
   * Retorna um label de um item de formulário através do nome de um atributo de
   * CoreExt_Entity.
   * @param string $key
   * @return string
   */
  protected function _getEntityLabel($key)
  {
    foreach ($this->_formMap as $oKey => $map) {
      if ($key == $map['entity'] || $key == $oKey) {
        return $map['label'];
      }
    }
  }

  /**
   * @see Core_Controller_Page_Validatable#getValidators()
   */
  public function getValidators()
  {
    return array();
  }

  /**
   * Sobrescreve o método Inicializar() de clsCadastro com operações padrões
   * para o caso de uma CoreExt_Entity que use o campo identidade id.
   *
   * Seu comportamento pode ser alterado sobrescrevendo-se os métodos _initNovo
   * e _initEditar.
   *
   * O retorno desse método é usado em RenderHTML() que define qual método de
   * sua API (Novo, Editar, Excluir ou Gerar) será chamado.
   *
   * @return string
   * @see    clsCadastro#RenderHTML()
   * @see    clsCadastro#Inicializar()
   * @todo   Controle de permissão
   */
  public function Inicializar()
  {
    if ($this->_initNovo()) {
      return "Novo";
    }

    if ($this->getOption('save_action')) {
      $this->_hasPermissaoCadastra();
    }

    // Habilita botão de exclusão de registro
    if ($this->getOption('delete_action')) {
      $this->fexcluir = $this->_hasPermissaoExcluir();
    }

    if ($this->_initEditar()) {
      return "Editar";
    }
  }

  /**
   * Verifica se o usuário possui privilégios de cadastro para o processo.
   * @return bool|void Redireciona caso a opção 'nivel_acesso_insuficiente' seja
   *   diferente de NULL.
   */
  protected function _hasPermissaoCadastra()
  {
    return $this->getClsPermissoes()->permissao_cadastra(
      $this->getBaseProcessoAp(),
      $this->getOption('id_usuario'),
      $this->getOption('nivel_acesso'),
      $this->getOption('nivel_acesso_insuficiente')
    );
  }

  /**
   * Verifica se o usuário possui privilégios de cadastro para o processo.
   * @return bool
   */
  protected function _hasPermissaoExcluir()
  {
    return $this->getClsPermissoes()->permissao_excluir(
      $this->getBaseProcessoAp(),
      $this->getOption('id_usuario'),
      $this->getOption('nivel_acesso')
    );
  }

  /**
   * Setter.
   * @param clsPemissoes $instance
   * @return CoreExt_Controller_Page_Abstract Provê interface fluída
   */
  public function setClsPermissoes(clsPermissoes $instance)
  {
    $this->_clsPermissoes = $instance;
    return $this;
  }

  /**
   * Getter.
   * @return clsPermissoes
   */
  public function getClsPermissoes()
  {
    if (is_null($this->_clsPermissoes)) {
      require_once 'include/pmieducar/clsPermissoes.inc.php';
      $this->setClsPermissoes(new clsPermissoes());
    }
    return $this->_clsPermissoes;
  }

  /**
   * Hook de execução para verificar se CoreExt_Entity é novo. Verifica
   * simplesmente se o campo identidade foi passado na requisição HTTP e, se não
   * for, cria uma instância de CoreExt_Entity vazia.
   *
   * @return bool
   */
  protected function _initNovo()
  {
    if (!isset($this->getRequest()->id)) {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance());
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Hook de execução para verificar se CoreExt_Entity é existente através do
   * campo identidade passado pela requisição HTTP.
   *
   * @return bool
   */
  protected function _initEditar()
  {
    try {
      $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
    } catch(Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Insere um novo registro no banco de dados e redireciona para a página
   * definida pela opção "new_success".
   * @see clsCadastro#Novo()
   */
  public function Novo()
  {
    if ($this->_save()) {
      $params = '';
      if (0 < count($this->getOption('new_success_params')) &&
          is_array($this->getOption('new_success_params'))) {
        $params = '?' . http_build_query($this->getOption('new_success_params'));
      }

      $this->redirect($this->getDispatcher()->getControllerName() . '/' .
      $this->getOption('new_success') . $params);
    }
    return FALSE;
  }

  /**
   * Atualiza um registro no banco de dados e redireciona para a página
   * definida pela opção "edit_success".
   *
   * Possibilita o uso de uma query string padronizada, usando o array
   * armazenado na opção "edit_success_params"
   *
   * @see clsCadastro#Editar()
   */
  public function Editar()
  {
    if ($this->_save()) {
      if (0 < count($this->getOption('edit_success_params')) &&
          is_array($this->getOption('edit_success_params'))) {
        $params = http_build_query($this->getOption('edit_success_params'));
      }
      else {
        $params = 'id=' . floatval($this->getEntity()->id);
      }
      $this->redirect($this->getDispatcher()->getControllerName() . '/'
                      . $this->getOption('edit_success')
                      . '?' . $params);
    }
    return FALSE;
  }

  /**
   * Apaga um registro no banco de dados e redireciona para a página indicada
   * pela opção "delete_success".
   * @see clsCadastro#Excluir()
   */
  function Excluir()
  {
    if (isset($this->getRequest()->id)) {
      if ($this->getDataMapper()->delete($this->getRequest()->id)) {
        if (is_array($this->getOption('delete_success_params'))) {
          $params = http_build_query($this->getOption('delete_success_params'));
        }

        $this->redirect(
          $this->getDispatcher()->getControllerName() . '/' .
          $this->getOption('delete_success') .
          (isset($params) ? '?' . $params : '')
        );
      }
    }
    return FALSE;
  }

  /**
   * Implementa uma rotina de criação ou atualização de registro padrão para
   * uma instância de CoreExt_Entity que use um campo identidade.
   * @return bool
   * @todo Atualizar todas as Exception de CoreExt_Validate, para poder ter
   *   certeza que o erro ocorrido foi gerado de alguma camada diferente, como
   *   a de conexão com o banco de dados.
   */
  protected function _save()
  {
    $data = array();

    foreach ($_POST as $key => $val) {
      if (array_key_exists($key, $this->_formMap)) {
        $data[$key] = $val;
      }
    }

    // Verifica pela existência do field identity
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $entity = $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
    }

    if (isset($entity)) {
      $this->getEntity()->setOptions($data);
    }
    else {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance($data));
    }

    try {
      $this->getDataMapper()->save($this->getEntity());
      return TRUE;
    }
    catch (Exception $e) {
      // TODO: ver @todo do docblock
      $this->mensagem = 'Erro no preenchimento do formulário. ';
      return FALSE;
    }
  }
}