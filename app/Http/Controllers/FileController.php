<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Services\FileService;
use Throwable;

class FileController extends Controller
{
    public function upload(FileRequest $request, FileService $fileService)
    {
        $file = $request->file('file');

        try {
            $url = $fileService->upload($file);
        } catch (Throwable $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        return [
            'file_url' => $url,
            'file_size' => $file->getSize(),
            'file_extension' => $file->getClientOriginalExtension(),
            'file_original_name' => $file->getClientOriginalName(),
        ];
    }
}
