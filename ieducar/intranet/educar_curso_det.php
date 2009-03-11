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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Curso" );
		$this->processoAp = "566";
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

	var $cod_curso;
	var $ref_usuario_cad;
	var $ref_cod_tipo_regime;
	var $ref_cod_nivel_ensino;
	var $ref_cod_tipo_ensino;
	var $ref_cod_tipo_avaliacao;
	var $nm_curso;
	var $sgl_curso;
	var $qtd_etapas;
	var $frequencia_minima;
	var $media;
	var $media_exame;
	var $falta_ch_globalizada;
	var $carga_horaria;
	var $ato_poder_publico;
	var $habilitacao;
	var $edicao_final;
	var $objetivo_curso;
	var $publico_alvo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_usuario_exc;
	var $ref_cod_instituicao;
	var $padrao_ano_escolar;
	var $hora_falta;
	var $avaliacao_globalizada;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Curso - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_curso=$_GET["cod_curso"];

		$tmp_obj = new clsPmieducarCurso( $this->cod_curso );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_curso_lst.php" );
			die();
		}
		if (class_exists("clsPmieducarInstituicao"))
		{
			$obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
			$obj_instituicao_det = $obj_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];
		}
		else
		{
			$cod_instituicao = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		if( class_exists( "clsPmieducarTipoRegime" ) )
		{
			$obj_ref_cod_tipo_regime = new clsPmieducarTipoRegime( $registro["ref_cod_tipo_regime"] );
			$det_ref_cod_tipo_regime = $obj_ref_cod_tipo_regime->detalhe();
			$registro["ref_cod_tipo_regime"] = $det_ref_cod_tipo_regime["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_tipo_regime"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTipoRegime\n-->";
		}

		if( class_exists( "clsPmieducarNivelEnsino" ) )
		{
			$obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino( $registro["ref_cod_nivel_ensino"] );
			$det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
			$registro["ref_cod_nivel_ensino"] = $det_ref_cod_nivel_ensino["nm_nivel"];
		}
		else
		{
			$registro["ref_cod_nivel_ensino"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarNivelEnsino\n-->";
		}

		if( class_exists( "clsPmieducarTipoEnsino" ) )
		{
			$obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino( $registro["ref_cod_tipo_ensino"] );
			$det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
			$registro["ref_cod_tipo_ensino"] = $det_ref_cod_tipo_ensino["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_tipo_ensino"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTipoEnsino\n-->";
		}

		if( class_exists( "clsPmieducarTipoAvaliacao" ) )
		{
			$obj_ref_cod_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $registro["ref_cod_tipo_avaliacao"] );
			$det_ref_cod_tipo_avaliacao = $obj_ref_cod_tipo_avaliacao->detalhe();
			$registro["ref_cod_tipo_avaliacao"] = $det_ref_cod_tipo_avaliacao["nm_tipo"];
			if(!$registro["ref_cod_tipo_avaliacao"])
				$registro["ref_cod_tipo_avaliacao"] = "Sem Avaliação";
		}
		else
		{
			$registro["ref_cod_tipo_avaliacao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTipoAvaliacao\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if ($registro["ref_cod_instituicao"])
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}" ) );
			}
		}
		if( $registro["ref_cod_tipo_regime"] )
		{
			$this->addDetalhe( array( "Tipo Regime", "{$registro["ref_cod_tipo_regime"]}") );
		}
		if( $registro["ref_cod_nivel_ensino"] )
		{
			$this->addDetalhe( array( "N&iacute;vel Ensino", "{$registro["ref_cod_nivel_ensino"]}") );
		}
		if( $registro["ref_cod_tipo_ensino"] )
		{
			$this->addDetalhe( array( "Tipo Ensino", "{$registro["ref_cod_tipo_ensino"]}") );
		}
		if( $registro["ref_cod_tipo_avaliacao"] )
		{
			$this->addDetalhe( array( "Tipo Avalia&ccedil;&atilde;o", "{$registro["ref_cod_tipo_avaliacao"]}") );
		}
		if( $registro["nm_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["nm_curso"]}") );
		}
		if( $registro["sgl_curso"] )
		{
			$this->addDetalhe( array( "Sigla Curso", "{$registro["sgl_curso"]}") );
		}
		if( $registro["qtd_etapas"] )
		{
			$this->addDetalhe( array( "Quantidade Etapas", "{$registro["qtd_etapas"]}") );
		}
		if( $registro["frequencia_minima"] )
		{
			$registro["frequencia_minima"] = number_format($registro["frequencia_minima"],2,",",".");
			$this->addDetalhe( array( "Frequ&ecirc;ncia M&iacute;nima", "{$registro["frequencia_minima"]}") );
		}
		if( $registro["media"] )
		{
			$registro["media"] = number_format($registro["media"],2,",",".");
			$this->addDetalhe( array( "M&eacute;dia", "{$registro["media"]}") );
		}
		if( $registro["media_exame"] )
		{
			$registro["media_exame"] = number_format($registro["media_exame"],2,",",".");
			$this->addDetalhe( array( "M&eacute;dia Exame", "{$registro["media_exame"]}") );
		}
		if( $registro["hora_falta"] )
		{
			$registro["hora_falta"] = number_format($registro["hora_falta"],2,",",".");
			$this->addDetalhe( array( "Hora/Falta", "{$registro["hora_falta"]}") );
		}
		if( $registro["falta_ch_globalizada"] )
		{
			if ($registro["falta_ch_globalizada"] == 0)
			{
				$registro["falta_ch_globalizada"] = 'n&atilde;o';
			}
			else if ($registro["falta_ch_globalizada"] == 1)
			{
				$registro["falta_ch_globalizada"] = 'sim';
			}
			$this->addDetalhe( array( "Falta/CH Globalizada", "{$registro["falta_ch_globalizada"]}") );
		}
		if( $registro["avaliacao_globalizada"] == 't' )
		{
			$this->addDetalhe( array( "Avalia&ccedil;&atilde;o Globalizada", "Sim") );
		}
		else if( $registro["avaliacao_globalizada"] == 'f' )
		{
			$this->addDetalhe( array( "Avalia&ccedil;&atilde;o Globalizada", "N&atilde;o") );
		}
		if( $registro["carga_horaria"] )
		{
			$registro["carga_horaria"] = number_format($registro["carga_horaria"],2,",",".");
			$this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}") );
		}
		if( $registro["ato_poder_publico"] )
		{
			$this->addDetalhe( array( "Ato Poder P&uacute;blico", "{$registro["ato_poder_publico"]}") );
		}

		$obj = new clsPmieducarHabilitacaoCurso( null, $this->cod_curso );
		$lst = $obj->lista( null, $this->cod_curso );
		if ($lst) {
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Nome</B></TD>
					       </TR>";
			$cont = 0;
			foreach ( $lst AS $valor ) {
				if ( ($cont % 2) == 0 ) {
					$color = " bgcolor=#E4E9ED ";
				}
				else {
					$color = " bgcolor=#FFFFFF ";
				}
				$obj = new clsPmieducarHabilitacao($valor["ref_cod_habilitacao"]);
				$obj_habilitacao = $obj->detalhe();
				$habilitacao = $obj_habilitacao["nm_tipo"];
				$tabela .= "<TR>
							    <TD {$color} align=left>{$habilitacao}</TD>
							</TR>";
				$cont++;
			}
			$tabela .= "</TABLE>";
		}
		if( $habilitacao )
		{
			$this->addDetalhe( array( "Habilita&ccedil;&atilde;o", "{$tabela}") );
		}
		if( $registro["edicao_final"] )
		{
			if ($registro["edicao_final"] == 0)
			{
				$registro["edicao_final"] = 'n&atilde;o';
			}
			else if ($registro["edicao_final"] == 1)
			{
				$registro["edicao_final"] = 'sim';
			}
			$this->addDetalhe( array( "Edi&ccedil;&atilde;o Resultado Final", "{$registro["edicao_final"]}") );
		}
		if( $registro["padrao_ano_escolar"] )
		{
			if ($registro["padrao_ano_escolar"] == 0)
			{
				$registro["padrao_ano_escolar"] = 'n&atilde;o';
			}
			else if ($registro["padrao_ano_escolar"] == 1)
			{
				$registro["padrao_ano_escolar"] = 'sim';
			}
			$this->addDetalhe( array( "Padr&atilde;o Ano Escolar", "{$registro["padrao_ano_escolar"]}" ) );
		}
		if( $registro["objetivo_curso"] )
		{
			$this->addDetalhe( array( "Objetivo Curso", "{$registro["objetivo_curso"]}") );
		}
		if( $registro["publico_alvo"] )
		{
			$this->addDetalhe( array( "P&uacute;blico Alvo", "{$registro["publico_alvo"]}") );
		}

		if( $obj_permissoes->permissao_cadastra( 566, $this->pessoa_logada,3 ) )
		{
			$this->url_novo = "educar_curso_cad.php";
			$this->url_editar = "educar_curso_cad.php?cod_curso={$registro["cod_curso"]}";
		}
		$this->url_cancelar = "educar_curso_lst.php";
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