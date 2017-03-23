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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Detalhe do Grupo" );
		$this->processoAp = "296";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do Grupo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet", false);

		$cod_grupo = $_GET['cod_grupos'];
		
		$obj = new clsGrupos($cod_grupo);
		$detalhe = $obj->detalhe();
		
		$this->addDetalhe(array("Nome", $detalhe['nm_grupo']));
		$this->addDetalhe(array("Data de Criação", date("d/m/Y H:i:s", strtotime(substr($detalhe['data_cadastro'],0,19)))  ));
		
		$obj = new clsGrupoModerador();
		$lista = $obj->lista(false,$cod_grupo);
		if($lista)
		{
			$this->addDetalhe(array("<b><i>Moderador(es)</i></b>", ""));
			$i = 1;
			foreach ($lista as $moderadores) {
				$obj = new clsPessoaFisica($moderadores['ref_ref_cod_pessoa_fj']);
				$detalhe = $obj->detalhe();
				$this->addDetalhe(array("Moderador $i", $detalhe['nome']));
				$i++;
			}
		}
		
		$obj = new clsGrupoPessoa();
		$lista = $obj->lista(false,$cod_grupo);
		if($lista)
		{
			$this->addDetalhe(array("<b><i>Membro(s)</i></b>", ""));
			$i = 1;
			foreach ($lista as $mebros) {
				$obj = new clsPessoaFisica($mebros['ref_idpes']);
				$detalhe = $obj->detalhe();
				$this->addDetalhe(array("Membro $i", $detalhe['nome']));
				$i++;
			}
		} 

		$this->url_novo = "otopic_grupos_cad.php";
		$this->url_editar = "otopic_grupos_cad.php?cod_grupos=$cod_grupo";
		$this->url_cancelar = "otopic_grupos_lst.php";
		$this->largura = "100%";
		
	}
}


/*class Listas extends clsListagem
{
	function Gerar()
	{
		@session_start();
		$id_visualiza = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->titulo = "Membros";
		$this->addBanner(  );
		
		$cod_membro = $_GET['cod_membro'];
		$cod_grupo = $_GET['cod_grupo'];
		
		$this->addCabecalhos( array( "Nome", "e-mail", "Ramal", "Função" ) );

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		$obj = new clsGrupoPessoa();
		$lista = $obj->pessoasGrupo($cod_grupo,"tipo ASC",1,$iniciolimit,$limite);
		foreach ($lista as $pessoas) 
		{
			$total = $pessoas['total'];
			$obj = new clsFuncionario($pessoas['id']);
			$detalhe = $obj->detalhe();
			$detalhe_pessoa = $detalhe['idpes']->detalhe();
			$funcao = $pessoas['tipo'] == 1 ? "Moderador" : "Membro";
			$this->addLinhas( array("<a href='otopic_membro_det.php?cod_membro={$pessoas['id']}&cod_grupo={$cod_grupo}'>{$detalhe_pessoa['nome']}</a>",$detalhe_pessoa['email'],$detalhe['ramal'], $funcao) );
					
		}
					

		
		
		$this->acao = "go(\"otopic_membros_cad.php?cod_grupo=$cod_grupo\")";
		$this->nome_acao = "Novo Membro";
	

		

		$this->largura = "100%";
		$this->addPaginador2( "otopic_membro_det.php?cod_membro=$cod_membro&cod_grupo=$cod_grupo", $total, $_GET, $this->nome, $limite );
	}
}*/

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );
//$miolo = new Listas();
//$pagina->addForm( $miolo );

$pagina->MakeAll();

?>