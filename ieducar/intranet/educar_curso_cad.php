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
		$this->processoAp = "566";
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

	var $incluir;
	var $excluir_;
	var $habilitacao_curso;
	var $curso_sem_avaliacao  = true;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_curso=$_GET["cod_curso"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 566, $this->pessoa_logada, 3, "educar_curso_lst.php" );

		if( is_numeric( $this->cod_curso ) )
		{

			$obj = new clsPmieducarCurso( $this->cod_curso );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = $obj_permissoes->permissao_excluir( 566, $this->pessoa_logada,3 );
				$this->curso_sem_avaliacao = is_null($registro['ref_cod_tipo_avaliacao']);
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_curso_det.php?cod_curso={$registro["cod_curso"]}" : "educar_curso_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
//-----------------------------
		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;


		if ( $_POST["habilitacao_curso"] )
			$this->habilitacao_curso = unserialize( urldecode( $_POST["habilitacao_curso"] ) );
		$qtd_habilitacao = ( count( $this->habilitacao_curso ) == 0 ) ? 1 : ( count( $this->habilitacao_curso ) + 1);
		if( is_numeric( $this->cod_curso ) && $_POST["incluir"] != 'S' && empty( $_POST["excluir_"] ) ) {
			$obj = new clsPmieducarHabilitacaoCurso( null, $this->cod_curso );
			$registros = $obj->lista( null, $this->cod_curso );
			if( $registros ) {
				foreach ( $registros AS $campo ) {
					$this->habilitacao_curso[$campo[$qtd_habilitacao]]["ref_cod_habilitacao_"] = $campo["ref_cod_habilitacao"];
					$qtd_habilitacao++;
				}
			}
		}
		if ( $_POST["habilitacao"] ) {
				$this->habilitacao_curso[$qtd_habilitacao]["ref_cod_habilitacao_"] 	  = $_POST["habilitacao"];
				$qtd_habilitacao++;
				unset( $this->habilitacao );
		}
//-------------------------------

		// primary keys
		$this->campoOculto( "cod_curso", $this->cod_curso );

		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarNivelEnsino" ) )
		{
			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarNivelEnsino();
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarNivelEnsino n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
	
		/*************************COLOCADO*********************************/
		$script = "javascript:showExpansivelIframe(520, 230, 'educar_nivel_ensino_cad_pop.php');";
		if ($this->ref_cod_instituicao)// && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_nivel_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_nivel_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		/*************************COLOCADO*********************************/		
		$this->campoLista( "ref_cod_nivel_ensino", "N&iacute;vel Ensino", $opcoes, $this->ref_cod_nivel_ensino, "", false, "", $script );


		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoEnsino" ) )
		{
			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarTipoEnsino();
				$objTemp->setOrderby("nm_tipo");
				$lista = $objTemp->lista( null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoEnsino n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		
		/*************************COLOCADO*********************************/
		$script = "javascript:showExpansivelIframe(520, 150, 'educar_tipo_ensino_cad_pop.php');";
		if ($this->ref_cod_instituicao)// && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_tipo_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_tipo_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		/*************************COLOCADO*********************************/	
		$this->campoLista( "ref_cod_tipo_ensino", "Tipo Ensino", $opcoes, $this->ref_cod_tipo_ensino, "", false, "", $script );

		$opcoes = array( "" => "Sem Avaliação" );
		if( class_exists( "clsPmieducarTipoAvaliacao" ) )
		{
			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarTipoAvaliacao();
				$objTemp->setOrderby("nm_tipo");
				$lista = $objTemp->lista( null,null,null,null,null,null,1,null,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_tipo_avaliacao']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoAvaliacao n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$script = "javascript:showExpansivelIframe(520, 300, 'educar_tipo_avaliacao_cad_pop.php');";
		if ($this->ref_cod_instituicao)// && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_tipo_avaliacao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_tipo_avaliacao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		/*************************COLOCADO*********************************/
		$this->campoLista( "ref_cod_tipo_avaliacao", "Tipo Avalia&ccedil;&atilde;o", $opcoes, $this->ref_cod_tipo_avaliacao, "tipo_avaliacao_onchange()",false,"", $script,false,false );


		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoRegime" ) )
		{
			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarTipoRegime();
				$objTemp->setOrderby("nm_tipo");
				$lista = $objTemp->lista( null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_tipo_regime']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarNivelEnsino n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		/*************************COLOCADO*********************************/
		$script = "javascript:showExpansivelIframe(520, 120, 'educar_tipo_regime_cad_pop.php');";
		if ($this->ref_cod_instituicao)// && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_tipo_regime' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_tipo_regime' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		/*************************COLOCADO*********************************/
		$this->campoLista( "ref_cod_tipo_regime", "Tipo Regime", $opcoes, $this->ref_cod_tipo_regime, "", false, "", $script, false, false );


		// text
		$this->campoTexto( "nm_curso", "Curso", $this->nm_curso, 30, 255, true );
		$this->campoTexto( "sgl_curso", "Sigla Curso", $this->sgl_curso, 15, 15, true );
		$this->campoNumero( "qtd_etapas", "Quantidade Etapas", $this->qtd_etapas, 2, 2, true );
		if (is_numeric($this->frequencia_minima))
			$this->campoMonetario( "frequencia_minima", "Frequ&ecirc;ncia M&iacute;nima", number_format($this->frequencia_minima,2,",",""), 6, 6, false,"","","",$this->curso_sem_avaliacao );
		else
			$this->campoMonetario( "frequencia_minima", "Frequ&ecirc;ncia M&iacute;nima", $this->frequencia_minima, 6, 6, false,"","","",true );

		if (is_numeric($this->media))
			$this->campoMonetario( "media", "M&eacute;dia", number_format($this->media,2,",",""), 6, 6, false,"","","",$this->curso_sem_avaliacao );
		else
			$this->campoMonetario( "media", "M&eacute;dia", $this->media, 6, 6, false,"","","",$this->curso_sem_avaliacao );

		if ($this->media_exame)
			$this->campoMonetario( "media_exame", "Media Exame", number_format($this->media_exame,2,",",""), 6, 6, false,"","","",$this->curso_sem_avaliacao  );
		else
			$this->campoMonetario( "media_exame", "Media Exame", $this->media_exame, 6, 6, false,"","","",$this->curso_sem_avaliacao  );

		if (is_numeric($this->hora_falta))
			$this->campoMonetario( "hora_falta", "Hora Falta", number_format($this->hora_falta,2,",",""), 5, 5, false,"","","",$this->curso_sem_avaliacao  );
		else
			$this->campoMonetario( "hora_falta", "Hora Falta", $this->hora_falta, 5, 5, false,"","","",$this->curso_sem_avaliacao  );

		$this->campoCheck( "falta_ch_globalizada", "Falta/CH Globalizada", $this->falta_ch_globalizada,"",false,false);

		$opcoes = array( 'f' => "N&atilde;o", 't' => "Sim" );
		$this->campoLista( "avaliacao_globalizada", "Avalia&ccedil;&atilde;o Globalizada", $opcoes, $this->avaliacao_globalizada );


		$this->campoMonetario( "carga_horaria", "Carga Hor&aacute;ria", $this->carga_horaria, 7, 7, true );

		$this->campoTexto( "ato_poder_publico", "Ato Poder P&uacute;blico", $this->ato_poder_publico, 30, 255, false );

//--------------------------
		$this->campoOculto( "excluir_", "" );
		$qtd_habilitacao = 1;
		$aux;

		$this->campoQuebra();
		if ( $this->habilitacao_curso )
		{
			foreach ( $this->habilitacao_curso AS $campo )
			{
				if ( $this->excluir_ == $campo["ref_cod_habilitacao_"] )
				{
					$this->habilitacao_curso[$campo["ref_cod_habilitacao"]] = null;
					$this->excluir_								   = null;
				}
				else
				{
					$obj_habilitacao = new clsPmieducarHabilitacao($campo["ref_cod_habilitacao_"]);
					$obj_habilitacao_det = $obj_habilitacao->detalhe();
					$nm_habilitacao = $obj_habilitacao_det["nm_tipo"];
					$this->campoTextoInv( "ref_cod_habilitacao_{$campo["ref_cod_habilitacao_"]}", "", $nm_habilitacao, 30, 255, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_').value = '{$campo["ref_cod_habilitacao_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
					//$aux[$qtd_habilitacao]["ref_cod_habilitacao_"] = $qtd_habilitacao;
					$aux[$qtd_habilitacao]["ref_cod_habilitacao_"] = $campo["ref_cod_habilitacao_"];

					$qtd_habilitacao++;
				}

			}
			unset($this->habilitacao_curso);
			$this->habilitacao_curso = $aux;
		}
		$this->campoOculto( "habilitacao_curso", serialize( $this->habilitacao_curso ) );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarHabilitacao" ) )
		{
			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarHabilitacao();
				$objTemp->setOrderby("nm_tipo");
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_habilitacao']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarHabilitacao n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		
		$script = "javascript:showExpansivelIframe(520, 225, 'educar_habilitacao_cad_pop.php');";
		$script = "<img id='img_habilitacao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		
		
		$this->campoLista( "habilitacao", "Habilita&ccedil;&atilde;o", $opcoes, $this->habilitacao,"",false,"","<a href='#' onclick=\"getElementById('incluir').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>{$script}",false,false );
		$this->campoOculto( "incluir", "" );
		$this->campoQuebra();
//--------------------------------------

		$this->campoCheck( "edicao_final", "Edi&ccedil;&atilde;o Resultado Final", $this->edicao_final );
		$this->campoCheck( "padrao_ano_escolar", "Padr&atilde;o Ano Escolar", $this->padrao_ano_escolar );
		$this->campoMemo( "objetivo_curso", "Objetivo Curso", $this->objetivo_curso, 60, 5, false );
		$this->campoMemo( "publico_alvo", "P&uacute;blico Alvo", $this->publico_alvo, 60, 5, false );


	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if ( $this->habilitacao_curso && $this->incluir != 'S' && empty( $this->excluir_ ) )
		{
			$this->frequencia_minima = str_replace(".","",$this->frequencia_minima);
			$this->frequencia_minima = str_replace(",",".",$this->frequencia_minima);
			$this->media = str_replace(".","",$this->media);
			$this->media = str_replace(",",".",$this->media);
			$this->media_exame = str_replace(".","",$this->media_exame);
			$this->media_exame = str_replace(",",".",$this->media_exame);
			$this->carga_horaria = str_replace(".","",$this->carga_horaria);
			$this->carga_horaria = str_replace(",",".",$this->carga_horaria);
			$this->hora_falta = str_replace(".","",$this->hora_falta);
			$this->hora_falta = str_replace(",",".",$this->hora_falta);

			if ($this->falta_ch_globalizada == 'on')
				$this->falta_ch_globalizada = 1;
			else
				$this->falta_ch_globalizada = 0;

			if ($this->edicao_final == 'on')
				$this->edicao_final = 1;
			else
				$this->edicao_final = 0;

			if ($this->padrao_ano_escolar == 'on')
				$this->padrao_ano_escolar = 1;
			else
				$this->padrao_ano_escolar = 0;

			$obj = new clsPmieducarCurso( null,$this->pessoa_logada,$this->ref_cod_tipo_regime,$this->ref_cod_nivel_ensino,$this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao,$this->nm_curso,$this->sgl_curso,$this->qtd_etapas,$this->frequencia_minima,$this->media,$this->media_exame,$this->falta_ch_globalizada,$this->carga_horaria,$this->ato_poder_publico,$this->edicao_final,$this->objetivo_curso,$this->publico_alvo,null,null,1,null,$this->ref_cod_instituicao,$this->padrao_ano_escolar,$this->hora_falta, $this->avaliacao_globalizada );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
//------------------------------
				$this->habilitacao_curso = unserialize( urldecode( $this->habilitacao_curso ) );
				if ($this->habilitacao_curso)
				{
					foreach ( $this->habilitacao_curso AS $campo )
					{
						$obj = new clsPmieducarHabilitacaoCurso( $campo["ref_cod_habilitacao_"], $cadastrou );
						$cadastrou2  = $obj->cadastra();
						if ( !$cadastrou2 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarHabilitacaoCurso\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["ref_cod_habilitacao_"]} ) )\n-->";
							return false;
						}
					}
				}
//------------------------------
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_curso_lst.php" );
				die();
				return true;
			}

			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarCurso\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_tipo_regime ) && is_numeric( $this->ref_cod_nivel_ensino ) && is_numeric( $this->ref_cod_tipo_ensino ) && is_numeric( $this->ref_cod_tipo_avaliacao ) && is_string( $this->nm_curso ) && is_string( $this->sgl_curso ) && is_numeric( $this->qtd_etapas ) && is_numeric( $this->frequencia_minima ) && is_numeric( $this->media ) && is_numeric( $this->falta_ch_globalizada ) && is_numeric( $this->edicao_final ) && is_string( $this->data_inicio ) && is_string( $this->data_fim )\n-->";
			return false;
		}
		return true;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if ( $this->habilitacao_curso && $this->incluir != 'S' && empty( $this->excluir_ ) )
		{
			$this->frequencia_minima = str_replace(".","",$this->frequencia_minima);
			$this->frequencia_minima = str_replace(",",".",$this->frequencia_minima);
			$this->media = str_replace(".","",$this->media);
			$this->media = str_replace(",",".",$this->media);
			$this->media_exame = str_replace(".","",$this->media_exame);
			$this->media_exame = str_replace(",",".",$this->media_exame);
			$this->carga_horaria = str_replace(".","",$this->carga_horaria);
			$this->carga_horaria = str_replace(",",".",$this->carga_horaria);
			$this->hora_falta = str_replace(".","",$this->hora_falta);
			$this->hora_falta = str_replace(",",".",$this->hora_falta);

			if ($this->falta_ch_globalizada == 'on')
				$this->falta_ch_globalizada = 1;
			else
				$this->falta_ch_globalizada = 0;

			if ($this->edicao_final == 'on')
				$this->edicao_final = 1;
			else
				$this->edicao_final = 0;

			if ($this->padrao_ano_escolar == 'on')
				$this->padrao_ano_escolar = 1;
			else
				$this->padrao_ano_escolar = 0;

			$obj = new clsPmieducarCurso( $this->cod_curso,null,$this->ref_cod_tipo_regime,$this->ref_cod_nivel_ensino,$this->ref_cod_tipo_ensino,$this->ref_cod_tipo_avaliacao,$this->nm_curso,$this->sgl_curso,$this->qtd_etapas,$this->frequencia_minima,$this->media,$this->media_exame,$this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso,$this->publico_alvo,null,null,1,$this->pessoa_logada,$this->ref_cod_instituicao,$this->padrao_ano_escolar,$this->hora_falta, $this->avaliacao_globalizada );
			$editou = $obj->edita();
			if( $editou )
			{
//------------------------------
				$this->habilitacao_curso = unserialize( urldecode( $this->habilitacao_curso ) );
				$obj  = new clsPmieducarHabilitacaoCurso( null, $this->cod_curso );
				$excluiu = $obj->excluirTodos();
				if ( $excluiu ) {
						if ($this->habilitacao_curso)
						{
							foreach ( $this->habilitacao_curso AS $campo )
							{
								$obj = new clsPmieducarHabilitacaoCurso( $campo["ref_cod_habilitacao_"], $this->cod_curso );
								$cadastrou2  = $obj->cadastra();
								if ( !$cadastrou2 )
								{
									$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
									echo "<!--\nErro ao editar clsPmieducarHabilitacaoCurso\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_curso ) && is_numeric( {$campo["ref_cod_habilitacao_"]} ) )\n-->";
									return false;
								}
							}
						}
				}
//------------------------------
				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_curso_lst.php" );
				die();
				return true;
			}
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarCurso\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->pessoa_logada ) )\n-->";
			return false;
		}
		return true;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarCurso( $this->cod_curso,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,0,$this->pessoa_logada );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_curso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCurso\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_curso ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
<script>

function getNivelEnsino(xml_nivel_ensino)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');

	campoNivelEnsino.length = 1;
	for (var j = 0; j < nivel_ensino.length; j++)
	{
		if (nivel_ensino[j][2] == campoInstituicao)
		{
			campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option( nivel_ensino[j][1], nivel_ensino[j][0],false,false);
		}
	}
	*/
	var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
	var DOM_array = xml_nivel_ensino.getElementsByTagName( "nivel_ensino" );

	if(DOM_array.length)
	{
		campoNivelEnsino.length = 1;
		campoNivelEnsino.options[0].text = 'Selecione um nível de ensino';
		campoNivelEnsino.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_nivel_ensino"),false,false);
		}
	}
	else
		campoNivelEnsino.options[0].text = 'A instituição não possui nenhum nível de ensino';
}

function getTipoEnsino(xml_tipo_ensino)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');

	campoTipoEnsino.length = 1;
	for (var j = 0; j < tipo_ensino.length; j++)
	{
		if (tipo_ensino[j][2] == campoInstituicao)
		{
			campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option( tipo_ensino[j][1], tipo_ensino[j][0],false,false);
		}
	}
	*/
	var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
	var DOM_array = xml_tipo_ensino.getElementsByTagName( "tipo_ensino" );

	if(DOM_array.length)
	{
		campoTipoEnsino.length = 1;
		campoTipoEnsino.options[0].text = 'Selecione um tipo de ensino';
		campoTipoEnsino.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_ensino"),false,false);
		}
	}
	else
		campoTipoEnsino.options[0].text = 'A instituição não possui nenhum tipo de ensino';
}

function getTipoAvaliacao(xml_tipo_avaliacao)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoTipoAvaliacao = document.getElementById('ref_cod_tipo_avaliacao');

	campoTipoAvaliacao.length = 1;
	for (var j = 0; j < tipo_avaliacao.length; j++)
	{
		if (tipo_avaliacao[j][2] == campoInstituicao)
		{
			campoTipoAvaliacao.options[campoTipoAvaliacao.options.length] = new Option( tipo_avaliacao[j][1], tipo_avaliacao[j][0],false,false);
		}
	}
	*/
	var campoTipoAvaliacao = document.getElementById('ref_cod_tipo_avaliacao');
	var DOM_array = xml_tipo_avaliacao.getElementsByTagName( "tipo_avaliacao" );

	if(DOM_array.length)
	{
		campoTipoAvaliacao.length = 1;
		campoTipoAvaliacao.options[0].text = 'Selecione um tipo de avaliação';
		campoTipoAvaliacao.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoTipoAvaliacao.options[campoTipoAvaliacao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_avaliacao"),false,false);
		}
	}
	else
		campoTipoAvaliacao.options[0].text = 'A instituição não possui nenhum tipo de avaliação';
}

