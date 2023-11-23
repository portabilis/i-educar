<?php

use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ano;

    public $ref_cod_instituicao;

    public $escola_em_andamento;

    public $segunda_fase = false;

    public $nome_url_sucesso = 'Analisar';

    public function Inicializar()
    {
        $codigoMenu = 846;

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: $codigoMenu,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $nomeTela = '1ª fase - Matrícula inicial';

        $this->breadcrumb(currentPage: $nomeTela, breadcrumbs: [
            url('intranet/educar_educacenso_index.php') => 'Educacenso',
        ]);

        $exportacao = $_POST['exportacao'];

        if ($exportacao) {
            $converted_to_iso88591 = mb_convert_encoding($exportacao, 'ISO-8859-1', 'UTF-8');

            $inepEscola = DB::selectOne('SELECT cod_escola_inep FROM modules.educacenso_cod_escola WHERE cod_escola = ?', [$_POST['escola']]);

            $nomeArquivo = $inepEscola->cod_escola_inep . '_' . date('dm_Hi') . '.txt';

            header('Content-type: text/plain');
            header('Content-Length: ' . strlen($converted_to_iso88591));
            header('Content-Disposition: attachment; filename=' . $nomeArquivo);
            echo $converted_to_iso88591;
            exit();
        }

        $this->acao_enviar = 'acaoExportar();';

        return 'Nova exportação';
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'enable_export', valor: (int) config('legacy.educacenso.enable_export'));
        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola']);
        $this->inputsHelper()->hidden(attrName: 'escola_em_andamento', inputOptions: ['value' => $this->escola_em_andamento]);

        if (!empty($this->ref_cod_escola)) {
            Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: '/vendor/legacy/Educacenso/Assets/Javascripts/Educacenso.js');
        }
    }

    public function Novo()
    {
        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-exportacao-educacenso.js');
    }

    public function Formular()
    {
        $this->title = 'Exportação Educacenso';
        $this->processoAp = 846;
    }
};
