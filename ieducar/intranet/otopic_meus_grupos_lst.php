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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Grupos de Reunião!" );
		$this->processoAp = "294";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		@session_start();
		$id_pesssoa = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->titulo = "Meus Grupos";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		// Busca
		$this->campoTexto("grupo","Grupo","",50,255);
		$lista_grupos = true;
		if($_GET['grupo'])
		{
			$lista_grupos = "";
			$obj = new clsGrupos();
			$lista = $obj->lista($_GET['grupo']);
			if($lista)
			{
				foreach ($lista as $grupo) {
					$lista_grupos[] = $grupo['cod_grupos'];
				}
			}
		}

		$this->addCabecalhos( array( "Grupo", "Status" ) );


		if($lista_grupos)
		{
			$obj = new clsFuncionarioSu($id_pesssoa);
			if(!$obj->detalhe())
			{
				$obj = new clsGrupoPessoa();
				$lista = $obj->meusGrupos($id_pesssoa, "tipo ASC",1,false,false,$lista_grupos);
				if($lista)
				{
					$objGrupos = new clsGrupos();
					foreach ($lista as $pessoa_grupo) {
						if(!$objGrupos->lista(false, false, false, false, false, 1, false, false, false, false, 1, $pessoa_grupo['ref_cod_grupos']))
						{
							$obj = new clsGrupos($pessoa_grupo['ref_cod_grupos']);
							$total = $pessoa_grupo['total'];
							$detalhe = $obj->detalhe();
							$this->addLinhas(array("<a href='otopic_meus_grupos_det.php?cod_grupo={$pessoa_grupo['ref_cod_grupos']}'>{$detalhe['nm_grupo']}</a>", ($pessoa_grupo['tipo'] == 1) ? "Moderador" : "Membro"));
						}
					}
				}
			}else
			{
				$obj = new clsGrupos();
				$lista = $obj->lista(false,false,false,false,false,1,false,false,$iniciolimit,$limite);
				if($lista)
				{
					$grupos = "";
					foreach ($lista as $grupo) {
						$total = $grupo['total'];
						$obj = new clsGrupoModerador($id_pesssoa,$grupo['cod_grupos']);
						$detalhe = $obj->detalhe();
						$status = "Super Usuário";
						if($detalhe['ativo'] == 1)
						{
							$status = "Moderador";
						}else {
							$obj = new clsGrupoPessoa($id_pesssoa,$grupo['cod_grupos']);
							$detalhe = $obj->detalhe();
							if($detalhe['ativo'] == 1)
							{
								$status = "Membro";
							}
						}
						$grupos[] = array($status,$grupo['nm_grupo'],$grupo['cod_grupos']);

					}
					rsort($grupos);
					reset($grupos);
					foreach ($grupos as $grupo) {
						$this->addLinhas(array("<a href='otopic_meus_grupos_det.php?cod_grupo={$grupo['2']}'>{$grupo['1']}</a>", $grupo['0']));
					}
				}
			}
		}
		$this->largura = "100%";
		$this->addPaginador2( "otopic_meus_grupos_lst.php", $total, $_GET, $this->nome, $limite );

	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>