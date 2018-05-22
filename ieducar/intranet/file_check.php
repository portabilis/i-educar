<?php

/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Api
 * @subpackage  Modules
 * @since       Arquivo disponÃ­vel desde a versÃ£o ?
 * @version     $Id$
 */

class FileController {

    var $file;
    var $errorMessage;
    var $maxSize;
    var $suportedExtensions;

    function __construct($file, $maxSize = NULL,
                             $suportedExtensions = NULL){

        
        $this->file = $file;       

        if ($maxSize!=null)
            $this->maxSize = $maxSize;
        else
            $this->maxSize = 250*1024;

        if ($suportedExtensions != null)
            $this->suportedExtensions = $suportedExtensions;
        else
            $this->suportedExtensions = array('jpg', 'pdf','png', 'doc', 'docx', 'xls');
    }

    function sendFile(){


        $tmp = $this->file["tmp_name"];
        include('s3_config.php');
        
        //Rename file name.
        $actual_file_name = $directory.time().md5($this->file["name"]);
        if($s3->putObjectFile($tmp, $bucket , $actual_file_name, S3::ACL_PUBLIC_READ) )
        {
                                                
            $s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_file_name;
            return $s3file;
        }
        else{
            $this->errorMessage = "Ocorreu um erro no servidor ao enviar arquivo. Tente novamente.";
            return '';
        }
    }

    function validateFile(){

        $msg='';

        $name = $this->file["name"];
        $size = $this->file["size"];
        $ext = $this->getExtension($name);


        if(strlen($name) > 0)
        {
            // File format validation
            if(in_array($ext,$this->suportedExtensions))
            {
                // File size validation
                if($size < $this->maxSize){
                    return true;   
                }
                else{
                    $this->errorMessage = "NÃ£o sÃ£o permitidos arquivos com mais de 250KB.";
                    return false;
                }
            }
            else{
                $this->errorMessage = "Deve ser enviado um arquivo do tipo jpg, pdf ou png.";
                return false;
            }
        }
        else{
            $this->errorMessage = "Selecione um arquivo."; 
            return false;
        }
        $this->errorMessage = "Arquivo invÃ¡lido."; 
        return false;
    }

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