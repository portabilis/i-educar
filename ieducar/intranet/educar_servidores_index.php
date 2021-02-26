<?php

$desvio_diretorio = '';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar");
        $this->processoAp = 71;
    }
}

class indice
{
    public function RenderHTML()
    {
        return '
                <table width=\'100%\' height=\'100%\'>
                    <tr align=center valign=\'top\'><td></td></tr>
                </table>
                ';
    }
}


