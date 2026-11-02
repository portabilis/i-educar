<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostLaudoRequest;
use App\Models\LegacyStudent;
use App\Services\FileService;
use Throwable;

class LaudoUploadController extends DiarioController
{
    public function __invoke(
        PostLaudoRequest $request,
        FileService $fileService
    ) {
        $alunoId = $request->integer('aluno_id');
        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'message' => 'O arquivo enviado é inválido.',
            ], 422);
        }

        try {
            $url = $fileService->upload($file);

            $fileService->saveFile(
                url: $url,
                size: $file->getSize(),
                originalName: $file->getClientOriginalName(),
                extension: $file->getClientOriginalExtension(),
                typeFileRelation: LegacyStudent::class,
                relationId: $alunoId,
                type: 'laudo'
            );
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Não foi possível salvar o laudo para o aluno informado.',
            ], 500);
        }

        return response()->json([
            'message' => 'Laudo salvo com sucesso.',
        ]);
    }
}
