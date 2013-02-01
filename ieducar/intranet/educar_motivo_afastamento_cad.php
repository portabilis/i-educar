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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Afastamento" );
		$this->processoAp = "633";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
  public $pessoa_logada = NULL;

  public
    $cod_motivo_afastamento = NULL,
    $ref_usuario_exc        = NULL,
    $ref_usuario_cad        = NULL,
    $nm_motivo              = NULL,
    $descricao              = NULL,
    $data_cadastro          = NULL,
    $data_exclusao          = NULL,
    $ativo                  = NULL,
    $ref_cod_instituicao    = NULL;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_motivo_afastamento = $_GET["cod_motivo_afastamento"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 633, $this->pessoa_logada, 7,  "educar_motivo_afastamento_lst.php" );

		if( is_numeric( $this->cod_motivo_afastamento ) )
		{

			$obj = new clsPmieducarMotivoAfastamento( $this->cod_motivo_afastamento );
			$registro  = $obj->detalhe();

			if( $registro )
			{
			  foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola( $this->ref_cod_escola );
				$det_escola = $obj_escola->detalhe();
				$this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

				if( $obj_permissoes->permissao_excluir( 633, $this->pessoa_logada, 7 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
				$this->ref_cod_instituicao = $registro['ref_cod_instituicao'];
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro["cod_motivo_afastamento"]}" : "educar_motivo_afastamento_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_motivo_afastamento", $this->cod_motivo_afastamento );

		// foreign keys
		$obrigatorio = true;
		$get_escola = false;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_motivo", "Motivo de Afastamento", $this->nm_motivo, 30, 255, true );
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 633, $this->pessoa_logada, 7,  "educar_motivo_afastamento_lst.php" );


		$obj = new clsPmieducarMotivoAfastamento( null, null, $this->pessoa_logada, $this->nm_motivo, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_motivo_afastamento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarMotivoAfastamento\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_motivo ) && is_numeric( $this->ref_cod_instituicao )\n-->";
		return false;
	}



  public function Editar() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(633, $this->pessoa_logada, 7,
      'educar_motivo_afastamento_lst.php');

    $obj = new clsPmieducarMotivoAfastamento($this->cod_motivo_afastamento,
      $this->pessoa_logada, NULL, $this->nm_motivo, $this->descricao, NULL,
      NULL, 1, $this->ref_cod_instituicao);

    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
      header('Location: educar_motivo_afastamento_lst.php');
      die();
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }



	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 633, $this->pessoa_logada, 7,  "educar_motivo_afastamento_lst.php" );


		$obj = new clsPmieducarMotivoAfastamento($this->cod_motivo_afastamento,
		  $this->pessoa_logada, NULL, $this->nm_motivo, $this->descricao, NULL,
		  NULL, 0, $this->ref_cod_instituicao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_motivo_afastamento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarMotivoAfastamento\nvalores obrigatorios\nif( is_numeric( $this->cod_motivo_afastamento ) && is_numeric( $this->pessoa_logada ) )\n-->";
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