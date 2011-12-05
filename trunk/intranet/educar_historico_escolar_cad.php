<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version SVN: $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Hist&oacute;rico Escolar" );
		$this->processoAp = "578";
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

	var $ref_cod_aluno;
	var $sequencial;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $carga_horaria;
	var $dias_letivos;
	var $escola;
	var $escola_cidade;
	var $escola_uf;
	var $observacao;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;
	var $nm_serie;
	var $origem;
	var $extra_curricular;
	var $ref_cod_matricula;

	var $faltas_globalizadas;
	var $cb_faltas_globalizadas;
	var $frequencia;
	
//------INCLUI DISCIPLINA------//
	var $historico_disciplinas;
	var $nm_disciplina;
	var $nota;
	var $faltas;
	var $excluir_disciplina;
	var $ultimo_sequencial;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->sequencial=$_GET["sequencial"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) )
		{
			$obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, $this->sequencial );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if (!$this->origem)
				{
					header( "Location: educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
					die();
				}

				if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}" : "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( !$this->$campo ) ?  $val : $this->$campo ;

		// primary keys
		$this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
		$this->campoOculto( "sequencial", $this->sequencial );

		$obj_aluno = new clsPmieducarAluno();
		$lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_aluno) )
		{
			$det_aluno = array_shift($lst_aluno);
			$this->nm_aluno = $det_aluno["nome_aluno"];
			$this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno );
		}
		//$obj_permissoes = new clsPermissoes();
		//$this->ref_cod_instituicao = $obj_permissoes->getInstituicao( $this->pessoa_logada );
		//$this->campoOculto( 'ref_cod_instituicao', $this->ref_cod_instituicao );

		$obj_nivel = new clsPmieducarUsuario($this->pessoa_logada);
		$nivel_usuario = $obj_nivel->detalhe();

		if ($nivel_usuario['ref_cod_tipo_usuario'] == 1)
		{
			$obj_instituicao = new clsPmieducarInstituicao();
			$lista = $obj_instituicao->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,1);
			$opcoes["1"] = "Selecione";
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
				}
			}
			$this->campoLista("ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, "");
		}
		else
		{
			$obj_instituicao = new clsPmieducarInstituicao($nivel_usuario['ref_cod_instituicao']);
			$inst = $obj_instituicao->detalhe();
			$this->campoOculto("ref_cod_instituicao", $inst['cod_instituicao']);
			$this->campoTexto("instituicao", "Instiui&ccedil;&atilde;o", $inst['nm_instituicao'], 30, 255, false, false, false, "", "", "", "", true);
		}

		// text
		$this->campoTexto( "escola", "Escola", $this->escola, 30, 255, true );
		$this->campoTexto( "escola_cidade", "Cidade da Escola", $this->escola_cidade, 30, 255, true );


		if($this->escola_uf)
		{
			//busca pais do estado
			$obj_uf = new clsUf($this->escola_uf);
			$det_uf = $obj_uf->detalhe();
		}
		$lista_pais_origem = array('45' => "País da escola");
		$obj_pais = new clsPais();
		$obj_pais_lista = $obj_pais->lista(null,null,null,"","","nome asc");
		if($obj_pais_lista)
		{
			foreach ($obj_pais_lista as $key => $pais)
			{
				$lista_pais_origem[$pais["idpais"]] = $pais["nome"];
			}
		}
		$this->campoLista("idpais", "Pa&iacute;s da Escola", $lista_pais_origem, $det_uf['45'] ); 

		$obj_uf = new clsUf();
		$lista_uf = $obj_uf->lista( false,false,$det_uf['idpais'],false,false, "sigla_uf" );
		$lista_estado = array( "SC" => "Selecione um pa&iacute;s" );
		if( $lista_uf )
		{
			foreach ($lista_uf as $uf)
			{
				$lista_estado[$uf['sigla_uf']] = $uf['sigla_uf'];
			}
		}
		$this->campoLista("escola_uf", "Estado da Escola", $lista_estado, $this->escola_uf );

		$this->campoTexto( "nm_curso", "Curso", $this->nm_curso, 30, 255, false );
		$this->campoTexto( "nm_serie", "S&eacute;rie", $this->nm_serie, 30, 255, true );
		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );
		$this->campoMonetario( "carga_horaria", "Carga Hor&aacute;ria", $this->carga_horaria, 8, 8, true );
		$this->campoCheck( "cb_faltas_globalizadas", "Faltas Globalizadas", $this->faltas_globalizadas );
		$this->campoNumero( "faltas_globalizadas", "Faltas Globalizadas", $this->faltas_globalizadas, 4, 4, false );
		$this->campoNumero( "dias_letivos", "Dias Letivos", $this->dias_letivos, 3, 3, true );
		$this->campoMonetario( "frequencia", "Frequência", $this->frequencia, 8, 8, true );
		$this->campoCheck( "extra_curricular", "Extra-Curricular", $this->extra_curricular );
		$this->campoMemo( "observacao", "Observa&ccedil;&atilde;o", $this->observacao, 60, 5, false );

		$opcoes = array( "" => "Selecione", 1 => "Aprovado", 2 => "Reprovado", 3 => "Em Andamento", 4 => "Transferido" );
		$this->campoLista( "aprovado", "Situa&ccedil;&atilde;o", $opcoes, $this->aprovado );

		$this->campoTexto( "registro", "Registro (arquivo)", $this->registro, 30, 50, false);
		$this->campoTexto( "livro", "Livro", $this->livro, 30, 50, false);
		$this->campoTexto( "folha", "Folha", $this->folha, 30, 50, false);
    

	//---------------------INCLUI DISCIPLINAS---------------------//
		$this->campoQuebra();

		//if ( $_POST["historico_disciplinas"] )
			//$this->historico_disciplinas = unserialize( urldecode( $_POST["historico_disciplinas"] ) );

		//$qtd_disciplinas = ( count( $this->historico_disciplinas ) == 0 ) ? 1 : ( count( $this->historico_disciplinas ) + 1);

		if( is_numeric( $this->ref_cod_aluno) && is_numeric( $this->sequencial) && !$_POST)
		{
			$obj = new clsPmieducarHistoricoDisciplinas();
			$obj->setOrderby("nm_disciplina ASC");
			$registros = $obj->lista( null, $this->ref_cod_aluno, $this->sequencial );
			$qtd_disciplinas = 0;
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->historico_disciplinas[$qtd_disciplinas][] = $campo["nm_disciplina"];
					$this->historico_disciplinas[$qtd_disciplinas][] = $campo["nota"];
					$this->historico_disciplinas[$qtd_disciplinas][] = $campo["faltas"];
					$this->historico_disciplinas[$qtd_disciplinas][] = $campo["sequencial"];
					$qtd_disciplinas++;
				}
			}
		}

		$this->campoTabelaInicio("notas","Notas",array("Disciplina","Nota","Faltas"),$this->historico_disciplinas);

    //$this->campoTexto( "nm_disciplina", "Disciplina", $this->nm_disciplina, 30, 255, false, false, false, '', '', 'autoCompleteComponentesCurricular(this)', 'onfocus' );
    $this->campoTexto( "nm_disciplina", "Disciplina", $this->nm_disciplina, 30, 255, false, false, false, '', '', 'disableAutoCompleteAndSetOnKeyUpEvent(this)', 'onfocus' );

			$this->campoTexto( "nota", "Nota", $this->nota, 10, 255, false );
			$this->campoNumero( "faltas", "Faltas", $this->faltas, 3, 3, false );
			//$this->campoOculto("sequencial","");

		$this->campoTabelaFim();

		//$this->campoOculto("ultimo_sequencial","$qtd_disciplinas");

		$this->campoQuebra();
	//---------------------FIM INCLUI DISCIPLINAS---------------------//
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

