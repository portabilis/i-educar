<?php

namespace App\Services\Educacenso;

use App\Models\EducacensoImport;
use App\User;
use Illuminate\Http\UploadedFile;

interface ImportServiceInterface
{

    public function __construct($school, User $user);

    public function handleFile(UploadedFile $file);

    public function createImportProcess($school);

    public function validateFile(UploadedFile $file);

    public function getSchoolNameByFile($school);

    public function getYear();
}
