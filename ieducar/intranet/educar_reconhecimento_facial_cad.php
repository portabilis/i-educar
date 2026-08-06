<?php

use App\Models\Enums\FacialRecognitionPosition;
use App\Models\File;
use App\Models\FileRelation;
use App\Models\LegacyStudent;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_aluno;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_aluno = request('ref_cod_aluno');

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 581, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_aluno_lst.php');

        $this->url_cancelar = "educar_reconhecimento_facial_det.php?ref_cod_aluno={$this->cod_aluno}";

        $this->breadcrumb(currentPage: 'Reconhecimento Facial - Editar', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $opcoes = FacialRecognitionPosition::getDescriptiveValues()->prepend('Selecione', '');
        $options = [
            'label' => 'Tipo de Imagem',
            'value' => null,
            'resources' => $opcoes,
            'inline' => true,
            'required' => true,
        ];

        $this->inputsHelper()->select(attrName: 'type', inputOptions: $options);

        $this->addHtml(html: view(view: 'uploads.upload', data: ['files' => [], 'label' => 'Reconhecimento Facial'])->render());

    }

    public function Novo()
    {
        $file_url = request('file_url');
        $type = request('type');
        $aluno = request('ref_cod_aluno');

        if ($file_url) {
            $newFiles = json_decode($file_url);
            foreach ($newFiles as $file) {
                $file = File::create([
                    'url' => $file->url,
                    'size' => $file->size,
                    'original_name' => $file->originalName,
                    'extension' => $file->extension,
                ]);

                FileRelation::updateOrCreate([
                    'relation_type' => LegacyStudent::class,
                    'relation_id' => $aluno,
                    'type' => $type,
                ], [
                    'relation_type' => LegacyStudent::class,
                    'relation_id' => $aluno,
                    'type' => $type,
                    'file_id' => $file->getKey(),
                ]);

                LegacyStudent::findOrFail($aluno)->update([
                    'updated_at' => now(),
                ]);
            }

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_reconhecimento_facial_det.php?ref_cod_aluno=' . $aluno);
        }

        $this->mensagem .= 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Reconhecimento Facial - Editar';
        $this->processoAp = '5781';
    }
};
