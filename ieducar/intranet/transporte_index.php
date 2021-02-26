<?php

$desvio_diretorio = '';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Transporte Escolar");
        $this->processoAp = '21234';
    }
}

class indice
{
    public function RenderHTML()
    {
        return '
                <table width=\'100%\' style=\'height: 100%;\'>
                    <tr align=center valign=\'top\'><td></td></tr>
                </table>
                ';
    }
}


