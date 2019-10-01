<?php

namespace App\Services\Educacenso\v2019;

use App\Services\Educacenso\ImportServiceInterface;
use App\Services\Educacenso\ImportTrait;
use App\User;
use Illuminate\Http\UploadedFile;

class ImportService implements ImportServiceInterface
{
    use ImportTrait;

    private $school;

    /**
     * @var User
     */
    private $user;

    public function __construct($school, User $user)
    {
        $this->school = $school;
        $this->user = $user;
    }

    public function getYear()
    {
        return 2019;
    }

    public function getSchoolNameByFile($school)
    {
        $columns = explode('|', $school[0]);

        return $columns[9];
    }

    /**
     * todo: Implementar validação do arquivo
     * @param UploadedFile $file
     */
    public function validateFile(UploadedFile $file)
    {

    }
}
