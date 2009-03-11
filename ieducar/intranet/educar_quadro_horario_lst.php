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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro de Hor&aacute;rio" );
//		$this->addScript( "quadro_horario" );
		$this->processoAp = "641";
	}
}

class indice extends clsConfig
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

	var $cod_calendario_ano_letivo;
	var $ref_cod_escola;
	var $ref_cod_curso;
	var $ref_cod_serie;
	var $ref_cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $data_cadastra;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;
	var $busca;

	function renderHTML()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$obj_permissoes = new clsPermissoes();


		if ( $obj_permissoes->nivel_acesso( $this->pessoa_logada ) > 7 )
		{
			$retorno .= '<table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
							<tbody>';
			$retorno .= '<tr >
							<td colspan="2" valig="center" height="50">
							<center class="formdktd">Usu&aacute;rio sem permiss&atilde;o para acessar esta p&aacute;gina</center>
							</td>
							</tr>';

			$retorno .='</tbody>
						</table>';

			return $retorno;
		}

		$retorno .= '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
						<tbody>';

		if ( $_POST )
		{
			$this->ref_cod_turma	   = $_POST['ref_cod_turma'] ? $_POST['ref_cod_turma'] : null;
			$this->ref_cod_serie 	   = $_POST['ref_cod_serie'] ? $_POST['ref_cod_serie'] : null;
			$this->ref_cod_curso 	   = $_POST['ref_cod_curso'] ? $_POST['ref_cod_curso'] : null;
			$this->ref_cod_escola 	   = $_POST['ref_cod_escola'] ? $_POST['ref_cod_escola'] : null;
			$this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ? $_POST['ref_cod_instituicao'] : null;
			$this->busca			   = $_GET['busca'] ? $_GET['busca'] : null;
		}
		else
		{
			if ( $_GET )
			{
				foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
					$this->$var = ( $val === "" ) ? null : $val;
			}
		}

		$nivel_usuario = $obj_permissoes->nivel_acesso( $this->pessoa_logada );

		if ( !$this->ref_cod_escola )
			$this->ref_cod_escola = $obj_permissoes->getEscola( $this->pessoa_logada );

		if ( !is_numeric( $this->ref_cod_instituicao ) )
			$this->ref_cod_instituicao = $obj_permissoes->getInstituicao( $this->pessoa_logada );

		$obrigatorio 	 = false;
		$get_instituicao = true;
		$get_escola		 = true;
		$get_curso		 = true;
		$get_serie 		 = true;
		$get_turma		 = true;
		include( "educar_quadro_horarios_pesquisas.php" );

//		echo "busca: {$this->busca}<br>";
		if ( $this->busca == "S" )
		{
			if ( is_numeric( $this->ref_cod_turma ) )
			{
				$obj_turma 	  = new clsPmieducarTurma( $this->ref_cod_turma );
				$det_turma 	  = $obj_turma->detalhe();

				$obj_quadro = new clsPmieducarQuadroHorario( null, null, null, $this->ref_cod_turma, null, null, 1 );
				$det_quadro = $obj_quadro->detalhe();

				if ( is_array( $det_quadro ) )
				{
					$quadro_horario = "<table class='calendar' cellspacing='0' cellpadding='0' border='0'><tr><td class='cal_esq' >&nbsp;</td><td background='imagens/i-educar/cal_bg.gif' width='100%' class='mes'>{$det_turma["nm_turma"]}</td><td align='right' class='cal_dir'>&nbsp;</td></tr><tr><td colspan='3' class='bordaM' style='border-bottom: 1px solid #8A959B;'  align='center'><table cellspacing='0' cellpadding='0'  border='0' ><tr class='header'><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>DOM</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>SEG</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>TER</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>QUA</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>QUI</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>SEX</td><td style='width: 100px;'>SAB</td></tr>";
					$texto = "<tr>";

					for ( $c = 1; $c <= 7; $c++ )
					{
						$obj_horarios = new clsPmieducarQuadroHorarioHorarios();
						$resultado 	  = $obj_horarios->retornaHorario( $this->ref_cod_instituicao, $this->ref_cod_escola, $this->ref_cod_serie, $this->ref_cod_turma, $c );

						$texto .= "<td valign=top align='center' width='100' style='cursor: pointer; ' onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro["cod_quadro_horario"]}, {$c} );'>";

						if ( is_array( $resultado ) )
						{
							foreach ( $resultado as $registro )
							{
								$obj_disciplina = new clsPmieducarDisciplina( $registro["ref_cod_disciplina"] );
								$det_disciplina = $obj_disciplina->detalhe();
								$obj_servidor   = new clsPmieducarServidor();
								$det_servidor   = array_shift($obj_servidor->lista($registro['ref_servidor'],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,true));
								$det_servidor['nome'] = array_shift(explode(' ',$det_servidor['nome']));
								$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; '>".substr( $registro["hora_inicial"], 0, 5 )." - ".substr( $registro["hora_final"], 0, 5 )." <br> {$det_disciplina["abreviatura"]} <br> {$det_servidor["nome"]}</div>";
							}
						}
						else
						{
							$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; height: 85%;'></div>";
						}
						$texto .= "</td>";
					}
					$texto .= "<tr><td colspan='7'>&nbsp;</td></tr>";
					$quadro_horario .= $texto;

					$quadro_horario .= "</table></td></tr></table>";
					$retorno .= "<tr><td colspan='2' ><center><b></b>{$quadro_horario}</center></td></tr>";
				}
				else
				{
					$retorno .= "<tr><td colspan='2' ><b><center>N&atilde;o existe nenhum quadro de hor&aacute;rio cadastrado para esta turma.</center></b></td></tr>";
				}
			}
		}
		if( $obj_permissoes->permissao_cadastra( 641, $this->pessoa_logada, 7 ) )
		{
			$retorno .= "<tr><td>&nbsp;</td></tr><tr>
						<td align=\"center\" colspan=\"2\">";
			if ( !$det_quadro )
			{
				$retorno .= "<input type=\"button\" value=\"Novo Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao};'\" class=\"botaolistagem\"/>";
			}
			else
			{
				if ( $obj_permissoes->permissao_excluir( 641, $this->pessoa_logada, 7 ) )
					$retorno .= "<input type=\"button\" value=\"Excluir Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_quadro_horario={$det_quadro["cod_quadro_horario"]}'\" class=\"botaolistagem\"/>";
			}
			$retorno .= "</td>
						</tr>";
		}

		$retorno .='</tbody>
			</table>';
		return $retorno;
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
var obj_instituicao;
var obj_escola;
var obj_curso;
var obj_serie;
var obj_turma;
var obj_botao_busca;

