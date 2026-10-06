<?php

use App\Models\Enums\FacialRecognitionPosition;
use App\Models\LegacyStudent;
use App\Services\FileService;
use App\Services\UrlPresigner;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_aluno;

    public $service;

    public function Gerar()
    {
        $this->titulo = 'Reconhecimento Facial - Detalhe';
        $this->cod_aluno = request('ref_cod_aluno');

        $student = LegacyStudent::find($this->cod_aluno);

        if (!$student) {
            $this->redirectNotFound(
                url: 'educar_aluno_lst.php',
                registro: 'aluno',
                codigo: $this->cod_aluno,
            );
        }

        $this->addDetalhe(detalhe: ['Código Aluno', $student->getKey()]);

        $this->addDetalhe(detalhe: ['Nome', $student->name]);

        $this->service = new FileService(new UrlPresigner);

        foreach (FacialRecognitionPosition::cases() as $position) {
            $this->getDetalhe(
                relation: $student,
                type: $position,
                name: $position->name()
            );
        }

        $this->url_cancelar = "educar_aluno_det.php?cod_aluno={$this->cod_aluno}";
        $this->url_editar = 'educar_reconhecimento_facial_cad.php?ref_cod_aluno=' . $this->cod_aluno;
        $this->largura = '100%';

        // Início > Escola > Alunos > Nome > Reconhecimento facial
        $this->breadcrumb(currentPage: 'Reconhecimento facial', breadcrumbs: [
            'educar_index.php' => 'Escola',
            'educar_aluno_lst.php' => 'Alunos',
            "educar_aluno_det.php?cod_aluno={$student?->getKey()}" => $student?->real_name,
        ]);
    }

    public function Formular()
    {
        $this->title = 'Reconhecimento Facial - Detalhes';
        $this->processoAp = '5781';
    }

    private function getDetalhe($relation, $type, $name)
    {
        $files = $this->service->getFiles(
            relation: $relation,
            type: $type
        );

        if ($files->isNotEmpty()) {
            $title = 'Reconhecimento Facial - ' . $name;
            $file = $files->first();
            $this->addDetalhe(
                [
                    $title,
                    "<img src='{$file->url}' alt='{$title}' style='max-width: 200px; max-height: 200px;' />",
                ]
            );
        }
    }
};
