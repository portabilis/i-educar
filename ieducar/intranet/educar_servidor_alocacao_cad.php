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
/**
 * @author Adriano Erik Weiguert Nagasava
 * 		   Haissam Yebahi
 */

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Alocação" );
		$this->processoAp = "635";
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
	var $cod_servidor_alocacao;
	var $ref_ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $ref_cod_servidor;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $carga_horaria_alocada;
	var $carga_horaria_disponivel;
	var $periodo;

	var $alocacao_array 		 = array();
	var $alocacao_excluida_array = array();

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_servidor 	   = $_GET["ref_cod_servidor"];
		$this->ref_ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_alocacao_lst.php" );

		if ( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) )
		{

			$obj 	= new clsPmieducarServidorAlocacao( );
			$lista  = $obj->lista( null, $this->ref_ref_cod_instituicao, null, null, null, $this->ref_cod_servidor, null, null, null, null, 1, null, null );

			if ( $lista )
			{
				foreach ( $lista AS $campo => $val )
				{
					$temp 							= array();
					$temp['carga_horaria_alocada'] 	= $val['carga_horaria'];
					$temp['periodo'] 				= $val['periodo'];
					$temp['ref_cod_escola'] 		= $val['ref_cod_escola'];
					$temp['novo'] 					= 0;

					$this->alocacao_array[] 		= $temp;
				}
				$retorno = "Novo";
			}

			$obj_servidor 		 			= new clsPmieducarServidor( $this->ref_cod_servidor, null, null, null, null, null, 1, $this->ref_ref_cod_instituicao );
			$det_servidor 		 			= $obj_servidor->detalhe();
			$this->carga_horaria_disponivel = $det_servidor['carga_horaria'];
		}
		else
		{
			header( "location: educar_servidor_lst.php" );
			die;
		}

		$this->url_cancelar 	 = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if ( $_POST )
		{
			foreach ( $_POST AS $campo => $val )
			{
				if ( is_string( $val ) )
				{
					$val = urldecode( $val );
				}
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;
			}
		}

		$obj_inst = new clsPmieducarInstituicao( $this->ref_ref_cod_instituicao );
		$inst_det = $obj_inst->detalhe();

		$this->campoRotulo( "nm_instituicao", "Institui&ccedil;&atilde;o", $inst_det['nm_instituicao'] );
		$this->campoOculto( "ref_ref_cod_instituicao", $this->ref_ref_cod_instituicao );

		if ( class_exists( "clsPmieducarServidor" ) )
		{
			$objTemp = new clsPmieducarServidor( $this->ref_cod_servidor );
			$det 	 = $objTemp->detalhe();
			if ( $det )
			{
				foreach ( $det as $key => $registro )
				{
					$this->$key = $registro;
				}
			}

			if ( $this->ref_cod_servidor )
			{
				$objTemp 	 = new clsFuncionario( $this->ref_cod_servidor );
				$detalhe 	 = $objTemp->detalhe();
				$detalhe 	 = $detalhe["idpes"]->detalhe();
				$nm_servidor = $detalhe["nome"];
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarServidor nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$this->campoRotulo( "nm_servidor", "Servidor", $nm_servidor );

		$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );

		if ( $_POST["alocacao_array"] )
		{
			$this->alocacao_array = unserialize( urldecode( $_POST["alocacao_array"] ) );
		}

		if ( $_POST["alocacao_excluida_array"] )
		{
			$this->alocacao_excluida_array = unserialize( urldecode( $_POST["alocacao_excluida_array"] ) );
		}

		if ( $_POST["carga_horaria_alocada"] && $_POST["periodo"] )
		{
			$aux 						  = array();
			$aux["carga_horaria_alocada"] = $_POST["carga_horaria_alocada"];
			$aux["periodo"] 			  = $_POST["periodo"];
			$aux["ref_cod_escola"] 		  = $_POST["ref_cod_escola"];
			$aux['novo'] 				  = 1;
			/*$achou 						  = false;

			foreach ( $this->alocacao_array as $alocacao )
			{
				if ( $alocacao['periodo'] == $aux["periodo"] )
				{
					$achou = true;
				  	echo "<script>alert('Horário já utilizado!\\nPeriodo: {$this->periodo[$aux["periodo"]]}\\n');</script>";
				}
			}

			if ( !$achou )
			{
				$this->alocacao_array[] = $aux;
			}
			*/
			$this->alocacao_array[] = $aux;

			unset( $this->periodo );
			unset( $this->carga_horaria_alocada );
			unset( $this->ref_cod_escola );
		}

		/**
		 * Exclusão
		 */
		if ( $this->alocacao_array )
		{
			foreach ( $this->alocacao_array as $key => $alocacao )
			{
				if ( is_numeric( $_POST['excluir_periodo'] ) )
				{
					if ( $_POST['excluir_periodo'] == $key )
					{
						$this->alocacao_excluida_array[] = $alocacao;
						unset( $this->alocacao_array[$key] );
						unset( $this->excluir_periodo );
					}
				}
			}
		}

		/**
		 * Carga Horaria
		 */
		$total_horas   = sprintf( "%02d", ( int ) ( floor( $this->carga_horaria_disponivel ) ) );
		$total_horas   = sprintf( "%02d", ( int ) ( floor( $this->carga_horaria_disponivel ) ) );
		$total_minutos = sprintf( "%02d", ( int ) ( ( floatval( $this->carga_horaria_disponivel ) - floatval( $total_horas ) ) * 60 ) );

		$horas_utilizadas   = 0;
		$minutos_utilizados = 0;

		if ( $this->alocacao_array )
		{
			foreach ( $this->alocacao_array as $alocacao )
			{
				$carga_horaria_ = explode( ":", $alocacao['carga_horaria_alocada'] );

				$horas_utilizadas   += ( $carga_horaria_[0] );
				$minutos_utilizados += ( $carga_horaria_[1] );
			}
		}

		$horas 				  = sprintf( "%02d", ( int ) $horas_utilizadas );
		$minutos 			  = sprintf( "%02d", ( int ) $minutos_utilizados );
		$str_horas_utilizadas =  "{$horas}:{$minutos}";

		$this->campoRotulo( "carga_horaria_disponivel", "Carga Hor&aacute;ria", "{$total_horas}:{$total_minutos}" );
		$this->campoRotulo( "horas_utilizadas", "Horas Utilizadas", $str_horas_utilizadas );

		$horas 				 = sprintf( "%02d", ( int ) $total_horas - $horas_utilizadas );
		$minutos 			 = sprintf( "%02d", ( int ) $total_minutos - $minutos_utilizados );
		$str_horas_restantes = "{$horas}:{$minutos}";

		$this->campoRotulo( "horas_restantes", "Horas Restantes", $str_horas_restantes );
		$this->campoOculto( "horas_restantes_", $str_horas_restantes );

		$this->campoQuebra();

		$this->campoOculto( "excluir_periodo", "" );
		unset( $aux );

//		array_multisort( $this->alocacao_array );

		if ( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_escola   = new clsPmieducarEscola();
			$lista_escola = $obj_escola->lista( null, null, null, $this->ref_ref_cod_instituicao, null, null, null, null, null, null, 1 );

			if ( $lista_escola )
			{
				$opcoes = array( '' => "Selecione" );
				foreach ( $lista_escola as $escola )
				{
					$opcoes[$escola['cod_escola']] = $escola['nome'];
				}
			}
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}

		$this->campoLista( "ref_cod_escola", "Escola", $opcoes, $this->ref_cod_escola, "", false, "", "", false, false );
		$this->campoLista( "periodo", "Período", array( "" => "Selecione", "1" => "Matutino", "2" => "Vespertino", "3" => "Noturno" ), $this->periodo, null, false, "", "", false, false );
		$this->campoHora( "carga_horaria_alocada", "Carga Horária", $this->carga_horaria_alocada, false );
		$this->campoOculto( "alocacao_array", serialize( $this->alocacao_array ) );
		$this->campoOculto( "alocacao_excluida_array", serialize( $this->alocacao_excluida_array ) );
		$this->campoRotulo( "bt_incluir_periodo", "Período", "<a href='#' onclick=\"if(validaHora()){document.getElementById('incluir_periodo').value = 'S'; document.getElementById('tipoacao').value = ''; document.{$this->__nome}.submit();}\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );

		if ( $this->alocacao_array )
		{
			$excluir_ok = false;

			if ( $_POST['excluir_periodo'] || $_POST['excluir_periodo'] == "0" )
			{
				$excluir_ok = true;
			}

			foreach ( $this->alocacao_array as $key => $alocacao )
			{
				$obj_permissoes = new clsPermissoes();
				$link_excluir 	= "";

				if ( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3 ) )
				{
					$link_excluir = "<a href='#' onclick=\"getElementById( 'excluir_periodo' ).value = '{$key}'; getElementById( 'tipoacao' ).value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>";
				}

				$obj_escola    = new clsPmieducarEscola( $alocacao['ref_cod_escola'] );
				$det_escola    = $obj_escola->detalhe();
				$det_escola    = $det_escola["nome"];

				switch( $alocacao["periodo"] )
				{
					case 1:
						$nm_periodo = "Matutino";
						break;
					case 2:
						$nm_periodo = "Vespertino";
						break;
					case 3:
						$nm_periodo = "Noturno";
						break;
				}

				$this->campoTextoInv( "periodo_{$key}", "", $nm_periodo, 10, 10, false, false, true, "", "", "", "", "periodo" );
				$this->campoTextoInv( "carga_horaria_alocada_{$key}", "", $alocacao['carga_horaria_alocada'], 5, 5, false, false, true, "", "", "", "", "ds_carga_horaria_" );
				$this->campoTextoInv( "ref_cod_escola_{$key}", "", $det_escola, 30, 255, false, false, false, "", "{$link_excluir}", "", "", "ref_cod_escola_" );
			}
		}
		$this->campoOculto( "incluir_periodo", "" );
		$this->campoQuebra();
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_alocacao_lst.php" );

		if ( $_POST["alocacao_array"] )
		{
			$this->alocacao_array = unserialize( urldecode( $_POST["alocacao_array"] ) );
		}

		if ( $_POST["alocacao_excluida_array"] )
		{
			$this->alocacao_excluida_array = unserialize( urldecode( $_POST["alocacao_excluida_array"] ) );
		}

		if ( $this->alocacao_excluida_array )
		{
			foreach ( $this->alocacao_excluida_array as $excluida )
			{
				$obj = new clsPmieducarServidorAlocacao( null, $this->ref_ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $excluida['ref_cod_escola'], $this->ref_cod_servidor, null, null, $this->ativo, $excluida['carga_horaria_alocada'], $excluida['periodo'] );
				$cadastrou = $obj->excluir_horario();
			}
		}

		if ( $_POST["carga_horaria_alocada"] && $_POST["periodo"] )
		{

			$aux 						  = array();
			$aux["periodo"] 			  = $_POST["periodo"];
			$aux["carga_horaria_alocada"] = $_POST["carga_horaria_alocada"];
			$aux["ref_cod_escola"] 		  = $_POST["ref_cod_escola"];
			$aux["novo"] 				  = 1;
			$achou 						  = false;

			foreach ( $this->alocacao_array as $alocacao )
			{
				if ( $alocacao['periodo'] == $aux["periodo"] )
				{
					$achou = true;
				}
			}
			if ( !$achou )
			{
				$this->alocacao_array[] = $aux;
			}
			unset( $this->periodo );
			unset( $this->carga_horaria_alocada );
		}

		if ( $this->alocacao_array )
		{
			foreach ( $this->alocacao_array as $alocacao )
			{
				if ( $alocacao['novo'] )
				{
					$obj = new clsPmieducarServidorAlocacao( null, $this->ref_ref_cod_instituicao, null, $this->pessoa_logada, $alocacao['ref_cod_escola'], $this->ref_cod_servidor, null, null, $this->ativo, $alocacao['carga_horaria_alocada'], $alocacao['periodo'] );
					$cadastrou = $obj->cadastra();

					if ( !$cadastrou )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->periodo ) && ( $this->carga_horaria_alocada )\n-->";
						return false;
					}
				}
			}
		}

		$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
		header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao} ");
		die();
		return true;
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
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

