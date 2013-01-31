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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Cadastro de Funcion&aacute;rios" );
		$this->processoAp = "36";
	}
}

class indice extends clsCadastro
{

	var $pessoa_logada;

	var $ref_pessoa;
	var $ref_cod_setor_new;

	//dados do funcionario
	var $nome;
	var $matricula;
	var $_senha;
	var $ativo;
	var $ref_cod_funcionario_vinculo;
	var $tempo_expira_conta;
	var $ramal;
	var $super;
	var $proibido;
	var $matricula_permanente;

	//senha carregada do banco (controle de criptografia)
	var $confere_senha;

	//setor e subsetores
	var $setor_0;
	var $setor_1;
	var $setor_2;
	var $setor_3;
	var $setor_4;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_pessoa = $_POST["ref_pessoa"];
		if( $_GET["ref_pessoa"] )
		{
			$this->ref_pessoa = $_GET["ref_pessoa"];
		}


		if( is_numeric( $this->ref_pessoa ) )
		{

			$obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);
			$det_funcionario = $obj_funcionario->detalhe();
			if( $det_funcionario )
			{
				foreach ($det_funcionario as $campo => $valor) {
					$this->$campo = $valor;
				}
				$this->_senha = $this->senha;
				$this->confere_senha = $this->_senha;
				$this->fexcluir = true;
				$retorno = "Editar";
			}

