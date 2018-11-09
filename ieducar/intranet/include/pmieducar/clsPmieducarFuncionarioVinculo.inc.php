<?php

require_once 'include/clsBanco.inc.php';

class clsPmieducarFuncionarioVinculo
{
    public function lista()
    {
        $retorno = [];
        $db = new clsBanco;

        $db->Consulta('SELECT cod_funcionario_vinculo, nm_vinculo FROM portal.funcionario_vinculo ORDER BY cod_funcionario_vinculo ASC;');

        while ($db->ProximoRegistro()) {
            $item = $db->Tupla();

            $retorno[$item['cod_funcionario_vinculo']] = $item['nm_vinculo'];
        }

        return $retorno;
    }
}
