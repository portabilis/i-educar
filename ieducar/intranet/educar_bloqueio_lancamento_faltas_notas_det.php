<?php

return new class extends clsDetalhe {
    public $titulo;

    public $cod_bloqueio;

    public function Gerar()
    {
        $this->titulo = 'Bloqueio de lançamento de notas e faltas por etapa - Detalhe';

        $this->cod_bloqueio = $_GET['cod_bloqueio'];

        $tmp_obj = new clsPmieducarBloqueioLancamentoFaltasNotas($this->cod_bloqueio);

        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_bloqueio_lancamento_faltas_notas_lst.php');
        }

        //Nome da etapa
        $etapas = [
      1 => '1ª Etapa',
      2 => '2ª Etapa',
      3 => '3ª Etapa',
      4 => '4ª Etapa'
    ];
        $registro['etapa'] = $etapas[$registro['etapa']];

        // Dados da escola
        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];

        if ($registro['ano']) {
            $this->addDetalhe(['Ano', $registro['ano']]);
        }

        if ($registro['ref_cod_escola']) {
            $this->addDetalhe(['Escola', $registro['ref_cod_escola']]);
        }

        if ($registro['etapa']) {
            $this->addDetalhe(['Etapa', $registro['etapa']]);
        }

        if ($registro['data_inicio']) {
            $this->addDetalhe(['Data início', dataToBrasil($registro['data_inicio'])]);
        }

        if ($registro['data_fim']) {
            $this->addDetalhe(['Data final', dataToBrasil($registro['data_fim'])]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(999848, $this->pessoa_logada, 7)) {
            $this->url_novo   = 'educar_bloqueio_lancamento_faltas_notas_cad.php';
            $this->url_editar = sprintf('educar_bloqueio_lancamento_faltas_notas_cad.php?cod_bloqueio=%d', $this->cod_bloqueio);
        }

        $this->url_cancelar = 'educar_bloqueio_lancamento_faltas_notas_lst.php';
        $this->largura      = '100%';

        $this->breadcrumb('Detalhe de bloqueio de lançamento de notas e faltas por etapa', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Bloqueio de lanÃ§amento de notas e faltas por etapa';
        $this->processoAp = 999848;
    }
};
