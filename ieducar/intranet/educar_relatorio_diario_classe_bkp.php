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
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Diário de Classe" );
		$this->processoAp = "664";
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


	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_cod_turma;

	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $sequencial;
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;
	var $nm_professor;
	var $nm_turma;
	var $nm_serie;
	var $nm_disciplina;

	var $page_y = 125;

	var $get_file;

	var $cursos = array();

	var $get_link;

	var $total;

	//var $array_disciplinas = array();

	var $ref_cod_modulo;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MAR&Ccedil;O"
							,"4" => "ABRIL"
							,"5" => "MAIO"
							,"6" => "JUNHO"
							,"7" => "JULHO"
							,"8" => "AGOSTO"
							,"9" => "SETEMBRO"
							,"10" => "OUTUBRO"
							,"11" => "NOVEMBRO"
							,"12" => "DEZEMBRO"
						);


	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//$obj_permissoes = new clsPermissoes();
		//if($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7)
			//header("location: index.php");

		return $retorno;
	}

	function Gerar()
	{

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		//if(!$nivel_usuario)
			//header("location: index.php");
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}



		$this->ano = $ano_atual = date("Y");
		$this->mes = $mes_atual = date("n");
		$lim = 5;
		for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
				$anos["{$a}"] = "{$a}";


		$this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );
		$this->campoLista( "mes", "M&ecirc;s",$this->meses_do_ano, $this->mes,"",false );

		$get_escola = true;
		$obrigatorio = true;
		$exibe_nm_escola = true;
		$get_escola_curso = true;
		$get_escola_curso_serie = true;
		//$get_turma = true;
		//$curso_padrao_ano_escolar = 1;
		//$exibe_campo_lista_curso_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		$db = new clsBanco();
		$consulta ="SELECT distinct
					       m.cod_turma
					       ,m.nm_turma
					       ,s.cod_serie
					       ,s.nm_serie
					       ,m.ref_ref_cod_escola
					  FROM pmieducar.turma          m
					       ,pmieducar.serie         s
					 WHERE m.ref_ref_cod_serie = s.cod_serie";

		$db->Consulta($consulta);

		$script = "<script>turma = new Array();\n";
		while ($db->ProximoRegistro()) {
			$tupla = $db->Tupla();
			$script .= "turma[turma.length] = new Array('{$tupla['cod_turma']}','{$tupla['nm_turma']}','{$tupla['cod_serie']}','{$tupla['nm_serie']}','{$tupla['ref_ref_cod_escola']}');\n";
		}
		echo $script .= "</script>";

		$this->campoLista("ref_cod_turma","Turma",array('' => 'Selecione'),'');

		if($this->ref_cod_escola)
			$this->ref_ref_cod_escola = $this->ref_cod_escola;

		if($this->get_link)
			$this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatório</a>");

		$this->url_cancelar = "educar_index.php";
		$this->nome_url_cancelar = "Cancelar";


	}

	function Novo()
	{
		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		if(empty($this->ref_cod_turma))
		{
	     	echo '<script>
	     			alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
	     			window.location = window.location;
	     		</script>';
	     	return true;
		}


		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

		}

	     $obj_calendario = new clsPmieducarEscolaAnoLetivo();
	     $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

	     $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
	     $det_turma = $obj_turma->detalhe();
	     $this->nm_turma = $det_turma['nm_turma'];

	     $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
	     $det_serie = $obj_serie->detalhe();
	     $this->nm_serie = $det_serie['nm_serie'];

		 $obj_pessoa = new clsPessoa_($det_turma["ref_cod_regente"]);
		 $det = $obj_pessoa->detalhe();
		 $this->nm_professor = $det["nome"];

	     if(!$lista_calendario)
	     {
	     	echo '<script>
	     			alert("Escola não possui calendário definido para este ano");
	     			window.location = window.location;
	     		</script>';
	     	return true;
	     }

		$prox_mes = $this->mes + 1;
		$this->pdf = new clsPDF("Diário de Classe - {$this->ano}", "Diário de Classe - {$this->meses_do_ano[$this->mes]} e {$this->meses_do_ano[$prox_mes]} de {$this->ano}", "A4", "", false, false);

		$this->pdf->largura  = 842.0;
  		$this->pdf->altura = 595.0;


		$altura_linha = 23;
		$inicio_escrita_y = 175;


		$obj = new clsPmieducarSerie();
		$obj->setOrderby('cod_serie,etapa_curso');
		$lista_serie_curso = $obj->lista(null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();

		$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
		$det_curso = $obj_curso->detalhe();

		if($det_curso['falta_ch_globalizada'])
		{
			/**
			 * numero de semanas dos meses
			 */
			//$obj_quadro_horarios = new clsPmieducarQuadroHorarioHorarios();
			//$obj_quadro_horarios->setCamposLista("dia_semana");
			//$obj_quadro_horarios->setOrderby("1 asc");
			//$lista_quadro_horarios = $obj_quadro_horarios->lista(null,$this->ref_cod_serie,$this->ref_cod_escola,null,$this->ref_cod_turma,null,null,null,null,null,null,null,null,null,null,null,null,null,1);

			$total_semanas = 0;
			$this->mes;
			$total_semanas = $this->getNumeroDiasMes($this->mes,$this->ano);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
		    $total_semanas += $this->getNumeroDiasMes($this->mes + 1,$this->ano);//,$lista_quadro_horarios[count($lista_quadro_horarios)-1]);

		    $this->total = $total_semanas;//$total_semanas * count($lista_quadro_horarios);
		    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
		    $lista_matricula = $obj_matricula_turma->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,true);


			if($lista_matricula)
			{
				$this->pdf->OpenPage();
				$this->addCabecalho();

			    foreach ($lista_matricula as $matricula)
			    {

					if($this->page_y > 500)
					{
						$this->desenhaLinhasVertical();
						$this->pdf->ClosePage();
						$this->pdf->OpenPage();
						$this->page_y = 125;
						$this->addCabecalho();


					}

			    	$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
			    	$det_matricula = $obj_matricula->detalhe();

			    	$obj_aluno = new clsPmieducarAluno();
			    	$det_aluno = array_shift($obj_aluno->lista($det_matricula['ref_cod_aluno']));

			    	$this->pdf->quadrado_relativo( 30, $this->page_y , 782, 19);
			    	$this->pdf->escreve_relativo($det_aluno['nome_aluno'] , 33 ,$this->page_y + 4,160, 15, $fonte, 7, $corTexto, 'left' );

			    	$this->page_y +=19;



			    }

		    	$this->desenhaLinhasVertical();

				$this->rodape();
				$this->pdf->ClosePage();
			}
			else
			{

				$this->mensagem = "Turma não possui matrículas";

				return;
			}


			//header( "location: " . $this->pdf->GetLink() );
			$this->pdf->CloseFile();
			$this->get_link = $this->pdf->GetLink();
		}
		else
		{
			/**
			 * CARGA HORARIA NAO GLOBALIZADA
			 * GERAR UMA PAGINA PARA CADA DISICIPLINA
			 */
			//$obj_turma_disc = new clsPmieducarTurmaDisciplina();
			$obj_turma_disc = new clsPmieducarDisciplinaSerie();
			$obj_turma_disc->setCamposLista("ref_cod_disciplina");
			$lst_turma_disc = $obj_turma_disc->lista(null,$this->ref_cod_serie,1);

			if($lst_turma_disc)
			{
				foreach ($lst_turma_disc as $disciplina) {
					$obj_disc = new clsPmieducarDisciplina($disciplina);
					$det_disc = $obj_disc->detalhe();
					$this->nm_disciplina = $det_disc['nm_disciplina'];
					$this->page_y = 125;

					/**
					 * numero de semanas dos meses
					 */
					$obj_quadro = new clsPmieducarQuadroHorario();
					$obj_quadro->setCamposLista("cod_quadro_horario");
					$quadro_horario = $obj_quadro->lista(null,null,null,$this->ref_cod_turma, null, null, null, null,1);
					if(!$quadro_horario)
					{
						echo '<script>alert(\'Turma não possui quadro de horários\'); window.location = "educar_relatorio_diario_classe.php";</script>';
						break;
					}

					$obj_quadro_horarios = new clsPmieducarQuadroHorarioHorarios();
					$obj_quadro_horarios->setCamposLista("dia_semana");
					$obj_quadro_horarios->setOrderby("1 asc");

					$lista_quadro_horarios = $obj_quadro_horarios->lista($quadro_horario,$this->ref_cod_serie,$this->ref_cod_escola,$disciplina,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1);



					$total_dias_semanas = 0;
					$this->mes;
					//$total_semanas = $this->getNumeroSemanasMes($this->mes,$this->ano,$lista_quadro_horarios[0],$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
				    //$total_semanas = $this->getNumeroSemanasMes($this->mes + 1,$this->ano,$lista_quadro_horarios[0],$lista_quadro_horarios[count($lista_quadro_horarios)-1]);
					if($lista_quadro_horarios)
					    for($mes_ = 0;$mes_ <=1; $mes_++)
						    foreach ($lista_quadro_horarios as $dia_semana)
								$total_dias_semanas += $this->getDiasSemanaMes($this->mes + $mes_,$this->ano,$dia_semana);

				   	$this->total = $total_dias_semanas;

				    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
				    $lista_matricula = $obj_matricula_turma->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,true);


					if($lista_matricula)
					{
						$this->pdf->OpenPage();
						$this->addCabecalho();

					    foreach ($lista_matricula as $matricula)
					    {

							if($this->page_y > 500)
							{
								$this->desenhaLinhasVertical();
								$this->pdf->ClosePage();
								$this->pdf->OpenPage();
								$this->page_y = 125;
								$this->addCabecalho();


							}

					    	$obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
					    	$det_matricula = $obj_matricula->detalhe();

					    	$obj_aluno = new clsPmieducarAluno();
					    	$det_aluno = array_shift($obj_aluno->lista($det_matricula['ref_cod_aluno']));

					    	$this->pdf->quadrado_relativo( 30, $this->page_y , 782, 19);
					    	$this->pdf->escreve_relativo($det_aluno['nome_aluno'] , 33 ,$this->page_y + 4,160, 15, $fonte, 7, $corTexto, 'left' );

					    	$this->page_y +=19;



					    }

				    	$this->desenhaLinhasVertical();

						$this->rodape();
						$this->pdf->ClosePage();
					}
					else
					{

						$this->mensagem = "Turma não possui matrículas";
						//die;
						return;
					}
				}
			}


			//header( "location: " . $this->pdf->GetLink() );
			$this->pdf->CloseFile();
			$this->get_link = $this->pdf->GetLink();
		}

		return true;
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';


		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 782, 80, $fonte, 18, $corTexto, 'center' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 52, 300, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",132, 64, 300, 80, $fonte, 7, $corTexto, 'left' );
		$dif = 0;
		if($this->nm_professor)
			$this->pdf->escreve_relativo( "Prof.Regente:{$this->nm_professor}",111, 76, 300, 80, $fonte, 7, $corTexto, 'left' );
		else
			$dif = 12;

		$this->pdf->escreve_relativo( "Série:{$this->nm_serie}",138, 88  - $dif, 300, 80, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Turma:{$this->nm_turma}",134, 100 - $dif, 300, 80, $fonte, 7, $corTexto, 'left' );

		//titulo
		if($this->nm_disciplina)
			$this->nm_disciplina = "- $this->nm_disciplina";
		$this->pdf->escreve_relativo( "Diário de Frequência {$this->nm_disciplina}", 30, 75, 782, 80, $fonte, 12, $corTexto, 'center' );

		$obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
		$det_modulo = $obj_modulo->detalhe();
		//Data
		$mes2 = $this->mes + 1;
		$this->pdf->escreve_relativo( ucfirst(strtolower($this->meses_do_ano[$this->mes]))." e ".ucfirst(strtolower($this->meses_do_ano[$mes2]))." de {$this->ano}", 45, 100, 782, 80, $fonte, 10, $corTexto, 'center' );

		$this->pdf->linha_relativa(201,125,612,0);


    	$this->page_y +=19;
	    $this->pdf->escreve_relativo( "Dias de aula: {$this->total}", 715, 100, 535, 80, $fonte, 10, $corTexto, 'left' );
	}

	function desenhaLinhasVertical()
	{

			/**
			 *
			 */

		$largura_anos = 612;

				if($this->total >= 1)
				{

					$incremental = floor($largura_anos/ ($this->total )) ;

				}else {

					$incremental = 1;
				}

				$reta_ano_x = 200 ;


				$resto = $largura_anos - ($incremental * $this->total);

				for($linha = 0;$linha <$this->total;$linha++)
				{

					if(( $resto > 0) /*|| ($linha + 1 == $total && $resto >= 1) */|| $linha == 0)
					{
						$reta_ano_x++;
						$resto--;
					}

						$this->pdf->linha_relativa($reta_ano_x,125,0,$this->page_y - 125);

					$reta_ano_x += $incremental;

				}

				$this->pdf->linha_relativa(812,125,0,$this->page_y - 125);


			/**
			 *
			 */
	}

	function rodape()
	{
		$corTexto = '#000000';
		$fonte = 'arial';
		$dataAtual = date("d/m/Y");
		$this->pdf->escreve_relativo( "Data: $dataAtual", 36,756, 100, 50, $fonte, 7, $corTexto, 'left' );

		$this->pdf->escreve_relativo( "Assinatura do Diretor(a)", 68,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Assinatura do secretário(a)", 677,520, 100, 50, $fonte, 7, $corTexto, 'left' );
		$this->pdf->linha_relativa(52,517,130,0);
		$this->pdf->linha_relativa(660,517,130,0);
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
	}


	//function getNumeroSemanasMes($mes,$ano,$primeiro_dia_semana = null,$ultimo_dia_semana = null)
	function getNumeroDiasMes($mes,$ano)
	{
	/*	$year = $ano;
		$month = $mes;

		$date = mktime(1, 1, 1, $month, date("d"), $year);

		$first_day_of_month = strtotime("-" . (date("d", $date)-1) . " days", $date);
		$last_day_of_month = strtotime("+" . (date("t", $first_day_of_month)-1) . " days", $first_day_of_month);

		$first_week_no = date("W", $first_day_of_month);
		$last_week_no = date("W", $last_day_of_month);

		if($last_week_no < $first_week_no) $last_week_no=date("W", strtotime("-1 week",$last_week_no)) + 1;
		$weeks_of_month = $last_week_no - $first_week_no + 1;

		if(is_numeric($ultimo_dia_semana))
		{
			$first_week_day = getdate( $first_day_of_month);
			$first_week_day = $first_week_day['weekday'] + 1;
			if($first_week_day > $ultimo_dia_semana)
				$weeks_of_month--;
		}
		if(is_numeric($primeiro_dia_semana))
		{
			$first_week_day = getdate( $last_day_of_month);
			$first_week_day = $first_week_day['weekday'] + 1;
			if($first_week_day < $primeiro_dia_semana)
				$weeks_of_month--;
		}*/

		$year = $ano;
		$month = $mes;

		$date = mktime(1, 1, 1, $month, date("d"), $year);

		$first_day_of_month = strtotime("-" . (date("d", $date)-1) . " days", $date);
		$last_day_of_month = strtotime("+" . (date("t", $first_day_of_month)-1) . " days", $first_day_of_month);

		//$first_week_day = date("l", $first_day_of_month);
		$last_day_of_month = date("d", $last_day_of_month);

		$numero_dias = 0;
		/**
		 * verifica se dia eh feriado
		 */
		$obj_calendario = new clsPmieducarCalendarioAnoLetivo();
		$obj_calendario->setCamposLista("cod_calendario_ano_letivo");
		$lista = $obj_calendario->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1);
		if($lista)
		{
			$lista_calendario = array_shift($lista);
		}
		$obj_dia = new clsPmieducarCalendarioDia();
		$obj_dia->setCamposLista("dia");
		$dias_nao_letivo = $obj_dia->lista($lista_calendario,$mes,null,null,null,null,null,null,null,null,null,null,1,"'n'");
		if(!$dias_nao_letivo)
			$dias_nao_letivo = array();

		for($day = 1; $day <= $last_day_of_month; $day++)
		{
			$date = mktime(1, 1, 1, $month, $day, $year);
			$dia_semana_corrente = getdate($date);
			$dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

			if( ($dia_semana_corrente != 1 &&  $dia_semana_corrente != 7) && (array_search($day,$dias_nao_letivo) === false))
				$numero_dias++;
		}



		return $numero_dias;
	}

	function getDiasSemanaMes($mes,$ano,$dia_semana)
	{
		$year = $ano;
		$month = $mes;

		$date = mktime(1, 1, 1, $month, date("d"), $year);

		$first_day_of_month = strtotime("-" . (date("d", $date)-1) . " days", $date);
		$last_day_of_month = strtotime("+" . (date("t", $first_day_of_month)-1) . " days", $first_day_of_month);

		//$first_week_day = date("l", $first_day_of_month);
		$last_day_of_month = date("d", $last_day_of_month);

		$numero_dias = 0;


		/**
		 * verifica se dia eh feriado
		 */
		$obj_calendario = new clsPmieducarCalendarioAnoLetivo();
		$obj_calendario->setCamposLista("cod_calendario_ano_letivo");
		$lista_calendario = $obj_calendario->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1);
		if(is_array($lista_calendario))
			$lista_calendario = array_shift($lista_calendario);
		$obj_dia = new clsPmieducarCalendarioDia();
		$obj_dia->setCamposLista("dia");
		$dias_nao_letivo = $obj_dia->lista($lista_calendario,$mes,null,null,null,null,null,null,null,null,null,null,1,"'n'");
		if(!$dias_nao_letivo)
			$dias_nao_letivo = array();
		for($day = 1; $day <= $last_day_of_month; $day++)
		{
			$date = mktime(1, 1, 1, $month, $day, $year);
			$dia_semana_corrente = getdate($date);
			$dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

			if(($dia_semana ==  $dia_semana_corrente) && (array_search($day,$dias_nao_letivo) === false))
				$numero_dias++;
		}
		return $numero_dias;
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
function getModulos()
{


	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoCurso= document.getElementById( 'ref_cod_curso' ).value;
	var campoModulo = document.getElementById( 'ref_cod_modulo' );
	var campoAno = document.getElementById( 'ano' ).value;

	campoModulo.length = 1;
	campoModulo.options[0] = new Option( 'Selecione um módulo', '', false, false );
	for ( var j = 0; j < modulos.length; j++ )
	{
		if ( modulos[j][2] == campoEscola && modulos[j][3] == campoAno)
		{
			campoModulo.options[campoModulo.options.length] = new Option( modulos[j][1], modulos[j][0], false, false );
		}
	}
	if ( campoModulo.length == 1 ) {
		campoModulo.options[0] = new Option( 'O curso não possui nenhum módulo', '', false, false );
	}


}

after_getEscolaCurso = function () {getModulos()};
getEscolaCurso();
after_getEscola = function(){getEscolaCurso()};
document.getElementById('ref_cod_escola').onchange = function(){getEscolaCurso()};
*/

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
	getTurma();
}

function getTurma()
{


	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoSerie = document.getElementById( 'ref_ref_cod_serie' ).value;
	var campoTurma = document.getElementById( 'ref_cod_turma' );

	campoTurma.length = 1;
	campoTurma.options[0] = new Option( 'Selecione uma Turma', '', false, false );
	for ( var j = 0; j < turma.length; j++ )
	{
		if ( ( turma[j][2] == campoSerie ) && ( turma[j][4] == campoEscola ) )
		{
			campoTurma.options[campoTurma.options.length] = new Option( turma[j][1], turma[j][0], false, false );
		}
	}
	if ( campoTurma.length == 1 && campoSerie != '' ) {
		campoTurma.options[0] = new Option( 'A série não possui nenhuma turma', '', false, false );
	}

}

</script>
