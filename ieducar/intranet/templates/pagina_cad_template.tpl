/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em #data_criacao#
*/
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/#schema_include_geral#.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - #upper_nome_schema# - Cadastro de #nome_tabela#" );
		$this->processoAp = "0";
	}
}

class miolo extends clsCadastro
{
	var $ref_cod_pessoa_fj;
	#inicia_variaveis#

	function Inicializar()
	{
		@session_start();
		$this->ref_cod_pessoa_fj = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$retorno = "Novo";
		$this->nome_url_cancelar = "Cancelar";
		$this->url_cancelar = "#arquivo_root#_lst.php";
		if( #primary_key_check_from_get# )
		{
			
			
			
			$retorno = "Editar";
			$this->url_cancelar = "#arquivo_root#_det.php?#primary_key_for_get#";
		}

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "ref_cod_pessoa_fj", $this->ref_cod_pessoa_fj );
		#campo_oculto_primary_key#
		#campos_preenchimento#
	}

	function Novo() 
	{
		#cadastra_item#
		if( $ok )
		{
			header("Location: #arquivo_root#_lst.php");
			die();
			return true;
		}
		return false;
	}

	function Editar() 
	{
		#edita_item#
		if( $ok )
		{
			header("Location: #arquivo_root#_lst.php");
			die();
			return true;
		}
		return false;
	}

	function Excluir()
	{
		#exclui_item#
		if( $ok )
		{
			header("Location: #arquivo_root#_lst.php");
			die();
			return true;
		}
		return false;
	}
}

// cria uma instancia da classe Base
$pagina = new clsIndex();
// cria um objeto para o conteudo
$miolo = new miolo();
// adiciona o conteudo na Base
$pagina->addForm( $miolo );
// gera a pagina
$pagina->MakeAll();