if ( document.getElementById( 'ref_cod_instituicao' ) )
{
	obj_instituicao = document.getElementById( 'ref_cod_instituicao' );
	obj_instituicao.onchange = function() { getEscola(); getCurso(); getSerie(); getTurma(); };
}
if ( document.getElementById( 'ref_cod_escola' ) )
{
	obj_escola = document.getElementById( 'ref_cod_escola' );
	obj_escola.onchange = function() { getCurso(); getSerie(); getTurma(); };
}
if ( document.getElementById( 'ref_cod_curso' ) )
{
	obj_curso = document.getElementById( 'ref_cod_curso' );
	obj_curso.onchange = function() { getSerie(); getTurma(); };
}
if ( document.getElementById( 'ref_cod_serie' ) )
{
	obj_serie = document.getElementById( 'ref_cod_serie' );
	obj_serie.onchange = function() { getTurma(); };
}
*/

var campoInstituicao = document.getElementById( 'ref_cod_instituicao' );
var campoEscola = document.getElementById( 'ref_cod_escola' );
var campoCurso = document.getElementById( 'ref_cod_curso' );
var campoSerie = document.getElementById( 'ref_cod_serie' );
var campoTurma = document.getElementById( 'ref_cod_turma' );

campoInstituicao.onchange = function()
{
	/*getEscola();
	getCurso();
	getSerie();
	getTurma(); */

	var campoInstituicao_ = document.getElementById( 'ref_cod_instituicao' ).value;

	campoEscola.length = 1;
	campoEscola.disabled = true;
	campoEscola.options[0].text = 'Carregando escola';

	campoCurso.length = 1;
	campoCurso.disabled = true;
	campoCurso.options[0].text = 'Selecione uma escola antes';

	campoSerie.length = 1;
	campoSerie.disabled = true;
	campoSerie.options[0].text = 'Selecione um curso antes';

	campoTurma.length = 1;
	campoTurma.disabled = true;
	campoTurma.options[0].text = 'Selecione uma Série antes';

	var xml_escola = new ajax( getEscola );
	xml_escola.envia( "educar_escola_xml2.php?ins="+campoInstituicao_ );
};

campoEscola.onchange = function()
{
	var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;

	campoCurso.length = 1;
	campoCurso.disabled = true;
	campoCurso.options[0].text = 'Carregando curso';

	campoSerie.length = 1;
	campoSerie.disabled = true;
	campoSerie.options[0].text = 'Selecione um curso antes';

	campoTurma.length = 1;
	campoTurma.disabled = true;
	campoTurma.options[0].text = 'Selecione uma série antes';

	var xml_curso = new ajax( getCurso );
	xml_curso.envia( "educar_curso_xml.php?esc="+campoEscola_ );
};

campoCurso.onchange = function()
{
	var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;
	var campoCurso_ = document.getElementById( 'ref_cod_curso' ).value;

	campoSerie.length = 1;
	campoSerie.disabled = true;
	campoSerie.options[0].text = 'Carregando série';

	campoTurma.length = 1;
	campoTurma.disabled = true;
	campoTurma.options[0].text = 'Selecione uma Série antes';

	var xml_serie = ajax( getSerie );
	xml_serie.envia( "educar_escola_curso_serie_xml.php?esc="+campoEscola_+"&cur="+campoCurso_ );
};

campoSerie.onchange = function()
{
	var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;
	var campoSerie_ = document.getElementById( 'ref_cod_serie' ).value;

	campoTurma.length = 1;
	campoTurma.disabled = true;
	campoTurma.options[0].text = 'Carregando turma';

	var xml_turma = new ajax( getTurma );
	xml_turma.envia( "educar_turma_xml.php?esc="+campoEscola_+"&ser="+campoSerie_ );
};

if ( document.getElementById( 'botao_busca' ) )
{
	obj_botao_busca = document.getElementById( 'botao_busca' );
	obj_botao_busca.onclick = function()
	{
		document.formcadastro.action = 'educar_quadro_horario_lst.php?busca=S';
		acao();
	};
}

/*if ( document.getElementById( 'botao_busca' ) )
{
	setVisibility( 'botao_busca', false );
}*/

function envia( obj, var1, var2, var3, var4, var5, var6, var7 )
{
	var identificador = Math.round(1000000000*Math.random());
	if ( obj.innerHTML )
	{
		document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&identificador=' + identificador;
		document.formcadastro.submit();
	}
	else
	{
		document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&identificador=' + identificador;
		document.formcadastro.submit();
	}
}

if(document.createStyleSheet)
{
	document.createStyleSheet('styles/calendario.css');
}
else
{
	var objHead = document.getElementsByTagName('head');
	var objCSS = objHead[0].appendChild(document.createElement('link'));
	objCSS.rel = 'stylesheet';
	objCSS.href = 'styles/calendario.css';
	objCSS.type = 'text/css';
}

</script>