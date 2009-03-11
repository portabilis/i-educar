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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Usuario" );
		$this->processoAp = "554";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_tipo_usuario;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nm_tipo;
	var $descricao;
	var $nivel;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{

		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->titulo = "Tipo Usuario - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_tipo_usuario=$_GET["cod_tipo_usuario"];

		$tmp_obj = new clsPmieducarTipoUsuario( $this->cod_tipo_usuario,null,null,null,null,null,null,null,1);
		if(!$registro = $tmp_obj->detalhe()){
			header("location: educar_tipo_usuario_lst.php");
		}

		if($registro["cod_tipo_usuario"])
			$this->addDetalhe( array( "C&oacute;digo Tipo Usu&aacute;rio", "{$registro["cod_tipo_usuario"]}") );

		if($registro["nm_tipo"])
			$this->addDetalhe( array( "Tipo de Usu&aacute;rio", "{$registro["nm_tipo"]}") );

		$array_nivel = array( "8" => "Biblioteca", '4' => "Escola", '2' => "Institucional", "1" => "Poli-institucional");

		if($array_nivel[$registro["nivel"]])
			$this->addDetalhe( array( "N&iacute;vel", "{$array_nivel[$registro["nivel"]]}") );

		if($registro["descricao"])
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );

		//** listagem de permissoes aos menus
		if( class_exists( "clsBanco" ) )
		{
			$objTemp = new clsBanco();
			$objTemp->Consulta("SELECT m.cod_menu_menu
								       ,m.nm_menu
								       ,sub.cod_menu_submenu
								       ,sub.nm_submenu
								       ,u.visualiza
								       ,u.cadastra
								       ,u.exclui
								  FROM menu_submenu sub
								       ,menu_menu   m
								       ,pmieducar.menu_tipo_usuario u
								 WHERE sub.cod_menu_submenu   = u.ref_cod_menu_submenu
								   AND sub.ref_cod_menu_menu  = m.cod_menu_menu
								   AND (m.cod_menu_menu       = 55 OR m.ref_cod_menu_pai = 55)
								   AND u.ref_cod_tipo_usuario = {$this->cod_tipo_usuario}
								 ORDER BY cod_menu_menu
								 		  ,upper(sub.nm_submenu)
							");
			while($objTemp->ProximoRegistro())
			{
				list ($menu_pai,$nm_menu_pai,$codigo, $nome,$visualiza,$cadastra,$exclui) = $objTemp->Tupla();
				$opcoes[$menu_pai]["nome_menu_pai"] = $nm_menu_pai;
				$opcoes[$menu_pai][$codigo]["nm_submenu"] = $nome;
				$opcoes[$menu_pai][$codigo]["cadastra"] = $cadastra;
				$opcoes[$menu_pai][$codigo]["visualiza"] = $visualiza;
				$opcoes[$menu_pai][$codigo]["exclui"] = $exclui;
			}

		}
		else
		{
			echo "<!--\nErro\nClasse clsMenuSubmenu nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		if($opcoes)
		{

			$det_menus = $this->lista_menus($opcoes);
			if($det_menus){

				$this->addDetalhe(array("Permiss&otilde;es de acesso aos menus</b>", "<a href='javascript:void(0);' onclick='trocaDisplay(\"det_pree\")'>Mostrar detalhe</a><div id='det_pree' name='det_pree' style='display:inline;'>".$det_menus."</div>"));
			}
		}


		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		if($obj_permissao->permissao_cadastra(554, $this->pessoa_logada,1,null,true))
		{
			$this->url_novo = "educar_tipo_usuario_cad.php";
			$this->url_editar = "educar_tipo_usuario_cad.php?cod_tipo_usuario={$registro["cod_tipo_usuario"]}";
		}
		//**


		$this->url_cancelar = "educar_tipo_usuario_lst.php";
		$this->largura = "100%";

	}

	//***
	// Inicio detalhe do preenchimento da CP
	//***
	function lista_menus($opcoes)
	{
		//echo "<pre>";
		//print_r($opcoes);die;

		$existe  = true;
		$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='80%'>";
//		$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='80%'>";
		$tabela .= "<tr bgcolor='#A1B3BD'><th width='400'>Menu - submenus</th><th>Visualizar</th><th>Cadastrar</th><th width='70'>Excluir</th></tr>";

		foreach ($opcoes as $key => $menu){

			$menu_pai = array_shift($menu);
			$cor =  "#a1b3bd";
			$tabela .= "<tr bgcolor='$cor' align='center'><td colspan='4' align='left' width='400'><b>{$menu_pai}</b></td></tr>";
			$cor =  "#E4E9ED";
			foreach ($menu as $cod_sub =>$sub_menu){
				$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";
				$sub_menu['visualiza'] = $sub_menu['visualiza'] == 0 ? "N&atilde;o" : "Sim";
				$sub_menu['cadastra']  = $sub_menu['cadastra'] == 0 ? "N&atilde;o" : "Sim";
				$sub_menu['exclui']    = $sub_menu['exclui'] == 0 ? "N&atilde;o" : "Sim";


				$tabela .= "<tr bgcolor='$cor' align='center'><td style='padding-left:20px' align='left' width='400'><img src=\"imagens/noticia.jpg\" border='0'>{$sub_menu['nm_submenu']}</td><td>{$sub_menu['visualiza']}</td><td>{$sub_menu['cadastra']}</td><td>{$sub_menu['exclui']}</td></tr>";
			}
		}
		$tabela .= "</tr>";

		$tabela .="</table>";
		return $existe == true ?  $tabela :  false;
	}
	//***
	// Fim detalhe
	//***
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>

<script>
function trocaDisplay(id)
{
	var element = document.getElementById(id);
  	element.style.display = (element.style.display == "none") ? "inline" : "none";
}
</script>