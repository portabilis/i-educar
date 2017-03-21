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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/clsEmail.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Editais" );
		$this->processoAp = "239";
	}
}

class indice extends clsCadastro
{
	var $cod_edital;
	var $ref_licitacao;
	var $versao;
	var $data_hora;
	var $arquivo;
	var $ref_pessoa;
	var $id_pessoa;
	var $motivo;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";

		if (@$_GET['cod_edital'])
		{
			$db = new clsBanco();

			$retorno = "Editar";
			$this->cod_edital = @$_GET['cod_edital'];

			$db->Consulta( "SELECT cod_compras_editais_editais, ref_cod_compras_licitacoes, versao, data_hora, arquivo, ref_ref_cod_pessoa_fj FROM compras_editais_editais WHERE cod_compras_editais_editais='{$this->cod_edital}'" );
			if ( $db->ProximoRegistro() )
			{
				list( $this->cod_edital, $this->ref_licitacao, $this->versao, $this->data_hora, $this->arquivo, $this->ref_pessoa ) = $db->Tupla();
				//$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ( $retorno == "Editar" ) ? "licitacoes_edital_det.php?cod_edital=$this->cod_edital" : "licitacoes_edital_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_edital", $this->cod_edital );
		$this->campoOculto( "ref_pessoa", $this->ref_pessoa );

		$db = new clsBanco();
		$nomePessoa = $db->CampoUnico( "SELECT nome FROM cadastro.pessoa WHERE idpes = '{$this->id_pessoa}'" );
		$this->campoRotulo( "pessoa", "Responsável", $nomePessoa );

		if( isset( $this->ref_pessoa ) )
		{
			if( isset( $_GET["ocultar"] ) )
			{
				$visivel = 1;
				if( $_GET["ocultar"] )
				{
					$visivel = 0;
				}
				if( is_numeric( $_GET["cod_edital"] ) )
				{
					$db->Consulta( "UPDATE compras_editais_editais SET visivel = $visivel WHERE cod_compras_editais_editais = '{$this->cod_edital}'" );
				}
			}

			if(is_numeric($this->ref_pessoa))
				$nomePessoa = $db->CampoUnico( "SELECT nome FROM cadastro.pessoa WHERE idpes = '{$this->ref_pessoa}'" );
			$this->campoRotulo( "ultimo_editor", "Ultimo Editor", $nomePessoa );
			$this->campoRotulo( "data", "Data da Ultima edi&ccedil;&atilde;o", date( "d/m/Y H:i", strtotime( substr($this->data_hora,0,19) ) ) );

			$versao = $db->CampoUnico( "SELECT count(0) FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = $this->ref_licitacao" );
			$this->campoRotulo( "versao", "Vers&atilde;o",  ++$versao  );

			$visivel = $db->CampoUnico( "SELECT visivel FROM compras_editais_editais WHERE cod_compras_editais_editais = $this->cod_edital" );
			if( $visivel )
			{
				$this->campoRotulo( "visivel", "Ocultar", "<b>Edital visivel</b> - <a href=\"licitacoes_edital_cad.php?cod_edital={$_GET["cod_edital"]}&ocultar=1\">Clique aqui para ocultar este Edital.</a>" );
			}
			else
			{
				$this->campoRotulo( "visivel", "N&atilde;o Ocultar", "<b>Edital oculto</b> - <a href=\"licitacoes_edital_cad.php?cod_edital={$_GET["cod_edital"]}&ocultar=0\">Clique aqui para exibir este Edital na lista de downloads.</a>" );
			}

			$this->campoMemo( "motivo", "Motivo da altera&ccedil;&atilde;o", $this->motivo, 50, 8, true );

			$this->campoOculto( "ref_licitacao", $this->ref_licitacao );
		}
		else
		{
			$this->campoRotulo( "versao", "Vers&atilde;o", 1 );
			$lista = array();
			$db = new clsBanco();
			$timestamp = time() - 60 * 60 * 24 * 3;
			$sqldata[0] = date( "Y", $timestamp );
			$sqldata[1] = date( "m", $timestamp );
			$sqldata[2] = date( "d", $timestamp );
			$db->Consulta( "SELECT cod_compras_licitacoes, numero, objeto, data_hora, nm_modalidade FROM compras_licitacoes, compras_modalidade WHERE cod_compras_modalidade = ref_cod_compras_modalidade AND data_hora >= '{$sqldata[0]}/{$sqldata[1]}/{$sqldata[2]} 00:00' AND cod_licitacao_semasa is null AND cod_compras_licitacoes NOT IN ( SELECT ref_cod_compras_licitacoes FROM compras_editais_editais ) ORDER BY data_hora DESC" );
			while ($db->ProximoRegistro())
			{
				list( $cod, $numero, $obj, $data, $modalidade ) = $db->Tupla();
				$arr_modalidade = explode( " ", $modalidade );
				$modalidade = "";
				foreach ( $arr_modalidade AS $key => $valor )
				{
					$modalidade .= substr( $valor, 0, 1 );
				}
				$fullStr = date( "d/m/Y", strtotime( substr($data,0,19) ) ) . " - $modalidade - $numero - $obj";
				if( strlen( $fullStr ) > 70 ) $fullStr = substr( $fullStr, 0, 67 ) . "...";
				$lista[$cod] = $fullStr;
			}
			$this->campoLista( "ref_licitacao", "Licitacao", $lista, $this->ref_licitacao );
		}
		$this->campoArquivo("arquivo", "Arquivo", $this->arquivo, "50" );
	}

	function Novo()
	{
		global $HTTP_POST_FILES;

		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$db = new clsBanco();

		if ( ! empty( $HTTP_POST_FILES['arquivo']['name'] ) )
		{
			$rand = rand( 0, 100 );
			$data = date( "d_m_Y_H_i_s", time() );
			$extensao = substr( strtolower( $HTTP_POST_FILES['arquivo']['name'] ), -3 );
			$arquivoSalvar = "arquivos/editais/{$data}_{$rand}.{$extensao}";

			//echo $arquivoSalvar;

			$this->arquivo = $HTTP_POST_FILES['arquivo']['tmp_name'];
			if($HTTP_POST_FILES['arquivo']['error'] == 1)
				die('Tamanho excedido!');
			if( file_exists( $this->arquivo ) )
			{
				// salva o arquivo temporario
				move_uploaded_file( $HTTP_POST_FILES['arquivo']['tmp_name'], $arquivoSalvar );

				$versao = 1;

				$db->Consulta( "INSERT INTO compras_editais_editais( ref_cod_compras_licitacoes, versao, data_hora, arquivo, ref_ref_cod_pessoa_fj, motivo_alteracao ) VALUES( '{$this->ref_licitacao}', '{$versao}', NOW(), '{$arquivoSalvar}', '{$this->id_pessoa}', '{$this->motivo}' )" );

				//die();
				header( "location: licitacoes_edital_lst.php" );
				die();
			}
		}
		return false;
	}

	function Editar()
	{
		global $HTTP_POST_FILES;

		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$db = new clsBanco();
		$db2 = new clsBanco();

		if ( ! empty( $HTTP_POST_FILES['arquivo']['name'] ) )
		{
			$data = date( "d_m_Y_H_i", time() );
			$arquivoSalvar = "arquivos/editais/". $data . strtolower( $HTTP_POST_FILES['arquivo']['name'] );
			$extensao = substr( $arquivoSalvar, -3 );

			$this->arquivo = $HTTP_POST_FILES['arquivo']['tmp_name'];
			if( file_exists( $this->arquivo ) )
			{
				// salva o arquivo temporario
				move_uploaded_file( $HTTP_POST_FILES['arquivo']['tmp_name'], $arquivoSalvar );

				// pega o total de editais ja cadastrados para esta licitacao
				$versao = $db->CampoUnico( "SELECT count(0) FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = '{$this->ref_licitacao}'" );

				// verifica se a licitacao esta visivel
				$visivel = $db->CampoUnico( "SELECT visivel FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = '{$this->ref_licitacao}' AND versao = $versao" );
				// versao = total + 1; para gravar no novo registro
				$versao++;

				// insere o novo edital
				$db->Consulta( "INSERT INTO compras_editais_editais( ref_cod_compras_licitacoes, versao, data_hora, arquivo, ref_ref_cod_pessoa_fj, motivo_alteracao, visivel ) VALUES( '{$this->ref_licitacao}', '{$versao}', NOW(), '{$arquivoSalvar}', '{$this->id_pessoa}', '{$this->motivo}', $visivel )" );
				$insertId = $db->InsertId("compras_editais_editais_cod_compras_editais_editais_seq");

				// seleciona todas as empresas que baixaram o edital
				$db->Consulta( "SELECT ref_cod_compras_editais_empresa FROM compras_editais_editais_empresas WHERE ref_cod_compras_editais_editais IN ( SELECT cod_compras_editais_editais FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = '{$this->ref_licitacao}' )" );
				$lista = array();
				while ( $db->ProximoRegistro() )
				{
					list( $cod_empresa ) = $db->Tupla();
					$email = $db2->CampoUnico( "SELECT email FROM compras_editais_empresa WHERE cod_compras_editais_empresa = '{$cod_empresa}'" );
					$lista[$cod_empresa] = $email;
				}
				// se existirem empresas que ja baixaram e o edital estiver visivel
				if( count( $email ) && $visivel )
				{
					$db->Consulta( "SELECT numero, nm_modalidade FROM compras_licitacoes, compras_modalidade WHERE cod_compras_licitacoes = '{$this->ref_licitacao}' AND cod_compras_modalidade = ref_cod_compras_modalidade" );
					$db->ProximoRegistro();
					list( $licitacao, $nm_modalidade ) = $db->Tupla();

					// gera conteudo
					$conteudo = "Informamos por meio desta, que o edital da licitação da modalidade {$nm_modalidade} {$licitacao} foi alterado e possui uma nova versão.<br>\n<br>\n";
					$conteudo .= "Para obter a nova versão <a href=\"http://www.itajai.sc.gov.br/licitacoes_editais_det.php?cod_edital={$insertId}\" target=\"_blank\">clique aqui</a> ou acesse: http://www.itajai.sc.gov.br/licitacoes.php?cod_edital={$insertId}<br>\n<br>\n";
					$conteudo .= "O motivo da alteração é:<br>\n{$this->motivo}";

					// envia o e-mail para as empresas separadamente
					foreach ( $lista AS $cod_empresa => $email )
					{
						$objEmail = new clsEmail( $email, "[PMI] Alteração de Edital", $conteudo );
						$objEmail->envia();
					}
				}
				header( "location: licitacoes_edital_relatorio.php?edital={$insertId}" );
				die();
			}
		}
		return false;
	}

	function Excluir()
	{
		return false;
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
