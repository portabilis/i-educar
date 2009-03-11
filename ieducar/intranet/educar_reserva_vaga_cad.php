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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Reserva Vaga" );
		$this->processoAp = "639";
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

	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_cod_aluno;
	var $nm_aluno;
	var $nm_aluno_;

	var $ref_cod_instituicao;
	var $ref_cod_curso;

	var $passo;

	var $nm_aluno_ext;
	var $cpf_responsavel;
	var $tipo_aluno;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_serie=$_GET["ref_cod_serie"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 639, $this->pessoa_logada, 7, "educar_reserva_vaga_lst.php" );

//		$this->url_cancelar = "educar_reserva_vaga_lst.php";
//		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if ($this->ref_cod_aluno)
		{
			$obj_reserva_vaga = new clsPmieducarReservaVaga();
			$lst_reserva_vaga = $obj_reserva_vaga->lista( null,null,null,null,null,$this->ref_cod_aluno,null,null,null,null,1 );
			// verifica se o aluno já possui reserva alguma reserva ativa no sistema
			if ( is_array($lst_reserva_vaga) )
			{
				echo "<script> alert('Aluno já possui reserva de vaga!\\nNão é possivel realizar a reserva.'); window.location = 'educar_reserva_vaga_lst.php';</script>";
				die();
			}
			echo "<script> alert('A reserva do aluno permanecerá ativa por apenas 2 dias!');</script>";
		}
		$this->campoOculto("ref_cod_serie", $this->ref_cod_serie);
		$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);

//		$this->nm_aluno = $_POST["nm_aluno"];
		$this->nm_aluno = $this->nm_aluno_;

		$this->campoTexto("nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/><span style='padding-left:20px;'><input type='button' value='Aluno externo' onclick='showAlunoExt(true);' class='botaolistagem'></span>","","",true);

		$this->campoOculto("nm_aluno_", $this->nm_aluno_);
		$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

		$this->campoOculto("tipo_aluno", "i");

		$this->campoTexto("nm_aluno_ext","Nome aluno", $this->nm_aluno_ext,50,255,false);
		$this->campoCpf("cpf_responsavel","CPF respons&aacute;vel", $this->cpf_responsavel,false,"<span style='padding-left:20px;'><input type='button' value='Aluno interno' onclick='showAlunoExt(false);' class='botaolistagem'></span>");

		$this->campoOculto("passo",1);

		$this->acao_enviar = 'acao2()';

		$this->url_cancelar = "educar_reserva_vaga_lst.php";
		$this->nome_url_cancelar = "Cancelar";
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if ($this->passo == 2)
			return true;


		$obj_reserva_vaga = new clsPmieducarReservaVaga( null,$this->ref_cod_escola,$this->ref_cod_serie,null,$this->pessoa_logada,$this->ref_cod_aluno,null,null,1, $this->nm_aluno_ext, idFederal2int($this->cpf_responsavel) );
		$cadastrou = $obj_reserva_vaga->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Reserva de Vaga efetuada com sucesso.<br>";
			header( "Location: educar_reservada_vaga_det.php?cod_reserva_vaga={$cadastrou}" );
			die();
			return true;
		}

		$this->mensagem = "Reserva de Vaga n&atilde;o realizada.<br>";
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

function pesquisa_aluno()
{
	pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

function showAlunoExt(acao)
{

	setVisibility('tr_nm_aluno_ext',acao);
	setVisibility('tr_cpf_responsavel',acao);
	setVisibility('tr_nm_aluno',!acao);

	document.getElementById('nm_aluno_ext').disabled = !acao;
	document.getElementById('cpf_responsavel').disabled = !acao;

	document.getElementById('tipo_aluno').value = (acao == true ? 'e' : 'i');

}


setVisibility('tr_nm_aluno_ext',false);
setVisibility('tr_cpf_responsavel',false);

function acao2()
{
	if(document.getElementById('tipo_aluno').value == 'e')
	{
		if(document.getElementById('nm_aluno_ext').value == '')
		{
			alert('Preencha o campo \'Nome aluno\' Corretamente');
			return false;
		}

		if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.formcadastro.cpf_responsavel.value) ))
		{
			alert('Preencha o campo \'CPF responsável\' Corretamente');
			return false;
		}
		else
		{
			if(! DvCpfOk( document.formcadastro.cpf_responsavel) )
				return false;
		}

		document.getElementById('nm_aluno_').value = '';
		document.getElementById('ref_cod_aluno').value = '';

		document.formcadastro.submit();
	}
	else
	{
		document.getElementById('nm_aluno_ext').value = '';
		document.getElementById('cpf_responsavel').value =  '';
	}
	acao();

}
</script>