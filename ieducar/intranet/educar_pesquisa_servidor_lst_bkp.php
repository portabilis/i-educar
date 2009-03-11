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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor" );
		$this->processoAp = "0";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $cod_servidor;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $ref_cod_funcao;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $horario;
	var $lst_matriculas;
	var $ref_cod_instituicao;
	var $professor;
	var $ref_cod_escola;
	var $nome_servidor;
	var $ref_cod_servidor;
	var $periodo;
	var $carga_horaria_usada;
	var $min_mat;
	var $min_ves;
	var $min_not;
	var $dia_semana;

	var $matutino 	= false;
	var $vespertino = false;
	var $noturno	= false;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];

		$_SESSION["campo1"] 		= $_GET["campo1"] ? $_GET["campo1"] : $_SESSION["campo1"];
		$_SESSION["campo2"] 		= $_GET["campo2"] ? $_GET["campo2"] : $_SESSION["campo2"];
		$_SESSION["dia_semana"] 	= isset($_GET["dia_semana"]) ? $_GET["dia_semana"] : $_SESSION["dia_semana"];
		$_SESSION["hora_inicial"] 	= $_GET["hora_inicial"] ? $_GET["hora_inicial"] : $_SESSION["hora_inicial"];
		$_SESSION["hora_final"] 	= $_GET["hora_final"] ? $_GET["hora_final"] : $_SESSION["hora_final"];
		$_SESSION["professor"] 		= $_GET["professor"] ? $_GET["professor"] : $_SESSION["professor"];
		$_SESSION["horario"] 		= $_GET["horario"] ? $_GET["horario"] : $_SESSION["horario"];
		$_SESSION["ref_cod_escola"] = $_GET["ref_cod_escola"] ? $_GET["ref_cod_escola"] : $_SESSION["ref_cod_escola"];
		$_SESSION["min_mat"]		= $_GET["min_mat"] ? $_GET["min_mat"] : $_SESSION["min_mat"];
		$_SESSION["min_ves"]		= $_GET["min_ves"] ? $_GET["min_ves"] : $_SESSION["min_ves"];
		$_SESSION["min_not"]		= $_GET["min_not"] ? $_GET["min_not"] : $_SESSION["min_not"];


		if ( isset( $_GET["lst_matriculas"] ) )
		{
			$_SESSION["lst_matriculas"] = $_GET["lst_matriculas"] ? $_GET["lst_matriculas"] : $_SESSION["lst_matriculas"];
		}

		if ( !isset( $_GET["tipo"] ) )
		{
			 $_SESSION["setAllField1"] = $_SESSION["setAllField2"] = $_SESSION["tipo"] = "";
		}
		/*else
		{
			$_SESSION["hora_final"] = $_SESSION["hora_inicial"] = $_SESSION["dia_semana"] = "";
		}*/

		$this->ref_cod_instituicao = $_SESSION["ref_cod_instituicao"] = $_GET["ref_cod_instituicao"] ? $_GET["ref_cod_instituicao"] : $_SESSION["ref_cod_instituicao"];
		$this->ref_cod_servidor    = $_SESSION["ref_cod_servidor"] 	  = $_GET["ref_cod_servidor"] ? $_GET["ref_cod_servidor"] : $_SESSION["ref_cod_servidor"];
		$this->professor 		   = $_SESSION["professor"] 		  = $_GET["professor"] ? $_GET["professor"] : $_SESSION["professor"];
		$this->horario 			   = $_SESSION["horario"] 			  = $_GET["horario"] ? $_GET["horario"] : $_SESSION["horario"];
		$this->ref_cod_escola 	   = $_GET["ref_cod_escola"] ? $_GET["ref_cod_escola"] : $_SESSION["ref_cod_escola"];
		$this->min_mat			   = $_SESSION["min_mat"] 			  = $_GET["min_mat"] ? $_GET["min_mat"] : $_SESSION["min_mat"];
		$this->min_ves			   = $_SESSION["min_ves"] 			  = $_GET["min_ves"] ? $_GET["min_ves"] : $_SESSION["min_ves"];
		$this->min_not			   = $_SESSION["min_not"] 			  = $_GET["min_not"] ? $_GET["min_not"] : $_SESSION["min_not"];


		if ( isset( $_GET["lst_matriculas"] ) && isset( $_SESSION["lst_matriculas"] ) )
		{
			$this->lst_matriculas = $_GET["lst_matriculas"] ? $_GET["lst_matriculas"] : $_SESSION["lst_matriculas"];
		}

		$_SESSION["tipo"] = $_GET["tipo"] ? $_GET["tipo"] : $_SESSION["tipo"];
		session_write_close();

		$this->titulo = "Servidores P&uacute;blicos - Listagem";

		foreach ( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
		{
			$this->$var = ( $val === "" ) ? null: $val;
		}

		if ( isset( $this->lst_matriculas ) )
		{
			$this->lst_matriculas = urldecode( $this->lst_matriculas );
		}

//		$obj_horarios = new clsPmieducarQuadroHorarioHorarios();
//		$lst_horarios = $obj_horarios->listaHoras( $this->ref_cod_instituicao, $this->ref_cod_servidor, 1, $_SESSION["dia_semana"] );
//
//		echo "<pre>";
//		print_r( $lst_horarios );
//
//		if ( is_array( $lst_horarios ) )
//		{
//			foreach ( $lst_horarios as $horario )
//			{
//				$hr_ini = explode( ":", $horario["hora_inicial"] );
//				$hr_fim = explode( ":", $horario["hora_final"] );
//
//				$min_ini = ( $hr_ini[0] * 60 ) + $hr_ini[1];
//				$min_fim = ( $hr_fim[0] * 60 ) + $hr_fim[1];
//
//				if (  $min_ini >= 480 && $min_ini <= 720 )
//				{
//					if ( $min_fim <= 720 )
//					{
//						$this->min_mat += $min_fim - $min_ini;
//					}
//					else if ( $min_fim >= 721 && $min_fim <= 1080 )
//					{
//						$this->min_mat += 720 - $min_ini;
//						$this->min_ves += $min_fim - 720;
//					}
//					else if ( ( $min_fim >= 1081 && $min_fim <= 1439 ) || $min_fim == 0 )
//					{
//						$this->min_mat += 720 - $min_ini;
//						$this->min_ves += 360;
//
//						if ( $min_fim >= 1081 && $min_fim <= 1439 )
//						{
//							$this->min_not += $min_fim - 1080;
//						}
//						else if ( $min_fim = 0 )
//						{
//							$this->min_not += 360;
//						}
//					}
//				}
//				else if ( $min_ini >= 721 && $min_ini <= 1080 )
//				{
//					if ( $min_fim <= 1080 )
//					{
//						$this->min_ves += $min_fim - $min_ini;
//					}
//					else if ( ( $min_fim >= 1081 && $min_fim <= 1439 ) || $min_fim == 0 )
//					{
//						$this->min_ves += 1080 - $min_ini;
//
//						if ( $min_fim >= 1081 && $min_fim <= 1439 )
//						{
//							$this->min_not += $min_fim - 1080;
//						}
//						else if ( $min_fim = 0 )
//						{
//							$this->min_not += 360;
//						}
//					}
//				}
//				else if ( ( $min_ini >= 1081 && $min_ini <= 1439 ) || $min_ini == 0 )
//				{
//					if ( $min_fim <= 1439 )
//					{
//						$this->min_not += $min_fim - $min_ini;
//					}
//					else if ( $min_fim == 0 )
//					{
//						$this->min_not += 1440 - $min_ini;
//					}
//				}
//			}
//		}

		$string1 = ( $this->min_mat - floor( $this->min_mat / 60 ) * 60 );
		$string1 = str_repeat( 0, 2 - strlen( $string1 ) ).$string1;

		$string2 = floor( $this->min_mat / 60 );
		$string2 = str_repeat( 0, 2 - strlen( $string2 ) ).$string2;

		$hr_mat  = $string2.":".$string1;

		$string1 = ( $this->min_ves - floor( $this->min_ves / 60 ) * 60 );
		$string1 = str_repeat( 0, 2 - strlen( $string1 ) ).$string1;

		$string2 = floor( $this->min_ves / 60 );
		$string2 = str_repeat( 0, 2 - strlen( $string2 ) ).$string2;

		$hr_ves  = $string2.":".$string1;


		$string1 = ( $this->min_not - floor( $this->min_not / 60 ) * 60 );
		$string1 = str_repeat( 0, 2 - strlen( $string1 ) ).$string1;

		$string2 = floor( $this->min_not / 60 );
		$string2 = str_repeat( 0, 2 - strlen( $string2 ) ).$string2;

		$hr_not  = $string2.":".$string1;

		$hora_inicial_ = explode( ":", $_SESSION["hora_inicial"] );
		$hora_final_   = explode( ":", $_SESSION["hora_final"] );
		$horas_ini 	   = sprintf( "%02d", ( int ) abs( $hora_final_[0] ) - abs( $hora_inicial_[0] ) );
		$minutos_ini   = sprintf( "%02d", ( int ) abs( $hora_final_[1] ) - abs( $hora_inicial_[1] ) );

		$h_m_ini = ( $hora_inicial_[0] * 60 ) + $hora_inicial_[1];
	 	$h_m_fim = ( $hora_final_[0]   * 60 ) + $hora_final_[1];

		if ( $h_m_ini >= 480 && $h_m_ini <= 720 )
		{
			$this->matutino = true;

			if ( $h_m_fim >= 721 && $h_m_fim <= 1080 )
			{
				$this->vespertino = true;
			}
			else if ( ( $h_m_fim >= 1801 && $h_m_fim <= 1439 ) || ( $h_m_fim == 0 ) )
			{
				//$this->vespertino = true;
				$this->noturno 	  = true;
			}
		}
		else if ( $h_m_ini >= 721 && $h_m_ini <= 1080 )
		{
			$this->vespertino = true;

			if ( ( $h_m_fim >= 1081 && $h_m_fim <= 1439 ) /*|| ( $h_m_fim == 0 )*/ )
			{
				$this->noturno = true;
			}
		}
		else if ( ( $h_m_ini >= 1081 && $h_m_ini <= 1439 ) || ( $h_m_ini == 0  ) )
		{
			$this->noturno = true;
		}

		$this->addCabecalhos( array(
			"Nome do Servidor",
			"Matr&iacute;cula",
			"Institui&ccedil;&atilde;o"
		) );

		$this->campoTexto( "nome_servidor", "Nome Servidor", $this->nome_servidor, 30, 255, false );
		$this->campoOculto( "tipo", $_GET["tipo"] );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite: 0;

		$obj_servidor = new clspmieducarservidor();
		$obj_servidor->setOrderby( "carga_horaria ASC" );
		$obj_servidor->setLimite( $this->limite, $this->offset );
		if ( $_SESSION["dia_semana"] && $_SESSION["hora_inicial"] && $_SESSION["hora_final"] )
		{
			$array_hora = array( $_SESSION["dia_semana"], $_SESSION["hora_inicial"], $_SESSION["hora_final"] );
		}

		$lista = $obj_servidor->lista(
			null,
			$this->ref_cod_deficiencia,
			$this->ref_idesco,
			$this->carga_horaria,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao,
			$_SESSION["tipo"],
			$array_hora,
			$this->ref_cod_servidor,
			$this->nome_servidor,
			$this->professor,
			$this->horario,
			false,
			$this->lst_matriculas,
			$this->matutino,
			$this->vespertino,
			$this->noturno,
			$this->ref_cod_escola,
			$hr_mat,
			$hr_ves,
			$hr_not,
			$_SESSION["dia_semana"],
			$this->ref_cod_instituicao

		);

		$total = $obj_servidor->_total;

		// pega detalhes de foreign_keys
		if ( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao 		 = new clsPmieducarInstituicao( $lista[0]["ref_cod_instituicao"] );
			$det_ref_cod_instituicao 		 = $obj_ref_cod_instituicao->detalhe();
			$nm_instituicao = $det_ref_cod_instituicao["nm_instituicao"];

		}
		else
		{
			//$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		// monta a lista
		if ( is_array( $lista ) && count( $lista ) )
		{

			foreach ( $lista AS $registro )
			{

				if ( class_exists( "clsFuncionario" ) )
				{
					$obj_cod_servidor 	   = new clsFuncionario( $registro["cod_servidor"] );
					$det_cod_servidor 	   = $obj_cod_servidor->detalhe();
					$registro["matricula"] = $det_cod_servidor['matricula'];
					//$det_cod_servidor 	   = $det_cod_servidor["idpes"]->detalhe();
					//$registro["nome"] 	   = $det_cod_servidor["nome"];

				}
				else
				{
					$registro["cod_servidor"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
				}


				/*if ( $this->ref_cod_escola )
				{
					$obj_servidor_alocacao = new clsPmieducarServidorAlocacao();
					$det_servidor_alocacao = $obj_servidor_alocacao->lista( null, $this->ref_cod_instituicao, null, null, $this->ref_cod_escola, $registro['cod_servidor'], null, null, null, null, 1 );

					if ( !$det_servidor_alocacao )
					{
						continue;
					}
				}*/

				if ( $_SESSION["tipo"] )
				{
					if ( is_string( $_SESSION['campo1'] ) && is_string( $_SESSION['campo2'] ) )
					{
						if ( is_string( $_SESSION['horario'] ) )
						{
							$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$_SESSION['campo2']}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
						}
						else
						{
							$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_servidor']}',null); addVal1('{$_SESSION['campo2']}','{$registro['nome']}',null); $setAll fecha();\"";
						}
					}
					elseif ( is_string( $_SESSION['campo1'] ) )
					{
						$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
					}
				}
				else
				{
					if ( is_string( $_SESSION['campo1'] ) && is_string( $_SESSION['campo2'] ) )
					{
						$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$_SESSION['campo2']}','{$registro['nome']}','{$registro['cod_servidor']}'); $setAll fecha();\"";
					}
					else if ( is_string( $_SESSION['campo2'] ) )
					{
						$script = " onclick=\"addVal1('{$_SESSION['campo2']}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
					}
					else if ( is_string( $_SESSION['campo1'] ) )
					{
						$script = " onclick=\"addVal1('{$_SESSION['campo1']}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
					}
				}

				$this->addLinhas( array(
					"<a href=\"javascript:void(0);\" $script>{$registro["nome"]}</a>",
					"<a href=\"javascript:void(0);\" $script>{$registro["matricula"]}</a>",
					"<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>"
				) );
			}
		}

		$this->addPaginador2( "educar_pesquisa_servidor_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		$this->largura  = "100%";
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
function addVal1( campo, valor, opcao )
{
	if ( window.parent.document.getElementById( campo ) )
	{
		if ( window.parent.document.getElementById( campo ).type == "select-one")
		{
			obj						= window.parent.document.getElementById( campo );
			novoIndice              = obj.options.length;
			obj.options[novoIndice] = new Option( opcao );
			opcao                   = obj.options[novoIndice];
			opcao.value				= valor;
			opcao.selected			= true;
			obj.onchange();
		}
		else if ( window.parent.document.getElementById( campo ) )
		{
			obj       =  window.parent.document.getElementById( campo );
			obj.value = valor;
		}
	}
}

function fecha()
{
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
}

function setAll(field,value){
	var elements = window.parent.document.getElementsByName(field);

	for(var ct =0;ct < elements.length;ct++)
	{
		elements[ct].value = value;
	}
}

function clearAll(){

	var elements = window.parent.document.getElementsByName('ref_cod_servidor_substituto');

	for(var ct =0;ct < elements.length;ct++)
	{
		elements[ct].value = '';
	}



	for(var ct =0;ct < num_alocacao;ct++)
	{
			var elements = window.parent.document.getElementById('ref_cod_servidor_substituto_' + ct).value='';
	}

}
/*
function ok(servidor,campo)
{
	var chave = /[0-9]+/.exec(campo)[0];

	var horas_trabalhadas = new Array(0,0);
	var horas_ = new Array(0,0);
	var total_horas = new Array(0,0);

	try{
		horas_trabalhadas = getArrayHora(window.parent.array_horas_utilizadas_servidor['' + servidor + '_'][0] );
	}catch(e){
		//servidor nao tem nenhuma hora trabalhada		return true;
		horas_trabalhadas = Array(0,0);
	}

	horas_ = getArrayHora(window.parent.array_servidores[chave][0] );

	for(var ct = 0 ;ct < window.parent.array_servidores.length;ct++)
	{
		if(window.parent.array_servidores[ct][1] ==  servidor && ct != chave)
		{
			var horas = getArrayHora(window.parent.array_servidores[ct][0]);
			horas_[0] = parseInt(horas_[0],10) + parseInt(horas[0],10);
			horas_[1] = parseInt(horas_[1],10) + parseInt(horas[1],10);
		}
	}



	total_horas = getArrayHora(window.parent.array_horas_servidor['' + servidor + '_'][0] );
//alert(parseInt(horas_trabalhadas[0])+ parseInt(horas_[0]));
//alert(total_horas);
	horas_trabalhadas = Date.UTC(1970,01,01,parseInt(horas_trabalhadas[0],10) + parseInt(horas_[0],10) ,horas_trabalhadas[1] + horas_[1],0);

	total_horas = Date.UTC(1970,01,01,total_horas[0] ,total_horas[1],0);
	if(total_horas >= horas_trabalhadas){
		window.parent.array_servidores[chave][1] = servidor;
		return true;
	}
	else
		alert('Excedeu o numero de horas trabalhadas');
	return false;
//	window.parent.array_servidores['' + valor + '']
}*/

function getArrayHora(hora){
	var array_h;
	if(hora)
		array_h = hora.split(":");
	else
		array_h = new Array(0,0);

	return array_h;

}
</script>