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
 * Faz stream de arquivo para o buffer do navegador.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.1.0
 * @version  $Id$
 */

require_once 'Utils/Mimetype.class.php';
require_once 'Utils/FileStream.class.php';

// Pega o nome do arquivo (caminho completo)
$filename = isset($_GET['filename']) ? $_GET['filename'] : NULL;

// Diretórios públicos (permitidos) para stream de arquivo.
$defaultDirectories = array('tmp', 'pdf');

// Classe Mimetype
$mimetype = new Mimetype();

// Classe FileStream
$fileStream = new FileStream($mimetype, $defaultDirectories);

try {
  $fileStream->setFilepath($filename);
}
catch (Exception $e) {
  print $e->getMessage();
  exit();
}

try {
  $fileStream->streamFile();
}
catch (Exception $e) {
  print $e->getMessage();
  exit();
}

unlink($filename);