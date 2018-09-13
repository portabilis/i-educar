<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/Portabilis/Controller/ErrorCoreController.php';

class UnauthorizedController extends Portabilis_Controller_ErrorCoreController
{
    protected $_titulo = 'Acesso n&atilde;o autorizado';

    protected function setHeader()
    {
        header('HTTP/1.1 403 Forbidden');
    }

    public function Gerar()
    {
        echo '
      <div id=\'error\' class=\'small\'>
        <div class=\'content\'>
         <h1>Acesso n&atilde;o autorizado</h1>

         <p class=\'explanation\'>
          Seu usu&aacute;rio n&atilde;o possui autoriza&ccedil;&atilde;o para realizar esta a&ccedil;&atilde;o,
          <strong> tente seguir as etapas abaixo:</strong>

          <ol>
            <li><a href=\'/intranet/index.php\'>Volte para o sistema</a></li>
            <li>Solicite ao respons&aacute;vel pelo sistema, para adicionar ao seu usu&aacute;rio a permiss&atilde;o necess&aacute;ria e tente novamente</li>
            <li>Caso o erro persista, por favor, <a target=\'_blank\' onclick=\'FreshWidget.show();\'>solicite suporte</a>.</li>
          </ol>
        </p>

        </div>
      </div>';
    }
}
