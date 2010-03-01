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
 * @package   Tests
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Runner/IncludePathTestCollector.php';

/**
 * TestCollector abstract class.
 *
 * Classe abstrata que provê um ponto de extensão para classes de definição de
 * suíte de testes do PHPUnit (veja {@link Orga})
 *
 * Ao estender corretamente essa classe, todos as classes de teste do diretório
 * da classe de definição de suíte de testes serão adicionados à suíte,
 * tornando desnecessário a nessidade de usar os construtores de linguagem
 * require e include para incluir esses arquivos.
 *
 * Para estender essa classe, basta informar o caminho para o arquivo da classe
 * de definição da suíte na variável protegida $_file, exemplo:
 *
 * <code>
 * class App_Model_AllTests extends TestCollector
 * {
 *   protected $_file = __FILE__;
 * }
 * </code>
 *
 * Isso é o suficiente para conseguir coletar todos os arquivos do diretório.
 * Para criar uma suíte de testes com todas as classes de teste do diretório,
 * basta criar uma instância da classe e chamar o método addDirectoryTests():
 *
 * <code>
 * public static function suite()
 * {
 *   $instance = new self();
 *   return $instance->createTestSuite('App_Model: testes unitários')
 *                   ->addDirectoryTests();
 * }
 * </code>
 *
 * Se a variável de instância $_name estiver sobrescrita, ela será utilizada
 * por padrão caso o método createTestSuite() seja chamado sem o parâmetro nome.
 * Dessa forma, basta chamar o método addDirectoryTests():
 *
 * <code>
 * protected $_name = 'App_model: testes unitários';
 *
 * public static function suite()
 * {
 *   $instance = new self();
 *   return $instance->addDirectoryTests();
 * }
 * </code>
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Tests
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class TestCollector
{
  /**
   * Caminho completo do arquivo da classe que estende TestCollector.
   * @var string
   */
  protected $_file = NULL;

  /**
   * Diretório onde residem os arquivos com as classes de teste.
   * @var array
   */
  protected $_directory = array();

  /**
   * Nome da suíte de testes.
   * @var string
   */
  protected $_name  = '';

  /**
   * Uma instância de PHPUnit_Framework_TestSuite.
   * @var PHPUnit_Framework_TestSuite
   */
  protected $_suite = NULL;

  /**
   * Construtor.
   * @return TestCollector
   */
  public function __construct()
  {
    $this->_defineCurrentDirectory();
  }

  /**
   * Cria um objeto PHPUnit_Framework_TestSuite com o nome passado como
   * argumento ou usando a variável de instância $_name.
   *
   * @param   string  $name  O nome para a suíte de testes
   * @return  TestCollector  Interface fluída
   * @throws  InvalidArgumentException
   */
  public function createTestSuite($name = '')
  {
    if ((trim((string) $name)) == '' && $this->_name == '') {
      throw new InvalidArgumentException('A classe concreta deve sobrescrever a '
                . 'variável "$_name" ou passar um nome válido ao chamar o método'
                . 'createTestSuite().');
    }
    if (trim((string) $name) != '') {
      $name = $this->_name;
    }

    $this->_suite = new PHPUnit_Framework_TestSuite($name);
    return $this;
  }

  /**
   * Adiciona os testes do diretório da classe de definição de suíte.
   *
   * @param   PHPUnit_Framework_TestSuite  $suite
   * @return  PHPUnit_Framework_TestSuite
   */
  public function addDirectoryTests(PHPUnit_Framework_TestSuite $suite = NULL)
  {
    // Se não existir um objeto PHPUnit_Framework_TestSuite, cria um com o nome
    // do arquivo da classe de definição da suíte
    if ($this->_suite == NULL && $suite == NULL) {
      $this->createTestSuite();
    }
    if ($suite == NULL) {
      $suite = $this->_suite;
    }

    $suite->addTestFiles($this->_collectTests());
    return $suite;
  }

  /**
   * Retorna um PHPUnit_Util_FilterIterator que contém as regras de inclusão
   * de testes do diretório definido por $_fir.
   *
   * @return PHPUnit_Util_FilterIterator
   */
  protected function _collectTests()
  {
    $testCollector = new PHPUnit_Runner_IncludePathTestCollector($this->_directory);
    return $testCollector->collectTests();
  }

  /**
   * Define o diretório atual da classe que estende TestCollector. O diretório é
   * definido pela variável de instância $_file.
   *
   * @throws  Exception  Lança exceção
   * @todo    Refatorar o código para utilizar {@link http://php.net/lsb Late static binding}
   *          quando a versão do PHP for a 5.3.
   */
  protected function _defineCurrentDirectory()
  {
    if ($this->_file === NULL) {
      throw new Exception('A classe concreta deve sobrescrever a variável "$_file".');
    }
    $directory = $this->_getDirectoryPath($this->_file);
    if (!array_search($directory, $this->_directory)) {
      $this->_directory[] = $directory;
    }
  }

  /**
   * Pega o caminho do diretório que será varrido para a inclusão de testes.
   * @param  string $path
   * @return string
   */
  protected function _getDirectoryPath($path)
  {
    $directory = realpath(dirname($path));
    if (!is_dir($directory)) {
      throw new Exception('The path "'. $directory .'" is not a valid directory');
    }
    return $directory;
  }

  public function addDirectory($directory)
  {
    $this->_directory[] = $this->_getDirectoryPath($directory);
  }
}