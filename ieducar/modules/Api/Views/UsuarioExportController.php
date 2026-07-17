<?php

use App\Models\LegacyUser;

class UsuarioExportController extends ApiCoreController
{
    protected function exportUsers()
    {
        $instituicao = $this->getRequest()->instituicao;
        $escola = $this->getRequest()->escola;
        $status = $this->getRequest()->status;
        $tipoUser = $this->getRequest()->tipoUsuario;
        $subEscola = "(SELECT REPLACE(TEXTCAT_ALL(relatorio.get_nome_escola(ref_cod_escola)), '<br>', ',') FROM pmieducar.escola_usuario WHERE ref_cod_usuario = usuario.cod_usuario"
            . (is_numeric($escola) ? ' AND ref_cod_escola = ?' : '')
            . ') AS nm_escola';

        $lstUsers = LegacyUser::query()
            ->join('cadastro.pessoa', 'cadastro.pessoa.idpes', 'pmieducar.usuario.cod_usuario')
            ->leftJoin('cadastro.fisica', 'cadastro.fisica.idpes', 'cadastro.pessoa.idpes')
            ->join('portal.funcionario', 'portal.funcionario.ref_cod_pessoa_fj', 'cadastro.pessoa.idpes')
            ->join('pmieducar.tipo_usuario', function ($j) {
                $j->on('pmieducar.tipo_usuario.cod_tipo_usuario', 'pmieducar.usuario.ref_cod_tipo_usuario');
                $j->where('pmieducar.tipo_usuario.ativo', 1);
            })
            ->join('pmieducar.instituicao', 'pmieducar.instituicao.cod_instituicao', 'pmieducar.usuario.ref_cod_instituicao')
            ->selectRaw(
                "nome,
                 matricula,
                 funcionario.email AS funcionario_email,
                 cpf,
                 CASE WHEN funcionario.ativo = 1 THEN 'Ativo' ELSE 'Inativo' END AS status,
                 nm_tipo,
                 nm_instituicao,
                 {$subEscola}",
                is_numeric($escola) ? [$escola] : []
            )
            ->when(is_numeric($escola), fn ($q) => $q->whereRaw('EXISTS (SELECT 1 FROM pmieducar.escola_usuario WHERE ref_cod_usuario = usuario.cod_usuario AND ref_cod_escola = ?)', [$escola]))
            ->when(is_numeric($instituicao), fn ($q) => $q->where('ref_cod_instituicao', $instituicao))
            ->when(is_numeric($tipoUser), fn ($q) => $q->where('ref_cod_tipo_usuario', $tipoUser))
            ->when(is_numeric($status) || $status, fn ($q) => $q->where('funcionario.ativo', $status))
            ->orderBy('nome')
            ->get();

        // Linhas do cabeçalho
        $csv = 'Nome,';
        $csv .= 'Matricula,';
        $csv .= 'E-mail,';
        $csv .= 'CPF,';
        $csv .= 'Status,';
        $csv .= 'Tipo_usuário,';
        $csv .= 'Instituição,';
        $csv .= 'Escola,';
        $csv .= PHP_EOL;

        foreach ($lstUsers as $row) {
            $csv .= '"' . $row['nome'] . '",';
            $csv .= '"' . $row['matricula'] . '",';
            $csv .= '"' . $row['funcionario_email'] . '",';
            $csv .= '"' . $row['cpf'] . '",';
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
