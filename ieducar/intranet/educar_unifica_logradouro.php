<?php
/**
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
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

require_once 'App/Date/Utils.php';

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Unifica&ccedil;&atilde;o de logradouros');
    $this->processoAp = 762;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $tabela_logradouros = array();
  var $logradouro_duplicado;

  function Inicializar()
  {
    $retorno = 'Novo';



    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(762, $this->pessoa_logada, 7,
       'index.php');

    $this->breadcrumb('Unificação de logradouros', [
        url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
    ]);

    return $retorno;
  }

  function Gerar()
  {
      $this->inputsHelper()->hidden('exibir_municipio');
      $this->inputsHelper()->simpleSearchlogradouro(null,array('label' => 'Logradouro principal' ));
      $this->campoTabelaInicio("tabela_logradouros","",array("Logradouro duplicado"),$this->tabela_logradouros);
      $this->campoTexto( "logradouro_duplicado", "Logradouro duplicado", $this->logradouro_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus' );
      $this->campoTabelaFim();

  }

  function Novo()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(762, $this->pessoa_logada, 7,
      'index.php');

    $logradouro_principal = $this->logradouro_id;
    $obj_logradouro = new clsPublicLogradouro($logradouro_principal);
    $obj_logradouro = $obj_logradouro->detalhe();
    $municipio_principal = $obj_logradouro['idmun'];

    $logradouros_duplicados = array();

    // Loop entre logradouros das tabelas
    foreach ( $this->logradouro_duplicado AS $key => $logradouro_duplicado ){

      $idlog = $this->retornaCodigo($logradouro_duplicado);

      // Verifica se o logradouro é válido e não é igual ao logradouro principal
      if(is_numeric($idlog) && $idlog != $logradouro_principal){
        $obj_logradouro = new clsPubliclogradouro($logradouro_principal);
        $obj_logradouro_det = $obj_logradouro->detalhe();
        if($obj_logradouro_det){
          // Verifica se o município é o mesmo que o logradouro principal
          if($obj_logradouro_det['idmun'] == $municipio_principal)
            $logradouros_duplicados[] = $idlog;
          else{
            $this->mensagem = 'Logradouros a serem unificados devem pertencer a mesma cidade que o logradouro principal.<br />';
            return FALSE;
          }
        }
      }
    }
    // Unifica o array de logradouros a serem unificados
    $logradouros_duplicados = array_keys(array_flip($logradouros_duplicados));
    $db = new clsBanco();
    foreach ($logradouros_duplicados as $key => $value) {
      $db->consulta("SELECT public.unifica_logradouro({$value}, {$logradouro_principal});");
    }

    $this->mensagem = "<span>Logradouros unificados com sucesso.</span>";
    return TRUE;
  }

  protected function retornaCodigo($palavra){

    return substr($palavra, 0, strpos($palavra, " -"));
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">

  var handleSelect = function(event, ui){
    $j(event.target).val(ui.item.label);
    return false;
  };

  var search = function(request, response) {
    var searchPath = '/module/Api/Logradouro?oper=get&resource=logradouro-search&exibir_municipio=true';
    var params     = { query : request.term };

    $j.get(searchPath, params, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  };

  function setAutoComplete() {
    $j.each($j('input[id^="logradouro_duplicado"]'), function(index, field) {

      $j(field).autocomplete({
        source    : search,
        select    : handleSelect,
        minLength : 1,
        autoFocus : true
      });

    });
  }

  setAutoComplete();

  // bind events

  var $addPontosButton = $j('#btn_add_tab_add_1');

  $addPontosButton.click(function(){
    setAutoComplete();
  });

$j('#btn_enviar').val('Unificar');


</script>
