<?php

$configuracoes = new clsPmieducarConfiguracoesGerais();
$configuracoes = $configuracoes->detalhe();
$reason = $configuracoes['ieducar_suspension_message'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
  <head>
    <title>Suspenso</title>
    <link rel='stylesheet' type='text/css' href='styles/reset.css'>
    <link rel='stylesheet' type='text/css' href='styles/portabilis.css'>
    <link rel='stylesheet' type='text/css' href='styles/min-portabilis.css'></head>

  </head>

  <body>
    <div id="error">
      <div class="content">
        <h1>Acesso suspenso</h1>
        <p class="explanation">
          <?php
            if (!empty($reason)) {
                echo $reason;
            } else {
                echo 'Desculpe, o sistema está temporariamente indisponível. Contate o responsável pelo sistema em seu município. Obrigado pela compreensão.';
            }
?>
        </p>
      </div>
    </div>
  </body>
</html>
