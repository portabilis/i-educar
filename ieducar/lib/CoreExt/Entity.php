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
 * @package   CoreExt_Entity
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Entity/Validatable.php';

/**
 * CoreExt_Entity abstract class.
 *
 * Um layer supertype para objetos da camada de domínio de todos os namespaces
 * da aplicação.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://martinfowler.com/eaaCatalog/layerSupertype.html
 * @package   CoreExt_Entity
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Possibilitar uso opcional do campo identidade, útil para casos
 *   de compound primary keys
 * @version   @@package_version@@
 */
abstract class CoreExt_Entity implements CoreExt_Entity_Validatable
{
  /**
   * Define se uma instância é "nova" ou "velha" (caso seja carregada via
   * CoreExt_DataMapper).
   * @var bool
   */
  protected $_new = TRUE;

  /**
   * Array associativo onde os índices se comportarão como atributos públicos
   * graças a implementação dos métodos mágicos de overload.
   *
   * @var array
   */
  protected $_data = array();

  /**
   * Array associativo onde os índices identificam o tipo de dado de uma
   * propriedade pública tal qual declarada em $_data.
   *
   * @var array
   * @see _getValue() Contém a lista de tipos atualmente suportados
   */
  protected $_dataTypes = array();

  /**
   * @var CoreExt_DataMapper
   */
  protected $_dataMapper = NULL;

  /**
   * Array associativo para referências a objetos que serão carregados com
   * lazy load.
   *
   * Uma reference pode ser de um dos tipos:
   * - CoreExt_DataMapper
   * - CoreExt_Enum
   *
   * Toda vez que uma reference for requisitada e acessada pela primeira vez,
   * ela não fará mais lookups (buscas SQL, acesso a arrays de valores).
   *
   * <code>
   * <?php
   * $_references = array(
   *   'area' => array(
   *     'value' => 1,
   *     'class' => 'AreaConhecimento_Model_AreaDataMapper',
   *     'file'  => 'AreaConhecimento/Model/AreaDataMapper.php'
   *   )
   * );
   * </code>
   *
   * @link http://martinfowler.com/eaaCatalog/lazyLoad.html Lazy load
   * @var array
   */
  protected $_references = array();

  /**
   * Coleção de validadores para as propriedades $_data de CoreExt_Entity.
   * @var array
   */
  protected $_validators = array();

  /**
   * Coleção de mensagens de erro retornado pelos validadores de $_validators
   * durante a execução do método isValid().
   *
   * @var array
   */
  protected $_errors = array();

  /**
   * Array com instâncias para classes pertecentes ao namespace iEd_*.
   *
   * <code>
   * <?php
   * $_classStorage = array(
   *   'stdclass' => array(
   *     'class'    => 'stdClass',
   *     'instance' => NULL,
   *   )
   * );
   * </code>
   *
   * @see CoreExt_Entity#addClassToStorage($class, $instance = NULL, $file = NULL)
   * @var array
   */
  protected static $_classStorage = array();

  /**
   * @var CoreExt_Locale
   */
  protected $_locale = NULL;

  /**
   * Construtor.
   *
   * @param array $options Array associativo para inicializar os valores dos
   *   atributos do objeto
   */
  public function __construct($options = array())
  {
    $this->_createIdentityField()
         ->setOptions($options);
  }

  /**
   * Adiciona um campo identidade como atributo da instância.
   *
   * @link   http://martinfowler.com/eaaCatalog/identityField.html
   * @return CoreExt_Entity Provê interface fluída
   */
  protected function _createIdentityField()
  {
    $id = array('id' => NULL);
    $this->_data = array_merge($id, $this->_data);
    return $this;
  }

  /**
   * Atribui valor para cada atributo da classe que tenha correspondência com
   * o indice do array $options passado como argumento.
   *
   * @param  array $options
   * @return CoreExt_Entity Provê interface fluída
   */
  public function setOptions($options = array())
  {
    foreach ($options as $key => $value) {
      $this->$key = $value;
    }
    return $this;
  }

