<?php

/*
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * FileStream class.
 *
 * Classe para stream de arquivos implementando checagens de seguran�a como
 * diret�rios permitidos e tipo de arquivo.
 *
 * @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe dispon�vel desde a vers�o 1.1.0
 * @version  $Id$
 */
class FileStream
{

  /**
   * Inst�ncia da classe Mimetype
   * @var Mimetype
   */
  protected $Mimetype    = NULL;

  /**
   * Caminho do arquivo para stream
   * @var string
   */
  protected $filepath    = NULL;

  /**
   * Array de diret�rios permitidos para stream de arquivos.
   * @var array
   */
  protected $allowedDirs = array();


  /**
   * Construtor.
   *
   * @param  Mimetype  $mimetype     Objeto Mimetype
   * @param  array     $allowedDirs  Diret�rios permitidos para stream
   */
  public function __construct(Mimetype $mimetype, array $allowedDirs = array()) {
    $this->Mimetype = $mimetype;
    $this->setAllowedDirectories((array) $allowedDirs);
  }

  /**
   * Configura o nome do arquivo, verificando se o mesmo encontra-se em um
   * diret�rio de acesso permitido e se � leg�vel.
   *
   * @param   string  $filepath  O caminho completo ou relativo do arquivo
   * @throws  Exception
   */
  public function setFilepath($filepath) {
    $this->isReadable($filepath);
    $this->filepath = $filepath;
  }

  /**
   * Configura os diret�rios permitidos para stream de arquivos.
   *
   * @param   array  $v
   */
  protected function setAllowedDirectories($v) {
    $this->allowedDirs = $v;
  }

  /**
   * Verifica se o arquivo � leg�vel e se est� em um diret�rio permitido
   * para stream de arquivos.
   *
   * @param   string  $filepath  O caminho completo ou relativo ao arquivo
   * @throws  Exception
   */
  protected function isReadable($filepath)
  {
    $fileinfo = pathinfo($filepath);

    if (! $this->isDirectoryAllowed($fileinfo['dirname'])) {
      throw new Exception('Acesso ao diret�rio negado.');
    }

    if (! is_readable($filepath)) {
      throw new Exception('Arquivo n�o existe.');
    }
  }

  /**
   * Verifica se o diret�rio est� na lista de diret�rios permitidos para
   * stream de arquivos.
   *
   * @param   string  $directory
   * @return  bool    Retorna TRUE se o diret�rio � permitido
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
    header('Content-Disposition: attachment');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header("Cache-Control: private",false);
    header('Pragma: public');
    header('Content-Length: ' . filesize($this->filepath));
    ob_clean();
    flush();

    // Lê o arquivo para stream buffer
    readfile($this->filepath);
  }

}