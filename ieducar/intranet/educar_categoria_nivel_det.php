<?php
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Detalhe Categoria N&iacute;vel" );
		$this->processoAp = "829";
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

	var $cod_categoria_nivel;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_categoria_nivel;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Categoria Nivel - Detalhe";
		$this->addBanner( "http://ieducar.dccobra.com.br/intranet/imagens/nvp_top_intranet.jpg", "http://ieducar.dccobra.com.br/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_categoria_nivel=$_GET["cod_categoria_nivel"];

		$tmp_obj = new clsPmieducarCategoriaNivel( $this->cod_categoria_nivel );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_categoria_nivel_lst.php" );
			die();
		}


		if( $registro["cod_categoria_nivel"] )
		{
			$this->addDetalhe( array( "Categoria", "{$registro["cod_categoria_nivel"]}") );
		}
		if( $registro["nm_categoria_nivel"] )
		{
			$this->addDetalhe( array( "Nome Categoria", "{$registro["nm_categoria_nivel"]}") );
		}

		$tab_niveis = null;

		$obj_nivel = new clsPmieducarNivel();
		$lst_nivel = $obj_nivel->buscaSequenciaNivel($this->cod_categoria_nivel);

		if($lst_nivel)
		{
			$tab_niveis .= "<table cellspacing='0' cellpadding='0' width='200' border='0' style='border:1px dotted #000000'>";

			$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;
			$tab_niveis .= " <tr>
								<td bgcolor='#A1B3BD' align='center' colspan='2'>N&iacute;veis</td>
							</tr>";
			foreach ($lst_nivel as $nivel)
			{

				$tab_niveis .= " <tr class='$class2' align='center'>
									<td align='left'>{$nivel['nm_nivel']}</td>
									<td align='left' width='30'><a href='javascript:popless(\"{$nivel['cod_nivel']}\")'><img src='imagens/nvp_bot_ad_sub.gif' border='0'></a></td>
								</tr>";

				$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;

			}
			$tab_niveis .=	"</table>";

			$this->addDetalhe(array("N&iacute;veis", "$tab_niveis"));
		}


		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3, null, true ) )
		{
			$this->url_novo = "educar_categoria_nivel_cad.php";
			$this->url_editar = "educar_categoria_nivel_cad.php?cod_categoria_nivel={$registro["cod_categoria_nivel"]}";
			$this->array_botao[] = 'Adicionar Níveis';
			$this->array_botao_url[] = "educar_nivel_cad.php?cod_categoria={$registro["cod_categoria_nivel"]}";
		}

		$this->url_cancelar = "educar_categoria_nivel_lst.php";
		$this->largura = "100%";
	}
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

<script type="text/javascript">
	function popless(nivel)
	{
		var campoCategoria = <?=$_GET["cod_categoria_nivel"];?>;
		pesquisa_valores_popless('educar_subniveis_cad.php?ref_cod_categoria='+campoCategoria+'&ref_cod_nivel='+nivel, '');
	}

</script>
