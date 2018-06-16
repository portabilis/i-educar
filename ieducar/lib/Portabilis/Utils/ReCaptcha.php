<?php

require_once 'vendor/autoload.php';

class Portabilis_Utils_ReCaptcha {

    public static function getWidget()
    {
        $config = $GLOBALS['coreExt']['Config']->app->recaptcha;
        $template = '<div class="g-recaptcha" data-sitekey="%s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>';

        return sprintf($template, $config->public_key, $config->options->lang);
    }

    public static function check($response)
    {
        $recaptcha = new \ReCaptcha\ReCaptcha($GLOBALS['coreExt']['Config']->app->recaptcha->private_key);
        $resp = $recaptcha->verify($response, $_SERVER['REMOTE_ADDR']);

        return $resp->isSuccess();
    }
}
