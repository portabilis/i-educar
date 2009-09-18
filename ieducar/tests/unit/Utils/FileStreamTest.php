<?php

/*
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
 * @package     Utils
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'UnitBaseTest.class.php';
require_once 'vfsStream/vfsStream.php';
require_once 'Utils/FileStream.class.php';
require_once 'Utils/Mimetype.class.php';


/**
 * FileStreamTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Utils
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class FileStreamTest extends UnitBaseTest
{
  protected $Mimetype = NULL;

  protected function setUp()
  {
    $this->Mimetype = new Mimetype();
  }

  protected function configVfs()
  {
    vfsStreamWrapper::register();
    vfsStreamWrapper::setRoot(new vfsStreamDirectory('tmp'));
  }

  public function testAllowedDirectory()
  {
    $directories = array('pdf', 'tmp');

    $fileStream = new FileStream($this->Mimetype, $directories);
    $this->assertTrue($fileStream->isDirectoryAllowed('pdf'));
  }

  public function testDisallowedDirectory()
  {
    $fileStream = new FileStream($this->Mimetype);
    $this->assertFalse($fileStream->isDirectoryAllowed('pdf'));
  }

  /**
   * @expectedException Exception
   */
  public function testSetFileDirectoryDisallowed()
  {
    $filename = 'pdf/example.pdf';
    $fileStream = new FileStream($this->Mimetype, array('tmp'));
    $fileStream->setFilepath($filename);
  }

  /**
   * @expectedException Exception
   */
  public function testSetFileDirectoryAllowed()
  {
    $filename = 'tmp/example.pdf';
    $fileStream = new FileStream($this->Mimetype, array('tmp'));
    $fileStream->setFilepath($filename);
  }

  public function testSetFile()
  {
    $this->configVfs();

    // Nome do arquivo
    $filename = 'tmp/example.pdf';

    // Adiciona o schema vfs:// ao caminho do arquivo
    $filepath = vfsStream::url($filename);

    // Cria um novo arquivo no vfs
    fopen($filepath, 'a+');

    // Pega o nome do diretório e coloca em array
    $directory = (array) vfsStream::url(vfsStreamWrapper::getRoot()->getName());

    $fileStream = new FileStream($this->Mimetype, $directory);
    $fileStream->setFilepath($filepath);
  }

  /**
   * @expectedException Exception
   */
  public function testStreamFileExtensionNotSupported()
  {
    $this->configVfs();
    $filename = 'tmp/example.dummy';
    $filepath = vfsStream::url($filename);
    fopen($filepath, 'a+');
    $directory = (array) vfsStream::url(vfsStreamWrapper::getRoot()->getName());

    $stub = $this->getMock('Mimetype');
    $stub->expects($this->once())
         ->method('getType')
         ->will($this->returnValue(FALSE));

    $fileStream = new FileStream($stub, $directory);
    $fileStream->setFilepath($filepath);

    $fileStream->streamFile();
  }

  /**
   * Tag disponível apenas no PHPUnit 3.4, ainda não disponível no pacote
   * Pear PHPUnit-beta3. Note o uso do '@' para supressão das mensagens de
   * erro.
   *
   * @outputBuffering enabled
   */
  public function testStreamFileExtensionSupported()
  {
    $this->configVfs();
    $filename = 'tmp/example.pdf';
    $filepath = vfsStream::url($filename);
    fopen($filepath, 'a+');
    $directory = (array) vfsStream::url(vfsStreamWrapper::getRoot()->getName());

    $stub = $this->getMock('Mimetype');
    $stub->expects($this->once())
         ->method('getType')
         ->will($this->returnValue('application/pdf'));

    $fileStream = new FileStream($stub, $directory);
    $fileStream->setFilepath($filepath);
    @$fileStream->streamFile();
  }
}