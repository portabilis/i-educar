<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'intranet/include/clsBanco.inc.php';
require_once 'intranet/include/clsBase.inc.php';

class UsuarioExportController extends ApiCoreController
{
    protected function exportUsers()
    {
        $instituicao = $this->getRequest()->instituicao;
        $escola = $this->getRequest()->escola;
        $status = $this->getRequest()->status;
        $tipoUser = $this->getRequest()->tipoUsuario;
        $getUsers = new clsPmieducarUsuario();
        $getUsers->setOrderby('nome ASC');

        $lstUsers = $getUsers->listaExportacao(
            $escola,
            $instituicao,
            $tipoUser,
            $status
        );

        //Linhas do cabeçalho
        $csv .= 'Nome,';
        $csv .= 'Matricula,';
        $csv .= 'E-mail,';
        $csv .= 'Status,';
        $csv .= Portabilis_String_Utils::toLatin1('Tipo_usuário,');
        $csv .= Portabilis_String_Utils::toLatin1('Instituição,');
        $csv .= 'Escola,';
        $csv .= PHP_EOL;

        foreach ($lstUsers as $row) {
            $csv .= '"' . $row['nome'] . '",';
            $csv .= '"' . $row['matricula'] . '",';
            $csv .= '"' . $row['email'] . '",';
            $csv .= '"' . $row['status'] . '",';
            $csv .= '"' . $row['nm_tipo'] . '",';
            $csv .= '"' . $row['nm_instituicao'] . '",';
            $csv .= '"' . $row['nm_escola'] . '",';
            $csv .= PHP_EOL;
        }

        return ['conteudo' => Portabilis_String_Utils::toUtf8($csv)];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'exportarDados')) {
            $this->appendResponse($this->exportUsers());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
