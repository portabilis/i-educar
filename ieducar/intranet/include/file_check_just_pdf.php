<?php

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class FileControllerPdf
{
    public $file;
    public $errorMessage;
    public $maxSize;
    public $suportedExtensions;

    public function __construct(
        $file,
        $maxSize = null,
        $suportedExtensions = null
    ) {
        $this->file = $file;

        if ($maxSize!=null) {
            $this->maxSize = $maxSize;
        } else {
            $this->maxSize = 2048*1024;
        }

        if ($suportedExtensions != null) {
            $this->suportedExtensions = $suportedExtensions;
        } else {
            $this->suportedExtensions = ['pdf'];
        }
    }

    public function sendFile()
    {
        $tmp = $this->file['tmp_name'];

        $tenant = config('legacy.app.database.dbname');

        $file = new File($tmp);

        if (Storage::put($tenant, $file)) {
            return Storage::url($file->hashName($tenant));
        } else {
            $this->errorMessage = 'Ocorreu um erro no servidor ao enviar foto. Tente novamente.';

            return '';
        }
    }

    public function validateFile()
    {
        $msg='';

        $name = $this->file['name'];
        $size = $this->file['size'];
        $ext = $this->getExtension($name);

        if (strlen($name) > 0) {
            // File format validation
            if (in_array($ext, $this->suportedExtensions)) {
                // File size validation
                if ($size < $this->maxSize) {
                    return true;
                } else {
                    $this->errorMessage = 'NÃ£o sÃ£o permitidos arquivos com mais de 2MB.';

                    return false;
                }
            } else {
                $this->errorMessage = 'Deve ser enviado um arquivo do tipo pdf.';

                return false;
            }
        } else {
            $this->errorMessage = 'Selecione um arquivo.';

            return false;
        }
        $this->errorMessage = 'Arquivo invÃ¡lido.';

        return false;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getExtension($name)
    {
        $i = strrpos($name, '.');
        if (!$i) {
            return '';
        }
        $l = strlen($name) - $i;
        $ext = substr($name, $i+1, $l);

        return $ext;
    }
}
