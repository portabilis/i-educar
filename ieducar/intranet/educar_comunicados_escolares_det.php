<?php

use App\Models\SchoolNotice;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Comunicado Escolar - Detalhe';

        $registro = SchoolNotice::query()
            ->with(['institution', 'schools.person', 'schools.organization', 'user'])
            ->find(request()->integer('id'));

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_comunicados_escolares_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes;
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro->institution) {
                $this->addDetalhe(detalhe: ['Instituição', $registro->institution->nm_instituicao]);
            }
        }

        $this->addDetalhe(detalhe: ['Escolas', $registro->schools->map(fn ($e) => $e->name)->implode('<br>')]);
        $this->addDetalhe(detalhe: ['Título', $registro->title]);
        $this->addDetalhe(detalhe: ['Descrição', $registro->description]);
        $this->addDetalhe(detalhe: ['Data', $registro->date->format('d/m/Y')]);
        $this->addDetalhe(detalhe: ['Hora', $registro->hour->format('H:i')]);
        $this->addDetalhe(detalhe: ['Local', $registro->local]);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: SchoolNotice::PROCESS, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_comunicados_escolares_cad.php';
            $this->url_editar = "educar_comunicados_escolares_cad.php?id={$registro->getKey()}";
        }
        $this->url_cancelar = 'educar_comunicados_escolares_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do Comunicado Escolar', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Comunicado Escolar';
        $this->processoAp = SchoolNotice::PROCESS;
    }
};
