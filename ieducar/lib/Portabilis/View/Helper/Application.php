<?php

use Tooleks\LaravelAssetVersion\Facades\Asset;

require_once 'CoreExt/View/Helper/Abstract.php';

class Portabilis_View_Helper_Application extends CoreExt_View_Helper_Abstract
{
    protected static $javascriptsLoaded = [];
    protected static $stylesheetsLoaded = [];

    /**
     * Construtor singleton.
     */
    protected function __construct()
    {
    }

    /**
     * Retorna uma instância singleton.
     *
     * @return CoreExt_View_Helper_Abstract
     */
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    /**
     * Adiciona elementos chamadas scripts javascript para instancia da view recebida, exemplo:
     *
     * <code>
     * $applicationHelper->javascript($viewInstance, array('/modules/ModuleName/Assets/Javascripts/ScriptName.js', '...'));
     * </code>
     *
     * @param object       $viewInstance Istancia da view a ser carregado os scripts.
     * @param array|string $files        Lista de scripts a serem carregados.
     *
     * @return null
     */
    public static function loadJavascript($viewInstance, $files, $appendAssetsVersionParam = true)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!in_array($file, self::$javascriptsLoaded)) {
                self::$javascriptsLoaded[] = $file;

                if ($appendAssetsVersionParam) {
                    $viewInstance->appendOutput("<script type='text/javascript' src='" . Asset::get($file) . "'></script>");
                } else {
                    $viewInstance->appendOutput("<script type='text/javascript' src='$file'></script>");
                }
            }
        }
    }

    /**
     * Adiciona links css para instancia da view recebida, exemplo:
     *
     * <code>
     * $applicationHelper->stylesheet($viewInstance, array('/modules/ModuleName/Assets/Stylesheets/StyleName.css', '...'));
     * </code>
     *
     * @param $viewInstance
     * @param array|string $files                    Lista de estilos a serem carregados.
     * @param bool         $appendAssetsVersionParam
     *
     * @return null
     */
    public static function loadStylesheet($viewInstance, $files, $appendAssetsVersionParam = true)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!in_array($file, self::$stylesheetsLoaded)) {
                self::$stylesheetsLoaded[] = $file;

                if ($appendAssetsVersionParam) {
                    $viewInstance->appendOutput("<link type='text/css' rel='stylesheet' href='" . Asset::get($file) . "'></script>");
                } else {
                    $viewInstance->appendOutput("<link type='text/css' rel='stylesheet' href='$file'></script>");
                }
            }
        }
    }

    public static function embedJavascript($viewInstance, $script, $afterReady = false)
    {
        if ($afterReady) {
            $script = "
                (function($){
                    $(document).ready(function(){
                        $script
                    });
                })(jQuery);
            ";
        }

        $viewInstance->appendOutput("<script type='text/javascript'>$script</script>");
    }

    public static function embedStylesheet($viewInstance, $css)
    {
        $viewInstance->appendOutput("<style type='text/css'>$css</style>");
    }

    public static function embedJavascriptToFixupFieldsWidth($viewInstance)
    {
        Portabilis_View_Helper_Application::loadJavascript(
            $viewInstance,
            '/modules/Portabilis/Assets/Javascripts/Utils.js'
        );

        Portabilis_View_Helper_Application::embedJavascript(
            $viewInstance,
            'fixupFieldsWidth();',
            $afterReady = true
        );
    }

    public static function loadJQueryLib($viewInstance)
    {
        self::loadJavascript($viewInstance, 'scripts/jquery/jquery-1.8.3.min.js', false);
        self::embedJavascript($viewInstance, 'if (typeof($j) == \'undefined\') { var $j = jQuery.noConflict(); }');
    }

    public static function loadJQueryFormLib($viewInstance)
    {
        self::loadJavascript($viewInstance, 'scripts/jquery/jquery.form.js', false);
    }

    public static function loadJQueryUiLib($viewInstance)
    {
        self::loadJavascript($viewInstance, 'scripts/jquery/jquery-ui.min-1.9.2/js/jquery-ui-1.9.2.custom.min.js', true);
        self::loadStylesheet($viewInstance, 'scripts/jquery/jquery-ui.min-1.9.2/css/custom/jquery-ui-1.9.2.custom.min.css', true);
        self::embedStylesheet($viewInstance, '.ui-autocomplete { font-size: 11px; }');
    }

    public static function loadChosenLib($viewInstance)
    {
        self::loadStylesheet($viewInstance, '/modules/Portabilis/Assets/Plugins/Chosen/chosen.css', false);
        self::loadJavascript($viewInstance, '/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js', false);
    }

    public static function loadAjaxChosenLib($viewInstance)
    {
        // AjaxChosen requires this fixup, see https://github.com/meltingice/ajax-chosen
        $fixupCss = '.chzn-container .chzn-results .group-result { display: list-item; }';

        Portabilis_View_Helper_Application::embedStylesheet($viewInstance, $fixupCss);

        self::loadJavascript($viewInstance, '/modules/Portabilis/Assets/Plugins/AjaxChosen/ajax-chosen.min.js', false);
    }
}
