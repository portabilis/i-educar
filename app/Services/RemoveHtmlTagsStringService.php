<?php

namespace App\Services;

class RemoveHtmlTagsStringService
{
    public const PATTERNS = [
        '/style=\"([^"]*)"/mi',
        '/<style.*?<\/style>/mi',
        '/(<style[\w\W]+style>)/mi',
        '/(?<=;|"|\s)font-family:[^;\']*(;)?/mi',
        '/font-family:[^;\']*(;)?/mi',
        '/(<font[^>]*>)|(<\/font>)/'
    ];

    public function execute(string $text): string
    {
        $text = preg_replace(self::PATTERNS, '', $text);

        if ($text === null) {
            throw new \Exception('Erro ao remover html tags.');
        }

        return $text;
    }
}
