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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Transfer&ecirc;ncia Solicita&ccedil;&atilde;o" );
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

	var $cod_transferencia_solicitacao;
	var $ref_cod_transferencia_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_matricula_entrada;
	var $ref_cod_matricula_saida;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_transferencia;

	var $ref_cod_matricula;
	var $transferencia_tipo;
	var $ref_cod_aluno;
	var $nm_aluno;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_matricula=$_GET["ref_cod_matricula"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];
		$cancela=$_GET["cancela"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );

		if ( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_aluno ) && ($cancela == true) )
		{
			if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
			{

        if ($_GET['reabrir_matricula']) {
          $this->reabrirMatricula($this->ref_cod_matricula);
        }

				$this->Excluir();
			}
		}

		$this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

  
  function reabrirMatricula($matriculaId) {
    $matricula = new clsPmieducarMatricula($matriculaId, NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL, 3);
    $matricula->edita();   

    $sql = "select ref_cod_turma, sequencial from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and sequencial = (select max(sequencial) from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId) and not exists(select 1 from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and ativo = 1 limit 1) limit 1";

    $db = new clsBanco();
    $ultimaEnturmacao = $db->Consulta($sql);
    $db->ProximoRegistro();
    $ultimaEnturmacao = $db->Tupla();

    if($ultimaEnturmacao) {
      $enturmacao = new clsPmieducarMatriculaTurma($matriculaId, $ultimaEnturmacao['ref_cod_turma'], $this->pessoa_logada, NULL, NULL, NULL, 1, null, $ultimaEnturmacao['sequencial']);
      $enturmacao->edita();
    }
  }


	function Gerar()
	{
		// primary keys
		$this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
		$this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );

		$obj_aluno = new clsPmieducarAluno();
		$lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_aluno) )
		{
			$det_aluno = array_shift($lst_aluno);
			$this->nm_aluno = $det_aluno["nome_aluno"];
			$this->campoTexto( "nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false,false,false,"","","","",true );
		}
		$obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
		$det_matricula = $obj_matricula->detalhe();
		$ref_cod_escola = $det_matricula['ref_ref_cod_escola'];

		$opcoes = array( 1 => "Escola do Sistema",2 => "Escola Externa" );
		$this->campoRadio( "transferencia_tipo", "Transfer&ecirc;ncia Tipo", $opcoes, $this->transferencia_tipo );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTransferenciaTipo" ) )
		{
			$objTemp = new clsPmieducarTransferenciaTipo();
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,$ref_cod_escola);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_transferencia_tipo']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTransferenciaTipo nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_transferencia_tipo", "Transfer&ecirc;ncia Motivo", $opcoes, $this->ref_cod_transferencia_tipo );

		// text
		$this->campoMemo( "observacao", "Observa&ccedil;&atilde;o", $this->observacao, 60, 5, false );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );

//		$obj_matricula = new clsPmieducarMatricula();
//		$lst_matricula = $obj_matricula->lista( null,null,null,null,null,null,$this->ref_cod_aluno,null,null,null,null,null,1 );
//		if ( is_array($lst_matricula) )
//		{
//			$det_matricula = array_shift($lst_matricula);
//			$this->ref_cod_matricula_saida = $det_matricula["cod_matricula"];

  
    // escola externa
		if ($this->transferencia_tipo == 2)
		{
			$this->data_transferencia = date("Y-m-d");
			$this->ativo = 1;

			$obj_matricula = new clsPmieducarMatricula( $this->ref_cod_matricula );
			$det_matricula = $obj_matricula->detalhe();
			$aprovado = $det_matricula["aprovado"];

			if ($aprovado == 3)
			{
				$obj = new clsPmieducarMatricula( $this->ref_cod_matricula, null,null,null,$this->pessoa_logada,null,null,4,null,null,1 );
				$editou = $obj->edita();
				if( !$editou )
				{
					$this->mensagem = "N&atilde;o foi poss&iacute;vel editar a Matr&iacute;cula do Aluno.<br>";
					return false;
				}
			
				$enturmacoes = new clsPmieducarMatriculaTurma();
				$enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, 1 );

				if($enturmacoes) 
				{
          // foreach necessário pois metodo edita e exclui da classe clsPmieducarMatriculaTurma, necessitam do
          // código da turma e do sequencial
					foreach ($enturmacoes as $enturmacao) {
					  $enturmacao = new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $enturmacao['ref_cod_turma'], $this->pessoa_logada, null, null, null, 0, null, $enturmacao['sequencial']);

					  if(! $enturmacao->edita())
					  {
    				  $this->mensagem = "N&atilde;o foi poss&iacute;vel desativar as enturma&ccedil;&otilde;es da matr&iacute;cula.";
						  return false;
					  }
          }
				}
			}
		}

		$obj = new clsPmieducarTransferenciaSolicitacao( null, $this->ref_cod_transferencia_tipo, null, $this->pessoa_logada, null, $this->ref_cod_matricula, $this->observacao, null, null, $this->ativo, $this->data_transferencia );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}
//		}
//		else
//		{
//			$this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar a Matr&iacute;cula do Aluno.<br>";
//			return false;
//		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarTransferenciaSolicitacao\nvalores obrigatorios\nis_numeric( $this->ref_cod_transferencia_tipo ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_aluno )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );

		$obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
		$lst_transferencia = $obj_transferencia->lista( null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,1,null,null,$this->ref_cod_aluno,false );
		if ( is_array($lst_transferencia) )
		{
			$det_transferencia = array_shift($lst_transferencia);
			$this->cod_transferencia_solicitacao = $det_transferencia["cod_transferencia_solicitacao"];
			$obj = new clsPmieducarTransferenciaSolicitacao($this->cod_transferencia_solicitacao, null, $this->pessoa_logada, null,null,null,null,null,null,0 );
			$excluiu = $obj->excluir();
			if( $excluiu )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );
				die();
				return true;
			}
		}
		else
		{
			$this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar a Solicita&ccedil;&atilde;o de Transfer&ecirc;ncia do Aluno.<br>";
			return false;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTransferenciaSolicitacao\nvalores obrigatorios\nif( is_numeric( $this->cod_transferencia_solicitacao ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
