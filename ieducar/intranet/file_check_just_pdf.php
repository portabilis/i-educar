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
 * @author    	Paula Bonot <bonot@portabilis.com.br>
 * @category  	i-Educar
 * @license   	@@license@@
 * @package   	Api
 * @subpackage  Modules
 * @since   	Arquivo disponível desde a versão ?
 * @version  	$Id$
 */

class FileController {

	var $file;
    var $errorMessage;
    var $maxSize;
    var $suportedExtensions;

    function FileController($file, $maxSize = NULL,
                             $suportedExtensions = NULL){

        $this->file = $file;

        if ($maxSize!=null)
            $this->maxSize = $maxSize;
        else
            $this->maxSize = 250*1024;

        if ($suportedExtensions != null)
            $this->suportedExtensions = $suportedExtensions;
        else
            $this->suportedExtensions = array('pdf');
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
                    $this->errorMessage = "Não são permitidos arquivos com mais de 250KB.";
                    return false;
                }
            }
            else{
                $this->errorMessage = "Deve ser enviado um arquivo do tipo pdf.";
                return false;
            }
        }
        else{
            $this->errorMessage = "Selecione um arquivo.";
            return false;
        }
        $this->errorMessage = "Arquivo inválido.";
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