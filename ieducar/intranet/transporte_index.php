<?php

$desvio_diretorio = '';


return new class
{
    public function RenderHTML()
    {
        return '
                <table width=\'100%\' style=\'height: 100%;\'>
                    <tr align=center valign=\'top\'><td></td></tr>
                </table>
                ';

    }

    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Transporte Escolar");
        $this->processoAp = '21234';
    }
};