function validaHora()
{
	var carga_horaria 	= document.getElementById('carga_horaria_alocada').value;
	var periodo 		= document.getElementById('periodo').value;
	var ref_cod_escola  = document.getElementById('ref_cod_escola').value;
	var horas_restantes = document.getElementById('horas_restantes_').value;

	if ( !ref_cod_escola )
	{
		alert( 'Preencha o campo \'Escola\' corretamente!' );
		return false;
	}

	if ( !( ( /[0-9]{2}:[0-9]{2}/ ).test( document.formcadastro.carga_horaria_alocada.value ) ) )
	{
		alert( "Preencha o campo 'Carga Horária' corretamente!" );
		return false;
	}

	if ( !periodo )
	{
		alert( "Preencha o campo 'Período' corretamente!" );
		return false;
	}

	horas_restantes = unescape( horas_restantes );
	horas_restantes = unescape( horas_restantes ).split( ":" );

	var carga_horaria_alocada_ 	  = document.getElementById('carga_horaria_alocada').value.split( ":" );

	hora_ 			= Date.UTC( 1970, 01, 01, carga_horaria_alocada_[0], carga_horaria_alocada_[1], 0 );
	hora_restantes_ = Date.UTC( 1970, 01, 01, horas_restantes[0], horas_restantes[1], 0 );

	if ( hora_ > hora_restantes_ )
	{
		alert( "Atenção número de horas excedem o número de horas disponíveis ,\nPor favor corriga!!!" );
		document.getElementById( 'ref_cod_escola' ).value 		 = '';
		document.getElementById( 'periodo' ).value 				 = '';
		document.getElementById( 'carga_horaria_alocada' ).value = '';
		return false;
	}

	return true;
}
</script>