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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/clsListagem.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Reuniões!" );
		$this->processoAp = "294";
	}
}

class indice extends clsCadastro
{
	var $codGrupo;
	var $codReuniao;
	var $data_inicial;
	var $hora_inicial;
	var $data_final;
	var $hora_final;
	var $listaTopicoReuniao;
	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		$this->codGrupo = $_GET['cod_grupo'];
		$this->codReuniao = $_GET['cod_reuniao'];
		
		$obj = new clsReuniao($this->codReuniao);
		if(!$obj->detalhe())
		{
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->codGrupo");
		}
		
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "codGrupo", $this->codGrupo );
		$this->campoOculto( "codReuniao", $this->codReuniao );
	 	
		$this->campoRotulo("dadosDaAudiencia","<b>Dados da Reunião</b>","");
		
		$this->campoData("data_inicial","Data Inicial", "", true);
		$this->campoHora("hora_inicial", "Hora inicial","", true);
		$this->campoData("data_final","Data Final", "", true);
		$this->campoHora("hora_final", "Hora final","", true);		

		$emailEnviado = ($detReuniao['email_enviado']) ? "Sim" : "Não";
		$this->campoRotulo("notificarPorEmail","Notificado por e-mail", $emailEnviado);
		
		//Participantes
		$this->campoRotulo("MembosPresentes","<b>Participantes</b>", "");
		$objGrupoModerador = new clsGrupoModerador();
   		$ListaGrupoModerador = $objGrupoModerador->lista(false, $this->codGrupo);
   		/*echo "<pre>";
   		print_r($ListaGrupoModerador);
   		die();*/
   		foreach ($ListaGrupoModerador AS $pessoa)
		{
			$objPessoaFisica = new clsPessoaFisica($pessoa['ref_ref_cod_pessoa_fj']);
			$detPessoaFisica = $objPessoaFisica->detalhe();
			$this->campoCheck("pes_{$pessoa['ref_ref_cod_pessoa_fj']}",$detPessoaFisica['nome'], "");	
		}
		
		$objGrupoPessoa = new clsGrupoPessoa();
		$listaGrupoPessoa = $objGrupoPessoa->lista(false, $this->codGrupo);
		if($listaGrupoPessoa)
		{
			foreach ($listaGrupoPessoa AS $pessoa)
			{
				$objPessoaFisica = new clsPessoaFisica($pessoa['ref_idpes']);
				$detPessoaFisica = $objPessoaFisica->detalhe();
				$this->campoCheck("pes_{$pessoa['ref_idpes']}",$detPessoaFisica['nome'], "");	
			}
		}
		$this->campoRotulo("topicosRelacionados","<b>Tópicos Relacionados</b>","");

		$obj = new clsTopicoReuniao();
		$this->listaTopicoReuniao = $obj->lista(false,false,false,false,false,false,false,$this->codReuniao);

		foreach ($this->listaTopicoReuniao as $topicos)
		{
			$obj = new clsTopico($topicos['ref_cod_topico']);
			$detalhe_topico = $obj->detalhe();
			$this->campoRotulo("assunto_{$topicos['ref_cod_topico']}","Assunto","{$detalhe_topico['assunto']}");
			$this->campoMemo("par_{$topicos['ref_cod_topico']}","Parecer",$topicos['parecer'],50,2);
			$this->campoCheck("fin_{$topicos['ref_cod_topico']}","Finalizado",$topicos['finalizado']);
			
			$obj = new clsTopicoReuniao();
			$lista_topico_reuniao = $obj->lista(false,false,false,false,false,false,false,false,$topicos['ref_cod_topico']);
			if(count($lista_topico_reuniao)> 1 && is_array($lista_topico_reuniao) )
			{
				foreach ($lista_topico_reuniao as $parecer) {
					
					if($parecer['parecer'] && $parecer['ref_cod_reuniao'] != $cod_reuniao)
					{
						$this->campoRotulo("outrosPareceres_{$parecer["data_parecer"]}","Outros Pareceres", $parecer['parecer']);
					}
				}
				
			}
			
		}
		
		$obj_moderador = new clsGrupoModerador($id_visualiza,$cod_grupo);
		$detalhe_moderador = $obj_moderador->detalhe();
		if( $detalhe_moderador && $detalhe_moderador['ativo'] == 1 && !$detalhe['data_inicio_real'] && !$detalhe['data_fim_real'] )
		{
			$this->url_novo = "otopic_reunioes_cad.php?cod_grupo=$cod_grupo";
			$this->url_editar = "otopic_reunioes_cad.php?cod_grupo=$cod_grupo&cod_reuniao=$cod_reuniao";
		}
		$this->url_cancelar = "otopic_meus_grupos_det2.php?cod_grupo=$cod_grupo";
	}
	
	
	function Novo() 
	{
		$inicio = explode( "/", $this->data_inicial  );
		$inicio = "{$inicio[2]}-{$inicio[1]}-{$inicio[0]}";
		$inicio .= " ".date( "H:i:s", strtotime( $this->hora_inicial ) );
		$fim = explode( "/", $this->data_final  );
		$fim = "{$fim[2]}-{$fim[1]}-{$fim[0]}";
		$fim .= " ".date( "H:i:s", strtotime( $this->hora_final ) );
		
		$objReuniao = new clsReuniao($this->codReuniao, false, false, false, false, false, false, $inicio, $fim );
		$objReuniao->edita();
		
		foreach ($_POST as $id=>$campo) 
		{
			if(substr($id,0,4) == "par_")
			{
				$cod = substr($id,4); 
				$finalizado = $_POST["fin_$cod"] ? "1" : "";
				$obj = new clsTopicoReuniao($cod,$this->codReuniao,$campo,$finalizado);
				$obj->edita();
			}
			
			if(substr($id,0,4) == "pes_")
			{
				$cod = substr($id,4);
				$objParticipante = new clsParticipante($cod, $this->codGrupo, $this->codReuniao, false, $inicio, $fim);
				$objParticipante->cadastra();
			}
		}
        header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->codGrupo");
	}
}
$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>