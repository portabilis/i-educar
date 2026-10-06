<?php

use App\Models\LegacyCalendarDayReason;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_calendario_dia_motivo;

    public $ref_cod_escola;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $sigla;

    public $descricao;

    public $tipo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Calendário Dia Motivo - Detalhe';

        $this->cod_calendario_dia_motivo = $_GET['cod_calendario_dia_motivo'];

        $registro = LegacyCalendarDayReason::find($this->cod_calendario_dia_motivo);

        if (!$registro) {
            $this->redirectNotFound(
                url: 'educar_calendario_dia_motivo_lst.php',
                registro: 'motivo de dia do calendário',
                codigo: $this->cod_calendario_dia_motivo,
            );
        }

        $this->addDetalhe(['Instituição', e($registro->institution_name)]);

        $this->addDetalhe(['Escola', e($registro->school_name)]);

        $this->addDetalhe(['Motivo', e($registro->name)]);

        $this->addDetalhe(['Sigla', e($registro->sigla)]);

        $this->addDetalhe(['Descricão', e($registro['descricao'])]);

        $this->addDetalhe(['Tipo', e($registro->type)]);

        $obj_permissao = new clsPermissoes;
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_calendario_dia_motivo_cad.php';
            $this->url_editar = "educar_calendario_dia_motivo_cad.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}";
        }
        $this->url_cancelar = 'educar_calendario_dia_motivo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do motivo de dias do calendário', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia Motivo';
        $this->processoAp = '576';
    }
};
