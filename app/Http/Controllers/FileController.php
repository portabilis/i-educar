<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\File;
use App\Services\FileService;
use App\Services\UrlPresigner;
use Throwable;

class FileController extends Controller
{
    public function show(File $file)
    {
        $url = (new UrlPresigner())->getPresignedUrl(
            $file->url
        );

        return redirect($url);
    }

    public function upload(FileRequest $request, FileService $fileService)
    {
        $file = $request->file('file');

        try {
            $url = $fileService->upload($file);
        } catch (Throwable $e) {
            return [
                'error' => $e->getMessage(),
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
