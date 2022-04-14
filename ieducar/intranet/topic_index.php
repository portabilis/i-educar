<?php

return new class {
    public function RenderHTML()
    {
        return '
                <table width=\'100%\' height=\'100%\'>
                    <tr align=center valign=\'top\'><td><img src=\'imagens/i-pauta/splashscreen.jpg\' style=\'padding-top: 50px\'></td></tr>
                </table>
                ';
    }

    public function Formular()
    {
        $this->title = 'i-OpeTopicE';
        $this->processoAp = '459';
    }
};
