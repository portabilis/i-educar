<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class download
{
	//path com o nome do arquivo
	var $file;

	//nome do arquivo
	var $base_name;

	var $download_name;

	function privBuildMimeArray() {
	      return array(
	         "mp3" => "audio/mpeg",
	         "wav" => "audio/x-wav",
	         "bmp" => "image/bmp",
	         "gif" => "image/gif",
	         "jpeg" => "image/jpeg",
	         "jpg" => "image/jpeg",
	         "jpe" => "image/jpeg",
	         "png" => "image/png",
	         "tiff" => "image/tiff",
	         "tif" => "image/tif",
	         "xml" => "text/xml",
	         "xsl" => "text/xml",
	         "mpeg" => "video/mpeg",
	         "mpg" => "video/mpeg",
	         "mpe" => "video/mpeg",
	         "avi" => "video/x-msvideo",
	         "pdf" => "pdf",
	         "doc" => "doc",
	         "pps" => "pps",
	         "cdr" => "cdr",

	      );
	   }

   function privFindType($ext) {
      // create mimetypes array
      $mimetypes = $this->privBuildMimeArray();

      // return mime type for extension
      if (isset($mimetypes[$ext])) {
         //return $mimetypes[$ext];
         return true;
      // if the extension wasn't found return octet-stream
      } else {
         return false;
      }

   }

   function getFileExtension()
   {
		$info = pathinfo($this->file);
		return $file_type = $info['extension'];
   }

   function download($file,$download_name = null)
   {
   		if(!file_exists($file))
   		{
   			die("arquivo inexistente");
   		}


		$this->file = $file;

		$file_info = pathinfo($this->file);

		$this->base_name = $file_info['basename'];
		$ext = $file_info['extension'];

		$this->download_name = $download_name == null ? $this->base_name : $this->download_name;

		if(!$this->privFindType($ext))
		{
			die('Acesso negado para este arquivo');
		}


       header("Pragma: public");
       header("Expires: 0");
       header('Content-type: application/octet-stream');


       header("Content-Disposition: attachment; filename=\"".$this->base_name."\";");
       set_time_limit(0);
       @readfile("$this->file") or die("File not found.");



   }
}

$down = new download($_GET['filename']);

?>
