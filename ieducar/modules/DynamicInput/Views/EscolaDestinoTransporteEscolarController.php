<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

/**
 * EscolaDestinoTransporteEscolarController class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão ??
 * @version     @@package_version@@
 */
class EscolaDestinoTransporteEscolarController extends ApiCoreController{

  protected function getEscolaDestinoTransporteEscolar() {

     $sql    = 'SELECT idpes AS id, 
                       nome AS nome
                          FROM cadastro.pessoa
                          WHERE idpes IN
                    (SELECT ref_idpes_destino
                               FROM modules.rota_transporte_escolar)
                    OR idpes IN
                    (SELECT ref_idpes_destino
                       FROM modules.pessoa_transporte)
                          ORDER BY (lower(nome)) ASC';



    $escolasDestinoTransporte = $this->fetchPreparedQuery($sql);
    $options = array();

    foreach ($escolasDestinoTransporte as $escolaDestinoTransporte){
      $options['__' . $escolaDestinoTransporte['id']] = $escolaDestinoTransporte['id'].' - '.$this->toUtf8($escolaDestinoTransporte['nome']);
    }

    return array('options' => $options);

  }

  public function Gerar() {
    if($this->isRequestFor('get','escola_destino_transporte_escolar')) 
      $this->appendResponse($this->getEscolaDestinoTransporteEscolar());
    else
      $this->notImplementedOperationError();
  }
}