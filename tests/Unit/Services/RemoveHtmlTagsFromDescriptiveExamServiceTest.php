<?php

namespace Tests\Unit\Services;
use App\Services\RemoveHtmlTagsFromDescriptiveExamService;
use Avaliacao_Model_ParecerDescritivoComponente;
use Tests\TestCase;

class RemoveHtmlTagsFromDescriptiveExamServiceTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testRemoveHtmlTag($text, $pattern)
    {
        $avaliacaoModelParecerDescritivoComponente = new Avaliacao_Model_ParecerDescritivoComponente([
            'parecer' => $text,
        ]);

        $parecer = (new RemoveHtmlTagsFromDescriptiveExamService())->execute($avaliacaoModelParecerDescritivoComponente);
        self::assertTrue(preg_match($pattern,$parecer) === 0);
    }

    public function provideData()
    {
        return [
            [
                '<p><font face="KacstFarsi"><i><u><b>testessss</b></u></i></font></p>',
                '/(<font[^>]*>)|(<\/font>)/'
            ],
            [
                '<p style="margin-bottom: 0cm; line-height: 1px; background: transparent; font-size: medium;"><i><u><b>testessss</b></u></i></p>',
                '/style=\"([^"]*)"/mi'
            ],
            [
                '<style> font="Arial"; </style><p><i><u><b>testessss</b></u></i></p>',
                '/(<style[\w\W]+style>)/mi'
            ],
            [
                '<style> font="Arial"; </style><p><i><u><b>testessss</b></u></i></p>',
                '/<style.*?<\/style>/mi'
            ],
            [
                '<p><i><u><b><h1 style="text-align: center; font-family: MinecrafterReg;">testess Minecraft</h1></b></u></i></p>',
                '/(?<=;|"|\s)font-family:[^;\']*(;)?/mi'
            ],
            [
                '<p><i><u><b><h1 style="text-align: center; font-family: MinecrafterReg;">testess Minecraft</h1></b></u></i></p>',
                '/(?<=;|"|\s)font-family:[^;\']*(;)?/mi'
            ],
        ];
    }
}
