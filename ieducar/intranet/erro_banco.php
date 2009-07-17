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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
  <head>
    <title></title>
    <style type="text/css">
      * {
        margin: 0;
        padding: 0
      }

      body {
        align: center;
        font-size: 85%;
        font-family: verdana, arial, sans-serif;
        line-height: 160%
      }

      div#error {
        width: 500px;
        position: absolute;
        left: 250px;
        top: 35px;
        border: 1px solid #666
      }

      div.content {
        padding: 25px
      }

      h1 {
        padding-bottom: 15px
      }

      ul {
        margin-top: 20px;
        list-style-position: inside
      }
    </style>

  </head>

  <body>
    <div id="error">
      <div class="content">
        <h1>Erro</h1>
        <p>Ocorreu um erro de conexão com o banco de dados.</p>
        <ul>Sugestões:
          <li>Verifique se o seu banco de dados está corretamente configurado e se o serviço relacionado está executando</li>
          <li>Verifique se configurou o i-Educar com os parâmetros corretos de conexão com o banco de dados no arquivo <code>configuration/ieducar.ini</code></li>
          <li><a href="http://svn.softwarepublico.gov.br/trac/ieducar#FAQsetutoriais">Consulte a documentação do i-Educar</a> sobre os procedimentos de instalação da versão desejada</li>
        </ul>
      </div>
    </div>
  </body>
</html>