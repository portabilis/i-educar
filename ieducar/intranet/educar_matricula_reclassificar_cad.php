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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Reclassificar Matr&iacute;cula" );
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

	var $cod_matricula;
	var $ref_cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ano;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $ref_cod_escola;

	var $ref_ref_cod_serie_antiga;

	var $descricao_reclassificacao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_matricula=$_GET["ref_cod_matricula"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		$obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
		$det_matricula = $obj_matricula->detalhe();

		if(!$det_matricula || $det_matricula['aprovado'] != 3)
			header("location: educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

		foreach ($det_matricula as $key => $value)
			$this->$key = $value;


		//$this->url_cancelar = "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";
		$this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		// primary keys
		$this->campoOculto( "cod_matricula", $this->cod_matricula );
		$this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
		$this->campoOculto( "ref_cod_escola", $this->ref_ref_cod_escola );

		$obj_aluno = new clsPmieducarAluno();
		$lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_aluno) )
		{
			$det_aluno = array_shift($lst_aluno);
			$this->nm_aluno = $det_aluno["nome_aluno"];
			$this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno);
		}


		$array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();

		$db = new clsBanco();


		$cursos = array();
		$sql_curso_aluno = "SELECT ref_cod_curso FROM pmieducar.serie WHERE cod_serie = {$this->ref_ref_cod_serie}";
		$this->ref_cod_curso = $db->CampoUnico($sql_curso_aluno);

		foreach ($array_inicio_sequencias as $serie_inicio)
		{
			$serie_inicio = $serie_inicio[0];

			$seq_ini = $serie_inicio;
			$seq_correta = false;
			do
			{
				$sql = "SELECT o.ref_serie_origem
				               ,s.nm_serie
						       ,o.ref_serie_destino
						       ,s.ref_cod_curso as ref_cod_curso_origem
						       ,sd.ref_cod_curso as ref_cod_curso_destino
						  FROM pmieducar.sequencia_serie o
						       ,pmieducar.serie s
						       ,pmieducar.serie sd
						 WHERE s.cod_serie = o.ref_serie_origem
						   AND s.cod_serie = $seq_ini
				           AND sd.cod_serie = o.ref_serie_destino
						";
//						   AND s.ref_cod_curso = $curso
				$db->Consulta($sql);
				$db->ProximoRegistro();
				$tupla = $db->Tupla();
				$serie_origem = $tupla['ref_serie_origem'];
				//$nm_serie_origem = $tupla['nm_serie'];
				$curso_origem = $tupla['ref_cod_curso_origem'];
				$curso_destino = $tupla['ref_cod_curso_destino'];
				$seq_ini = $serie_destino = $tupla['ref_serie_destino'];

				$obj_curso = new clsPmieducarCurso($curso_origem);
				$det_curso = $obj_curso->detalhe();
				$cursos[$curso_origem] = $det_curso['nm_curso'];

				$obj_curso = new clsPmieducarCurso($curso_destino);
				$det_curso = $obj_curso->detalhe();
				$cursos[$curso_destino] = $det_curso['nm_curso'];

				if($this->ref_ref_cod_serie == $serie_origem)
					$seq_correta = true;


				//$todas_sequencias .= "sequencia_serie[sequencia_serie.length] = new Array({$curso_origem},$serie_origem,'$nm_serie_origem');\n";

				$sql = "SELECT 1
						  FROM pmieducar.sequencia_serie s
						 WHERE s.ref_serie_origem = $seq_ini
					    ";
				$true = $db->CampoUnico($sql);

			}while($true);

			$obj_serie = new clsPmieducarSerie($serie_destino);
			$det_serie = $obj_serie->detalhe();

			//$todas_sequencias .= "sequencia_serie[sequencia_serie.length] = new Array({$curso_destino},$serie_destino,'{$det_serie['nm_serie']}');\n";

			if($this->ref_ref_cod_serie == $serie_destino)
				$seq_correta = true;

			if($seq_correta == false)
			{
				///$todas_sequencias = "var sequencia_serie = new Array();\n";
				$cursos = array('' => 'Não existem cursos/séries para reclassificação');
			}else
			{
				break;
			}
		}



		$this->campoOculto("serie_matricula",$this->ref_ref_cod_serie);

		//echo "<script>\n{$todas_sequencias}var serie_matricula = {$this->ref_ref_cod_serie};\n</script>";


		// foreign keys
		//$obrigatorio = true;
		//$get_escola = false;
	//	$get_instituicao = false;
	//	$get_escola_curso = true;
		//$get_escola_curso_serie = true;
		//$get_matricula = true;
		//include("include/pmieducar/educar_campo_lista.php");



		$this->campoLista("ref_cod_curso","Curso",$cursos,$this->ref_cod_curso,"getSerie();");
		$this->campoLista("ref_ref_cod_serie","S&eacute;rie",array('' => 'Selecione uma série'),'');
		//$this->campoOculto("ref_ref_cod_serie_antiga",$this->ref_ref_cod_serie);
		$this->campoMemo("descricao_reclassificacao","Descri&ccedil;&atilde;o",$this->descricao_reclassificacao,100,10,true);

		$this->acao_enviar = 'if(confirm("Deseja reclassificar está matrícula?"))acao();';


	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

		if($this->ref_ref_cod_serie == $this->ref_ref_cod_serie_antiga)
			header("location: educar_matricula_det.php?cod_matricula={$this->cod_matricula}");

		$obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
		$det_matricula = $obj_matricula->detalhe();

		if(!$det_matricula || $det_matricula['aprovado'] != 3)
			header("location: educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

		$obj_matricula = new clsPmieducarMatricula($this->cod_matricula,null,null,null,$this->pessoa_logada,null,null,5,null,null,1,null,0,null,null,$this->descricao_reclassificacao);
		if(!$obj_matricula->edita())
		{
			echo "<script>alert('Erro ao reclassificar matrícula'); window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';</script>";
			die("Erro ao reclassificar matrícula");
		}
		$obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
		$det_serie = $obj_serie->detalhe();

		$obj_matricula = new clsPmieducarMatricula(null,null,$this->ref_cod_escola,$this->ref_ref_cod_serie,null,$this->pessoa_logada,$this->ref_cod_aluno,3,null,null,1,$det_matricula['ano'],1,null,null,null,1,$det_serie["ref_cod_curso"] );
		$cadastrou = $obj_matricula->cadastra();

		if(!$cadastrou){
			echo "<script>alert('Erro ao reclassificar matrícula'); window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';</script>";
			die("Erro ao reclassificar matrícula");
		}else{
			/**
			 * desativa todas as enturmacoes da matricula anterior
			 */
			$obj_matricula_turma = new clsPmieducarMatriculaTurma($this->cod_matricula);
			if(!$obj_matricula_turma->reclassificacao())
			{
				echo "<script>alert('Erro ao desativar enturmações da matrícula: {$this->cod_matricula}\nContate o administrador do sistema informando a matrícula!');</script>";
			}
			//window.location='educar_matricula_det.php?cod_matricula={$this->cod_matricula}&ref_cod_aluno={$this->ref_cod_aluno}';
			echo "<script>alert('Reclassificação realizada com sucesso!\\nO Código da nova matrícula é: $cadastrou.');
			window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';
			</script>";
			die('Reclassificação realizada com sucesso!');

		}

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
/*
document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}*/
function getSerie()
{
	var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoSerie = document.getElementById('serie_matricula').value;
	var xml1 = new ajax(getSerie_XML);
	strURL = "educar_sequencia_serie_curso_xml.php?cur="+campoCurso+"&ser_dif="+campoSerie;
	xml1.envia(strURL);

}

function getSerie_XML(xml)
{
	//var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoSerie = document.getElementById('ref_ref_cod_serie');
	//var campoSerieMatricula = document.getElementById('serie_matricula').value;

	var seq_serie = xml.getElementsByTagName( "serie" );
	campoSerie.length = 1;

	for( var ct = 0;ct < seq_serie.length;ct++ )
	{
	//	if( curso == sequencia_serie[ct][0] && sequencia_serie[ct][1] != campoSerieMatricula)
		//{
		campoSerie[campoSerie.length] = new Option(seq_serie[ct].firstChild.nodeValue,seq_serie[ct].getAttribute("cod_serie"),false,false);
	//	}
	}
}
getSerie();
</script>