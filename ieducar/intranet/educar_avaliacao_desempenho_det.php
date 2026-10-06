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
        $this->titulo = 'Avaliação de desempenho - Detalhe';

        $avaliacao = PerformanceEvaluation::find(request()->integer('id'));

        if (!$avaliacao) {
            $this->redirectNotFound(
                url: 'educar_avaliacao_desempenho_lst.php',
                registro: 'avaliação de desempenho',
                feminino: true,
                codigo: request()->integer('id'),
            );
        }

        $obj_permissoes = new clsPermissoes;
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $this->addDetalhe(detalhe: ['Instituição', e($avaliacao->institution->name)]);
        }

        $this->addDetalhe(detalhe: ['Servidor', e($avaliacao->employee->person->name)]);
        $this->addDetalhe(detalhe: ['Avaliação', e($avaliacao->sequential)]);
        $this->addDetalhe(detalhe: ['Descrição', e($avaliacao->description)]);

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$avaliacao->employee_id}&ref_ref_cod_instituicao={$avaliacao->institution_id}";
            $this->url_editar = 'educar_avaliacao_desempenho_cad.php?id='. $avaliacao->id;
        }

        $this->url_cancelar = "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$avaliacao->employee_id}&ref_ref_cod_instituicao={$avaliacao->institution_id}";
        $this->largura = '100%';

        // Início > Servidores > Nome > Avaliação de desempenho > Avaliação N
        $this->breadcrumb(currentPage: "Avaliação {$avaliacao->sequential}", breadcrumbs: [
            url(path: 'intranet/educar_servidor_lst.php') => 'Servidores',
            url(path: "intranet/educar_servidor_det.php?cod_servidor={$avaliacao->employee_id}&ref_cod_instituicao={$avaliacao->institution_id}") => $avaliacao->employee->person->name,
            url(path: "intranet/{$this->url_cancelar}") => 'Avaliação de desempenho',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação de desempenho';
        $this->processoAp = '635';
    }
};
