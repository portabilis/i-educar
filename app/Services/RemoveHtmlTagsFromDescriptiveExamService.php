<?php

namespace App\Services;

use Avaliacao_Model_ParecerDescritivoAbstract;

class RemoveHtmlTagsFromDescriptiveExamService
{
    public const PATTERNS = [
        '/style=\"([^"]*)"/mi',
        '/<style.*?<\/style>/mi',
        '/(<style[\w\W]+style>)/mi',
        '/(?<=;|"|\s)font-family:[^;\']*(;)?/mi',
        '/font-family:[^;\']*(;)?/mi',
        '/(<font[^>]*>)|(<\/font>)/'
    ];

    public function execute(Avaliacao_Model_ParecerDescritivoAbstract $descriptiveExam): string
    {
        $FormattedDescriptiveExam = preg_replace(self::PATTERNS, '', $descriptiveExam->parecer);

        if ($FormattedDescriptiveExam === null) {
            throw new \Exception('Erro ao remover html tags dos pareceres descritivos');
        }

        return $FormattedDescriptiveExam;
    }

}
