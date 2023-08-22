<?php

use App\Models\PerformanceEvaluation;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public function Gerar()
    {
        $this->titulo = 'Avaliação Desempenho - Detalhe';

        $avaliacao = PerformanceEvaluation::find(request()->integer('id'));

        if (!$avaliacao) {
            $this->simpleRedirect(url: 'educar_avaliacao_desempenho_lst.php');
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $this->addDetalhe(detalhe: ['Instituição', "{$avaliacao->institution->name}"]);
        }

        $this->addDetalhe(detalhe: ['Servidor', $avaliacao->employee->person->name]);
        $this->addDetalhe(detalhe: ['Avaliação', $avaliacao->sequential]);
        $this->addDetalhe(detalhe: ['Descrição', $avaliacao->description]);

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$avaliacao->employee_id}&ref_ref_cod_instituicao={$avaliacao->institution_id}";
            $this->url_editar = 'educar_avaliacao_desempenho_cad.php?id='. $avaliacao->id;
        }

        $this->url_cancelar = "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$avaliacao->employee_id}&ref_ref_cod_instituicao={$avaliacao->institution_id}";
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da avaliação de desempenho', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação Desempenho';
        $this->processoAp = '635';
    }
};
