<?php

require_once 'include/pmieducar/clsPmieducarConfiguracoesGerais.inc.php';

$configuracoes = new clsPmieducarConfiguracoesGerais();
$configuracoes = $configuracoes->detalhe();
$reason = $configuracoes['ieducar_suspension_message'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
  <head>
    <!--style type="text/css">
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
        /*border: 1px solid #666*/
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
    </style-->

    <link rel='stylesheet' type='text/css' href='styles/reset.css'>
    <link rel='stylesheet' type='text/css' href='styles/portabilis.css'>
    <link rel='stylesheet' type='text/css' href='styles/min-portabilis.css'></head>

  </head>

  <body>
    <div id="error">
      <div class="content">
        <h1>Acesso suspenso</h1>
        <p class="explanation">
          <?PHP
            if(!empty($reason)){
              echo $reason;
            }else{
              echo "Desculpe, o sistema está temporariamente indisponível. Contate o responsável pelo sistema em seu município. Obrigado pela compreensão.";
            }
          ?>
        </p>
      </div>
    </div>
  </body>
</html>
