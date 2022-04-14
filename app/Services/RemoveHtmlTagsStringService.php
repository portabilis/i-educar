<?php

namespace App\Services;

class RemoveHtmlTagsStringService
{
    public const PATTERNS = [
        '/style=\"([^"]*)"/mi',
        '/style=\\\"([^"]*)"/mi',
        '/<style.*?<\/style>/mi',
        '/(<style[\w\W]+style>)/mi',
        '/(?<=;|"|\s)font-family:[^;\']*(;)?/mi',
        '/font-family:[^;\']*(;)?/mi',
        '/(<font[^>]*>)|(<\/font>)/',
        '/<span.*?<\/span>/mi'
    ];

    public function execute(string $text = ''): string
    {
        if (empty($text) === true) {
            return $text;
        }

        return preg_replace(self::PATTERNS, '', $text);
    }
}
