<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileRelation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function createFile(UploadedFile $uploadFile, $typeFileRelation, $relationId)
    {
        $url = $this->upload($uploadFile);

        $this->saveFile($url, $uploadFile->getType(), $typeFileRelation, $relationId);
    }

    public function saveFile($url, $type, $typeFileRelation, $relationId)
    {
        $file = File::create([
            'url' => $url,
            'type' => $type,
        ]);

        FileRelation::create([
            'type' => $typeFileRelation,
            'relation_id' => $relationId,
            'file_id' => $file->id,
        ]);
    }

    public function getFiles($typeFileRelation, $relationId)
    {
        $files = File::query()->whereHas('relations', function($queryRelation) use ($typeFileRelation, $relationId) {
            $queryRelation
                ->where('type', $typeFileRelation)
                ->where('relation_id', $relationId);
        })->get();

        $urlPresigner = new UrlPresigner();

        foreach ($files as $file) {
            $file->url = $urlPresigner->getPresignedUrl($file->url);
        }

        return $files;
    }

    public function deleteFiles($deletedFiles)
    {
        $this->deleteFilesFromStorage($deletedFiles);
        $filesRelations = FileRelation::query()
            ->whereIn('file_id', $deletedFiles)
            ->pluck('id')
            ->toArray();
        FileRelation::destroy($filesRelations);
        File::destroy($deletedFiles);
    }

    public function deleteFilesFromStorage($deletedFiles)
    {
        $urls = File::query()
            ->whereIn('id', $deletedFiles)
            ->pluck('url')
            ->toArray();

        foreach ($urls as $url) {
            $url = implode('/', array_slice(explode('/', $url), 3));
            Storage::delete($url);
        }
    }

    public function upload($uploadFile)
    {
        $tenant = config('legacy.app.database.dbname');
        Storage::put($tenant, $uploadFile);

        return Storage::url($uploadFile->hashName($tenant));
    }
}