/*		$this->historico_disciplinas = unserialize( urldecode( $this->historico_disciplinas ) );
		if ($this->historico_disciplinas)
		{
		*/
			$this->carga_horaria = str_replace(".","",$this->carga_horaria);
			$this->carga_horaria = str_replace(",",".",$this->carga_horaria);
			$this->frequencia = str_replace(".","",$this->frequencia);
			$this->frequencia = str_replace(",",".",$this->frequencia);

			if ($this->extra_curricular == 'on')
				$this->extra_curricular = 1;
			else
				$this->extra_curricular = 0;

//				echo "clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, null, null, $this->pessoa_logada, $this->nm_serie, $this->ano, $this->carga_horaria, $this->dias_letivos, $this->escola, $this->escola_cidade, $this->escola_uf, $this->observacao, $this->aprovado, null, null, 1, null, $this->ref_cod_instituicao, 1, $this->extra_curricular )";
			$obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, null, null, $this->pessoa_logada, $this->nm_serie, $this->ano, $this->carga_horaria, $this->dias_letivos, $this->escola, $this->escola_cidade, $this->escola_uf, $this->observacao, $this->aprovado, null, null, 1, $this->faltas_globalizadas, $this->ref_cod_instituicao, 1, $this->extra_curricular, null, $this->frequencia, $this->registro, $this->livro, $this->folha, $this->nm_curso);
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
			//--------------CADASTRA DISCIPLINAS--------------//
				if ($this->nm_disciplina)
				{
					$sequencial = 1;
					foreach ( $this->nm_disciplina AS $key => $disciplina )
					{

						$obj_historico = new clsPmieducarHistoricoEscolar();
						$this->sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );

						$obj = new clsPmieducarHistoricoDisciplinas( $sequencial, $this->ref_cod_aluno, $this->sequencial, $disciplina, $this->nota[$key], $this->faltas[$key] );
						$cadastrou1 = $obj->cadastra();
						if( !$cadastrou1 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarHistoricoDisciplinas\nvalores obrigatorios\nis_numeric( {$this->ref_cod_aluno} ) && is_numeric( {$this->sequencial} ) && is_string( {$campo["nm_disciplina"]} ) && is_numeric( {$campo["sequencial"]} ) && is_string( {$campo["nota"]} ) && is_numeric( {$campo["faltas"]} )\n-->";
							return false;
						} 

						$sequencial++;
					}
					$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
					header( "Location: educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
					die();
					return true;
				}
			//--------------FIM CADASTRA DISCIPLINAS--------------//
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarHistoricoEscolar\nvalores obrigatorios\nis_numeric( $this->ref_cod_aluno ) && is_numeric( $this->pessoa_logada ) && is_string( $this->nm_serie ) && is_numeric( $this->ano ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->dias_letivos ) && is_string( $this->escola ) && is_string( $this->escola_cidade ) && is_string( $this->escola_uf ) && is_numeric( $this->aprovado ) && is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->extra_curricular )\n-->";
			return false;
