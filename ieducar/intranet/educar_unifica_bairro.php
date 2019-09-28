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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Unifica&ccedil;&atilde;o de bairros');
    $this->processoAp = 761; // @TODO CORRIGIR PROCESSOAP
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $tabela_bairros = array();
  var $bairro_duplicado;

  function Inicializar()
  {
    $retorno = 'Novo';



    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(761, $this->pessoa_logada, 7,
      'index.php');

    $this->breadcrumb('Unificação de bairros', [
        url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
    ]);

    return $retorno;
  }

  function Gerar()
  {
      $this->inputsHelper()->hidden('exibir_municipio');
      $this->inputsHelper()->simpleSearchBairro(null,array('label' => 'Bairro principal' ));
      $this->campoTabelaInicio("tabela_bairros","",array("Bairro duplicado"),$this->tabela_bairros);
      $this->campoTexto( "bairro_duplicado", "Bairro duplicado", $this->bairro_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus' );
      $this->campoTabelaFim();

  }

  function Novo()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(761, $this->pessoa_logada, 7,
      'index.php');

    $bairro_principal = $this->bairro_id;
    $obj_bairro = new clsPublicBairro(NULL, NULL, $bairro_principal);
    $obj_bairro = $obj_bairro->detalhe();
    $municipio_principal = $obj_bairro['idmun'];

    $bairros_duplicados = array();

    // Loop entre bairros das tabelas
    foreach ( $this->bairro_duplicado AS $key => $bairro_duplicado ){

      $idbai = $this->retornaCodigo($bairro_duplicado);

      // Verifica se o bairro é válido e não é igual ao bairro principal
      if(is_numeric($idbai) && $idbai != $bairro_principal){
        $obj_bairro = new clsPublicBairro(NULL, NULL, $bairro_principal);
        $obj_bairro_det = $obj_bairro->detalhe();
        if($obj_bairro_det){
          // Verifica se o município é o mesmo que o bairro principal
          if($obj_bairro_det['idmun'] == $municipio_principal)
            $bairros_duplicados[] = $idbai;
          else{
            $this->mensagem = 'Bairros a serem unificados devem pertencer a mesma cidade que o bairro principal.<br />';
            return FALSE;
          }
        }
      }
    }
    // Unifica o array de bairros a serem unificados
    $bairros_duplicados = array_keys(array_flip($bairros_duplicados));
    $db = new clsBanco();
    foreach ($bairros_duplicados as $key => $value) {
      $db->consulta("SELECT public.unifica_bairro({$value}, {$bairro_principal});");
    }

    $this->mensagem = "<span>Bairros unificados com sucesso.</span>";
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
    var searchPath = '/module/Api/Bairro?oper=get&resource=bairro-search&exibir_municipio=true';
    var params     = { query : request.term };

    $j.get(searchPath, params, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  };

  function setAutoComplete() {
    $j.each($j('input[id^="bairro_duplicado"]'), function(index, field) {

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
