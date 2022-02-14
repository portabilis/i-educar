<?php

declare(strict_types=1);

function asset($path)
{
    if (env('ASSETS_SECURE')) {
        return secure_asset($path);
    }

    return asset($path);
}
