<?php

declare(strict_types=1);

// Carregado pelo autoload do Composer, antes de a cobertura começar a medir.
// @codeCoverageIgnoreStart
if (function_exists('asset') === false) {
    function asset($path)
    {
        if (env('ASSETS_SECURE')) {
            return secure_asset($path);
        }

        return asset($path);
    }
}
// @codeCoverageIgnoreEnd
