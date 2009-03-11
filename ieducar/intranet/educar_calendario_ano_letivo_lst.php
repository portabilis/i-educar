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
require_once("clsCalendario.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calend&aacute;rio Ano Letivo" );
		$this->addScript("calendario");
		$this->processoAp = "620";
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
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastra;
	var $data_exclusao;
	var $ativo;
	var $inicio_ano_letivo;
	var $termino_ano_letivo;


	var $ref_cod_instituicao;
	var $ano;
	var $mes;

	function renderHTML()
	{
		@session_start();

		$this->pessoa_logada = $_SESSION['id_pessoa'];
		$_SESSION["calendario"]['ultimo_valido'] = 0;
		session_write_close();





		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7){
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

		if($_POST){

				$this->ref_cod_escola = $_POST['ref_cod_escola'] ? $_POST['ref_cod_escola'] :  $_SESSION["calendario"]['ref_cod_escola']  ;

				$this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ? $_POST['ref_cod_instituicao'] :  $_SESSION["calendario"]['ref_cod_instituicao']  ;

			if($_POST['mes'])
				$this->mes =  $_POST['mes'];// : $_SESSION["calendario"]['mes'] ;

			if($_POST['ano'])
				$this->ano = $_POST['ano'];// : $_SESSION["calendario"]['ano'] ;

				//verificao se ano existe se nao busca o maximo que tiver
		}else{

			if($_GET){
				foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
					$this->$var = ( $val === "" ) ? null: $val;
			}elseif ($_SESSION['calendario'])
				foreach( $_SESSION['calendario'] AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
					$this->$var = ( $val === "" ) ? null: $val;

		}

		if($_GET)
			header("location: educar_calendario_ano_letivo_lst.php");

		if(!$this->mes)
			$this->mes = date("n");

		if(!$this->ano)
			$this->ano = date("Y");

			$obj_cal = new clsPmieducarCalendarioAnoLetivo();
			if($this->ref_cod_escola && $this->ano)
			{
				if( $obj_cal->lista(null,$this->ref_cod_escola,null,null,$this->ano,null,null,null,null,1) )
				{
					@session_start();
						$_SESSION["calendario"]['ultimo_valido'] = 1;

					if($this->ref_cod_escola)
						$_SESSION["calendario"]["ref_cod_escola"] = $this->ref_cod_escola;

					if($this->ref_cod_instituicao)
						$_SESSION["calendario"]["ref_cod_instituicao"] = $this->ref_cod_instituicao;

					if($this->ano)
						$_SESSION["calendario"]["ano"] = $this->ano;

					if($this->mes)
						$_SESSION["calendario"]["mes"] = $this->mes;

					session_write_close();
				}


			}
			elseif(!$_POST){
				if($_SESSION["calendario"]["ref_cod_escola"])
					$this->ref_cod_escola = $_SESSION["calendario"]["ref_cod_escola"];

				if($_SESSION["calendario"]["ref_cod_instituicao"])
					$this->ref_cod_instituicao = $_SESSION["calendario"]["ref_cod_instituicao"];

				if($_SESSION["calendario"]["mes"])
					$this->ano = $_SESSION["calendario"]["mes"];

				if($_SESSION["calendario"]["mes"])
					$this->mes = $_SESSION["calendario"]["mes"];
				}



		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if(!$this->ref_cod_escola)
			$this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
		if(!$this->ref_cod_instituicao)
			$this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

		$get_escola     = 1;
		$obrigatorio    = false;
		include("educar_calendario_pesquisas.php");


		$obj_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
		$obj_calendario_ano_letivo->setOrderby( "ano ASC" );
		$obj_calendario_ano_letivo->setLimite( $this->limite, $this->offset );


		$lista = array();
		$obj_calendario_ano_letivo->setOrderby("ano");
		switch ($nivel_usuario){
			case 1: // poli-institucional
			case 2:
			case 4:
				if(!isset($this->ref_cod_escola))
					break;

				$lista = $obj_calendario_ano_letivo->lista(
					null,
					$this->ref_cod_escola,
					null,
					null,
					$this->ano,
					null,
					null,
					1,
					null,
					null,
					null,
					null,
					null,
					null,
					null,//true
					null
				);

				break;




		}

		$total = $obj_calendario_ano_letivo->_total;
		if(empty( $lista )/* && isset($this->ref_cod_escola)*/ )
		{
			
			if($nivel_usuario == 4)
									$retorno .= "<tr><td colspan='2' align='center' class='formdktd'>Sem Calend&aacute;rios letivo</td></tr>";
			else
				if($_POST)
					$retorno .= "<tr><td colspan='2' align='center' class='formdktd'>Sem Calend&aacute;rios para o ano selecionado</td></tr>";
				else
					$retorno .= "<tr ><td colspan='2' align='center' class='formdktd'>Selecione uma escola para exibir o calendario</td></tr>";
	
		} 

		@session_start();

		@session_write_close();

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $key => $registro )
			{
				$registro["inicio_ano_letivo_time"] = strtotime( substr( $registro["inicio_ano_letivo"], 0, 16 ) );
				$registro["inicio_ano_letivo_br"] = date( "d/m/Y", $registro["inicio_ano_letivo_time"] );

				$registro["termino_ano_letivo_time"] = strtotime( substr( $registro["termino_ano_letivo"], 0, 16 ) );
				$registro["termino_ano_letivo_br"] = date( "d/m/Y", $registro["termino_ano_letivo_time"] );


				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["nm_escola"] = $det_ref_cod_escola["nome"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}
				
				$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
				$inicio_ano = $obj_ano_letivo_modulo->menorData( $this->ano, $this->ref_cod_escola );
				$fim_ano = $obj_ano_letivo_modulo->maiorData( $this->ano, $this->ref_cod_escola );
				$inicio_ano = explode("/",dataFromPgToBr($inicio_ano));
				$fim_ano = explode("/",dataFromPgToBr($fim_ano));

				$obj_calendario = new clsCalendario();
				$obj_calendario->setLargura(600);
				$obj_calendario->permite_trocar_ano = true;
				
				$obj_calendario->setCorDiaSemana(array(0,6),"ROSA");


				$obj_dia_calendario = new clsPmieducarCalendarioDia($registro["cod_calendario_ano_letivo"],$this->mes,null,null,null,null,null);
				$lista_dia = $obj_dia_calendario->lista($registro["cod_calendario_ano_letivo"],$this->mes,null,null,null,null);
				if($lista_dia){
					$array_dias = array();
					$array_descricao = array();
					foreach ($lista_dia as $dia)
					{
						$descricao = "";


							$botao_editar = "<div style=' z-index: 0;'>
							<Br />
							<input type=\"button\" value=\"Anota&ccedil;&otilde;es\" onclick=\"window.location='educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo={$registro["cod_calendario_ano_letivo"]}&ref_cod_escola={$this->ref_cod_escola}&dia={$dia['dia']}&mes={$dia['mes']}&ano={$this->ano}';\" class=\"botaolistagem\"/>
							</div>";

						if($dia['ref_cod_calendario_dia_motivo']){
							$array_dias[$dia['dia']] = $dia['dia'];
							$obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
							$det_motivo = $obj_motivo->detalhe();
							$tipo = strtoupper($det_motivo['tipo']) == 'E' ? "Dia Extra-Letivo" : "Dia N&atilde;o Letivo";


							$descricao = "<div style=' z-index: 0;'>{$tipo}</div><div align='left' style=' z-index: 0;'>Motivo: {$det_motivo['nm_motivo']}<br />Descri&ccedil;&atilde;o: {$dia['descricao']}</div>{$botao_editar}";
							$array_descricao[$dia['dia']] = $descricao;
							if(strtoupper($det_motivo['tipo']) == 'E')
							{
								$obj_calendario->adicionarLegenda("Extra Letivo","LARANJA_ESCURO");
								$obj_calendario->adicionarArrayDias("Extra Letivo",array($dia['dia']));
							}
							elseif(strtoupper($det_motivo['tipo']) == 'N'){
								$obj_calendario->adicionarLegenda("N&atilde;o Letivo","#VERDE_ESCURO");
								$obj_calendario->adicionarArrayDias("N&atilde;o Letivo",array($dia['dia']));

								$descricao = "<div style=' z-index: 0;'>Descri&ccedil;&atilde;o: {$dia['descricao']}</div>{$botao_editar}";
								$array_descricao[$dia['dia']] = $descricao;
								$obj_calendario->diaDescricao($array_dias,$array_descricao);
							}
						}elseif($dia['descricao']){
							$array_dias[$dia['dia']] = $dia['dia'];
							$descricao = "<div style=' z-index: 0;'>Descri&ccedil;&atilde;o: {$dia['descricao']}</div>{$botao_editar}";
							$array_descricao[$dia['dia']] = $descricao;

						}
					}
					if(!empty($array_dias))
						$obj_calendario->diaDescricao($array_dias,$array_descricao);



				}
				if($this->mes <= (int)$inicio_ano[1] && $this->ano == (int)$inicio_ano[2] ){

					if($this->mes == (int)$inicio_ano[1] ){
						$obj_calendario->adicionarLegenda("Inicio Ano Letivo","AMARELO");
						$obj_calendario->adicionarArrayDias("Inicio Ano Letivo",array($inicio_ano[0]));
					}

				 	 $dia_inicio = (int)$inicio_ano[0];
					 $dias = array();
					 if($this->mes < (int)$inicio_ano[1]){

					 	$NumeroDiasMes = (int) date('t',$this->mes);

						 for ($d = 1 ; $d <= $NumeroDiasMes; $d++)
						 {
							$dias[] = $d;
						 }
						 $obj_calendario->setLegendaPadrao("N&atilde;o Letivo");

						 if(!empty($dias)){
						 	$obj_calendario->adicionarArrayDias("N&atilde;o Letivo",$dias);

						 }
					 }else
					 {
					 	 $dia_inicio;
						 for ($d = 1 ; $d < $dia_inicio ; $d++)
						 {
							$dias[] = $d;
						 }
						 $obj_calendario->setLegendaPadrao("Dias Letivos","AZUL_CLARO");
						 if(!empty($dias)){
							$obj_calendario->adicionarLegenda("N&atilde;o Letivo","#F7F7F7");
						 	$obj_calendario->adicionarArrayDias("N&atilde;o Letivo",$dias);

						 }
					 }

				}elseif($this->mes >= (int)$fim_ano[1] && $this->ano == (int)$fim_ano[2] ){


				 	 $dia_inicio = (int)$fim_ano[0];
					 $dias = array();
					 if($this->mes > (int)$fim_ano[1]){

					 	$NumeroDiasMes = (int) date('t',$this->mes);

						 for ($d = 1 ; $d <= $NumeroDiasMes; $d++)
						 {
							$dias[] = $d;
						 }
						 $obj_calendario->setLegendaPadrao("N&atilde;o Letivo");

						 if(!empty($dias)){
						 	$obj_calendario->adicionarArrayDias("N&atilde;o Letivo",$dias);

						 }
					 }else
					 {
					 	 $NumeroDiasMes = (int) date('t',$this->mes);
						 for ($d = $fim_ano[0] ; $d <= $NumeroDiasMes; $d++)
						 {
							$dias[] = $d;
						 }
						 $obj_calendario->setLegendaPadrao("Dias Letivos","AZUL_CLARO");
						 if(!empty($dias)){
							$obj_calendario->adicionarLegenda("N&atilde;o Letivo","#F7F7F7");
						 	$obj_calendario->adicionarArrayDias("N&atilde;o Letivo",$dias);

						 }
					 }

					if($this->mes == (int)$fim_ano[1] ){
						$obj_calendario->adicionarLegenda("Termino Ano Letivo","AMARELO");
						$obj_calendario->adicionarArrayDias("Termino Ano Letivo",array($fim_ano[0]));
					}

				}
				else{
					 $obj_calendario->setLegendaPadrao("Dias Letivos","AZUL_CLARO");
				}


				$obj_calendario->setCorDiaSemana(array(0,6),"ROSA");

				$obj_anotacao = new clsPmieducarCalendarioDiaAnotacao();
				$lista_anotacoes = $obj_anotacao->lista(null,$this->mes,$registro['cod_calendario_ano_letivo'],null,1);
				if($lista_anotacoes)
				{
					$dia_anotacao = array();
					foreach ($lista_anotacoes as $anotacao)
					{
						if($this->mes == (int)$anotacao['ref_mes'])
							$dia_anotacao[$anotacao['ref_dia']] = $anotacao['ref_dia'];

					}

					$obj_calendario->adicionarIconeDias($dia_anotacao,'A');
				}
				$obj_calendario->all_days_url = "educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo={$registro["cod_calendario_ano_letivo"]}";
				$calendario = $obj_calendario->getCalendario($this->mes,$registro["ano"],"mes_corrente",$_GET);
				$retorno .= "<tr><td colspan='2'><center><b>{$registro["nm_escola"]}</b>$calendario</center></td></tr>";



			}
		}
			if( $obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7 ) )
			{

				if($_POST && empty($lista) && $_SESSION['calendario']['ultimo_valido']){

					$bt_voltar = "<input type=\"button\" value=\"Voltar\" onclick=\"window.location='educar_calendario_ano_letivo_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ano={$_SESSION["calendario"]["ano"]}';\" class=\"botaolistagem\"/>";
				}
				$retorno .= "<tr><td>&nbsp;</td></tr><tr>
							<td align=\"center\" colspan=\"2\">
							{$bt_voltar}
							<input type=\"button\" value=\"Novo Calend&aacute;rio Letivo\" onclick=\"window.location='educar_calendario_ano_letivo_cad.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}';\" class=\"botaolistagem\"/>
							</td>
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
