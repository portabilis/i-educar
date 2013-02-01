require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/#nome_schema#/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - #nome_pagina#" );
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

	#inicia_variaveis#

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

#get_pk_from_get#
#verificacao_especial_cad#
		#check_pk_start#
#check_pk_tabulacao#		$obj = new #nome_classe#( #pk_obj_params# );
#check_pk_tabulacao#		$registro  = $obj->detalhe();
#check_pk_tabulacao#		if( $registro )
#check_pk_tabulacao#		{
#check_pk_tabulacao#			foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
#check_pk_tabulacao#				$this->$campo = $val;
#ajusta_data_pg#
#verificacao_especial_exc_ini#
#check_pk_tabulacao#			$this->fexcluir = true;
#verificacao_especial_exc_end#
#check_pk_tabulacao#			$retorno = "Editar";
#check_pk_tabulacao#		}
		#check_pk_end#
		$this->url_cancelar = ($retorno == "Editar") ? "#nome_schema_limpo#_#nome_tabela#_det.php#get_pk_params#" : "#nome_schema_limpo#_#nome_tabela#_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
#campos_pk#
		// foreign keys
#campos_fk#
		// text
#campos_texto#
		// data
#campos_data#
		// time
#campos_time#
		// bool
#campos_bool#
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

#verificacao_especial_cad#

		$obj = new #nome_classe#( #cadastra_variaveis# );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: #nome_schema_limpo#_#nome_tabela#_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar #nome_classe#\nvalores obrigatorios\n#check_obrigatorio#\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

#verificacao_especial_cad#

		$obj = new #nome_classe#(#cadastra_variaveis#);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: #nome_schema_limpo#_#nome_tabela#_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar #nome_classe#\nvalores obrigatorios\n#debug_pk_start_edicao#-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

#verificacao_especial_exc#

		$obj = new #nome_classe#(#exclui_variaveis#);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: #nome_schema_limpo#_#nome_tabela#_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir #nome_classe#\nvalores obrigatorios\n#debug_pk_start_edicao#-->";
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