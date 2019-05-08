<?php

class Portabilis_Utils_ReCaptcha
{
    public static function getWidget()
    {
        $template = '<div class="g-recaptcha" data-sitekey="%s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>';

        return sprintf($template, config('legacy.app.recaptcha.public_key'), config('legacy.app.recaptcha.options.lang'));
    }

    public static function check($response)
    {
        $recaptcha = new \ReCaptcha\ReCaptcha(config('legacy.app.recaptcha.private_key'));
        $resp = $recaptcha->verify($response, $_SERVER['REMOTE_ADDR']);

        return $resp->isSuccess();
    }
}
