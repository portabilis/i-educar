<?php

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class PontoController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'i-Educar - Pontos';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 21239;
    protected $_deleteOption = true;

    protected $_formMap = [

        'id' => [
            'label' => 'Código do ponto',
            'help' => '',
        ],
        'desc' => [
            'label' => 'Descrição',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_ponto_lst.php',
            'delete_success' => '/intranet/transporte_ponto_lst.php'
        ], $this->_options);
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'In&iacute;cio',
            'educar_transporte_escolar_index.php' => 'Transporte escolar',
            '' => "$nomeMenu ponto"
        ]);
        $this->enviaLocalizacao($localizacao->montar());
    }

    protected function _initNovo()
    {
        return false;
    }

    protected function _initEditar()
    {
        return false;
    }

    public function Gerar()
    {
        $this->url_cancelar = '/intranet/transporte_ponto_lst.php';

        // Código do ponto
        $options = [
            'label' => $this->_getLabel('id'),
            'disabled' => true,
            'required' => false,
            'size' => 25
        ];
        $this->inputsHelper()->integer('id', $options);

        // descricao
        $options = [
            'label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc')),
            'required' => true,
            'size' => 50,
            'max_length' => 70
        ];
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

        $options = [
            'label' => Portabilis_String_Utils::toLatin1('Município'),
            'required' => $enderecamentoObrigatorio,
            'disabled' => $desativarCamposDefinidosViaCep
        ];

        $helperOptions = [
            'objectName' => 'municipio',
            'hiddenInputOptions' => ['options' => ['value' => $this->municipio_id]]
        ];

        $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

        $options = [
            'label' => Portabilis_String_Utils::toLatin1('Distrito'),
            'required' => $enderecamentoObrigatorio,
            'disabled' => $desativarCamposDefinidosViaCep
        ];

        $helperOptions = [
            'objectName' => 'distrito',
            'hiddenInputOptions' => ['options' => ['value' => $this->distrito_id]]
        ];

        $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->bairro_id]]];

        $options = [
            'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'),
            'required' => $enderecamentoObrigatorio,
            'disabled' => $desativarCamposDefinidosViaCep
        ];

        $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

        $options = [
            'label' => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
            'placeholder' => 'Bairro',
            'value' => $this->bairro,
            'max_length' => 40,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('bairro', $options);

        // zona localização

        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

        $options = [
            'label' => '',
            'placeholder' => 'Zona localização',
            'value' => $this->zona_localizacao,
            'disabled' => $desativarCamposDefinidosViaCep,
            'resources' => $zonas,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->select('zona_localizacao', $options);

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->logradouro_id]]];

        $options = [
            'label' => 'Tipo / Logradouro - <b>Buscar</b>',
            'required' => $enderecamentoObrigatorio,
            'disabled' => $desativarCamposDefinidosViaCep
        ];

        $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

        // tipo logradouro

        $options = [
            'label' => 'Tipo / Logradouro - <b>Cadastrar</b>',
            'value' => $this->idtlog,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $helperOptions = [
            'attrName' => 'idtlog'
        ];

        $this->inputsHelper()->tipoLogradouro($options, $helperOptions);

        // logradouro

        $options = [
            'label' => '',
            'placeholder' => 'Logradouro',
            'value' => '',
            'max_length' => 150,
            'disabled' => $desativarCamposDefinidosViaCep,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('logradouro', $options);

        // numero

        $options = [
            'required' => false,
            'label' => 'Número',
            'placeholder' => Portabilis_String_Utils::toLatin1('Número'),
            'value' => '',
            'max_length' => 6
        ];

        $this->inputsHelper()->integer('numero', $options);

        // complemento

        $options = [
            'required' => false,
            'value' => '',
            'max_length' => 20
        ];

        $this->inputsHelper()->text('complemento', $options);

        $this->inputsHelper()->text('latitude', ['required' => false]);

        $this->inputsHelper()->text('longitude', ['required' => false]);

        $script = [
            '/modules/Cadastro/Assets/Javascripts/Endereco.js',
            '/lib/Utils/gmaps.js',
            '/modules/Portabilis/Assets/Javascripts/Frontend/ieducar.singleton_gmap.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $script);

        $this->loadResourceAssets($this->getDispatcher());
    }
}
