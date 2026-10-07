<?php

use App\Models\LegacyInstitutionDocument;
use Illuminate\Support\Facades\Auth;

class InstituicaoDocumentacaoController extends ApiCoreController
{
    /**
     * Processo da tela de documentação padrão, que inclui e exclui os documentos.
     */
    private const PROCESSO_DOCUMENTACAO = 578;

    protected function validatesAuthorizationToChangeDocuments()
    {
        return $this->validatesAuthorizationToAnyProcess([self::PROCESSO_DOCUMENTACAO], 'modify');
    }

    protected function insertDocuments()
    {
        LegacyInstitutionDocument::create([
            'instituicao_id' => request()->integer('instituicao_id'),
            'titulo_documento' => request()->string('titulo_documento'),
            'url_documento' => request()->string('url_documento'),
            'ref_usuario_cad' => Auth::id() ?? request()->integer('ref_usuario_cad'),
            'ref_cod_escola' => request()->integer('ref_cod_escola'),
        ]);

        return [
            'id' => LegacyInstitutionDocument::query()->select('id')->max('id'),
        ];
    }

    protected function getDocuments()
    {
        $instituitionId = request()->integer('instituicao_id');

        $documents = LegacyInstitutionDocument::query()
            ->select([
                'id',
                'titulo_documento',
                'url_documento',
                'ref_usuario_cad',
                'ref_cod_escola',
            ])
            ->where('instituicao_id', $instituitionId)
            ->orderByDesc('id')
            ->get()
            ->toArray();

        return ['documentos' => $documents];
    }

    protected function deleteDocuments()
    {
        $documentId = request()->integer('id');

        return LegacyInstitutionDocument::query()
            ->whereKey($documentId)
            ->delete();
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'insertDocuments')) {
            if ($this->validatesAuthorizationToChangeDocuments()) {
                $this->appendResponse($this->insertDocuments());
            }
        } elseif ($this->isRequestFor('get', 'getDocuments')) {
            $this->appendResponse($this->getDocuments());
        } elseif ($this->isRequestFor('get', 'deleteDocuments')) {
            if ($this->validatesAuthorizationToChangeDocuments()) {
                $this->appendResponse($this->deleteDocuments());
            }
        }
    }
}