  /**
   * Implementação do método mágico __set().
   *
   * Esse método é um pouco complicado devido a lógica de configuração das
   * referências para lazy loading.
   *
   * @link   http://php.net/manual/en/language.oop5.overloading.php
   * @param  string $key
   * @param  mixed  $val
   * @return bool|NULL TRUE caso seja uma referência válida ou NULL para o fluxo
   *   normal do método
   */
  public function __set($key, $val)
  {
    if ($this->_hasReference($key)) {
      // Se houver uma referência e ela pode ser NULL, atribui NULL quando
      // a referência for carregada por CoreExt_DataMapper (new = FALSE).
      // Se for uma referência a CoreExt_DataMapper, 0 será equivalente a NULL.
      // Aqui, nem instância tem, nem lazy load acontecerá.
      if (
        isset($this->_references[$key]['null']) &&
        TRUE == $this->_references[$key]['null'] &&
        (
          is_null($val) || (FALSE == $this->_new && "NULL" == $val)
          || ($this->_isReferenceDataMapper($key) && (is_numeric($val) && 0 == $val))
        )
      ) {
        $this->_references[$key]['value'] = NULL;
        return TRUE;
      }

      // Se a referência for numérica, usa-a, marcando apenas a referência e
      // deixando o atributo NULL para o lazy load.
      if (is_numeric($val)) {
        $this->_references[$key]['value'] = $this->_getValue($key, $val);
        return TRUE;
      }

      // Se for uma instância de CoreExt_Entity e tiver um identificador,
      // usa-o. Referências sem um valor poderão ser consideradas como novas
      // numa implementação de save() de CoreExt_DataMapper que leve em
      // consideração as referências, salvando-as ou atualizando-as.
      elseif ($val instanceof CoreExt_Entity && isset($val->id)) {
        $this->_references[$key]['value'] = $this->_getValue($key, $val->id);
        // Não retorna, queremos aproveitar a instância para não mais carregá-la
        // em __get().
      }

      // Aqui, identificamos que o atributo não se encaixa em nenhum dos itens
      // anteriores, lançando um Exceção. Como CoreExt_Enum não contém um
      // estado (o valor corrente, por ser um Enum!), aceitamos apenas
      // instâncias de CoreExt_Entity como parâmetro
      elseif (!($val instanceof CoreExt_Entity)) {
        require_once 'CoreExt/Exception/InvalidArgumentException.php';
        throw new CoreExt_Exception_InvalidArgumentException('O argumento passado para o atributo "' . $key
                  . '" é inválido. Apenas os tipos "int" e "CoreExt_Entity" são suportados.');
      }
    }

    // Se o atributo não existir, lança exceção
    if (!array_key_exists($key, $this->_data)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException('A propriedade '
                . $key . ' não existe em ' . __CLASS__ . '.');
    }

    // Se for string vazia, o valor é NULL
    if ('' == trim($val)) {
      $this->_data[$key] = NULL;
    }
    // Chama _getValue(), para fazer conversões que forem necessárias
    else {
      $this->_data[$key] = $this->_getValue($key, $val);
    }
  }

  /**
   * Implementação do método mágico __get().
   *
   * @link   http://php.net/manual/en/language.oop5.overloading.php
   * @param  string $key
   * @return mixed
   */
  public function __get($key)
  {
    if ('id' === $key) {
      return floatval($this->_data[$key]) > 0  ?
        floatval($this->_data[$key]) : NULL;
    }

    if ($this->_hasReference($key) && !isset($this->_data[$key])) {
      $this->_data[$key] = $this->_loadReference($key);
    }

    return $this->_data[$key];
  }

  /**
   * Getter. Não resolve referências com lazy load, ao invés disso, retorna
   * o valor da referência.
   *
   * @param  string $key
   * @return mixed
   */
  public function get($key)
  {
    if ($this->_hasReference($key)) {
      return $this->_getReferenceValue($key);
    }
    return $this->__get($key);
  }


