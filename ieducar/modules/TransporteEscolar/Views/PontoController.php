<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @package   TransporteEscolar
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class PontoController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Pontos';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21239;
  protected $_deleteOption      = true;

  protected $_formMap    = array(

    'id' => array(
      'label'  => 'Código do ponto',
      'help'   => '',
    ),
    'desc' => array(
      'label'  => 'Descrição',
      'help'   => '',
    )
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_ponto_lst.php','delete_success' => '/intranet/transporte_ponto_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu ponto"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    $this->url_cancelar = '/intranet/transporte_ponto_lst.php';

    // Código do ponto
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // descricao
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc')), 'required' => true, 'size' => 50, 'max_length' => 70);
    $this->inputsHelper()->text('desc', $options);

    $enderecamentoObrigatorio = false;
    $desativarCamposDefinidosViaCep = true;


    $this->campoCep(
      'cep_',
      'CEP',
      '',
      $enderecamentoObrigatorio,
      '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'/intranet/educar_pesquisa_cep_log_bairro2.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
      false
    );


    $options       = array('label' => Portabilis_String_Utils::toLatin1('Município'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'municipio',
                           'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id)));

    $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Distrito'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'distrito',
                           'hiddenInputOptions' => array('options' => array('value' => $this->distrito_id)));

    $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->bairro_id)));

    $options       = array( 'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

    $options = array(
      'label'       => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('bairro', $options);

    // zona localização

    $zonas = App_Model_ZonaLocalizacao::getInstance();
    $zonas = $zonas->getEnums();
    $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localização',
      'value'       => $this->zona_localizacao,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'resources'   => $zonas,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->select('zona_localizacao', $options);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->logradouro_id)));

    $options       = array('label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

    // tipo logradouro

    $options = array(
      'label'       => 'Tipo / Logradouro - <b>Cadastrar</b>',
      'value'       => $this->idtlog,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $helperOptions = array(
      'attrName' => 'idtlog'
    );

    $this->inputsHelper()->tipoLogradouro($options, $helperOptions);


    // logradouro

    $options = array(
      'label'       => '',
      'placeholder' => 'Logradouro',
      'value'       => '',
      'max_length'  => 150,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('logradouro', $options);

    // numero

    $options = array(
      'required'    => false,
      'label'       => 'Número',
      'placeholder' => Portabilis_String_Utils::toLatin1('Número'),
      'value'       => '',
      'max_length'  => 6
    );

    $this->inputsHelper()->integer('numero', $options);

    // complemento

    $options = array(
      'required'    => false,
      'value'       => '',
      'max_length'  => 20
    );

    $this->inputsHelper()->text('complemento', $options);

    $this->inputsHelper()->text('latitude', array('required' => false));

    $this->inputsHelper()->text('longitude', array('required' => false));

    $script = array('/modules/Cadastro/Assets/Javascripts/Endereco.js',
                    '/lib/Utils/gmaps.js',
                    '/modules/Portabilis/Assets/Javascripts/Frontend/ieducar.singleton_gmap.js'
                    );

    Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->loadResourceAssets($this->getDispatcher());
  }

}
?>