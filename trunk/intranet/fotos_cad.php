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

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Fotos!" );
		$this->processoAp = "27";
	}
}

class indice extends clsCadastro
{
	var $id_foto;
	var $id_pessoa;
	var $nm_credito;
	var $largura;
	var $altura;
	var $secao;
	var $data_foto;
	var $titulo;
	var $descricao;
	var $foto;

	var $nome_;

	function Inicializar()
	{
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		 
		if (@$_GET['id_foto'])
		{
			$this->id_foto = @$_GET['id_foto'];
			
			$objPessoa = new clsPessoaFisica();
			$db = new clsBanco();
			$db->Consulta( "SELECT f.ref_ref_cod_pessoa_fj, f.cod_foto_portal, f.titulo, f.descricao, f.data_foto, f.caminho, f.nm_credito, f.altura, f.largura, f.ref_cod_foto_secao FROM foto_portal f WHERE cod_foto_portal=$this->id_foto" );
			if ($db->ProximoRegistro())
			{
				list($this->cod_pessoa, $this->id_foto, $this->titulo, $this->descricao, $this->data_foto, $this->foto, $this->nm_credito, $this->altura, $this->largura, $this->secao ) = $db->Tupla();
				list($this->nome_) = $objPessoa->queryRapida($this->cod_pessoa, "nome");
				$this->data_foto= date('d/m/Y', strtotime(substr($this->data_foto,0,19)));

				$this->fexcluir = true;
				$retorno = "Editar";
			}
			else
			{
				$dba = new clsBanco();
				list ($this->nome_) = $objPessoa->queryRapida($id_pessoa, "nome");
							
				$this->data_foto = date('d/m/Y');
			}
		}
		else
		{
			$objPessoa = new clsPessoaFisica();
			$dba = new clsBanco();
			list($this->nome_) = $objPessoa->queryRapida($id_pessoa, "nome");
			$this->data_foto = date('d/m/Y');
		}
		$this->url_cancelar = ($retorno == "Editar") ? "fotos_det.php?id_foto=$this->id_foto" : "fotos_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->largura = "100%";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_foto", $this->id_foto );
		$this->campoRotulo( "pessoa", "Respons&aacute;vel", $this->nome_);
		$this->campoTexto( "titulo", "Titulo", $this->titulo, "50", "100", true );
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o",  $this->descricao, "50", "4", false );
		
		$lista = array(""=>"Selecione");
		$obj = new clsFuncionario();
		$db = new clsBanco();
		
		while ($db->ProximoRegistro())
		{
			list($id, $nm) = $db->Tupla();
			$lista[$id] = $nm;
		}

