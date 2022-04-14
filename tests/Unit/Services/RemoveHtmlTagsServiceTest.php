<?php

namespace Tests\Unit\Services;

use App\Services\RemoveHtmlTagsStringService;
use Tests\TestCase;

class RemoveHtmlTagsServiceTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testRemoveHtmlTag(string $text, string $pattern)
    {
        $parecer = (new RemoveHtmlTagsStringService())->execute($text);
        self::assertTrue(preg_match($pattern, $parecer) === 0);
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
