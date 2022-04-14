<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileRelation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileService
{
    private $urlPresigner;

    public function __construct(UrlPresigner $urlPresigner)
    {
        $this->urlPresigner = $urlPresigner;
    }

    public function createFile(UploadedFile $uploadFile, $typeFileRelation, $relationId)
    {
        $url = $this->upload($uploadFile);

        $this->saveFile(
            $url,
            $uploadFile->getSize(),
            $uploadFile->getClientOriginalName(),
            $uploadFile->getClientOriginalExtension(),
            $typeFileRelation,
            $relationId
        );
    }

    public function saveFile($url, $size, $originalName, $extension, $typeFileRelation, $relationId)
    {
        DB::beginTransaction();

        try {
            $file = File::create([
                'url' => $url,
                'size' => $size,
                'original_name' => $originalName,
                'extension' => $extension,
            ]);

            FileRelation::create([
                'type' => $typeFileRelation,
                'relation_id' => $relationId,
                'file_id' => $file->id,
            ]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }

    public function getFiles($relation)
    {
        $files = $relation->files;

        foreach ($files as $file) {
            $file->url = $this->urlPresigner->getPresignedUrl($file->url);
        }

        return $files;
    }

    public function deleteFiles($deletedFiles)
    {
        foreach ($deletedFiles as $deletedFile) {
            DB::beginTransaction();

            try {
                $this->deleteFilesFromStorage($deletedFile);
                $filesRelation = FileRelation::query()
                    ->where('file_id', $deletedFile)
                    ->pluck('id')
                    ->first();
                FileRelation::destroy($filesRelation);
                File::destroy($deletedFile);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
            }
        }
    }

    public function deleteFilesFromStorage($deletedFile)
    {
        $url = File::query()
            ->where('id', $deletedFile)
            ->pluck('url')
            ->first();

        switch (config('filesystems.default')) {
            case 'local':
                $url = config('legacy.app.database.dbname') . '/' . implode('/', array_slice(explode('/', $url), 5));

                break;
            case 's3':
                $url = implode('/', array_slice(explode('/', $url), 3));

                break;
        }

        Storage::delete($url);
    }

    public function upload($uploadFile)
    {
        $tenant = config('legacy.app.database.dbname');
        Storage::put($tenant, $uploadFile);

        return Storage::url($uploadFile->hashName($tenant));
    }
}