/*		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina!') </script>";
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		return false;
		*/
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

/*		$this->historico_disciplinas = unserialize( urldecode( $this->historico_disciplinas ) );
		if ($this->historico_disciplinas)
		{
		*/
			$this->carga_horaria = str_replace(".","",$this->carga_horaria);
			$this->carga_horaria = str_replace(",",".",$this->carga_horaria);
			$this->frequencia = str_replace(".","",$this->frequencia);
			$this->frequencia = str_replace(",",".",$this->frequencia);
			
			if ($this->extra_curricular == 'on')
				$this->extra_curricular = 1;
			else
				$this->extra_curricular = 0;


			if(!$this->cb_faltas_globalizadas)
				$this->faltas_globalizadas = 'NULL';
			$obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, $this->sequencial, $this->pessoa_logada, null, $this->nm_serie, $this->ano, $this->carga_horaria, $this->dias_letivos, $this->escola, $this->escola_cidade, $this->escola_uf, $this->observacao, $this->aprovado, null, null, 1, $this->faltas_globalizadas, $this->ref_cod_instituicao, 1, $this->extra_curricular, null, $this->frequencia, $this->registro, $this->livro, $this->folha, $this->nm_curso);
			$editou = $obj->edita();
			
			if( $editou )
			{
			//--------------EDITA DISCIPLINAS--------------//
				if ($this->nm_disciplina)
				{
					//$this->historico_disciplinas = unserialize( urldecode( $this->historico_disciplinas ) );

					$obj  = new clsPmieducarHistoricoDisciplinas();
					$excluiu = $obj->excluirTodos( $this->ref_cod_aluno,$this->sequencial );
					if ( $excluiu )
					{
						$sequencial = 1;
						foreach ( $this->nm_disciplina AS $key => $disciplina )
						{
							//$campo['nm_disciplina_'] = urldecode($campo['nm_disciplina_']);


							$obj = new clsPmieducarHistoricoDisciplinas( $sequencial, $this->ref_cod_aluno, $this->sequencial, $disciplina, $this->nota[$key], $this->faltas[$key] );
							$cadastrou1 = $obj->cadastra();
							if( !$cadastrou1 )
							{
								$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
								echo "<!--\nErro ao cadastrar clsPmieducarHistoricoDisciplinas\nvalores obrigatorios\nis_numeric( {$this->ref_cod_aluno} ) && is_numeric( {$this->sequencial} ) && is_string( {$campo["nm_disciplina_"]} ) && is_numeric( {$campo["sequencial_"]} ) && is_string( {$campo["nota_"]} ) && is_numeric( {$campo["faltas_"]} )\n-->";
								return false;
							}
							$sequencial++;
						}
					}
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
					die();
					return true;
				}
			//--------------FIM EDITA DISCIPLINAS--------------//
			}
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarHistoricoEscolar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) && is_numeric( $this->pessoa_logada ) )\n-->";
			return false;
/*		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina!') </script>";
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		return false;
		*/
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );


		$obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, $this->sequencial, $this->pessoa_logada,null,null,null,null,null,null,null,null,null,null,null,null,0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$obj  = new clsPmieducarHistoricoDisciplinas();
			$excluiu = $obj->excluirTodos( $this->ref_cod_aluno,$this->sequencial );
			if ( $excluiu )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
				die();
				return true;
			}
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarHistoricoEscolar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->sequencial ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

