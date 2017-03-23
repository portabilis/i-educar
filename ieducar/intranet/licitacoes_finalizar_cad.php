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
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Licita&ccedil;&otilde;es!" );
		$this->processoAp = "136";
	}
}

class indice extends clsCadastro
{
	var $id_licitacao;
	var $ref_cod_compras_final_pregao;
	var $modalidade;
	var $objeto;
	var $data;
	var $hora;
	var $numero;
	var $ref_pregoeiro;
	var $ref_equipe1;
	var $ref_equipe2;
	var $ref_equipe3;
	var $ano_processo;
	var $mes_processo;
	var $seq_processo;
	var $seq_portaria;
	var $ano_portaria;
	var $valor_referencia;
	var $valor_real;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		
		if (@$_GET['id_licitacao'])
		{
			$this->id_licitacao = @$_GET['id_licitacao'];
			$db = new clsBanco();
			$db->Consulta( "SELECT valor_real, valor_referencia, ref_cod_compras_final_pregao FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->valor_real, $this->valor_referencia, $this->ref_cod_compras_final_pregao ) = $db->Tupla();
			}
			else 
			{
				$this->mensagem = "Esta solicitação não pode ser fechada (defina a equipe primeiro)";
			}
			$retorno = "Editar";
		}
		else 
		{
			$this->mensagem = "Nenhuma licitação selecionada";
		}
		if( isset( $_POST["id_licitacao"] ) )
		{
			$this->id_licitacao = $_POST["id_licitacao"];
			$this->valor_real = $_POST["valor_real"];
			$this->valor_referencia = $_POST["valor_referencia"];
			$this->ref_cod_compras_final_pregao = $_POST["ref_cod_compras_final_pregao"];
		}
		
		if($this->id_licitacao)
		{
			$objPessoa = new clsPessoaFisica();
			$db = new clsBanco();
			$db->Consulta( "SELECT l.ref_ref_cod_pessoa_fj, m.nm_modalidade, l.numero, l.objeto, l.data_hora, exec.ano_processo,exec.mes_processo, exec.seq_processo, exec.seq_portaria, exec.ano_portaria, exec.valor_referencia, exec.valor_real, ref_pregoeiro, ref_equipe1, ref_equipe2, ref_equipe3 FROM compras_licitacoes l, compras_modalidade m, compras_pregao_execucao exec WHERE m.cod_compras_modalidade=l.ref_cod_compras_modalidade AND cod_compras_licitacoes={$this->id_licitacao} AND l.cod_compras_licitacoes=exec.ref_cod_compras_licitacoes" );
			if ($db->ProximoRegistro())
			{
				list ($cod_pessoa, $this->modalidade,$this->numero, $this->objeto, $this->data, $this->ano_processo,$this->mes_processo,$this->seq_processo,$this->seq_portaria,$this->ano_portaria,$this->valor_referencia,$this->valor_real, $this->ref_pregoeiro, $this->ref_equipe1,$this->ref_equipe2,$this->ref_equipe3) = $db->Tupla();
				
				$this->hora= date('H:i', strtotime(substr($this->hora,0,19)));
				$this->data= date('d/m/Y', strtotime(substr($this->data,0,19)));

			
				list( $this->ref_pregoeiro ) = $objPessoa->queryRapida($this->ref_pregoeiro, "nome");
			
				list( $this->ref_equipe1 ) = $objPessoa->queryRapida($this->ref_equipe1, "nome");
			
				list( $this->ref_equipe2 ) = $objPessoa->queryRapida($this->ref_equipe2, "nome");
			
				list( $this->ref_equipe3 ) = $objPessoa->queryRapida($this->ref_equipe3, "nome");
			}
		}
			
		$this->url_cancelar = "licitacoes_finalizar_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_licitacao", $this->id_licitacao );
		if( $this->id_licitacao )
		{
			$this->campoRotulo("modalidade","Modalidade",$this->modalidade." ".$this->numero);
			$this->campoRotulo("objeto","Objeto",$this->objeto);
			$this->campoRotulo("data","Data",$this->data);
			$this->campoRotulo("hora","Hora",$this->hora);
			$this->campoRotulo("pregoeiro","Pregoeiro",$this->ref_pregoeiro);
			$this->campoRotulo("equipe1","Equipe 1",$this->ref_equipe1);
			$this->campoRotulo("equipe2","Equipe 2",$this->ref_equipe2);
			$this->campoRotulo("equipe3","Equipe 3	",$this->ref_equipe3);
			$this->campoRotulo("ano","Ano do Processo",$this->ano_processo);
			$this->campoRotulo("mes","Mes do Processo",$this->mes_processo);
			$this->campoRotulo("seq_proce","Sequencial do Processo",$this->seq_processo);
			$this->campoRotulo("seq_port","Sequencial da Portaria",$this->seq_portaria);
			$this->campoRotulo("ano_portaria","Ano da Portaria",$this->ano_portaria);
			$this->campoRotulo("valor_ref","Valor Referencia", number_format($this->valor_referencia,2,",","" ) );
			$this->campoRotulo("valor_real","Valor Real",number_format($this->valor_real,2,",","" ) );
			
			$this->campoTexto( "valor_real", "Valor Real", $this->valor_real, 30, 30, true );
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_compras_final_pregao, nm_final FROM compras_final_pregao" );
			$opcoes= array( "Selecione" );
			while ( $db->ProximoRegistro() )
			{
				list( $cod, $nome ) = $db->Tupla();
				$opcoes[$cod] = $nome;
			}
			$this->campoLista( "ref_cod_compras_final_pregao", "Status final", $opcoes, $this->ref_cod_compras_final_pregao );
		}
	}

	function Novo() 
	{
		return false;
	}

	function Editar() 
	{
		$this->valor_real = str_replace(",",".",$this->valor_real);
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
		if( $db->Num_Linhas() )
		{
			$db->Consulta( "UPDATE compras_pregao_execucao SET valor_real = '{$this->valor_real}', ref_cod_compras_final_pregao = '{$this->ref_cod_compras_final_pregao}' WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
			echo "<script>document.location='licitacoes_finalizar_lst.php';</script>";
			return true;
		}
		else 
		{
			$this->mensagem = "Não foi possível realizar a edição";
		}
	}

	function Excluir()
	{
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
