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
require_once ("include/clsBanco.inc.php");

class clsAgendaFuncionario
{
	var $cod_funcionario_agenda;
	var $ref_ref_cod_pessoa_fj;
	var $data_inicio;
	var $data_fim;
	var $compromisso;
	var $versao;
	var $ref_cod_funcionario_agenda;
	var $tabela;
	
	function  clsAgendaFuncionario($int_cod_funcionario_agenda=false,$int_ref_ref_cod_pessoa_fj=false,$str_data_inicio=false, $str_data_fim=false, $str_compromisso=false,$int_versao=false,$int_ref_cod_funcionario_agenda=false)
	{
		$this->cod_funcionario_agenda = $int_cod_funcionario_agenda;
		$this->ref_ref_cod_pessoa_fj = $int_ref_ref_cod_pessoa_fj;
		$this->data_inicio = $str_data_inicio;
		$this->data_fim = $str_data_fim;
		$this->compromisso = $str_compromisso;
		$this->versao = $int_versao;
		$this->ref_cod_funcionario_agenda = $int_ref_cod_funcionario_agenda;
		$this->tabela = "funcionario_agenda";
	}

	function cadastra()
	{
		if(is_numeric($this->ref_ref_cod_pessoa_fj) && is_string($this->data_inicio) && is_string($this->compromisso))
		{
			$campos = "";
			$valores = "";
			if(is_string($this->data_fim))
			{
				$campos = ", data_fim";
				$valores = ", '{$this->data_fim}'";
			}
			$db = new clsBanco();
			//die("INSERT INTO funcionario_agenda (ref_ref_cod_pessoa_fj, data_inicio, compromisso, versao $campos) VALUES ('$this->ref_ref_cod_pessoa_fj', '$this->data_inicio', '$this->compromisso', 1 $valores)");
			$db->Consulta("INSERT INTO funcionario_agenda (ref_ref_cod_pessoa_fj, data_inicio, compromisso, versao $campos) VALUES ('$this->ref_ref_cod_pessoa_fj', '$this->data_inicio', '$this->compromisso', 1 $valores)");
			return $db->InsertId("funcionario_agenda_cod_funcionario_agenda_seq");
		}
	}
	
	function detalhe()
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_funcionario_agenda, ref_ref_cod_pessoa_fj, data_inicio, data_fim, compromisso, versao, ref_cod_funcionario_agenda FROM {$this->tabela} WHERE cod_funcionario_agenda = '{$this->cod_funcionario_agenda}'" );
		if( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			return $tupla;
		}
	}


}
?>
