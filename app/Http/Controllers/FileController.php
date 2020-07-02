<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Services\FileService;
use Throwable;

class FileController extends Controller
{
    public function upload(FileRequest $request, FileService $fileService)
    {
        try {
            $url = $fileService->upload($request->file()['file']);
        } catch(Throwable $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        return [
            'file_url' => $url
        ];
    }
}
