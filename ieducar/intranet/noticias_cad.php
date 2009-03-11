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
		$this->SetTitulo( "{$this->_instituicao} Not&iacute;cias!" );
		$this->processoAp = "26";
	}
}

class indice extends clsCadastro
{
	var $id_noticia_altera;
	var $id_pessoa;
	var $titulo;
	var $data_noticia;
	var $data_noticia2;
	var $descricao;
	//TIPOS
	var $id_tipo_deletar;
	var $id_tipo;
	var $qtd_tipos;
	var $todos_tipos;
	//FOTOS
	var $id_foto_deletar;
	var $id_foto;
	var $qtd_fotos;
	var $todas_fotos;
	//NOTICIAS
	var $id_noticia_deletar;
	var $id_noticia;
	var $qtd_noticias;
	var $todas_noticias;
	//ARQUIVOS
	var $arquivo_deletar;
	var $caminho_arquivo;
	var $qtd_arquivos;
	var $todos_arquivos;
	var $nome_arquivo;
	var $nome_;
	var $sobrenome;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		unset($_SESSION['campo3']);
		session_write_close();
		$this->id_noticia_deletar = @$_POST["id_noticia_deletar"];
		$this->titulo = @$_POST["titulo"];
		$this->descricao = str_replace("\\", "" ,@$_POST["descricao"]) ;
		$this->data_noticia = urldecode(@$_POST["data_noticia"]);
		$this->id_noticia_altera = @$_POST["id_noticia_altera"];

		// TIPOS
		if(!empty($_POST["todos_tipos"]))
			$this->todos_tipos = unserialize(urldecode($_POST["todos_tipos"]));
		if(!empty($_POST["qtd_tipos"]))
			$this->qtd_tipos = $_POST["qtd_tipos"];
		else
			$this->qtd_tipos = 0;
		if( $_POST["id_tipo"])
		{
			$conitnua = "true";
			if(is_array($this->todos_tipos))
				foreach($this->todos_tipos as $tipo)
				{
					if($_POST["id_tipo"] == $tipo)
						$conitnua = "false";
				}
			if($conitnua =="true")
				{
					$this->qtd_tipos +=1;
					$this->todos_tipos[] =  $_POST["id_tipo"];
				}
		}
		if(!empty($_POST["id_tipo_deletar"]))
		{
			foreach($this->todos_tipos as $i=>$id_tipo)
			{
				if($id_tipo == $_POST["id_tipo_deletar"])
				{
					unset($this->todos_tipos[$i] );
					$this->qtd_tipos -= 1;
				}
			}
			$this->id_tipo_deletar="";
		}

