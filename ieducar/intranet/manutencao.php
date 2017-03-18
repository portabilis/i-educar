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
 * Mostra mensagem de erro de conexão com o banco de dados.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.1
 * @version  $Id$
 */

header("HTTP/1.1 503 Service Unavailable");

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
  <head>
    <link rel='stylesheet' type='text/css' href='styles/reset.css'>
    <link rel='stylesheet' type='text/css' href='styles/portabilis.css'>
    <link rel='stylesheet' type='text/css' href='styles/min-portabilis.css'></head>

  </head>

  <body>
    <div id="error" style="border: 0px solid #666;">
      <div class="content">
        <h1>Em manutenção</h1>
        <p class="explanation">Desculpe-nos, estamos passando por uma manutenção<strong>, por favor, tente novamente mais tarde.</strong></p>
      </div>
    </div>
  </body>
</html>
