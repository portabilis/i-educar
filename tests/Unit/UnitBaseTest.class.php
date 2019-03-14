<?php

use Tests\TestCase;

abstract class UnitBaseTest extends TestCase
{
  /**
   * Métodos a serem excluídos da lista de métodos a serem mockados por
   * getCleanMock().
   *
   * @var array
   */
  protected $_excludedMethods = array();

  /**
   * Setter para o atributo $_excludedMethods.
   *
   * @param array $methods
   * @return UnitBaseTest Provê interface fluída
   */
  public function setExcludedMethods(array $methods)
  {
    $this->_excludedMethods = $methods;
    return $this;
  }

  /**
   * Getter para o atributo $_excludedMethods.
   * @return array
   */
  public function getExcludedMethods()
  {
    return $this->_excludedMethods;
  }

  /**
   * Reseta o valor do atributo $_excludedMethods.
   *
   * @return TestCase Provê interface fluída
   */
  public function resetExcludedMethods()
  {
    $this->_excludedMethods = array();
    return $this;
  }

  /**
   * Remove os métodos indicados por setExcludedMethods() da lista de métodos
   * a serem mockados.
   *
   * @param array $methods
   * @return array
   */
  protected function _cleanMockMethodList(array $methods)
  {
    foreach ($methods as $key => $method) {
      if (FALSE !== array_search($method, $this->getExcludedMethods())) {
        unset($methods[$key]);
      }
    }
    $this->resetExcludedMethods();
    return $methods;
  }

  /**
   * Retorna um objeto mock do PHPUnit, alterando os valores padrões dos
   * parâmetros $call* para FALSE.
   *
   * Faz uma limpeza da lista de métodos a serem mockados ao chamar
   * _cleanMockMethodList().
   *
   * @param  string  $className
   * @param  array   $mockMethods
   * @param  array   $args
   * @param  string  $mockName
   * @param  bool    $callOriginalConstructor
   * @param  bool    $callOriginalClone
   * @param  bool    $callOriginalAutoload
   * @return PHPUnit\Framework\MockObject\MockObject
   */
  public function getCleanMock($className, array $mockMethods = array(),
    array $args = array(), $mockName = '', $callOriginalConstructor = FALSE,
    $callOriginalClone = FALSE, $callOriginalAutoload = FALSE)
  {
    if (0 == count($mockMethods)) {
      $reflectiveClass = new ReflectionClass($className);
      $methods = $reflectiveClass->getMethods();
      $mockMethods = array();

      foreach ($methods as $method) {
        if (!$method->isFinal() && !$method->isAbstract() && !$method->isPrivate()) {
          $mockMethods[] = $method->name;
        }
      }
    }

    $mockMethods = $this->_cleanMockMethodList($mockMethods);

    if ($mockName == '') {
      $mockName = $className . '_Mock_' . substr(md5(uniqid()), 0, 6);
    }

    return $this->getMockForAbstractClass($className, $args, $mockName,
        $callOriginalConstructor, $callOriginalClone, $callOriginalAutoload, $mockMethods);
  }

  /**
   * Retorna um mock da classe de conexão clsBanco.
   * @return clsBanco
   */
  public function getDbMock()
  {
    // Cria um mock de clsBanco, preservando o código do método formatValues
    return $this->setExcludedMethods(array('formatValues'))
                ->getCleanMock('clsBanco');
  }

  /**
   * Controla o buffer de saída.
   * @param  bool $enable
   * @return bool|string
   */
  public function outputBuffer($enable = TRUE)
  {
    if (TRUE == $enable) {
      ob_start();
      return TRUE;
    }
    else {
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
  }
}