		// FOTOS
		if(!empty($_POST["todas_fotos"]))
			$this->todas_fotos = unserialize(urldecode($_POST["todas_fotos"]));
		if(!empty($_POST["qtd_fotos"]))
			$this->qtd_fotos = $_POST["qtd_fotos"];
		else
			$this->qtd_fotos = 0;
		if( $_POST["id_foto"] != "")
		{
			$conitnua = "true";
			if(is_array($this->todas_fotos))
				foreach($this->todas_fotos as $foto)
				{
					if($_POST["id_foto"] == $foto)
						$conitnua = "false";
				}
			if($conitnua =="true")
				{
					$this->qtd_fotos +=1;
					$this->todas_fotos[] =  $_POST["id_foto"];
				}
		}
		if(!empty($_POST["id_foto_deletar"]))
		{
			foreach($this->todas_fotos as $i=>$id_foto)
			{
				if($id_foto == $_POST["id_foto_deletar"])
				{
					unset($this->todas_fotos[$i] );
					$this->qtd_fotos -= 1;
				}
			}
			$this->id_foto_deletar="";
		}
		//NOTICIAS
		if(!empty($_POST["todas_noticias"]))
			$this->todas_noticias = unserialize(urldecode($_POST["todas_noticias"]));
		if(!empty($_POST["qtd_noticias"]))
			$this->qtd_noticias = $_POST["qtd_noticias"];
		else
			$this->qtd_noticias = 0;
		if( $_POST["id_noticia"] != "")
		{
			$conitnua = "true";
			if(is_array($this->todas_noticias))
				foreach($this->todas_noticias as $noticia)
				{
					if($_POST["id_noticia"] == $noticia)
						$conitnua = "false";
				}
			if($conitnua =="true")
				{
					$this->qtd_noticias +=1;
					$this->todas_noticias[] =  $_POST["id_noticia"];
				}
		}
		if(!empty($_POST["id_noticia_deletar"]))
		{
			foreach($this->todas_noticias as $i=>$id_noticia)
			{
				if($id_noticia == $_POST["id_noticia_deletar"])
				{
					unset($this->todas_noticias[$i] );
					$this->qtd_noticias -= 1;
				}
			}
			$this->id_noticia_deletar="";
		}
		//ARQUIVOS
		if(!empty($_POST["todos_arquivos"]))
			$this->todos_arquivos = unserialize(urldecode($_POST["todos_arquivos"]));
		if(!empty($_POST["qtd_arquivos"]))
			$this->qtd_arquivos = $_POST["qtd_arquivos"];
		else
			$this->qtd_arquivos = 0;
		if(empty($_POST["arquivo_deletar"]))
		{
			if(!empty($_POST["nome_arquivo"]))
			{
				$nome_arquivo = $_POST["nome_arquivo"];
				if (!empty($_FILES['caminho_arquivo']['name']) )
				{
					$continua = "true";
					$fotoOriginal = $_FILES['caminho_arquivo']['name'];
					$parte = explode(".",$fotoOriginal);
					$novocaminho = date('Y-m-d')."-".substr(md5($parte[0]), 0, 10).".".$parte[1];
					$caminho = "tmp/".date('Y-m-d')."-".substr(md5($parte[0]), 0, 10).".".$parte[1];
					$tmp = 0;
					if(file_exists($caminho))
					{
						$caminho = "";
					}
					copy($_FILES['caminho_arquivo']['tmp_name'], $caminho);
				}
				if( $caminho != "")
				{
					$conitnua = "true";
					if(is_array($this->todos_arquivos))
						foreach($this->todos_arquivos as $arquivo)
						{
							if($caminho == $arquivo[0])
								$conitnua = "false";
						}
					if($continua =="true")
					{
						$this->qtd_arquivos +=1;
						if(!empty($this->todos_arquivos))
							$this->todos_arquivos[] = array($caminho,$nome_arquivo,$novocaminho);
						else
							$this->todos_arquivos[1] = array($caminho,$nome_arquivo,$novocaminho);
					}
				}
			}else
				if(!empty($_FILES['caminho_arquivo']['name']))
					echo"<script>alert('Falha ao salvar o arquivo, por favor prencha o nome do arquivo')</script>";
		}
		else
		{
			foreach($this->todos_arquivos as $i=>$nome_arquivo)
			{
				if($i == $_POST["arquivo_deletar"])
				{
					unset($this->todos_arquivos[$i] );
					$this->qtd_arquivos -= 1;
					unlink($nome_arquivo[0]);
				}
			}
			$this->arquivo_deletar="";
		}