function getTipoRegime(xml_tipo_regime)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');

	campoTipoRegime.length = 1;
	for (var j = 0; j < tipo_regime.length; j++)
	{
		if (tipo_regime[j][2] == campoInstituicao)
		{
			campoTipoRegime.options[campoTipoRegime.options.length] = new Option( tipo_regime[j][1], tipo_regime[j][0],false,false);
		}
	}
	*/
	var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
	var DOM_array = xml_tipo_regime.getElementsByTagName( "tipo_regime" );

	if(DOM_array.length)
	{
		campoTipoRegime.length = 1;
		campoTipoRegime.options[0].text = 'Selecione um tipo de regime';
		campoTipoRegime.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoTipoRegime.options[campoTipoRegime.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_regime"),false,false);
		}
	}
	else
		campoTipoRegime.options[0].text = 'A instituição não possui nenhum tipo de regime';
}

function getHabilitacao(xml_habilitacao)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoHabilitacao = document.getElementById('habilitacao');

	campoHabilitacao.length = 1;
	for (var j = 0; j < habilitacao.length; j++)
	{
		if (habilitacao[j][2] == campoInstituicao)
		{
			campoHabilitacao.options[campoHabilitacao.options.length] = new Option( habilitacao[j][1], habilitacao[j][0],false,false);
		}
	}
	*/
	var campoHabilitacao = document.getElementById('habilitacao');
	var DOM_array = xml_habilitacao.getElementsByTagName( "habilitacao" );

	if(DOM_array.length)
	{
		campoHabilitacao.length = 1;
		campoHabilitacao.options[0].text = 'Selecione uma habilitação';
		campoHabilitacao.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoHabilitacao.options[campoHabilitacao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_habilitacao"),false,false);
		}
	}
	else
		campoHabilitacao.options[0].text = 'A instituição não possui nenhuma habilitação';
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

	var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
	campoNivelEnsino.length = 1;
	campoNivelEnsino.disabled = true;
	campoNivelEnsino.options[0].text = 'Carregando nível de ensino';

	var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
	campoTipoEnsino.length = 1;
	campoTipoEnsino.disabled = true;
	campoTipoEnsino.options[0].text = 'Carregando tipo de ensino';

	var campoTipoAvaliacao = document.getElementById('ref_cod_tipo_avaliacao');
	campoTipoAvaliacao.length = 1;
	campoTipoAvaliacao.disabled = true;
	campoTipoAvaliacao.options[0].text = 'Carregando tipo de avaliacão';

	var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
	campoTipoRegime.length = 1;
	campoTipoRegime.disabled = true;
	campoTipoRegime.options[0].text = 'Carregando tipo de regime';

	var campoHabilitacao = document.getElementById('habilitacao');
	campoHabilitacao.length = 1;
	campoHabilitacao.disabled = true;
	campoHabilitacao.options[0].text = 'Carregando habilitação';

	var xml_nivel_ensino = new ajax( getNivelEnsino );
	xml_nivel_ensino.envia( "educar_nivel_ensino_xml.php?ins="+campoInstituicao );

	var xml_tipo_ensino = new ajax( getTipoEnsino );
	xml_tipo_ensino.envia( "educar_tipo_ensino_xml.php?ins="+campoInstituicao );

	var xml_tipo_avaliacao = new ajax( getTipoAvaliacao );
	xml_tipo_avaliacao.envia( "educar_tipo_avaliacao_xml.php?ins="+campoInstituicao );

	var xml_tipo_regime = new ajax( getTipoRegime );
	xml_tipo_regime.envia( "educar_tipo_regime_xml.php?ins="+campoInstituicao );

	var xml_habilitacao = new ajax( getHabilitacao );
	xml_habilitacao.envia( "educar_habilitacao_xml.php?ins="+campoInstituicao );

	/*getNivelEnsino();
	getTipoEnsino();
	getTipoRegime();
	getTipoAvaliacao();
	getHabilitacao();*/
	
	if (this.value == '')
	{
		$('img_nivel_ensino').style.display = 'none;';
		$('img_tipo_regime').style.display = 'none;';
		$('img_tipo_ensino').style.display = 'none;';
		$('img_tipo_avaliacao').style.display = 'none;';
	}
	else
	{
		$('img_nivel_ensino').style.display = '';
		$('img_tipo_regime').style.display = '';
		$('img_tipo_ensino').style.display = '';
		$('img_tipo_avaliacao').style.display = '';
	}
	
}

function tipo_avaliacao_onchange()
{
	var campoTipoAvaliacao = document.getElementById('ref_cod_tipo_avaliacao').value;
	var disabled = false;
	if(campoTipoAvaliacao == '')
		disabled = true;

	document.getElementById('frequencia_minima').disabled = disabled;
	document.getElementById('media').disabled = disabled;
	document.getElementById('media_exame').disabled = disabled;
	document.getElementById('hora_falta').disabled = disabled;
	document.getElementById('falta_ch_globalizada').disabled = disabled;
}

</script>
