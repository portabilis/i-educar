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
		$this->SetTitulo( "{$this->_instituicao} Jornal!" );
		$this->processoAp = "34";
	}
}

class indice extends clsCadastro
{
	var $cod_jornal,
		$ano,
		$edicao,
		$data_inicial,
		$data_final,
		$caminho,
		$tamanho,
		$extra,
		$cod_cad;
	var $arquivo_deletar;
	var $caminho_arquivo;
	var $qtd_arquivos;
	var $todos_arquivos;

	function Inicializar()
	{
		@session_start();
		$this->cod_cad = $_SESSION['id_pessoa'];
		session_write_close();
		$this->ano = $_POST["ano"];
		$this->edicao = $_POST["edicao"];
		$this->data_final = $_POST["data_final"];
		$this->data_inicial = $_POST["data_inicial"];
		//ARQUIVOS
		if(!empty($_POST["todos_arquivos"]))
			$this->todos_arquivos = unserialize(urldecode($_POST["todos_arquivos"]));
		if(!empty($_POST["qtd_arquivos"]))
			$this->qtd_arquivos = $_POST["qtd_arquivos"];
		else
			$this->qtd_arquivos = 0;
		if(empty($_POST["arquivo_deletar"]))
		{
				if (!empty($_FILES['caminho_arquivo']['name']) )
				{

					$continua = "true";
					$fotoOriginal = explode(".",$_FILES['caminho_arquivo']['name']);
					$novocaminho = date('Y-m-d')."-".substr(md5($fotoOriginal[0]), 0, 10).".pdf";
					$caminho = "tmp/".date('Y-m-d')."-".substr(md5($fotoOriginal[0]), 0, 10).".pdf";
					$tmp = 0;
					while(file_exists($caminho))
					{
							$mud .= "u";
							$caminho = "tmp/".date('Y-m-d')."-".substr(md5("{$fotoOriginal[0]}{$mud}"), 0, 10).".pdf";
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
							$this->todos_arquivos[] = array($caminho,$novocaminho);
						else
							$this->todos_arquivos[1] = array($caminho,$novocaminho);
					}
				}
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


		if(@$_GET['cod_jornal'] =="")
		{
			$retorno = "Novo";
		}
		else
		{
			$retorno = "Editar";
			if($this->edicao == "")
			{
				$this->cod_jornal = @$_GET['cod_jornal'];
				$db = new clsBanco();
				$db->Consulta( "SELECT jor_ano_edicao, jor_edicao, j.jor_dt_inicial, j.jor_dt_final, jor_extra FROM jor_edicao j WHERE  j.cod_jor_edicao = {$this->cod_jornal}" );
				if ($db->ProximoRegistro())
				{
					list($this->ano, $edicao, $this->data_inicial, $this->data_final, $extra) = $db->Tupla();
					$this->data_final= date('d/m/Y', strtotime(substr($this->data_final,0,19) ));
					$this->data_inicial= date('d/m/Y', strtotime(substr($this->data_inicial,0,19) ));

					$this->fexcluir = true;

					$teste = explode ("/", $this->data_inicial);
					if($teste[2] < 10) $this->data_inicial = "0".$this->data_inicial;

					$teste = explode ("/", $this->data_final);
					if($teste[2] < 10) $this->data_final = "0".$this->data_final;
					$db_tmp = new clsBanco();
					$db_tmp->Consulta( "SELECT jor_caminho FROM jor_arquivo j WHERE j.ref_cod_jor_edicao = {$this->cod_jornal}" );
						While($db_tmp->ProximoRegistro())
						{
							list($caminho) = $db_tmp->Tupla();
							$this->qtd_arquivos+=1;
							copy($caminho,"tmp/".date('Y-m-d')."-{$this->qtd_arquivos}.tmp");
							if(!empty($this->todos_arquivos))
								$this->todos_arquivos[] = array("tmp/".date('Y-m-d')."-{$this->qtd_arquivos}.tmp",$caminho,"s");
							else
								$this->todos_arquivos[1] = array("tmp/".date('Y-m-d')."-{$this->qtd_arquivos}.tmp",$caminho,"s");
						}
					$this->edicao =$edicao;
				}
				else
				{
					$retorno = "Novo";
				}

			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "jornal_det.php?cod_jornal=$this->cod_jornal" : "jornal_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_cad", $this->cod_cad);
		$this->campoOculto( "cod_jornal", $this->cod_jornal);

		$db = new clsBanco();
		//$db->Consulta( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = {$this->cod_cad}" );
		$objPessoa = new clsPessoaFj();
		list($nome_) = $objPessoa->queryRapida($this->cod_cad,"nome");
		$this->campoRotulo( "pessoa", "Respons&aacute;vel", $nome_);

		$this->campoTexto( "ano", "Ano",  $this->ano, "8", "4", true );
		$this->campoTexto( "edicao", "Edi&ccedil;&atilde;o",  $this->edicao, "8", "4", false, false, false, "", "deixar em branco caso seja edi&ccedil;&atilde;o extra" );

		$this->campoData( "data_inicial", "Data Inicial", $this->data_inicial, true );
		$this->campoData( "data_final", "Data Final", $this->data_final, false, "deixar em branco caso seja de apenas um dia." );
		// Jornais
		$this->campoOculto( "arquivo_deletar", $this->arquivo_deletar);
		if(is_array($this->todos_arquivos))
		foreach($this->todos_arquivos as $id=>$arquivo)
		{
			$this->campoTextoInv( "nome_arquivo_$id", "Nome Arquivo", $arquivo[1],  "30", "30", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit($id,'arquivo_deletar') \">Clique aqui para Excluir</a>");
		}
		$this->campoOculto( "todos_arquivos", serialize($this->todos_arquivos));
		$this->campoArquivo("caminho_arquivo","Vincular Arquivo",$this->caminho_arquivo,"22","<a  href='#' onclick=\"javascript:insereSubmit()\">Salvar Arquivo</a>");

	}
	function Novo()
	{

		global $HTTP_POST_FILES;
		if(!empty($this->todos_arquivos))
		{
			if (empty($this->data_final))
			{
				$this->data_final = $this->data_inicial;
			}
			$data_final = explode("/", $this->data_final);
			$this->data_final = "{$data_final[2]}-{$data_final[1]}-{$data_final[0]}";
			$data_inicial = explode("/", $this->data_inicial);
			$this->data_inicial = "{$data_inicial[2]}-{$data_inicial[1]}-{$data_inicial[0]}";
			if(empty($this->edicao))
			{
				$this->extra = 1;
				$this->edicao= 0;
			}
			else
			{
				$this->extra = 0;
				$this->edicao = $this->edicao;
			}
			$db = new clsBanco();
			$sql = "INSERT INTO jor_edicao (jor_ano_edicao, jor_edicao, jor_dt_inicial, jor_dt_final,ref_ref_cod_pessoa_fj, jor_extra) VALUES ('{$this->ano}', {$this->edicao}, '{$this->data_inicial}', '{$this->data_final}', {$this->cod_cad}, {$this->extra})";
			$db->Consulta( $sql );
			$last_id = 	$db->insertId('portal.jor_edicao_cod_jor_edicao_seq');
			$temp = 1;
			$this->todos_arquivos =  unserialize(urldecode($this->todos_arquivos));

			foreach ($this->todos_arquivos as $id=>$caminho)
			{
				$continua = "true";
				$novo_caminho = "pdf/".$caminho[1];
				copy($caminho[0],$novo_caminho);
				unlink($caminho[0]);
				$sql = "INSERT INTO jor_arquivo  (jor_arquivo,ref_cod_jor_edicao, jor_caminho) VALUES ({$temp}, {$last_id} , '{$novo_caminho}')";
				$db->Consulta( $sql );
				$temp ++;
			}
			echo "<script>document.location = 'jornal_lst.php';</script>";
		}
		else
		{
			return false;
		}

	}

	function Editar()
	{
		@session_start();
		$id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		global $HTTP_POST_FILES;
		$this->cod_jornal = $_GET["cod_jornal"];
		if($id_pessoa)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT jor_caminho FROM jor_arquivo WHERE ref_cod_jor_edicao={$this->cod_jornal}" );
			while($db->ProximoRegistro())
			{
				list($caminho) = $db->Tupla();
				unlink($caminho);
			}
			$db->Consulta( "DELETE FROM jor_arquivo WHERE ref_cod_jor_edicao={$this->cod_jornal}");
			$this->todos_arquivos =  unserialize(urldecode($this->todos_arquivos));
			if(!empty($this->todos_arquivos))
			{
				if (empty($this->data_final))
				{
					$this->data_final = $this->data_inicial;
				}
				$data_final = explode("/", $this->data_final);
				$this->data_final = "{$data_final[2]}-{$data_final[1]}-{$data_final[0]}";
				$data_inicial = explode("/", $this->data_inicial);
				$this->data_inicial = "{$data_inicial[2]}-{$data_inicial[1]}-{$data_inicial[0]}";
				if(empty($this->edicao))
				{
					$this->extra = 1;
					$this->edicao= 0;
				}
				else
				{
					$this->extra = 0;
					$this->edicao = $this->edicao;
				}
				$db = new clsBanco();
				$db->Consulta("UPDATE jor_edicao SET jor_ano_edicao='$this->ano', jor_edicao=$this->edicao, jor_dt_inicial='$this->data_inicial', jor_dt_final='$this->data_final', jor_extra='$this->extra' WHERE cod_jor_edicao={$this->cod_jornal}");
				$temp = 1;
				foreach ($this->todos_arquivos as $id=>$caminho)
				{
					$novo_caminho = "pdf/".$caminho[1];
					if($caminho[2] == "s")
					{
						$novo_caminho = $caminho[1];
					}
					copy($caminho[0],$novo_caminho);
					unlink($caminho[0]);
					$sql = "INSERT INTO jor_arquivo  (jor_arquivo,ref_cod_jor_edicao, jor_caminho) VALUES ({$temp}, {$this->cod_jornal} , '{$novo_caminho}')";
					$db->Consulta( $sql );
					$temp ++;
				}
			echo "<script>document.location = 'jornal_lst.php';</script>";
			}
			else
			{
				return false;
			}
		}

	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta("Select jor_caminho from jor_arquivo WHERE ref_cod_jor_edicao = $this->cod_jornal");
		$this->todos_arquivos =  unserialize(urldecode($this->todos_arquivos));
		while($db->ProximoRegistro())
		{
			list($caminho) = $db->Tupla();
			unlink($caminho);
		}
		$sql = "DELETE FROM  jor_arquivo WHERE ref_cod_jor_edicao = {$this->cod_jornal};";
		$db->Consulta( $sql );
		$sql = "DELETE FROM  jor_edicao WHERE cod_jor_edicao = {$this->cod_jornal};";
		$db->Consulta( $sql );
		foreach ($this->todos_arquivos as $id=>$caminho)
		{
			unlink($caminho[0]);
		}
		echo "<script>document.location = 'jornal_lst.php';</script>";

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