			$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa, null, null, 0);
			$det_menu_funcionario = $obj_menu_funcionario->detalhe();
			if( $det_menu_funcionario )
			{
				$this->super = true;
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "funcionario_det.php?ref_pessoa={$this->ref_pessoa}" : "funcionario_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{

		$this->campoOculto("ref_pessoa", $this->ref_pessoa);

		if( is_numeric($this->ref_pessoa) )
		{
			$this->campoOculto("confere_senha", $this->confere_senha);
		}

		//--------------------------------------------------------------------
		if( $_POST )
		{
			foreach( $_POST AS $campo => $val )
			{
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;
			}
		}

		 //--------------------------------------------------------------------
		$this->ref_cod_setor_new = 0;
		if( ! $this->ref_cod_setor_new && is_numeric( $this->ref_pessoa ) )
		{
			$objFuncionario = new clsPortalFuncionario( $this->ref_pessoa );
			$detFunc = $objFuncionario->detalhe();
			$this->ref_cod_setor_new = $detFunc["ref_cod_setor_new"];
		}

		if( $this->ref_cod_setor_new )
		{
			$objSetor = new clsSetor();
			$parentes = $objSetor->getNiveis( $this->ref_cod_setor_new );
			for( $i = 0; $i < 5; $i++ )
			{
				if( isset( $parentes[$i] ) && $parentes[$i] )
				{
					$nmvar = "setor_{$i}";
					$this->$nmvar = $parentes[$i];
				}
			}
		}
		 //--------------------------------------------------------------------
		if( $_GET["ref_pessoa"] )
		{
			$obj_funcionario = new clsPessoaFj($this->ref_pessoa);
			$det_funcionario = $obj_funcionario->detalhe();

			$this->nome = $det_funcionario["nome"];

			$this->campoRotulo("nome", "Nome", $this->nome);
		}
		else
		{
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 1 );
			$parametros->setPessoa( "F" );
			$parametros->setPessoaNovo( 'S' );
			$parametros->setPessoaEditar( 'N' );
			$parametros->setPessoaTela( "frame" );
			$parametros->setPessoaCPF('N');
			$parametros->adicionaCampoTexto("nome", "nome");
			$parametros->adicionaCampoTexto("nome_busca", "nome");
			$parametros->adicionaCampoTexto("ref_pessoa", "idpes");
			$this->campoTextoPesquisa("nome_busca", "Nome", $this->nome, 30, 255, true, "pesquisa_pessoa_lst.php", false, false, "", "", $parametros->serializaCampos()."&busca=S", true );
			$this->campoOculto("nome", $this->nome);
			$this->campoOculto("ref_pessoa", $this->ref_pessoa);
		}

		$this->campoTexto("matricula", "Matr&iacute;cula", $this->matricula, 12, 12, true);
		$this->campoSenha("_senha", "Senha", $this->_senha, true);
		$this->campoEmail("email", "E-mail usuário", $this->email, 50, 50, false, false, false, 'Utilizado para redefinir a senha, caso o usúario esqueça<br />Este campo pode ser gravado em branco, neste caso será solicitado um e-mail ao usuário, após entrar no sistema.');

		$obj_setor = new clsSetor();
		$lst_setor = $obj_setor->lista(null, null, null, null, null, null, null, null, null, 1, 0);

		$opcoes = array("" => "Selecione");

		if( is_array($lst_setor) && count($lst_setor) )
		{
			foreach ($lst_setor as $setor) {
				$opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
			}
		}
		$this->campoLista("setor_0", "Setor", $opcoes, $this->setor_0, "oproDocumentoNextLvl( this.value, '1' )", NULL, NULL, NULL, NULL, FALSE);

		$lst_setor = $obj_setor->lista($this->setor_0);

		$opcoes = array("" => "Selecione");

		if( is_array($lst_setor) && count($lst_setor) )
		{
			foreach($lst_setor as $setor)
			{
				$opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
			}
		}
		else
		{
			$opcoes[""] = "---------";
		}
		$this->campoLista("setor_1", "Subsetor 1", $opcoes, $this->setor_1, "oproDocumentoNextLvl(this.value, '2')", false, "", "", $this->setor_0 == "" ? true : false, false);

		$opcoes = array("" => "Selecione");

		$lst_setor = $obj_setor->lista($this->setor_1);

		if( is_array($lst_setor) && count($lst_setor) )
		{
			foreach ($lst_setor as $setor)
			{
				$opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
			}
		}
		else
		{
			$opcoes[""] = "---------";
		}
		$this->campoLista("setor_2", "Subsetor 2", $opcoes, $this->setor_2, "oproDocumentoNextLvl(this.value, '3')", false, "", "", $this->setor_1 == "" ? true : false, false);

		$opcoes = array("" => "Selecione");

		$lst_setor = $obj_setor->lista($this->setor_2);

		if( is_array($lst_setor) && count($lst_setor) )
		{
			foreach ($lst_setor as $setor)
			{
				$opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
			}
		}
		else
		{
			$opcoes[""] = "---------";
		}
		$this->campoLista("setor_3", "Subsetor 3", $opcoes, $this->setor_3, "oproDocumentoNextLvl(this.value, '4')", false, "", "", $this->setor_2 == "" ? true : false, false);

		$opcoes = array("" => "Selecione");

		$lst_setor = $obj_setor->lista($this->setor_3);

		if( is_array($lst_setor) && count($lst_setor) )
		{
			foreach ($lst_setor as $setor)
			{
				$opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
			}
		}
		else
		{
			$opcoes[""] = "---------";
		}
		$this->campoLista("setor_4", "Subsetor 4", $opcoes, $this->setor_4, "oproDocumentoNextLvl(this.value, '5')", false, "", "", $this->setor_3 == "" ? true : false, false);

		$opcoes = array(0 => "Inativo", 1 => "Ativo");
		$this->campoLista("ativo", "Status", $opcoes, $this->ativo);

		$opcoes = array("" => "Selecione", 5 => "Comissionado", 4 => "Contratado", 3 => "Efetivo", 6 => "Estagi&aacute;rio");
		$this->campoLista("ref_cod_funcionario_vinculo", "V&iacute;nculo", $opcoes, $this->ref_cod_funcionario_vinculo);

		$opcoes = array("" => "Selecione",
						 5 => "5",
						 6 => "6",
						 7 => "7",
						 10 => "10",
						 14 => "14",
						 20 => "20",
						 21 => "21",
						 28 => "28",
						 30 => "30",
						 35 => "35",
						 60 => "60",
						 90 => "90",
						120 => "120",
						150 => "150",
						180 => "180",
						210 => "210",
						240 => "240",
						270 => "270",
						300 => "300",
						365 => "365"
						);

		$this->campoLista("tempo_expira_conta", "Dias p/ expirar a conta", $opcoes, $this->tempo_expira_conta);

		$tempoExpiraSenha = $GLOBALS['coreExt']['Config']->app->user_accounts->default_password_expiration_period;

		if (is_numeric($tempoExpiraSenha))
			$this->campoOculto("tempo_expira_senha", $tempoExpiraSenha);
		else {
			$opcoes = array('' => 'Selecione', 5 => '5', 30 => '30', 60 => '60', 90 => '90', 120 => '120', 180 => '180');
			$this->campoLista("tempo_expira_senha", "Dias p/ expirar a senha", $opcoes, $this->tempo_expira_senha);
		}

		$this->campoTexto("ramal", "Ramal", $this->ramal, 11, 30);
		$this->campoCheck("super", "Super usu&aacute;rio", $this->super);
		$this->campoCheck("proibido", "Banido", $this->proibido);
		$this->campoCheck("matricula_permanente", "Matr&iacute;cula permanente", $this->matricula_permanente);

		//-----------------------------------------------------------------------------------------------

		$this->campoRotulo("rotulo_permissoes", "<b><i>Permiss&otilde;es</i></b>", "");

		$obj_menu = new clsPortalMenuMenu();
		$obj_menu->setOrderby("nm_menu ASC");
		$lst_menu = $obj_menu->lista();

		//busca todos os submenus liberado para o funcionario
		if(is_numeric($this->ref_pessoa))
		{
			$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa);
			$lst_menu_funcionario = $obj_menu_funcionario->lista(null, null, $this->ref_pessoa);
			if(is_array($lst_menu_funcionario) && count($lst_menu_funcionario))
			{
				foreach ($lst_menu_funcionario as $id_submenu)
				{
					$array_submenu[] = $id_submenu["ref_cod_menu_submenu"];
				}
			}
		}
		if( is_array($lst_menu) && count($lst_menu) )
		{
			foreach ($lst_menu as $key => $menu)
			{
				$array_valores = array();
				if($menu["cod_menu_menu"] != 1)
				{
/*					if( $menu['nm_menu'] == "i-Frotas")
					{
						echo $menu["cod_menu_menu"];
					}*/

					$obj_submenu = new clsPortalMenuSubmenu();
					$obj_submenu->setOrderby("nm_submenu ASC");
					$lst_submenu = $obj_submenu->lista($menu["cod_menu_menu"], 2);
					$opcoes = array("" => "Selecione");

					if( is_array($lst_submenu) && count($lst_submenu) )
					{
						foreach ($lst_submenu as $submenu)
						{
							$opcoes[$submenu["cod_menu_submenu"]] = $submenu["nm_submenu"];
						}
					}

					if( is_numeric($this->ref_pessoa) )
					{
						if(is_array($array_submenu) && count($array_submenu))
						{
							//faz a interseccao dos submenus do funcionario e os submenus do menu atual (do foreach)
							$array_menu_submenu = array_intersect(array_flip($opcoes), $array_submenu);
						}
						$contador = 0;
						if( is_array($array_menu_submenu) && count($array_menu_submenu) )
						{
							//monta a matriz que conterao os valores da tabela (do BD)
							foreach ($array_menu_submenu as $id_submenu)
							{
								$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa, null, null, $id_submenu);
								$det_menu_funcionario = $obj_menu_funcionario->detalhe();
								$array_valores[$contador][] = $det_menu_funcionario["ref_cod_menu_submenu"];
								$array_valores[$contador][] = $det_menu_funcionario["cadastra"];
								$array_valores[$contador++][] = $det_menu_funcionario["exclui"];
							}
						}
					}
					$this->campoTabelaInicio(str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"]))), $menu["nm_menu"], array("Submenu", "Cadastrar", "Excluir"), $array_valores, "500");
						$this->campoLista(str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"])))."_", "", $opcoes, "", "", false, "", "", false, false);
						$this->campoCheck("cad_".str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"]))), "", "");
						$this->campoCheck("exc_".str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"]))), "", "");
					$this->campoTabelaFim();
				}
			}
		}

	}

	function cadastrarTabelas()
	{
		$obj_menu = new clsPortalMenuMenu();
		$obj_menu->setOrderby("nm_menu ASC");
		$lst_menu = $obj_menu->lista();

		if(!empty($this->super))
		{
			$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa, 0, 0, 0);
			$obj_menu_funcionario->cadastra();
		}
		if( is_array($lst_menu) && count($lst_menu) )
		{
			foreach ($lst_menu as $key => $menu)
			{
				if(is_array($_POST[str_replace(" ", "_",limpa_acentos(strtolower($menu["nm_menu"]))."_")]) && count($_POST[str_replace(" ", "_",limpa_acentos(strtolower($menu["nm_menu"]))."_")]))
				{
					$array_cad = $_POST["cad_".str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"])))];
					$array_exc = $_POST["exc_".str_replace(" ", "_", limpa_acentos(strtolower($menu["nm_menu"])))];
					foreach ($_POST[str_replace(" ", "_",limpa_acentos(strtolower($menu["nm_menu"])."_"))] as $ind => $id_submenu)
					{
						if($id_submenu)
						{
							$cadastrar = empty($array_cad[$ind]) ? 0 : 1;
							$excluir   = empty($array_exc[$ind]) ? 0 : 1;
							$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa, $cadastrar, $excluir, $id_submenu);
							if(!$obj_menu_funcionario->cadastra())
							{
								$this->mensagem = "Cadastro de menu n&atilde;o realizado.<br>";
								echo "<!--\nErro ao cadastrar clsPortalMenuFuncionario-->";
								return false;
							}
						}
					}
				}
			}
		}
		return true;
	}


	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//setor recebe o id do ultimo subsetor selecionado
		$this->ref_cod_setor_new = 0;
		for( $i = 0; $i < 5; $i++ )
		{
			$nmvar = "setor_{$i}";
			if( is_numeric( $this->$nmvar ) && $this->$nmvar )
			{
				$this->ref_cod_setor_new = $this->$nmvar;
			}
		}

    if (! $this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula))
      return false;

    if (! $this->validatesPassword($this->matricula, $this->_senha))
      return false;

		$obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, md5($this->_senha), $this->ativo, null, $this->ramal, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->tempo_expira_conta, "NOW()", "NOW()", $this->pessoa_logada, empty($this->proibido) ? 0 : 1, $this->ref_cod_setor_new, null, empty($this->matricula_permanente)? 0 : 1, 1, $this->email);
		if( $obj_funcionario->cadastra() )
		{
			if($this->cadastrarTabelas())
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: funcionario_lst.php" );
				return true;
			}
			$this->mensagem = "Cadastro de menus n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPortalMenuFuncionario-->";
			return false;
		}
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar -->";
		return false;
	}


	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_setor_new = 0;
		for( $i = 0; $i < 5; $i++ )
		{
			$nmvar = "setor_{$i}";
			if( is_numeric( $this->$nmvar ) && $this->$nmvar )
			{
				$this->ref_cod_setor_new = $this->$nmvar;
			}
		}

    if (! $this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula))
      return false;

    if (! $this->validatesPassword($this->matricula, $this->_senha))
      return false;

		//verifica se a senha ja esta criptografada
		if($this->_senha != $this->confere_senha)
		{
			$this->_senha = md5($this->_senha);
		}

		$obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, $this->_senha, $this->ativo, null, $this->ramal, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->tempo_expira_conta, "NOW()", "NOW()", $this->pessoa_logada, empty($this->proibido) ? 0 : 1, $this->ref_cod_setor_new, null, empty($this->matricula_permanente) ? 0 : 1, 1, $this->email);
		if( $obj_funcionario->edita() )
		{
			$obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa);
			$obj_menu_funcionario->excluir();
			if( $this->cadastrarTabelas() )
			{
				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: funcionario_lst.php" );
			}
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPortalFuncionario-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);
		if($obj_funcionario->excluir())
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: funcionario_lst.php" );
			return true;
		}
		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPortalFuncionario\n-->";
		return false;
	}


  function validatesUniquenessOfMatricula($pessoaId, $matricula) {
    $sql = "select 1 from portal.funcionario where lower(matricula) = lower('$matricula') and ref_cod_pessoa_fj != $pessoaId";
    $db = new clsBanco();

		if ($db->CampoUnico($sql) == '1') {
      $this->mensagem = "A matrícula '$matricula' já foi usada, por favor, informe outra.";
      return false;
    }
    return true;
  }

  function validatesPassword($matricula, $password) {
    $msg = '';

		if ($password == $matricula)
      $msg = 'Informe uma senha diferente da matricula.';
    elseif (strlen($password) < 8)
      $msg = 'Por favor informe uma senha segura, com pelo menos 8 caracteres.';

    if ($msg) {
      $this->mensagem = $msg;
      return false;
    }
    return true;
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
