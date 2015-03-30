<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
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
require_once "lib/Portabilis/String/Utils.php";
require_once "lib/Portabilis/Date/Utils.php";

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Hist&oacute;rico Escolar" );
		$this->processoAp = "578";
		$this->addEstilo('localizacaoSistema');
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

  var $cod_distribuicao_uniforme;
  var $ref_cod_aluno;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Hist&oacute;rico Escolar - Detalhe";
		

		$this->cod_distribuicao_uniforme=$_GET["cod_distribuicao_uniforme"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];

		$tmp_obj = new clsPmieducarDistribuicaoUniforme( $this->cod_distribuicao_uniforme);
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
			die();
		}

		$obj_aluno = new clsPmieducarAluno();
		$lst_aluno = $obj_aluno->lista( $registro["ref_cod_aluno"],null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_aluno) )

		{
			$det_aluno = array_shift($lst_aluno);
			$nm_aluno = $det_aluno["nome_aluno"];
		}

		if( $nm_aluno )
		{
			$this->addDetalhe( array( "Aluno", "{$nm_aluno}") );
		}

		if( $registro["ano"] )
		{
			$this->addDetalhe( array( "Ano", "{$registro["ano"]}") );
		}

		if( $registro["data"] )
		{
			$this->addDetalhe( array( Portabilis_String_Utils::toLatin1("Data da distribuição"), Portabilis_Date_Utils::pgSQLToBr($registro["data"]) ) );
		}

		if ( $registro["ref_cod_escola"]){
			$obj_escola = new clsPmieducarEscola();
			$lst_escola = $obj_escola->lista($registro["ref_cod_escola"]);
			if ( is_array($lst_escola) ){
				$det_escola = array_shift($lst_escola);
				$nm_escola = $det_escola["nome"];
				$this->addDetalhe(array("Escola fornecedora: ", Portabilis_String_Utils::toLatin1($nm_escola)));
			}

		}

		if( dbBool($registro["kit_completo"]) )
			$this->addDetalhe( array( "Recebeu kit completo", "Sim") );
		else{
			
			$this->addDetalhe( array( "Recebeu kit completo", Portabilis_String_Utils::toLatin1("Não")) );

			$this->addDetalhe( array( Portabilis_String_Utils::toLatin1("Quantidade de agasalhos (jaqueta e calça)"), $registro['agasalho_qtd'] ?: '0' ));
			$this->addDetalhe( array( "Quantidade de camisetas (manga curta)", $registro['camiseta_curta_qtd'] ?: '0' ));
			$this->addDetalhe( array( "Quantidade de camisetas (manga longa)", $registro['camiseta_longa_qtd'] ?: '0' ));
			$this->addDetalhe( array( "Quantidade de meias", $registro['meias_qtd'] ?: '0' ));
			$this->addDetalhe( array( "Bermudas tectels (masculino)", $registro['bermudas_tectels_qtd'] ?: '0' ));
			$this->addDetalhe( array( "Bermudas coton (feminino)", $registro['bermudas_coton_qtd'] ?: '0' ));
			$this->addDetalhe( array( Portabilis_String_Utils::toLatin1("Quantidade de tênis"), $registro['tenis_qtd'] ?: '0' ));
		}
		
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->url_novo = "educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}";
			$this->url_editar = "educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&cod_distribuicao_uniforme={$registro["cod_distribuicao_uniforme"]}";
		}

		$this->url_cancelar = "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$registro["ref_cod_aluno"]}";
		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Detalhe da distribui&ccedil;&atilde;o de uniforme"
    ));
    $this->enviaLocalizacao($localizacao->montar());				
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