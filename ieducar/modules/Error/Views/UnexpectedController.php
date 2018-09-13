<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/Portabilis/Controller/ErrorCoreController.php';

class UnexpectedController extends Portabilis_Controller_ErrorCoreController
{
    protected $_titulo = 'Erro inesperado';

    protected function setHeader()
    {
        header('HTTP/1.1 500 Internal Server Error');
    }

    public function Gerar()
    {
        if ($GLOBALS['coreExt']['Config']->modules->error->show_details) {
            $detail = "<br /><h3>Erro app</h3>{$this->getSession()->last_error_message}";
            $detail .= "<br /><h3>Erro php</h3>{$this->getSession()->last_php_error_message}";
            $detail .= "<br /><h3>Arquivo</h3>(linha: {$this->getSession()->last_php_error_line}) ";
            $detail .= "{$this->getSession()->last_php_error_file}";

            unset($this->getSession()->last_error_message);
            unset($this->getSession()->last_php_error_message);
            unset($this->getSession()->last_php_error_line);
            unset($this->getSession()->last_php_error_file);

            if (!$detail) {
                $detail = 'Sem detalhes do erro.';
            }

            $detail = "<h2>Detalhes:</h2>
                  <p>$detail</p>";
        } else {
            $detail = '<p>Visualiza&ccedil;&atilde;o de detalhes do erro desativada.</p>';
        }

        echo "
      <div id='error' class='small'>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
        <div class='content'>
         <h1>Erro inesperado</h1>

         <p class='explanation'>
          Desculpe-nos, algum erro inesperado ocorreu,
          <strong> tente seguir as etapas abaixo:</strong>

          <ol>
            <li><a href='/intranet/index.php'>Tente novamente</a></li>
            <li><a href='/intranet/logof.php'>Fa&ccedil;a logoff do sistema</a> e tente novamente</li>
            <li>Caso o erro persista, por favor, <a target='_blank' onclick='FreshWidget.show();'>solicite suporte</a>.</li>
          </ol>
        </p>

        <div class='detail'>
          $detail
        </div>

        </div>
      </div>";
    }
}
