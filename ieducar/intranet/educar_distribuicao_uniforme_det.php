<?php

use App\Models\UniformDistribution;

return new class extends clsDetalhe {
    public $titulo;
    public $cod_distribuicao_uniforme;
    public $ref_cod_aluno;

    public function Gerar()
    {
        $this->titulo = 'Distribuições de uniforme escolar - Detalhe';
        $this->cod_distribuicao_uniforme = $_GET['cod_distribuicao_uniforme'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $uniformDistribution = UniformDistribution::find($this->cod_distribuicao_uniforme);

        if (!$uniformDistribution) {
            $this->simpleRedirect("educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $nm_aluno = $uniformDistribution->student->name;

        if ($nm_aluno) {
            $this->addDetalhe([
                'Aluno',
                "{$nm_aluno}"
            ]);
        }

        if ($uniformDistribution->year) {
            $this->addDetalhe([
                'Ano',
                "{$uniformDistribution->year}"
            ]);
        }

        if ($uniformDistribution->type) {
            $this->addDetalhe([
                'Tipo',
                $uniformDistribution->type
            ]);
        }

        if ($uniformDistribution->created_at) {
            $this->addDetalhe([
                'Data da distribuição',
                $uniformDistribution->distribution_date?->format('d/m/Y')
            ]);
        }

        $this->addDetalhe([
            'Escola fornecedora',
            $uniformDistribution->school->name
        ]);

        if ($uniformDistribution->complete_kit) {
            $this->addDetalhe([
                'Recebeu kit completo',
                'Sim'
            ]);
        } else {
            $this->addDetalhe([
                'Recebeu kit completo',
                'Não'
            ]);
            $this->addDetalhe([
                'Quantidade de agasalhos (jaqueta)',
                $uniformDistribution->coat_pants_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de agasalhos (calça)',
                $uniformDistribution->coat_jacket_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de camisetas (manga curta)',
                $uniformDistribution->shirt_short_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de camisetas (manga longa)',
                $uniformDistribution->shirt_long_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de camisetas infantis (sem manga)',
                $uniformDistribution->kids_shirt_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de calça jeans',
                $uniformDistribution->pants_jeans_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de meias',
                $uniformDistribution->meias_qtd ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de saias',
                $uniformDistribution->skirt_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Bermudas tactel (masculino)',
                $uniformDistribution->shorts_tactel_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Bermudas coton (feminino)',
                $uniformDistribution->shorts_coton_qty ?: '0'
            ]);
            $this->addDetalhe([
                'Quantidade de tênis',
                $uniformDistribution->sneakers_qty ?: '0'
            ]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->url_novo = "educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$uniformDistribution->student_id}";
            $this->url_editar = "educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}";
        }

        $this->url_cancelar = "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$uniformDistribution->student_id}";
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Distribuições de uniforme escolar', breadcrumbs: [
            'educar_index.php' => 'Escola'
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribuições de uniforme escolar';
        $this->processoAp = '578';
    }
};