		if(@$_GET['id_noticia'] =="")
			$retorno = "Novo";
		else
		{
			$this->id_noticia_altera = @$_GET['id_noticia'];
			$db = new clsBanco();
			$db->Consulta("SELECT ref_ref_cod_pessoa_fj FROM not_portal WHERE cod_not_portal =$this->id_noticia_altera ");
			$db->ProximoRegistro();
			list($cod_pessoa) =$db->Tupla();
			//if($this->id_pessoa == $cod_pessoa)
				$retorno = "Editar";
			//else
			//	$retorno = "Novo";
		}
		if (@$_GET['id_noticia'] && $this->titulo=="")
		{
			$this->id_noticia_altera = @$_GET['id_noticia'];
			$db =new clsBanco();
			$db->Consulta("SELECT titulo, descricao, data_noticia FROM not_portal WHERE cod_not_portal =$this->id_noticia_altera");
			if ($db->ProximoRegistro())
			{
				list($titulo, $descricao, $data_noticia) = $db->Tupla();
				$data_noticia = explode(".",$data_noticia);
				$data_noticia= date("d/m/Y", strtotime(substr($data_noticia[0],0,19)) );
			}
			$this->fexcluir = true;
			$this->titulo = $titulo;
			$this->data_noticia = $data_noticia;
			$this->descricao = $descricao;
			$retorno = "Editar";
			$db->Consulta("SELECT ref_cod_not_tipo FROM not_portal_tipo WHERE ref_cod_not_portal =$this->id_noticia_altera");
			while ($db->ProximoRegistro()) {
				list($tipo) = $db->Tupla();
				$this->qtd_tipos +=1;
				$this->todos_tipos[] = $tipo;
			}
			$db->Consulta("SELECT tipo, cod_vinc, caminho, nome_arquivo FROM not_vinc_portal WHERE ref_cod_not_portal =$this->id_noticia_altera");
			while ($db->ProximoRegistro())
			{
				list($tipo, $cod, $caminho, $nome_arquivo) = $db->Tupla();
				if($tipo == "F")
				{
					$this->qtd_fotos +=1;
					$this->todas_fotos[] = $cod;
				}
				if($tipo == "N")
				{
					$this->qtd_noticias+=1;
					$this->todas_noticias[] = $cod;
				}
				if($tipo == "A")
				{
					$this->qtd_arquivos+=1;
					copy($caminho,"tmp/{$this->qtd_arquivos}.tmp");

					if(!empty($this->todos_arquivos))
						$this->todos_arquivos[] = array("tmp/{$this->qtd_arquivos}.tmp",$nome_arquivo,$caminho);
					else
						$this->todos_arquivos[1] = array("tmp/{$this->qtd_arquivos}.tmp",$nome_arquivo,$caminho);
				}
			}
		}
		else
			$this->data_noticia = date('d/m/Y');
		$this->url_cancelar = ($retorno == "Editar") ? "noticias_det.php?id_noticia=$this->id_noticia_altera" : "noticias_lst.php";
		$this->nome_url_cancelar = "Cancelar";