  /**
   * Implementação do método mágico __isset().
   *
   * @link   http://php.net/manual/en/language.oop5.overloading.php
   * @param  string $key
   * @return bool
   */
  public function __isset($key)
  {
    $val = $this->get($key);
    return isset($val);
  }

  /**
   * Implementação do método mágico __unset().
   *
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string $key
   */
  public function __unset($key)
  {
    $this->_data[$key] = NULL;
  }

  /**
   * Implementação do método mágico __toString().
   *
   * @link http://br2.php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
   * @return string
   */
  public function __toString()
  {
    return get_class($this);
  }


  /**
   * Carrega um objeto de uma referência, usando o CoreExt_DataMapper
   * especificado para tal.
   *
   * @param  string $key
   * @return CoreExt_Entity
   * @todo   Se mais classes puderem ser atribuídas como references, implementar
   *         algum design pattern para diminuir a complexidade ciclomática
   *         desse método e de setReferenceClass().
   * @todo   Caso a classe não seja um CoreExt_DataMapper ou CoreExt_Enum,
   *         lançar uma Exception.
   * @todo   Referências CoreExt_Enum só podem ter seu valor atribuído na
   *         instanciação. Verificar se isso é desejado e ver possibilidade
   *         de flexibilizar esse comportamente. Ver para CoreExt_DataMapper
   *         também.
   */
  protected function _loadReference($key)
  {
    $reference = $this->_references[$key];

    // Se a referência tiver valor NULL
    $value = $reference['value'];
    if (in_array($value, array(NULL), true)) {
      return $value;
    }

    // Verifica se a API da classe para saber qual tipo de instanciação usar
    $class = $reference['class'];
    if ($this->_isReferenceDataMapper($key)) {
      $class  = new $class();
    }
    elseif ($this->_isReferenceEnum($key)) {
      $class = $class . '::getInstance()';
      eval('?><?php $class = ' . $class . '?>');
    }

    // Faz a chamada a API, recupera o valor original (objetos). Usa a instância
    // da classe.
    if ($class instanceof CoreExt_DataMapper) {
      $return = $class->find($value);
    }
    elseif ($class instanceof CoreExt_Enum) {
      if (!isset($class[$value])) {
        return NULL;
      }
      $return = $class[$value];
    }

    return $return;
  }

  /**
   * Verifica se existe uma referência para uma certa chave $key.
   * @param string $key
   * @return bool
   */
  protected function _hasReference($key)
  {
    return array_key_exists($key, $this->_references);
  }

  /**
   * Configura ou adiciona uma nova referência para possibilitar o lazy load
   * entre objetos.
   *
   * @param  string  $key   O nome do atributo que mapeia para a referência
   * @param  array   $data  O array com a especificação da referência
   * @return CoreExt_Entity Provê interface fluída
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public function setReference($key, $data)
  {
    if (!array_key_exists($key, $this->_data)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException('Somente é possível '
                . 'criar referências para atributos da classe.');
    }

    $layout = array('value' => NULL, 'class' => NULL, 'file' => NULL, 'null' => NULL);

    $options       = array_keys($layout);
    $passedOptions = array_keys($data);

    if (0 < count($diff = array_diff($passedOptions, $options))) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException("" . implode(', ', $diff));
    }

    if (!array_key_exists($key, $this->_references)) {
      $this->_references[$key] = $layout;
    }
    if (isset($data['value'])) {
      $this->setReferenceValue($key, $data['value']);
    }
    if (isset($data['class'])) {
      $this->setReferenceClass($key, $data['class']);
    }
    if (isset($data['file'])) {
      $this->setReferenceFile($key, $data['file']);
    }

    return $this;
  }

  /**
   * Setter para o valor de referência de uma reference.
   * @param  string $key
   * @param  int    $value
   * @return CoreExt_Entity Provê interface fluída
   */
  public function setReferenceValue($key, $value)
  {
    $this->_references[$key]['value'] = (int) $value;
    return $this;
  }

