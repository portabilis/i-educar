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
 */

/**
 * FileStream class.
 *
 * Classe para stream de arquivos implementando checagens de segurança como
 * diretórios permitidos e tipo de arquivo.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe disponível desde a versão 1.1.0
 * @version  $Id$
 */
class FileStream
{

  /**
   * Instância da classe Mimetype
   * @var Mimetype
   */
  protected $Mimetype    = NULL;

  /**
   * Caminho do arquivo para stream
   * @var string
   */
  protected $filepath    = NULL;

  /**
   * Array de diretórios permitidos para stream de arquivos.
   * @var array
   */
  protected $allowedDirs = array();


  /**
   * Construtor.
   *
   * @param  Mimetype  $mimetype     Objeto Mimetype
   * @param  array     $allowedDirs  Diretórios permitidos para stream
   */
  public function __construct(Mimetype $mimetype, array $allowedDirs = array()) {
    $this->Mimetype = $mimetype;
    $this->setAllowedDirectories((array) $allowedDirs);
  }

  /**
   * Configura o nome do arquivo, verificando se o mesmo encontra-se em um
   * diretório de acesso permitido e se é legível.
   *
   * @param   string  $filepath  O caminho completo ou relativo do arquivo
   * @throws  Exception
   */
  public function setFilepath($filepath) {
    $this->isReadable($filepath);
    $this->filepath = $filepath;
  }

  /**
   * Configura os diretórios permitidos para stream de arquivos.
   *
   * @param   array  $v
   */
  protected function setAllowedDirectories($v) {
    $this->allowedDirs = $v;
  }

  /**
   * Verifica se o arquivo é legível e se está em um diretório permitido
   * para stream de arquivos.
   *
   * @param   string  $filepath  O caminho completo ou relativo ao arquivo
   * @throws  Exception
   */
  protected function isReadable($filepath)
  {
    $fileinfo = pathinfo($filepath);

    if (! $this->isDirectoryAllowed($fileinfo['dirname'])) {
      throw new Exception('Acesso ao diretório negado.');
    }

    if (! is_readable($filepath)) {
      throw new Exception('Arquivo não existe.');
    }
  }

  /**
   * Verifica se o diretório está na lista de diretórios permitidos para
   * stream de arquivos.
   *
   * @param   string  $directory
   * @return  bool    Retorna TRUE se o diretório é permitido
   */
  public function isDirectoryAllowed($directory)
  {
    if (FALSE === array_search($directory, $this->allowedDirs)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Faz o stream do arquivo.
   *
   * @throws  Exception
   */
  public function streamFile()
  {
    $mimetype = $this->Mimetype->getType($this->filepath);

    if (FALSE === $mimetype) {
      throw new Exception('Extensão não suportada.');
    }

    // Headers para stream de arquivo
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mimetype);
    header('Content-Disposition: attachment; filename='.basename($this->filepath));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($this->filepath));
    ob_clean();
    flush();

    // Lê o arquivo para stream buffer
    readfile($this->filepath);
  }

}