		return $retorno;
	}

	function Gerar()
	{
		// CAMPOS DA NOTÍCIA A INSERIR
		$objPessoa = new clsPessoaFj();
		list($nome_) = $objPessoa->queryRapida($this->id_pessoa, "nome");
		$db = new clsBanco();
		/*
		$db->Consulta( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = {$this->id_pessoa}" );
		$db->ProximoRegistro();
		list($nome_) = $db->Tupla();
		*/
		$this->campoOculto( "id_noticia_altera", $this->id_noticia_altera );
		$this->campoRotulo( "pessoa", "Respons&aacute;vel", $nome_);
		$this->campoTexto( "titulo", "Titulo",  $this->titulo, "50", "100", true );
		$this->campoOculto( "data_noticia", $this->data_noticia);
		$this->campoTextoInv( "data_noticia_", "Data", $this->data_noticia,  "15", "15", true);
		$this->campoMemo("descricao","Descri&ccedil;&atilde;o",$this->descricao, "50", "8", true );

		//TIPO DA NOTÍCIA
		$this->campoOculto( "id_tipo_deletar", $this->id_tipo_deletar );
		$this->campoOculto( "qtd_tipos", $this->qtd_tipos);
		if(is_array($this->todos_tipos))
		foreach($this->todos_tipos as $id=>$tipo)
		{
			$db->Consulta( "SELECT nm_tipo FROM not_tipo WHERE cod_not_tipo = $tipo" );
			if($db->ProximoRegistro())
			{
				list($nm_tipo) = $db->Tupla();
			}
			$this->campoTextoInv( "id_tipo_$id", "Tipo", $nm_tipo,  "15", "15", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit({$tipo},'id_tipo_deletar') \">Clique aqui para Excluir</a>");

		}
		$this->campoOculto( "todos_tipos", serialize($this->todos_tipos));
		$db->Consulta("SELECT * FROM not_tipo ORDER BY nm_tipo ASC");
		$opcoes =array( "Selecione" );
		while ($db->ProximoRegistro()) {
			list($cod,$nome) = $db->Tupla();
			$opcoes[$cod] = $nome;
		}
		$this->campoLista("id_tipo", "Vincular com Tipo", $opcoes, 0, "insereSubmit()");

		//FOTOS VINCULADAS
		$this->campoOculto( "id_foto_deletar", $this->id_foto_deletar );
		$this->campoOculto( "qtd_fotos", $this->qtd_fotos);
		if(is_array($this->todas_fotos))
		foreach($this->todas_fotos as $id=>$foto)
		{
			$this->campoTextoInv( "id_foto_$id", "Fotos", $foto,  "15", "15", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit({$foto},'id_foto_deletar') \">Clique aqui para Excluir</a>");
		}
		$this->campoOculto( "todas_fotos", serialize($this->todas_fotos));
		$this->campoOculto( "id_foto", $this->id_foto);
		$this->campoProcurarAdicionar("id_foto_", "Vincular com foto", $this->id_foto, 10, 5, "showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'add_fotos.php?campo=id_foto\'></iframe>');", "Procurar","insereSubmit()","");//"openurl('add_fotos.php?campo=id_foto')"

		//NOTICIAS VINCULADAS
		$this->campoOculto( "id_noticia_deletar", $this->id_noticia_deletar );
		$this->campoOculto( "qtd_noticias", $this->qtd_noticias);
		if(is_array($this->todas_noticias))
		foreach($this->todas_noticias as $id=>$noticia)
		{
			$this->campoTextoInv( "id_noticias_$id", "Noticias", $noticia,  "15", "15", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit({$noticia},'id_noticia_deletar') \">Clique aqui para Excluir</a>");

		}
		$this->campoOculto( "todas_noticias", serialize($this->todas_noticias));
		$this->campoOculto( "id_noticia", $this->id_noticia);
		$this->campoProcurarAdicionar("id_noticia_", "Vincular com noticia", $this->id_noticia, 10, 5, "showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'add_noticias.php?campo=id_noticia\'></iframe>');", "Procurar","insereSubmit()","");//openurl('add_noticias.php?campo=id_noticia')

		//ARQUIVOS VINCULADOS
		$this->campoOculto( "arquivo_deletar", $this->arquivo_deletar);
		$this->campoOculto( "qtd_arquivos", $this->qtd_arquivos);
		if(is_array($this->todos_arquivos))
		foreach($this->todos_arquivos as $id=>$arquivo)
		{
			$this->campoTextoInv( "nome_arquivo_$id", "Nome Arquivo", $arquivo[1],  "15", "15", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit($id,'arquivo_deletar') \">Clique aqui para Excluir</a>");
		}

		$this->campoOculto( "todos_arquivos", serialize($this->todos_arquivos));
		$this->campoArquivo("caminho_arquivo","Vincular Arquivo",$this->caminho_arquivo,"22","<a  href='#' onclick=\"javascript:insereSubmit()\">Salvar Arquivo</a>");
		$this->campoTexto("nome_arquivo","Nome do Arquivo", $this->nome_arquivo,"22","250",false);
	}


	function Novo()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		$this->data_noticia = explode("%2F",$this->data_noticia);
		$this->data_noticia = "{$this->data_noticia[2]}/{$this->data_noticia[1]}/{$this->data_noticia[0]}";

		if (empty($this->id_pessoa) || empty($this->titulo) || empty($this->data_noticia) || empty($this->descricao))
		{
			return false;
		}
		else
		{
			$temp_num=  1;
			$db = new clsBanco();
			$this->descricao = str_replace( "?", "\?", $this->descricao );

			$db->Consulta( "INSERT INTO not_portal (ref_ref_cod_pessoa_fj, titulo, descricao , data_noticia) VALUES ({$this->id_pessoa}, '{$this->titulo}', '{$this->descricao}', NOW())" );
			//$db->Consulta( "SELECT LAST_INSERT_ID() FROM not_portal" );
			$last_id = $db->insertId('not_portal_cod_not_portal_seq');
			$this->todas_fotos =  unserialize(urldecode($this->todas_fotos));
			if(!empty($this->todas_fotos))
				foreach ($this->todas_fotos as $id=>$foto)
				{
					$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo, cod_vinc) VALUES ({$last_id},{$temp_num},'F', {$foto})" );
					$temp_num ++;
				}
			$this->todas_noticias =  unserialize(urldecode($this->todas_noticias));
			if(!empty($this->todas_noticias))
				foreach ($this->todas_noticias as $id=>$noticia)
				{
					$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo, cod_vinc) VALUES ({$last_id},{$temp_num},'N', {$noticia})" );
					$temp_num ++;
				}
			$this->todos_arquivos =  unserialize(urldecode($this->todos_arquivos));
			if(!empty($this->todos_arquivos))
				foreach ($this->todos_arquivos as $id=>$caminho)
				{
					$novo_caminho = "arquivos/".$caminho[2];
					copy($caminho[0],$novo_caminho);
					$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo,caminho,nome_arquivo) VALUES ({$last_id},{$temp_num},'A','$novo_caminho' , '$caminho[1]')" );
					$temp_num ++;
				}
			$this->todos_tipos =  unserialize(urldecode($this->todos_tipos));

			if(!empty($this->todos_tipos))
				foreach ($this->todos_tipos as $id=>$tipo)
				{
					$db->Consulta( "INSERT INTO not_portal_tipo (ref_cod_not_portal,ref_cod_not_tipo) VALUES ({$last_id},{$tipo})" );
				}
			echo "<script>document.location='noticias_lst.php';</script>";

			return true;
		}
		return true;
	}

	function Editar()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		$temp_num=  1;
		$db = new clsBanco();
		$db->Consulta( "SELECT caminho FROM not_vinc_portal WHERE ref_cod_not_portal={$this->id_noticia_altera} AND tipo='A'" );
		while($db->ProximoRegistro())
		{
			list($caminho) = $db->Tupla();
			unlink($caminho);
		}
		$db->Consulta( "UPDATE not_portal SET titulo='{$this->titulo}', descricao='{$this->descricao}', ref_ref_cod_pessoa_fj={$this->id_pessoa} WHERE cod_not_portal={$this->id_noticia_altera}");
		$db->Consulta( "DELETE FROM not_vinc_portal WHERE ref_cod_not_portal={$this->id_noticia_altera}");
		$db->Consulta( "DELETE FROM not_portal_tipo WHERE ref_cod_not_portal={$this->id_noticia_altera}");


		$this->todas_fotos =  unserialize(urldecode($this->todas_fotos));
		if(!empty($this->todas_fotos))
			foreach ($this->todas_fotos as $id=>$foto)
			{
				$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo, cod_vinc) VALUES ({$this->id_noticia_altera},{$temp_num},'F', {$foto})" );
				$temp_num ++;
			}
		$this->todas_noticias =  unserialize(urldecode($this->todas_noticias));
		if(!empty($this->todas_noticias))
			foreach ($this->todas_noticias as $id=>$noticia)
			{
				$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo, cod_vinc) VALUES ({$this->id_noticia_altera},{$temp_num},'N', {$noticia})" );
				$temp_num ++;
			}
		$this->todos_arquivos =  unserialize(urldecode($this->todos_arquivos));
		if(!empty($this->todos_arquivos))
			foreach ($this->todos_arquivos as $id=>$caminho)
			{
				if(substr_count($caminho[2],"arquivos/"))
				{
					$novo_caminho = $caminho[2];
				}else
				{
					$novo_caminho = "arquivos/".$caminho[2];
				}
				copy($caminho[0],$novo_caminho);
				$db->Consulta( "INSERT INTO not_vinc_portal (ref_cod_not_portal,vic_num,tipo,caminho,nome_arquivo) VALUES ({$this->id_noticia_altera},{$temp_num},'A','$novo_caminho' , '$caminho[1]')" );
				$temp_num ++;
			}
		$this->todos_tipos =  unserialize(urldecode($this->todos_tipos));
		if(!empty($this->todos_tipos))
			foreach ($this->todos_tipos as $id=>$tipo)
			{
				$db->Consulta( "INSERT INTO not_portal_tipo (ref_cod_not_portal,ref_cod_not_tipo) VALUES ({$this->id_noticia_altera},{$tipo})" );
			}
		echo "<script>document.location='noticias_lst.php';</script>";
		return true;

	}

	function Excluir()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();


		echo $this->id_noticia_altera;


		if (empty($this->id_pessoa) || empty($this->id_noticia_altera))
		{
			return false;
		}
		else
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT caminho FROM not_vinc_portal WHERE ref_cod_not_portal={$this->id_noticia_altera} AND tipo='A'" );
			while($db->ProximoRegistro())
			{
				list($caminho) = $db->Tupla();
				unlink($caminho);

			}
			$db->Consulta( "DELETE FROM mailling_historico WHERE ref_cod_not_portal={$this->id_noticia_altera}");
			$db->Consulta( "DELETE FROM not_portal_tipo WHERE ref_cod_not_portal={$this->id_noticia_altera}");
			$db->Consulta( "DELETE FROM not_vinc_portal WHERE ref_cod_not_portal={$this->id_noticia_altera}");
			$db->Consulta( "DELETE FROM not_portal WHERE cod_not_portal=$this->id_noticia_altera" );

			echo "<script>document.location='noticias_lst.php';</script>";

			return true;
		}
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
