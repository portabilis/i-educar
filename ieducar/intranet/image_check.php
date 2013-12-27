<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Api
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão ?
 * @version     $Id$
 */

class PictureController {

    var $imageFile;
    var $errorMessage;
    var $maxWidth;
    var $maxHeight;
    var $maxSize;
    var $suportedExtensions;
    var $imageName;

    function PictureController($imageFile, $maxWidth = NULL, $maxHeight = NULL, $maxSize = NULL,
                             $suportedExtensions = NULL){

        
       $this->imageFile = $imageFile;
       

        if ($maxWidth!=null)
            $this->maxWidth = $maxWidth;
        else
            $this->maxWidth = 500;

        if ($maxHeight!=null)
            $this->maxHeight = $maxHeight;
        else
            $this->maxHeight = 500;

        if ($maxSize!=null)
            $this->maxSize = $maxSize;
        else
            $this->maxSize = 150*1024;

        if ($suportedExtensions != null)
            $this->suportedExtensions = $suportedExtensions;
        else
            $this->suportedExtensions = array('jpeg','jpg','gif','png');
    }

    /**
    * Envia imagem caso seja válida e retorna caminho
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
    function sendPicture($imageName){

        $this->imageName = $imageName;
        $tmp = $this->imageFile["tmp_name"];
        include('s3_config.php');
        //Rename image name.

        $actual_image_name = $directory.$this->imageName; 
        if($s3->putObjectFile($tmp, $bucket , $actual_image_name, S3::ACL_PUBLIC_READ) )
        {
                                                
            $s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_image_name;
            return $s3file;
        }
        else{
            $this->errorMessage = "Ocorreu um erro no servidor ao enviar foto. Tente novamente.";
            return '';
        }
    }

    /**
    * Verifica se a imagem é válida
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return boolean
    */
    function validatePicture(){

        $msg='';

        $name = $this->imageFile["name"];
        $size = $this->imageFile["size"];
        $ext = $this->getExtension($name);


        if(strlen($name) > 0)
        {
            // Verifica formato do arquivo
            if(in_array($ext,$this->suportedExtensions))
            {
                // Verifica tamanho do arquivo
                // @TODO Validar largura e altura da imagem
                if($size < $this->maxSize){
                    return true;   
                }
                else{
                    $this->errorMessage = "N&atilde;o &eacute; permitido fotos com mais de 150KB.";
                    return false;
                }
            }
            else{
                $this->errorMessage = "Deve ser enviado uma imagem do tipo jpeg, jpg, png ou gif.";
                return false;
            }
        }
        else{
            $this->errorMessage = "Selecione uma imagem."; 
            return false;
        }
        $this->errorMessage = "Imagem inv&aacute;lida."; 
        return false;
    }

    /**
    * Retorna a mensagem de erro
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
    function getErrorMessage(){
        return $this->errorMessage;
    }


    function getExtension($name) 
    {
        $i = strrpos($name,".");
        if (!$i)
          return "";
        $l = strlen($name) - $i;
        $ext = substr($name,$i+1,$l);

        return $ext;
    }
}

?>