		$this->campoTexto( "nm_credito", "Crédito", $this->nm_credito,50,255);
		
//		$lista = array();
//		$db->Consulta( "SELECT cod_foto_secao, nm_secao FROM foto_secao" );
//		while ($db->ProximoRegistro())
//		{
//			list($id, $nm) = $db->Tupla();
//			$lista[$id] = $nm;
//		}
//		$this->campoLista("secao", "Seção",  $lista, $this->secao);
		
		
		$this->data_foto = str_replace("%2F", "/", $this->data_foto);
		$this->campoOculto( "data_foto", $this->data_foto);
		$this->campoTextoInv( "data_foto_", "Data", $this->data_foto,  "15", "15", true);

		
		$this->campoArquivo("foto", "Foto", $this->foto, "50");

	}

	function Novo() 
	{
		global $HTTP_POST_FILES;

		if ( !empty($HTTP_POST_FILES['foto']['name']) )
		{
			$fotoOriginal = "tmp/".$HTTP_POST_FILES['foto']['name'];
			if (file_exists($fotoOriginal))
			{
				unlink($fotoOriginal);
			}
			copy($HTTP_POST_FILES['foto']['tmp_name'], $fotoOriginal);

			list ($imagewidth, $imageheight, $img_type) = getImageSize($fotoOriginal);
			$src_img_original = "";

			$fim_largura = $imagewidth;
			$fim_altura = $imageheight;
			$extensao = ($img_type == "2") ? ".jpg" : (($img_type == "3") ? ".png" : "");
			$nome_do_arquivo = date('Y-m-d-h-i')."-".substr(md5($fotoOriginal), 0, 10).$extensao;
			$caminhoDaBig = "fotos/big/{$nome_do_arquivo}";
			$caminhoDaSBig = "fotos/sbig/{$nome_do_arquivo}";
			
			if ($imagewidth > 700 && $imageheight < $imagewidth)
			{
				$new_w = 500;
				$ratio = ($imagewidth / $new_w);
				$new_h = ceil($imageheight / $ratio);
				
				if ( !file_exists($caminhoDaBig) )
				{
					if ($img_type=="2")
					{
						$src_img_original = imagecreatefromjpeg($fotoOriginal);
						$dst_img = imagecreatetruecolor($new_w,$new_h);
						
						imagecopyresized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,imagesx($src_img_original),imagesy($src_img_original));
						imagejpeg($dst_img,$caminhoDaBig);
					}
					else if ($img_type=="3")
					{
						$src_img_original=@ImageCreateFrompng($fotoOriginal);

						$dst_img=@imagecreatetruecolor($new_w,$new_h);
						ImageCopyResized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,ImageSX($src_img_original),ImageSY($src_img_original));
						Imagepng($dst_img, $caminhoDaBig);
					}
				}
			}
			elseif ($imagewidth > 400 && $imageheight>$imagewidth)
			{
				$new_w = 400;
				$ratio = ($imagewidth / $new_w);
				$new_h = ceil($imageheight / $ratio);
				
				$fim_largura = $new_w;
				$fim_altura = $new_h;

				if ( !file_exists($caminhoDaBig) )
				{
					if ($img_type=="2")
					{
						$src_img_original = @imagecreatefromjpeg($fotoOriginal);
						$dst_img = @imagecreatetruecolor($new_w,$new_h);
						imagecopyresized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,imagesx($src_img_original),imagesy($src_img_original));
						imagejpeg($dst_img, $caminhoDaBig);
					}
					else if ($img_type=="3")
					{
						$src_img_original=@ImageCreateFrompng($fotoOriginal);

						$dst_img=@imagecreatetruecolor($new_w,$new_h);
						ImageCopyResized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,ImageSX($src_img_original),ImageSY($src_img_original));
						Imagepng($dst_img, $caminhoDaBig);
					}
				}
				
			}
			else
			{
				if ( !file_exists($caminhoDaBig) )
				{
					copy ($fotoOriginal, $caminhoDaBig);

					if ($img_type=="2")
					{
						$src_img_original = @imagecreatefromjpeg($fotoOriginal);
					}
					else if ($img_type=="3")
					{
						$src_img_original=@imagecreatefrompng($fotoOriginal);
					}
				}
			}
			
			$new_w = 100;
			$ratio = ($imagewidth / $new_w);
			$new_h = round($imageheight / $ratio);

			$caminhoDaSmall = "fotos/small/{$nome_do_arquivo}";
			
			if ( !file_exists($caminhoDaSmall) )
			{
				if ($img_type=="2")
				{
					$dst_img = @imagecreatetruecolor($new_w,$new_h);
					@imagecopyresized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,imagesx($src_img_original),imagesy($src_img_original));

					@imagejpeg($dst_img, $caminhoDaSmall);
					
				}
				else if ($img_type=="3")
				{
					$dst_img=@imagecreatetruecolor($new_w,$new_h);
					@imageCopyResized($dst_img,$src_img_original,0,0,0,0,$new_w,$new_h,ImageSX($src_img_original),imageSY($src_img_original));

					@imagepng($dst_img, $caminhoDaSmall);
				}
			}

			copy($fotoOriginal, $caminhoDaSBig);

			if( ! ( file_exists( $fotoOriginal ) && file_exists( $caminhoDaSmall ) && file_exists( $caminhoDaBig ) ) )
			
			{
				die( "<center><br>Um erro ocorreu ao inserir a foto.<br>Por favor tente novamente.</center>" );
			}
			
		}
		else
		{
		
			return false;
		}
	
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$this->data_foto = str_replace( "%2F", "/", $this->data_foto );

		$db = new clsBanco();
		$db->Consulta( "INSERT INTO foto_portal ( nm_credito, ref_cod_foto_secao, ref_ref_cod_pessoa_fj, data_foto, titulo, descricao, caminho, altura, largura ) VALUES ( '{$this->nm_credito}', '1',  {$this->id_pessoa}, now(), '{$this->titulo}', '{$this->descricao}', '{$nome_do_arquivo}', $fim_largura, $fim_altura )" );

		echo "<script>document.location='fotos_lst.php';</script>";

		return true;
	}

	function Editar() 
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE foto_portal SET titulo='{$this->titulo}', descricao='{$this->descricao}', ref_cod_foto_secao = '{$this->secao}', nm_credito = '{$this->nm_credito}'  WHERE cod_foto_portal='{$this->id_foto}'" );

		echo "<script>document.location='fotos_lst.php';</script>";

		return true;
	}

	function Excluir()
	{
		{
			$db = new clsBanco();
			$db->Consulta("SELECT caminho FROM foto_portal WHERE cod_foto_portal = {$this->id_foto}");
			$db->ProximoRegistro();
			list ($caminho) = $db->Tupla();
			$db->Consulta( "DELETE FROM foto_portal WHERE cod_foto_portal = {$this->id_foto}" );
			@unlink("fotos/big/{$caminho}");
			@unlink("fotos/small/{$caminho}");
			
			echo "<script>document.location='fotos_lst.php';</script>";			
		}
	}

}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
