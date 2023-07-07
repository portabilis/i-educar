<?php

use App\Models\LegacySequenceGrade;

return new class extends clsDetalhe
{
    public $titulo;

    public $ref_serie_origem;

    public $ref_serie_destino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $id;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Sequência Enturmação - Detalhe';

        $this->id = $_GET['id'];

        $registro = LegacySequenceGrade::query()
            ->with([
                'gradeOrigin:cod_serie,nm_serie,ref_cod_curso',
                'gradeDestiny:cod_serie,nm_serie,ref_cod_curso',
                'gradeOrigin.course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'gradeDestiny.course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'gradeOrigin.course.institution:cod_instituicao,nm_instituicao',
            ])
            ->find($this->id);

        if (!$registro) {
            $this->simpleRedirect('educar_sequencia_serie_lst.php');
        }
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro->gradeOrigin->course->institution) {
                $this->addDetalhe(['Instituição', "{$registro->gradeOrigin->course->institution->name}"]);
            }
        }
        if ($registro->gradeOrigin->course) {
            $this->addDetalhe(['Curso Origem', "{$registro->gradeOrigin->course->name}"]);
        }
        if ($registro->gradeOrigin) {
            $this->addDetalhe(['Série Origem', "{$registro->gradeOrigin->name}"]);
        }
        if ($registro->gradeDestiny->course) {
            $this->addDetalhe(['Curso Destino', "{$registro->gradeDestiny->course->name}"]);
        }
        if ($registro->gradeDestiny) {
            $this->addDetalhe(['Série Destino', "{$registro->gradeDestiny->name}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_sequencia_serie_cad.php';
            $this->url_editar = "educar_sequencia_serie_cad.php?id={$this->id}";
        }

        $this->url_cancelar = 'educar_sequencia_serie_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da sequência de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Sequência Enturmação';
        $this->processoAp = '587';
    }
};