<script type="text/javascript">
	document.getElementById('cb_faltas_globalizadas').onclick =function()
	{
		setVisibility('tr_faltas_globalizadas',this.checked);
	}

	document.getElementById('cb_faltas_globalizadas').onclick();



document.getElementById('idpais').onchange = function()
{
	var campoPais = document.getElementById( 'idpais' ).value;
	var campoEstado = document.getElementById( 'escola_uf' );

	campoEstado.length = 1;
	campoEstado.disabled = true;
	campoEstado.options[0] = new Option( 'Carregando estados', '', false, false );

	var xml1 = new ajax(getEstado_XML);
	strURL = "public_uf_xml.php?pais="+campoPais;
	xml1.envia(strURL);
}

function getEstado_XML(xml)
{


	var campoEstado = document.getElementById( 'escola_uf' );


	var estados = xml.getElementsByTagName( "estado" );

	campoEstado.length = 1;
	campoEstado.options[0] = new Option( 'Selecione um estado', '', false, false );
	for ( var j = 0; j < estados.length; j++ )
	{

		campoEstado.options[campoEstado.options.length] = new Option( estados[j].firstChild.nodeValue, estados[j].getAttribute('sigla_uf'), false, false );

	}
	if ( campoEstado.length == 1 ) {
		campoEstado.options[0] = new Option( 'País não possui estados', '', false, false );
	}

	campoEstado.disabled = false;
}

function disableAutoCompleteAndSetOnKeyUpEvent(targetElement)
{
  targetElement.setAttribute('autocomplete', 'off');

  if (document.addEventListener)
    targetElement.addEventListener('keyup', autoCompleteComponentesCurricular, false);
  else
    targetElement.attachEvent('onkeyup', autoCompleteComponentesCurricular);
}

function autoCompleteComponentesCurricular(_event)
{

  if (! _event)
    _event = event;

  targetElement = _event.srcElement || _event.target;

/*  if (! targetElement.hasAttribute('old_value'))*/
  if (! targetElement.getAttribute('old_value') && targetElement.getAttribute('value').length > 1)
    targetElement.setAttribute('old_value', targetElement.getAttribute('value'));
  else if (! targetElement.getAttribute('old_value'))
    targetElement.setAttribute('old_value', '');

  if (targetElement.value != targetElement.getAttribute('old_value'))
    targetElement.value = targetElement.value.replace(/^\s+/,'');  

  if (! targetElement.value.length || targetElement.value != targetElement.getAttribute('old_value'))
  {
    var minLength = 0;
    var className = 'auto_complete_componente_curricular';

    var listsComponentes = document.getElementsByClassName(className);
    for (var i = 0; i < listsComponentes.length; i++)
    {
      listsComponentes[i].parentNode.removeChild(listsComponentes[i]);
    }

    if (targetElement.value.length && targetElement.value.length >= minLength)
    {    
      var instituicaoId = document.getElementById('ref_cod_instituicao').value;
      var word = targetElement.value;
      var limit = 15;

      targetElement.setAttribute('old_value', targetElement.value);
      var url = "portabilis_auto_complete_componente_curricular_xml.php?instituicao_id="+instituicaoId+"&limit="+limit+"&word="+word+"&target_element_id="+targetElement.id+"&element_class_name="+className;
      //alert(url);
      new ajax(createListAutoComplete).envia(url);
    }
  }
}

function createListAutoComplete(xml)
{
	var entity = xml.documentElement.getAttribute('entity');
  var targetElementId = xml.documentElement.getAttribute('target_element_id');
  var className = xml.documentElement.getAttribute('element_class_name');

  var targetElement = document.getElementById(targetElementId);

  var listComponentes = document.createElement('ul');
  listComponentes.className = className + ' autocomplete';    

	var items = xml.getElementsByTagName(entity);
  for (var i = 0; i < items.length; i++)
  {
    var _i = document.createElement('li');
    _i.innerHTML = items[i].getAttribute('value');
    _i.id = entity + ':' + items[i].getAttribute('id');
    _i.onclick = function(){targetElement.value = this.innerHTML; /*if (this.parentNode.childElementCount == 1){*/ this.parentNode.style.display='none';/*} this.remove();*/};

    /*
    if (el.addEventListener){
      el.addEventListener('click', modifyText, false); 
    } else if (el.attachEvent){
      el.attachEvent('onclick', modifyText);
    }
    */

    listComponentes.appendChild(_i);
  }
  if (! items.length)
  {
      var _i = document.createElement('li');
      _i.innerHTML = 'Sem sugestões para "' + targetElement.value + '"';
  }
  
  listComponentes.appendChild(_i);

  //TODO criar estilo para lista / itens, (remover bullets, margin / padding 0, onhover change color
  //TODO call ajax / in callback function criar itens / append in list

  targetElement.parentNode.appendChild(listComponentes);
}
</script>
