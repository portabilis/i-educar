<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesPontoTransporteEscolar.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Date/Utils.php';

/**
 * Class PontoController
 * @deprecated Essa versão da API pública será descontinuada
 */
class PontoController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  protected function createOrUpdatePonto($id = null){

    $ponto                          = new clsModulesPontoTransporteEscolar();
    $ponto->cod_ponto_transporte_escolar = $id;

    // após cadastro não muda mais id pessoa
    $ponto->descricao                     = Portabilis_String_Utils::toLatin1($this->getRequest()->desc);

    $ponto->latitude                      = $this->getRequest()->latitude;
    $ponto->longitude                     = $this->getRequest()->longitude;

    $cep = idFederal2Int($this->getRequest()->cep_);

    $objCepLogradouro = new ClsCepLogradouro($cep, $this->getRequest()->logradouro_id);

    // Endereço do ponto //
    if (! $objCepLogradouro->existe())
      $objCepLogradouro->cadastra();

    $objCepLogradouroBairro = new ClsCepLogradouroBairro();
    $objCepLogradouroBairro->cep = $cep;
    $objCepLogradouroBairro->idbai = $this->getRequest()->bairro_id;
    $objCepLogradouroBairro->idlog = $this->getRequest()->logradouro_id;

    if (! $objCepLogradouroBairro->existe())
      $objCepLogradouroBairro->cadastra();

    $ponto->cep                       = $cep;
    $ponto->idbai                     = $this->getRequest()->bairro_id;
    $ponto->idlog                     = $this->getRequest()->logradouro_id;
    $ponto->numero                    = $this->getRequest()->numero;

    $ponto->complemento                     = Portabilis_String_Utils::toLatin1($this->getRequest()->complemento);

    // Fim do ndereço do ponto //

