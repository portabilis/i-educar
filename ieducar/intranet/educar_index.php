<?php

return new class {
    public function RenderHTML()
    {
        return '<!--
                <table width=\'100%\' style=\'height: 100%;\'>
                    <tr align=center valign=\'top\'><td><div id=\'flash-container\' align=\'right\' style=\'width: 200px; right: 10px;top: 27px; position: absolute;\'><p style=\'min-height: 0px;\'\' class=\'flash sucess\'>Olá! Alteramos o menu do lançamento de notas, agora, acesse apenas <strong>Movimentação > Faltas/Notas</strong> e pronto! Qualquer dúvida, entre em contato. :)</p></div></td></tr>
                </table>-->
                ';
    }

    public function Formular()
    {
        $this->title = 'Escola';
        $this->processoAp = 55;
    }
};