  /**
   * Setter para uma classe ou nome de classe de um CoreExt_DataMapper da
   * reference.
   * @param  string $key
   * @param  CoreExt_DataMapper|CoreExt_Enum|string $class
   * @return CoreExt_Entity Provê interface fluída
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public function setReferenceClass($key, $class)
  {
    if (!is_string($class) && !($class instanceof CoreExt_DataMapper || $class instanceof CoreExt_Enum)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException('Uma classe de referência '
                . ' precisa ser especificada pelo seu nome (string), ou, uma instância de CoreExt_DataMapper ou CoreExt_Enum.');
    }
    $this->_references[$key]['class'] = $class;
    return $this;
  }

  /**
   * Setter para o arquivo da classe CoreExt_DataMapper da classe de reference
   * informada por setReferenceClass.
   * @param  string $key
   * @param  int    $value
   * @return CoreExt_Entity Provê interface fluída
   */
  public function setReferenceFile($key, $file)
  {
    $this->_references[$key]['file'] = $file;
    return $this;
  }

  /**
   * Getter.
   * @param  string $key
   * @return mixed
   */
  protected function _getReferenceValue($key)
  {
    return $this->_references[$key]['value'];
  }

  /**
   * Getter.
   * @param string $key
   * @return string
   */
  protected function _getReferenceClass($key)
  {
    return $this->_references[$key]['class'];
  }

  /**
   * Verifica se a classe da referência é uma instância de CoreExt_DataMapper.
   * @param string $key
   * @return bool
   */
  protected function _isReferenceDataMapper($key)
  {
    $class = $this->_getReferenceClass($key);
    return $this->_isReferenceOf($class, $this->_references[$key]['file'],
      'CoreExt_DataMapper');
  }

  /**
   * Verifica se a classe da referência é uma instância de CoreExt_Enum.
   * @param string $key
   * @return bool
   */
  protected function _isReferenceEnum($key)
  {
    $class = $this->_getReferenceClass($key);
    return $this->_isReferenceOf($class, $this->_references[$key]['file'],
      'CoreExt_Enum');
  }

  /**
   * Verifica se a referência é subclasse de $parentClass.
   *
   * @param string $subClass
   * @param string $subClassFile
   * @param string $parentClass
   * @return bool
   */
  private function _isReferenceOf($subClass, $subClassFile, $parentClass)
  {
    static $required = array();

    if (is_string($subClass)) {
      if (!in_array($subClassFile, $required)) {
        // Inclui o arquivo com a definição de subclasse para que o interpretador
        // tenha o símbolo de comparação.
        require_once $subClassFile;
        $required[] = $subClassFile;
      }
      return (is_subclass_of($subClass, $parentClass));
    }
    return FALSE;
  }