    return (is_null($id) ? $ponto->cadastra() : $ponto->edita());
  }


  protected function get() {

    $id                   = $this->getRequest()->id;
    $ponto                 = new clsModulesPontoTransporteEscolar();
    $ponto->cod_ponto_transporte_escolar       = $id;
    $ponto                 = $ponto->detalhe();

    $attrs  = array(
      'cod_ponto_transporte_escolar'  => 'id',
      'descricao' => 'desc',
      'cep' => 'cep',
      'idlog' => 'idlog',
      'idbai' => 'idbai',
      'numero' => 'numero',
      'complemento' => 'complemento',
      'bairro' => 'bairro',
      'distrito' => 'distrito',
      'iddis' => 'iddis',
      'logradouro' => 'logradouro',
      'idtlog' => 'idtlog',
      'zona_localizacao' => 'zona_localizacao',
      'sigla_uf' => 'sigla_uf',
      'municipio' => 'municipio',
      'idmun' => 'idmun',
      'latitude' => 'latitude',
      'longitude' => 'longitude',
    );

    $pt = Portabilis_Array_Utils::filter($ponto, $attrs);
    $pt['desc'] = Portabilis_String_Utils::toUtf8($pt['desc']);

    $pt['bairro']           = $this->toUtf8($pt['bairro']);
    $pt['distrito']           = $this->toUtf8($pt['distrito']);
    $pt['logradouro']       = $this->toUtf8($pt['logradouro']);

    $pt['municipio'] = $this->toUtf8($pt['municipio']);
    $pt['sigla_uf'] = $this->toUtf8($pt['sigla_uf']);

    $pt['cep'] = int2CEP($pt['cep']);

    return $pt;

  }

  protected function validateIfPontoIsNotInUse(){
      $itinerario          = new clsModulesItinerarioTransporteEscolar();
      $lista = $itinerario->lista(NULL,NULL,NULL,NULL,NULL,$this->getRequest()->id);
      if(is_array($lista) && count($lista)>0){
        $this->messenger->append('Não é possível excluir um ponto que está vinculado a um itinerário.',
                                 'error', false, 'error');
        return false;
      }else{
        return true;
      }
  }


  protected function post() {

    if (!($this->getRequest()->cep_ && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id)))
      $this->normalizaEndereco();

    $id = $this->createOrUpdatePonto();
    if (is_numeric($id)) {
      $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o ponto não pode ser cadastrada, por favor, verifique.');

    return array('id' => $id);
 }

  protected function sqlsForNumericSearch() {

    $sqls[] = "select distinct cod_ponto_transporte_escolar as id, descricao as name from
                 modules.ponto_transporte_escolar where cod_ponto_transporte_escolar::varchar like $1||'%'";

    return $sqls;
  }


  protected function sqlsForStringSearch() {

    $sqls[] = "select distinct cod_ponto_transporte_escolar as id, descricao as name  from
                 modules.ponto_transporte_escolar where lower((descricao)) like '%'||lower(($1))||'%'";

    return $sqls;
  }

  protected function put() {
      $id = $this->getRequest()->id;

      if (!($this->getRequest()->cep_ && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id)))
        $this->normalizaEndereco();

      $editou = $this->createOrUpdatePonto($id);

      if ($editou) {

        $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o ponto não pode ser alterado, por favor, verifique.');


    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;

    $pessoas = new clsModulesPessoaTransporte();
    $lista = $pessoas->lista(NULL,NULL,NULL,$id);

    foreach($lista as $registro){
      $editaPessoa = new clsModulesPessoaTransporte($registro['cod_pessoa_transporte'],
        $registro['ref_cod_rota_transporte_escolar'],$registro['ref_idpes'],
        null,$registro['ref_idpes_destino'],$registro['observacao']);
      $editaPessoa->edita();
    }

    $ponto                  = new clsModulesPontoTransporteEscolar();
    $ponto->cod_ponto_transporte_escolar       = $id;

    if($ponto->excluir()){
     $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
    }else
      $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                               'error', false, 'error');


    return array('id' => $id);
  }

  protected function canCreateBairro(){
    return !empty($this->getRequest()->bairro) && !empty($this->getRequest()->zona_localizacao);
  }

  protected function canCreateLogradouro(){
    return !empty($this->getRequest()->logradouro) && !empty($this->getRequest()->idtlog);
  }

  protected function createBairro(){

    $objBairro = new clsBairro(null,$this->getRequest()->municipio_id,null,Portabilis_String_Utils::toLatin1($this->getRequest()->bairro), $this->currentUserId());
    $objBairro->zona_localizacao = $this->getRequest()->zona_localizacao;
    $objBairro->iddis = $this->getRequest()->distrito_id;

    return $objBairro->cadastra();
  }

  protected function createLogradouro(){
    $objLogradouro = new clsLogradouro(null,$this->getRequest()->idtlog, Portabilis_String_Utils::toLatin1($this->getRequest()->logradouro), $this->getRequest()->municipio_id,
                                           null, 'S', $this->currentUserId());
    return $objLogradouro->cadastra();
  }

protected function normalizaEndereco() {

    if($this->getRequest()->cep_ && is_numeric($this->getRequest()->municipio_id) && is_numeric($this->getRequest()->distrito_id)){

      if (!is_numeric($this->getRequest()->bairro_id)){
        if ($this->canCreateBairro())
          $this->getRequest()->bairro_id = $this->createBairro();
        else
          return;
      }

      if (!is_numeric($this->getRequest()->logradouro_id)){
        if($this->canCreateLogradouro())
          $this->getRequest()->logradouro_id = $this->createLogradouro();
        else
          return;
      }
    }
  }

  public function Gerar() {

    if ($this->isRequestFor('get', 'ponto'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'ponto-search'))
      $this->appendResponse($this->search());

    // create
    elseif ($this->isRequestFor('post', 'ponto'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'ponto'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'ponto')){
      if($this->validateIfPontoIsNotInUse()){
        $this->appendResponse($this->delete());
        echo "<script language= \"JavaScript\">
                location.href=\"intranet/transporte_ponto_lst.php\";
              </script>";
        die();
      }
    }else
      $this->notImplementedOperationError();
  }
}
