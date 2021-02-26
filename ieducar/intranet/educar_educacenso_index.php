<?php

$desvio_diretorio = "";

return new class
{
    function RenderHTML()
    {
        return "
                <table width='100%' height='100%'>
                    <tr align=center valign='top'><td></td></tr>
                </table>
                ";
    }

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar" );
        $this->processoAp = 70;
    }
};




?>
