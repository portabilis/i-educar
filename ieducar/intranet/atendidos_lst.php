<?php
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "Pessoas Físicas" );
		$this->processoAp = "43";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Pessoas Físicas";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array( "Nome", "CPF") );
		$this->campoTexto( "nm_pessoa", "Nome",  $_GET['nm_pessoa'], "50", "255", true );
		$this->campoCpf( "id_federal", "CPF",  $_GET['id_federal'], "50", "", true );

		$where="";
		$par_nome = false;
		if ($_GET['nm_pessoa'])
		{
			$par_nome = $_GET['nm_pessoa'];
		}
		$par_id_federal = false;
		if ($_GET['id_federal'])
		{
			$par_id_federal = idFederal2Int($_GET['id_federal']);
		}
		$dba = $db = new clsBanco();

		$objPessoa = new clsPessoaFisica();

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;


		$pessoas = $objPessoa->lista($par_nome, $par_id_federal,$iniciolimit,$limite);
		if($pessoas)
		{
			foreach ($pessoas as $pessoa)
			{
				$cod = $pessoa['idpes'];
				$nome = $pessoa['nome'];
				$total = $pessoa['total'];
				$cpf = $pessoa['cpf'] ? int2CPF($pessoa['cpf']) : "";
				$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0><a href='atendidos_det.php?cod_pessoa={$cod}'>$nome</a>", $cpf ) );
			}
		}

		$this->acao = "go(\"atendidos_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
		$this->addPaginador2( "atendidos_lst.php", $total, $_GET, $this->nome, $limite );
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>