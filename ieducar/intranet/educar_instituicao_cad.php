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
require_once ("include/Geral.inc.php");
require_once 'includes/bootstrap.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'Portabilis/Currency/Utils.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Institui&ccedil;&atilde;o" );
		$this->processoAp = "559";
		$this->addEstilo("localizacaoSistema");
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

	var $cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $complemento;
	var $nm_responsavel;
	var $ddd_telefone;
	var $telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_instituicao;
	var $data_base_transferencia;
	var $data_base_remanejamento;
	var $exigir_vinculo_turma_professor;
	var $controlar_espaco_utilizacao_aluno;
	var $percentagem_maxima_ocupacao_salas;
	var $quantidade_alunos_metro_quadrado;
	var $gerar_historico_transferencia;
	var $matricula_apenas_bairro_escola;
	var $restringir_historico_escolar;
	var $restringir_multiplas_enturmacoes;
  	var $permissao_filtro_abandono_transferencia;
	var $multiplas_reserva_vaga;
  	var $reserva_integral_somente_com_renda;
  	var $data_base_matricula;
	var $data_expiracao_reserva_vaga;
	var $data_fechamento;
	var $componente_curricular_turma;
	var $reprova_dependencia_ano_concluinte;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 559, $this->pessoa_logada, 3, "educar_instituicao_lst.php" );

		$this->cod_instituicao=$_GET["cod_instituicao"];

		if( is_numeric( $this->cod_instituicao ) )
		{

			$obj = new clsPmieducarInstituicao( $this->cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

				$this->fexcluir = $obj_permissoes->permissao_excluir( 559, $this->pessoa_logada, 3 );
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_instituicao_det.php?cod_instituicao={$registro["cod_instituicao"]}" : "educar_instituicao_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "i-Educar - Escola",
             ""        => "{$nomeMenu} institui&ccedil;&atilde;o"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->gerar_historico_transferencia 	= dbBool($this->gerar_historico_transferencia);
        $this->matricula_apenas_bairro_escola 	= dbBool($this->matricula_apenas_bairro_escola);
        $this->restringir_historico_escolar   	= dbBool($this->restringir_historico_escolar);
        $this->restringir_multiplas_enturmacoes	= dbBool($this->restringir_multiplas_enturmacoes);
        $this->permissao_filtro_abandono_transferencia  = dbBool($this->permissao_filtro_abandono_transferencia);
        $this->multiplas_reserva_vaga = dbBool($this->multiplas_reserva_vaga);
        $this->componente_curricular_turma = dbBool($this->componente_curricular_turma);
        $this->reprova_dependencia_ano_concluinte = dbBool($this->reprova_dependencia_ano_concluinte);
        $this->reserva_integral_somente_com_renda	= dbBool($this->reserva_integral_somente_com_renda);

		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_instituicao", $this->cod_instituicao );

		// text
		$this->campoTexto( "nm_instituicao", "Nome da Instituição", $this->nm_instituicao, 30, 255, true );
		$this->campoCep( "cep", "CEP", int2CEP( $this->cep ), true, "-", false, false );
		$this->campoTexto( "logradouro", "Logradouro", $this->logradouro, 30, 255, true );
		$this->campoTexto( "bairro", "Bairro", $this->bairro, 30, 40, true );
		$this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsTipoLogradouro" ) )
		{
			$objTemp = new clsTipoLogradouro();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_idtlog", "Tipo do Logradouro", $opcoes, $this->ref_idtlog, "", false, "", "", false, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			$objTemp = new clsUf();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				asort($lista);
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['sigla_uf']}"] = "{$registro['sigla_uf']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_sigla_uf", "UF", $opcoes, $this->ref_sigla_uf, "", false, "", "", false, true );

		$this->campoNumero( "numero", "Número", $this->numero, 6, 6 );
		$this->campoTexto( "complemento", "Complemento", $this->complemento, 30, 50, false );
		$this->campoTexto( "nm_responsavel", "Nome do Responsável", $this->nm_responsavel, 30, 255, true );
		$this->campoNumero( "ddd_telefone", "DDD Telefone", $this->ddd_telefone, 2, 2 );
		$this->campoNumero( "telefone", "Telefone", $this->telefone, 11, 11 );


		$this->campoData('data_base_transferencia', 'Data máxima para deslocamento', Portabilis_Date_Utils::pgSQLToBr($this->data_base_transferencia), null, null, false);

        $this->campoData('data_base_remanejamento', 'Data máxima para troca de sala', Portabilis_Date_Utils::pgSQLToBr($this->data_base_remanejamento), null, null, false);

		///$hiddenInputOptions = array('options' => array('value' => $this->coordenador_transporte));
		//$helperOptions      = array('objectName' => 'gestor', 'hiddenInputOptions' => $hiddenInputOptions);
	  	$options            = array('label'      => 'Coordenador(a) de transporte',
		                                'size'       => 50,
		                                'value'		 => $this->coordenador_transporte,
		                                'required'   => false);

		$this->inputsHelper()->simpleSearchPessoa('coordenador_transporte', $options, $helperOptions);

	    $this->campoCheck("exigir_vinculo_turma_professor", "Exigir vínculo com turma para lançamento de notas do professor?", $this->exigir_vinculo_turma_professor );

	    $this->campoCheck("gerar_historico_transferencia", "Gerar histórico de transferência ao transferir matrícula?", $this->gerar_historico_transferencia);

	    $this->campoCheck("matricula_apenas_bairro_escola", "Permitir matrícula de alunos apenas do bairro da escola?", $this->matricula_apenas_bairro_escola);

		$this->campoCheck("restringir_historico_escolar", "Restringir modificações de históricos escolares?", $this->restringir_historico_escolar, NULL, false, false, false, 'Com esta opção selecionada, somente será possível cadastrar/editar históricos escolares de alunos que pertençam a mesma escola do funcionário.' );

	  	$this->campoCheck("controlar_espaco_utilizacao_aluno", "Controlar espaço utilizado pelo aluno?", $this->controlar_espaco_utilizacao_aluno );
		$this->campoMonetario( "percentagem_maxima_ocupacao_salas", "Percentagem máxima de ocupação da sala",
															  Portabilis_Currency_Utils::moedaUsToBr($this->percentagem_maxima_ocupacao_salas),
															  6,
															  6,
															  false);
		$this->campoNumero( "quantidade_alunos_metro_quadrado", "Quantidade máxima de alunos permitidos por metro quadrado", $this->quantidade_alunos_metro_quadrado, 6, 6 );

		$this->campoCheck("restringir_multiplas_enturmacoes", "Não permitir múltiplas enturmações para o aluno no mesmo curso e série/ano", $this->restringir_multiplas_enturmacoes);
    $this->campoCheck("permissao_filtro_abandono_transferencia", "Não permitir a apresentação de alunos com matrícula em abandono ou transferida na emissão do relatório de frequência", $this->permissao_filtro_abandono_transferencia);

    $this->campoCheck("multiplas_reserva_vaga", "Permitir múltiplas reservas de vagas para o mesmo candidato em escolas diferentes", $this->multiplas_reserva_vaga);

		$this->campoCheck("reserva_integral_somente_com_renda", "Permitir reserva de vaga para o turno integral somente quando a renda for informada", $this->reserva_integral_somente_com_renda);

    $this->campoCheck("componente_curricular_turma",
    			      "Permitir definir componentes curriculares diferenciados nas turmas",
    			      $this->componente_curricular_turma);

    $this->campoCheck("reprova_dependencia_ano_concluinte",
    			      "Não permitir dependência em séries/anos concluintes",
    			      $this->reprova_dependencia_ano_concluinte,
    			      null,
    			      false,
    			      false,
    			      false,
    			      "Caso marcado, o aluno que reprovar em algum componente em ano concluinte será automaticamente reprovado.");

    	$this->inputsHelper()->text('data_base', array('label' => 'Data base para matrícula (dia/mês)',
					    								'size' => 5,
					    								'max_length' => 5,
					    								'placeholder' => 'dd/mm',
					    								'required' => false,
					    								'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_base_matricula)));

    $this->campoData('data_expiracao_reserva_vaga', 'Data para indeferimento automático da reserva de vaga', Portabilis_Date_Utils::pgSQLToBr($this->data_expiracao_reserva_vaga), null, null, false);

    $this->inputsHelper()->text('data_fechamento', array('label' => 'Data de fechamento das turmas para matrícula',
					    							'size' => 5,
					    							'max_length' => 5,
					    							'placeholder' => 'dd/mm',
					    							'required' => false,
					    							'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_fechamento)));
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmieducarInstituicao( null, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao, null, null, $this->quantidade_alunos_metro_quadrado);
		$obj->data_base_remanejamento 			= Portabilis_Date_Utils::brToPgSQL($this->data_base_remanejamento);
    $obj->data_base_transferencia       = Portabilis_Date_Utils::brToPgSQL($this->data_base_transferencia);
		$obj->data_expiracao_reserva_vaga 			= Portabilis_Date_Utils::brToPgSQL($this->data_expiracao_reserva_vaga);
		$obj->exigir_vinculo_turma_professor	= is_null($this->exigir_vinculo_turma_professor) ? 0 : 1;
		$obj->gerar_historico_transferencia 	= !is_null($this->gerar_historico_transferencia);
		$obj->matricula_apenas_bairro_escola 	= !is_null($this->matricula_apenas_bairro_escola);
		$obj->restringir_historico_escolar 		= !is_null($this->restringir_historico_escolar);
		$obj->restringir_multiplas_enturmacoes  = !is_null($this->restringir_multiplas_enturmacoes);
    $obj->permissao_filtro_abandono_transferencia  = !is_null($this->permissao_filtro_abandono_transferencia);
    $obj->multiplas_reserva_vaga  = !is_null($this->multiplas_reserva_vaga);
    	$obj->componente_curricular_turma  = !is_null($this->componente_curricular_turma);
    	$obj->reprova_dependencia_ano_concluinte  = !is_null($this->reprova_dependencia_ano_concluinte);
		$obj->reserva_integral_somente_com_renda  = !is_null($this->reserva_integral_somente_com_renda);
		$obj->coordenador_transporte 			= $this->pessoa_coordenador_transporte;
		$obj->controlar_espaco_utilizacao_aluno = is_null($this->controlar_espaco_utilizacao_aluno) ? 0 : 1;
		$obj->percentagem_maxima_ocupacao_salas = Portabilis_Currency_Utils::moedaBrToUs($this->percentagem_maxima_ocupacao_salas);
		$obj->data_base_matricula               = Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_base);
		$obj->data_fechamento               = Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_fechamento);
		$obj->auditar_notas = !is_null($this->auditar_notas);
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInstituicao\nvalores obrigatorios\nis_numeric( $ref_usuario_cad ) && is_string( $ref_idtlog ) && is_string( $ref_sigla_uf ) && is_numeric( $cep ) && is_string( $cidade ) && is_string( $bairro ) && is_string( $logradouro ) && is_string( $nm_responsavel ) && is_string( $data_cadastro ) && is_numeric( $ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInstituicao( $this->cod_instituicao, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao, null, null, $this->quantidade_alunos_metro_quadrado);
		$obj->data_base_remanejamento 			= Portabilis_Date_Utils::brToPgSQL($this->data_base_remanejamento);
    $obj->data_base_transferencia       = Portabilis_Date_Utils::brToPgSQL($this->data_base_transferencia);
		$obj->data_expiracao_reserva_vaga 			= Portabilis_Date_Utils::brToPgSQL($this->data_expiracao_reserva_vaga);
		$obj->exigir_vinculo_turma_professor 	= is_null($this->exigir_vinculo_turma_professor) ? 0 : 1;
		$obj->gerar_historico_transferencia 	= !is_null($this->gerar_historico_transferencia);
		$obj->matricula_apenas_bairro_escola 	= !is_null($this->matricula_apenas_bairro_escola);
		$obj->restringir_historico_escolar 		= !is_null($this->restringir_historico_escolar);
		$obj->restringir_multiplas_enturmacoes 	= !is_null($this->restringir_multiplas_enturmacoes);
    $obj->permissao_filtro_abandono_transferencia   = !is_null($this->permissao_filtro_abandono_transferencia);
    $obj->multiplas_reserva_vaga  = !is_null($this->multiplas_reserva_vaga);
    	$obj->componente_curricular_turma  = !is_null($this->componente_curricular_turma);
    	$obj->reprova_dependencia_ano_concluinte = !is_null($this->reprova_dependencia_ano_concluinte);
		$obj->reserva_integral_somente_com_renda = !is_null($this->reserva_integral_somente_com_renda);
		$obj->coordenador_transporte 			= $this->pessoa_coordenador_transporte;
		$obj->controlar_espaco_utilizacao_aluno = is_null($this->controlar_espaco_utilizacao_aluno) ? 0 : 1;
		$obj->percentagem_maxima_ocupacao_salas = Portabilis_Currency_Utils::moedaBrToUs($this->percentagem_maxima_ocupacao_salas);
		$obj->data_base_matricula               = Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_base);
		$obj->data_fechamento               	= Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_fechamento);

		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$verificaEscolasVinculadas = new clsPmieducarEscola();
		$listaEscolasVinculadas = $verificaEscolasVinculadas->lista(null, null, null, $this->cod_instituicao);
		if ( is_array($listaEscolasVinculadas)){
			$this->mensagem = "Exclus&atilde;o n&atilde;o realizada. Esta instituic&atilde;o possui escolas vinculadas.<br>";
			return false;
		}else{
			$obj = new clsPmieducarInstituicao($this->cod_instituicao, $this->pessoa_logada, $this->ref_usuario_cad, $this->ref_idtlog, $this->ref_sigla_uf, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, $this->ativo);
			$excluiu = $obj->excluir();
			if( $excluiu )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_instituicao_lst.php" );
				die();
				return true;
			}

			$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao excluir clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
			return false;
		}
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

	$j('#controlar_espaco_utilizacao_aluno').click(onControlarEspacoUtilizadoClick);

	if(!$j('#controlar_espaco_utilizacao_aluno').prop('checked')){
		$j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
		$j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
	}

	function onControlarEspacoUtilizadoClick(){
		if(!$j('#controlar_espaco_utilizacao_aluno').prop('checked')){
			$j('#percentagem_maxima_ocupacao_salas').val('');
			$j('#quantidade_alunos_metro_quadrado').val('');
			$j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
			$j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
		}else{
			$j('#percentagem_maxima_ocupacao_salas').closest('tr').show();
			$j('#quantidade_alunos_metro_quadrado').closest('tr').show();
		}
	}

	$j('#data_base').mask("99/99");
	$j('#data_fechamento').mask("99/99");

</script>