<?php



use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class PictureController
{
    public $imageFile;
    public $errorMessage;
    public $maxWidth;
    public $maxHeight;
    public $maxSize;
    public $suportedExtensions;

    public function __construct(
        $imageFile,
        $maxWidth = null,
        $maxHeight = null,
        $maxSize = null,
        $suportedExtensions = null
    ) {
        $this->imageFile = $imageFile;

        if ($maxWidth!=null) {
            $this->maxWidth = $maxWidth;
        } else {
            $this->maxWidth = 500;
        }

        if ($maxHeight!=null) {
            $this->maxHeight = $maxHeight;
        } else {
            $this->maxHeight = 500;
        }

        if ($maxSize!=null) {
            $this->maxSize = $maxSize;
        } else {
            $this->maxSize = 2048*1024;
        }

        if ($suportedExtensions != null) {
            $this->suportedExtensions = $suportedExtensions;
        } else {
            $this->suportedExtensions = ['jpeg','jpg','gif','png'];
        }
    }

    /**
    * Envia imagem caso seja vÃ¡lida e retorna caminho
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    *
    * @return String
    */
    public function sendPicture()
    {
        $tmp = $this->imageFile['tmp_name'];

        $file = new File($tmp);

        $tenant = config('legacy.app.database.dbname');

        if (Storage::put($tenant, $file)) {
            return Storage::url($file->hashName($tenant));
        } else {
            $this->errorMessage = 'Ocorreu um erro no servidor ao enviar foto. Tente novamente.';

            return '';
        }
    }

    /**
    * Verifica se a imagem Ã© vÃ¡lida
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    *
    * @return boolean
    */
    public function validatePicture()
    {
        $msg='';

        $name = $this->imageFile['name'];
        $size = $this->imageFile['size'];
        $ext = $this->getExtension($name);

        if (strlen($name) > 0) {
            // File format validation
            if (in_array($ext, $this->suportedExtensions)) {
                // File size validation
                if ($size < $this->maxSize) {
                    return true;
                } else {
                    $this->errorMessage = 'O cadastro n&atilde;o pode ser realizado, a foto possui um tamanho maior do que o permitido.';

                    return false;
                }
            } else {
                $this->errorMessage = 'O cadastro n&atilde;o pode ser realizado, a foto possui um formato diferente daqueles permitidos.';

                return false;
            }
        } else {
            $this->errorMessage = 'Selecione uma imagem.';

            return false;
        }

        $this->errorMessage = 'Imagem inv&aacute;lida.';

        return false;
    }
    /**
    * Retorna a mensagem de erro
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    *
    * @return String
    */
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
