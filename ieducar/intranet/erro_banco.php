<?php

/**
 * Mostra mensagem de erro de conexão com o banco de dados.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @since   1.0.1
 * @version $Id$
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
          <li>Verifique se o seu banco de dados está corretamente configurado e se o serviço está rodando</li>
          <li>Verifique se configurou o i-Educar com os parâmetros corretos de conexão com o banco de dados no arquivo <code>include/clsBanco.inc.php</code></li>
          <li><a href="http://svn.softwarepublico.gov.br/trac/ieducar#FAQsetutoriais">Consulte a documentação do i-Educar</a> sobre os procedimentos de instalação da versão desejada</li>
        </ul>
      </div>
    </div>
  </body>
</html>