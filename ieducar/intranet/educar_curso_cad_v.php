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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Curso" );
		$this->processoAp = "0";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

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

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_curso=$_GET["cod_curso"];

		/*$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
	    */
		if( is_numeric( $this->cod_curso ) )
		{

			$obj = new clsPmieducarCurso( $this->cod_curso );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

			//$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 0, $this->pessoa_logada, 0 ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_curso_det.php?cod_curso={$registro["cod_curso"]}" : "educar_curso_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_curso", $this->cod_curso );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$objTemp = new clsPmieducarInstituicao();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarInstituicao nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_instituicao", "Instituic&atilde;o", $opcoes, $this->ref_cod_instituicao );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoEnsino" ) )
		{
			$objTemp = new clsPmieducarTipoEnsino();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoEnsino nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_ensino", "Tipo Ensino", $opcoes, $this->ref_cod_tipo_ensino );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoAvaliacao" ) )
		{
			$objTemp = new clsPmieducarTipoAvaliacao();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['cod_tipo_avaliacao']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoAvaliacao nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_avaliacao", "Tipo Avaliac&atilde;o", $opcoes, $this->ref_cod_tipo_avaliacao );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarNivelEnsino" ) )
		{
			$objTemp = new clsPmieducarNivelEnsino();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarNivelEnsino nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_nivel_ensino", "Nivel Ensino", $opcoes, $this->ref_cod_nivel_ensino );


		// text
		$this->campoTexto( "nm_curso", "Nome Curso", $this->nm_curso, 30, 255, true );
		$this->campoTexto( "sgl_curso", "Sgl Curso", $this->sgl_curso, 30, 255, true );
		$this->campoNumero( "qtd_etapas", "Qtd Etapas", $this->qtd_etapas, 15, 255, true );
		$this->campoMonetario( "frequencia_minima", "Frequencia Minima", $this->frequencia_minima, 15, 255, true );
		$this->campoMonetario( "media", "Media", $this->media, 15, 255, true );
		$this->campoMonetario( "media_exame", "Media Exame", $this->media_exame, 15, 255, false );
		$this->campoNumero( "falta_ch_globalizada", "Falta Ch Globalizada", $this->falta_ch_globalizada, 15, 255, true );
		$this->campoMonetario( "carga_horaria", "Carga Horaria", $this->carga_horaria, 15, 255, true );
		$this->campoTexto( "ato_poder_publico", "Ato Poder Publico", $this->ato_poder_publico, 30, 255, false );
		$this->campoNumero( "edicao_final", "Edic&atilde;o Final", $this->edicao_final, 15, 255, true );
		$this->campoMemo( "objetivo_curso", "Objetivo Curso", $this->objetivo_curso, 60, 10, false );
		$this->campoMemo( "publico_alvo", "Publico Alvo", $this->publico_alvo, 60, 10, false );
		$this->campoNumero( "padrao_ano_escolar", "Padr&atilde;o Ano Escolar", $this->padrao_ano_escolar, 15, 255, true );
		$this->campoMonetario( "hora_falta", "Hora Falta", $this->hora_falta, 15, 255, true );

		// data

		// time

		// bool

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/*$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
*/

		$obj = new clsPmieducarCurso( $this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_curso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCurso\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_nivel_ensino ) && is_numeric( $this->ref_cod_tipo_ensino ) && is_string( $this->nm_curso ) && is_string( $this->sgl_curso ) && is_numeric( $this->qtd_etapas ) && is_numeric( $this->frequencia_minima ) && is_numeric( $this->media ) && is_numeric( $this->falta_ch_globalizada ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->edicao_final ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->padrao_ano_escolar ) && is_numeric( $this->hora_falta )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/*$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
*/

		$obj = new clsPmieducarCurso($this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_curso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCurso\nvalores obrigatorios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/*$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
*/

		$obj = new clsPmieducarCurso($this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, 0, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_curso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCurso\nvalores obrigatorios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
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