  /**
   * Setter.
   * @param CoreExt_DataMapper $dataMapper
   * @return CoreExt_Entity
   */
  public function setDataMapper(CoreExt_DataMapper $dataMapper)
  {
    $this->_dataMapper = $dataMapper;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_DataMapper|NULL
   */
  public function getDataMapper()
  {
    return $this->_dataMapper;
  }

  /**
   * Adiciona uma classe para o repositório de classes estático, instanciando
   * caso não seja passada uma instância explícita e carregando o arquivo
   * em que a classe está armazenada caso seja informado.
   *
   * Quando uma instância não é passada explicitamente, verifica-se se a
   * instância já existe, retornado-a caso positivo e/ou instanciando uma nova
   * (sem passar argumentos para seu construtor) e retornando-a.
   *
   * Permite armazenar apenas uma instância de uma classe por vez. Por usar
   * armazenamento estático, pode ter efeitos indesejados ao ser usado por
   * diferentes objetos.
   *
   * Caso seja necessário instanciar a classe passando argumentos ao seu
   * construtor, instancie a classe e passe a referencia na chamada ao método:
   *
   * <code>
   * <?php
   * $obj = new CoreExt_Entity(array('key1' => 'value1'));
   * CoreExt_Entity::addClassToStorage('CoreExt_Entity', $obj);
   * </code>
   *
   * @param  string  $class     O nome da classe
   * @param  mixed   $instance  Uma instância da classe
   * @param  string  $file      O nome do arquivo onde se encontra a classe
   * @param  bool    $sticky    Se a instância da classe de ser "grundenda",
   *   não podendo ser posteriormente substituída por uma chamada subsequente
   * @return mixed
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public static function addClassToStorage($class, $instance = NULL, $file = NULL, $sticky = FALSE)
  {
    $search = strtolower($class);
    if (TRUE === array_key_exists($search, self::$_classStorage)) {
      self::_setStorageClassInstance($search, $instance, $sticky);
    }
    else {
      if (!is_null($file)) {
        require_once $file;
      }
      self::$_classStorage[$search] = array(
        'class' => $class,
        'instance' => NULL,
        'sticky' => FALSE
      );
      self::_setStorageClassInstance($class, $instance, $sticky);
    }
    return self::$_classStorage[$search]['instance'];
  }

  /**
   * Instancia uma classe de $class ou atribui uma instância passada
   * explicitamente para o repositório de classes estático.
   *
   * @param string $class
   * @param mixed $instance
   * @return mixed
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  protected static function _setStorageClassInstance($class, $instance = NULL, $sticky = FALSE)
  {
    if (!is_null($instance)) {
      if (!($instance instanceof $class)) {
        require_once 'CoreExt/Exception/InvalidArgumentException.php';
        throw new CoreExt_Exception_InvalidArgumentException('A instância '
                  . 'passada como argumento precisa ser uma instância de "' . $class . '".');
      }

      if (FALSE == self::$_classStorage[strtolower($class)]['sticky']) {
        self::$_classStorage[strtolower($class)]['instance'] = $instance;
        self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
      }
      // Se for sticky, só sobrescreve por outro
      elseif (TRUE == self::$_classStorage[strtolower($class)]['sticky'] && TRUE == $sticky) {
        self::$_classStorage[strtolower($class)]['instance'] = $instance;
        self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
      }
    }
    else {
      if (is_null(self::$_classStorage[strtolower($class)]['instance'])) {
        self::$_classStorage[strtolower($class)]['instance'] = new $class();
        self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
      }
    }
  }

  /**
   * Getter.
   * @param string $class
   * @return mixed|NULL
   */
  public static function getClassFromStorage($class)
  {
    if (self::hasClassInStorage($class)) {
      return self::$_classStorage[strtolower($class)]['instance'];
    }
    return NULL;
  }

  /**
   * Verifica se uma classe existe no repositório de classes estático.
   * @param string $class
   * @return bool
   */
  public static function hasClassInStorage($class)
  {
    if (array_key_exists(strtolower($class), self::$_classStorage)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Setter.
   * @param CoreExt_Locale $instance
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setLocale(CoreExt_Locale $instance)
  {
    $this->_locale = $instance;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_Locale
   */
  public function getLocale()
  {
    if (is_null($this->_locale)) {
      require_once 'CoreExt/Locale.php';
      $this->setLocale(CoreExt_Locale::getInstance());
    }
    return $this->_locale;
  }

  /**
   * Verifica se a propriedade informada por $key é válida, executando o
   * CoreExt_Validate_Interface relacionado.
   *
   * Utiliza lazy initialization para inicializar os validadores somente quando
   * necessário.
   *
   * @link    http://martinfowler.com/eaaCatalog/lazyLoad.html Lazy initialization
   * @param   string  $key  Propriedade a ser validade. Caso seja string vazia,
   *   executa todos os validadores da instância
   * @return  bool
   * @see     CoreExt_Validate_Validatable#isValid($key)
   */
  public function isValid($key = '')
  {
    $this->_setDefaultValidatorCollection()
         ->_setDefaultErrorCollectionItems();

    $key = trim($key);
    $return = NULL;

    if ('' != $key && !is_null($this->getValidator($key))) {
      $return = $this->_isValidProperty($key);
    }
    elseif ('' === $key) {
      $return = $this->_isValidEntity();
    }

    return $return;
  }

  /**
   * Verifica se uma instância é nula, isto é, quando todos os seus atributos
   * tem o valor NULL.
   *
   * @return bool
   */
  public function isNull()
  {
    $data  = $this->toDataArray();
    $count = count($this->_data);
    $nils  = 0;

    foreach ($data as $value) {
      if (is_null($value)) {
        $nils++;
      }
    }

    if ($nils == $count) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Alias para setNew(FALSE).
   * @return CoreExt_Entity Provê interface fluída
   */
  public function markOld()
  {
    return $this->setNew(FALSE);
  }

  /**
   * Setter.
   * @param bool $new
   * @return CoreExt_Entity Provê interface fluída
   */
  public function setNew($new)
  {
    $this->_new = (bool) $new;
    return $this;
  }

  /**
   * Verifica se a instância é "nova".
   * @return bool
   * @see CoreExt_Entity#_new
   */
  public function isNew()
  {
    return $this->_new;
  }

  /**
   * Verifica se uma propriedade da classe é válida de acordo com um validador
   * CoreExt_Validate_Interface.
   *
   * Utiliza o valor sanitizado pelo validador como valor de atributo.
   *
   * @param  string $key
   * @return bool
   */
  protected function _isValidProperty($key)
  {
    try {
      $this->getValidator($key)->isValid($this->get($key));
      $this->$key = $this->getValidator($key)->getSanitizedValue();
      return TRUE;
    }
    catch (Exception $e) {
      $this->_setError($key, $e->getMessage());
      return FALSE;
    }
  }

  /**
   * Verifica se todas as propriedades da classe são válida de acordo com uma
   * coleção de validadores CoreExt_Validate_Interface.
   *
   * @return bool
   */
  protected function _isValidEntity()
  {
    $return = TRUE;

    // Como eu quero todos os erros de validação, apenas marco $return como
    // FALSE e deixo o iterador exaurir.
    foreach ($this->getValidatorCollection() as $key => $validator) {
      if (FALSE === $this->_isValidProperty($key)) {
        $return = FALSE;
      }
    }

    return $return;
  }

  /**
   * @see CoreExt_Validate_Validatable#setValidator($key, $validator)
   */
  public function setValidator($key, CoreExt_Validate_Interface $validator)
  {
    if (!array_key_exists($key, $this->_data)) {
      throw new Exception('A propriedade ' . $key . ' não existe em ' . __CLASS__ . '.');
    }

    $this->_validators[$key] = $validator;
    $this->_setError($key, NULL);
    return $this;
  }

  /**
   * @see CoreExt_Validate_Validatable#getValidator($key)
   */
  public function getValidator($key)
  {
    $return = NULL;

    if (isset($this->_validators[$key])) {
      $return = $this->_validators[$key];
    }

    return $return;
  }

  /**
   * @param $overwrite TRUE para que as novas instâncias sobrescrevam as já
   *   existentes
   * @see CoreExt_Entity_Validatable#setValidatorCollection($validators)
   */
  public function setValidatorCollection(array $validators, $overwrite = FALSE)
  {
    foreach ($validators as $key => $validator) {
      if ($overwrite == FALSE && !is_null($this->getValidator($key))) {
        continue;
      }
      $this->setValidator($key, $validator);
    }
    return $this;
  }

  /**
   * @see CoreExt_Entity_Validatable#getValidatorCollection()
   */
  public function getValidatorCollection()
  {
    $this->_setDefaultValidatorCollection();
    return $this->_validators;
  }

  /**
   * Configura os validadores padrão da classe.
   * @return CoreExt_Entity Provê interface fluída
   */
  protected function _setDefaultValidatorCollection()
  {
    $this->setValidatorCollection($this->getDefaultValidatorCollection());
    return $this;
  }

  /**
   * Retorna um instância de um validador caso um atributo da instância tenha
   * seu valor igual ao da condição.
   *
   * @param  string $key                 O atributo a ser comparado
   * @param  mixed  $value               O valor para comparação
   * @param  string $validatorClassName  O nome da classe de validação. Deve ser
   *   subclasse de CoreExt_Validate_Abstract
   * @param  array  $equalsParams        Array de opções para o a classe de
   *   validação caso de $key ser igual a $value
   * @param  array  $notEqualsParams     Array de opções para o a classe de
   *   validação caso de $key ser diferente de $value
   * @return CoreExt_Validate_Abstract
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public function validateIfEquals($key, $value = NULL, $validatorClassName,
    array $equalsParams = array(), array $notEqualsParams = array())
  {
    if ($value == $this->get($key)) {
      $params = $equalsParams;
    }
    else {
      $params = $notEqualsParams;
    }

    if (!is_subclass_of($validatorClassName, 'CoreExt_Validate_Abstract')) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException('A classe "'
                . $validatorClassName . '" não é uma subclasse de CoreExt_Validate_Abstract'
                . ' e por isso não pode ser usada como classe de validação.');
    }

    return new $validatorClassName($params);
  }

  /**
   * Configura uma mensagem de erro.
   *
   * @param  string $key
   * @param  string|NULL $message
   * @return CoreExt_Entity Provê interface fluída
   */
  protected function _setError($key, $message = NULL)
  {
    $this->_errors[$key] = $message;
    return $this;
  }

  /**
   * Retorna uma mensagem de erro de validaçao para determinada propriedade.
   *
   * @param  string $key
   * @return mixed
   */
  public function getError($key)
  {
    return $this->_errors[$key];
  }

  /**
   * Retorna um array de mensagens de erro de validação.
   * @return array
   */
  public function getErrors()
  {
    return $this->_errors;
  }

  /**
   * Verifica se uma propriedade tem um erro de validação.
   *
   * @param string $key
   * @return bool
   */
  public function hasError($key)
  {
    if (!is_null($this->getError($key))) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Verifica se houve algum erro de validação geral.
   * @return bool
   */
  public function hasErrors()
  {
    foreach ($this->getErrors() as $key => $error) {
      if ($this->hasError($key)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Configura os itens padrão do array de erros.
   * @return CoreExt_Entity Provê interface fluída
   */
  protected function _setDefaultErrorCollectionItems()
  {
    $items = array_keys($this->getValidatorCollection());
    $this->_errors = array_fill_keys($items, NULL);
    return $this;
  }

  /**
   * Retorna o valor de uma propriedade do objeto convertida para o seu tipo
   * qual definido pelo array $_dataTypes.
   *
   * Atualmente suporte os tipos:
   * - boolean (informado como bool ou boolean)
   * - numeric (converte para número, usando informação do locale atual e
   *  convertendo para número com {@link http://br.php.net/floatval floatval())}
   *
   * <code>
   * <?php
   * class Example extends CoreExtEntity {
   *   protected $_data = array('hasChild' => NULL);
   *   protected $_dataTypes = array('hasChild' => 'bool');
   * }
   * </code>
   *
   * @param  string $key O nome da propriedade
   * @param  mixed  $val O valor original da propriedade
   * @return mixed  O valor convertido da propriedade
   */
  protected function _getValue($key, $val)
  {
    if (!array_key_exists($key, $this->_dataTypes)) {
      // Converte com floatval (que converte para int caso não tenha decimais,
      // para permitir formatação correta com o locale da aplicação)
      if (is_numeric($val)) {
        $val = floatval($val);
      }
      return $val;
    }

    $cmpVal = strtolower($val);
    $return = NULL;

    switch (strtolower($this->_dataTypes[$key])) {
      case 'bool':
      case 'boolean':
        if ($cmpVal == 't') {
          $return = TRUE;
        }
        elseif ($cmpVal == 'f') {
          $return = FALSE;
        }
        else {
          $return = (bool) $cmpVal;
        }
        break;

      case 'numeric':
        $return = $this->getFloat($cmpVal);
        break;

      case 'string':
        $return = (string)$cmpVal;
        break;
    }
    return $return;
  }

  /**
   * Retorna um número float, verificando o locale e substituindo o separador
   * decimal pelo separador compatível com o separador padrão do PHP ("." ponto).
   *
   * @param numeric $value
   * @return float
   */
  public function getFloat($value)
  {
    $locale = $this->getLocale();
    $decimalPoint = $locale->getCultureInfo('decimal_point');

    // Verifica se possui o ponto decimal do locale e substitui para o
    // padrão do locale en_US (ponto ".")
    if (FALSE !== strstr($value, $decimalPoint)) {
      $value = strtr($value, $decimalPoint, '.');
    }

    return floatval($value);
  }

  /**
   * Retorna um array onde o índice é o valor do atributo $atr1 e o valor
   * é o próprio valor do atributo referenciado por $atr2. Se $atr2 não for
   * informado, retorna o valor referenciado por $atr1.
   *
   * Exemplo:
   * <code>
   * <?php
   * // class Pessoa extends CoreExt_Entity
   * protected $_data = array(
   *   'nome' => NULL,
   *   'sobrenome' => NULL
   * );
   *
   * // em um script:
   * $pessoa = new Pessoa(array('id' => 1, 'nome' => 'Carlos Santana'));
   * print_r($pessoa->filterAttr('id' => 'nome');
   *
   * // Iria imprimir:
   * // Array
   * // (
   * //    [1] => Carlos Santana
   * // )
   * </code>
   *
   * @param string $atr1
   * @param string $atr2
   * @return array
   */
  public function filterAttr($atr1, $atr2 = '')
  {
    $data = array();

    if ('' == $atr2) {
      $atr2 = $atr1;
    }

    $data[$this->$atr1] = $this->$atr2;
    return $data;
  }

  /**
   * Retorna um array para cada instância de CoreExt_Entity, onde cada entrada
   * é um array onde o índice é o valor do atributo $atr1 e o valor
   * é o próprio valor do atributo referenciado por $atr2. Se $atr2 não for
   * informado, retorna o valor referenciado por $atr1.
   *
   * @param  CoreExt_Entity|array $instance
   * @param  string $atr1
   * @param  string $atr2
   * @return array
   * @see    CoreExt_Entity#filterAttr($atr1, $atr2)
   */
  public static function entityFilterAttr($instance, $atr1, $atr2 = '')
  {
    $instances = $data = array();

    if (!is_array($instance)) {
      $instances[] = $instance;
    }
    else {
      $instances = $instance;
    }

    foreach ($instances as $instance) {
      $arr = $instance->filterAttr($atr1, $atr2);
      $key = key($arr);
      $data[$key] = $arr[$key];
    }

    return $data;
  }

  /**
   * Retorna o estado (valores dos atributos) da instância em array. Se um
   * atributo for uma referência a um objeto, faz o lazy load do mesmo.
   * @return array
   */
  public function toArray()
  {
    $data = array();
    foreach ($this->_data as $key => $val) {
      $data[$key] = $this->$key;
    }
    return $data;
  }

  /**
   * Retorna o estado (valores dos atributos) da instância. Se um atributo
   * for uma referência a um objeto, retorna o valor da referência.
   * @return array
   */
  public function toDataArray()
  {
    $data = array();
    foreach ($this->_data as $key => $value) {
      if ($this->_hasReference($key)) {
        $data[$key] = $this->_references[$key]['value'];
        continue;
      }
      $data[$key] = $value;
    }
    return $data;
  }
}
