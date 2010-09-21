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
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Finalizar" );
		$this->processoAp = "294";
	}
}

class indice
{

	function RenderHTML()
	{
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$cod_grupo = $_GET['cod_grupo'];
		$cod_reuniao = $_GET['cod_reuniao'];
		
		$data = date("Y-m-d H:i:s", time());
		
		$obj = new clsParticipante();
		$lista_participantes = $obj->lista(false,false,$cod_reuniao);
		if($lista_participantes)
		{
			foreach ($lista_participantes as $participantes) {
				if(!$participantes['data_saida'])
				{
					$data_saida = date("Y-m-d H:i:s",time());
					$obj = new clsParticipante($participantes['ref_ref_idpes'],$participantes['ref_ref_cod_grupos'],$participantes['ref_cod_reuniao'],$participantes['sequencial'],false,$data_saida);
					$obj->edita();
				}
			}
		}
		$obj = new clsReuniao($cod_reuniao,false,false,false,false,false,false,false,$data);
		$obj->edita();
		
		header("Location: otopic_reunioes_det.php?cod_reuniao=$cod_reuniao&cod_grupo=$cod_grupo");
		die();
	}
}



$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>