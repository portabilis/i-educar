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
		$this->processoAp = "135";
	}
}

class indice extends clsCadastro
{
	var $id_licitacao;
	var $pregoeiro;
	var $equipe1;
	var $equipe2;
	var $equipe3;
	var $ano_processo;
	var $mes_processo;
	var $seq_processo;
	var $seq_portaria;
	var $processo;
	var $portaria;
	var $valor_referencia;
	var $ano_portaria;
	var $pagina_anterior;
	var $nm, $objeto, $data, $hora, $numero;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		
		$this->pagina_anterior = $_SERVER["HTTP_REFERER"];
		
		if (@$_GET['id_licitacao'])
		{
			$this->id_licitacao = @$_GET['id_licitacao'];
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_pregoeiro, ref_equipe1, ref_equipe2, ref_equipe3, ano_processo, mes_processo, seq_processo, seq_portaria, ano_portaria, valor_referencia FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->pregoeiro, $this->equipe1, $this->equipe2, $this->equipe3, $this->ano_processo, $this->mes_processo, $this->seq_processo, $this->seq_portaria, $this->ano_portaria, $this->valor_referencia ) = $db->Tupla();
			}
			$retorno = "Editar";
			$this->fexcluir = true;
			$objPessoa = new clsPessoaFisica();
			$db = new clsBanco();
			$db->Consulta( "SELECT m.nm_modalidade, l.numero, l.objeto, l.data_hora  FROM compras_licitacoes l, compras_modalidade m WHERE m.cod_compras_modalidade=l.ref_cod_compras_modalidade AND cod_compras_licitacoes={$this->id_licitacao}" );
			if ($db->ProximoRegistro())
			{
				list ($this->nm,$this->numero, $this->objeto, $this->data, $this->hora) = $db->Tupla();
				$this->hora= date('H:i', strtotime(substr($this->data,0,19)));
				$this->data = date('d/m/Y', strtotime(substr($this->data,0,19)));
			}
		}
		else 
		{
			$this->mensagem = "Nenhuma licitação selecionada";
		}

		$this->url_cancelar = "$this->pagina_anterior";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_licitacao", $this->id_licitacao );
		if( $this->id_licitacao )
		{
			$objPessoas = new clsPessoaFisica();
			$db = new clsBanco();
			
			$db->Consulta( "SELECT compras.ref_ref_cod_pessoa_fj FROM compras_funcionarios compras " );
			$opcoes = array();
			$opcoes[""] = "Selecione";
			while( $db->ProximoRegistro() )
			{
				list( $cod ) = $db->Tupla();
				list( $nome ) = $objPessoas->queryRapida($cod, "nome");
				$opcoes[$cod] = $nome;
			}
			$this->campoRotulo("Modalidade","Modalidade" ,$this->nm." ".$this->numero  );
			$this->campoRotulo("Objeto","Objeto" ,$this->objeto );
			$this->campoRotulo("Data","Data", $this->data );
			$this->campoRotulo("Hora","Hora", $this->hora );
			$this->campoLista( "pregoeiro", "Pregoeiro", $opcoes, $this->pregoeiro );
			$this->campoLista( "equipe1", "Equipe 1", $opcoes, $this->equipe1 );
			$this->campoLista( "equipe2", "Equipe 2", $opcoes, $this->equipe2 );
			$this->campoLista( "equipe3", "Equipe 3", $opcoes, $this->equipe3 );
			$this->campoTexto( "mes_processo", "Mes do Processo", $this->mes_processo, 14, 14, true );
			$this->campoTexto( "ano_processo", "Ano do Processo", $this->ano_processo, 14, 14, true );
			$this->campoTexto( "processo", "Sequencial do Processo", $this->seq_processo, 14, 14, true );
			$this->campoTexto( "portaria", "Sequencial da Portaria", $this->seq_portaria, 14, 14);
			$this->campoTexto( "ano_portaria", "Ano da Portaria", $this->ano_portaria, 14, 14 );
			
			$this->campoTexto( "valor_referencia", "Valor de Referencia", str_replace(".",",",$this->valor_referencia ), 14, 14);
		}

		$this->campoOculto("pagina_anterior",$this->pagina_anterior);
	}

	function Novo() 
	{
		return false;
	}

	function Editar() 
	{
		$this->pagina_anterior = urldecode($this->pagina_anterior);
		if( isset( $_POST["id_licitacao"] ) )
		{
			$msg = "";
			if( ! is_numeric( $_POST["mes_processo"] ) )
			{
				$msg .= "O Mês de processo deve ser um valor numérico<br>";
			}
			if( ! is_numeric( $_POST["ano_processo"] ) )
			{
				$msg .= "O Ano de processo deve ser um valor numérico<br>";
			}
			if( ! is_numeric( $_POST["processo"] ) )
			{
				$msg .= "A Sequencia de Processo deve ser um valor numérico<br>";
			}
			if( ! is_numeric( $_POST["portaria"] ) )
			{
				$msg .= "A Sequencia de Portaria deve ser um valor numérico<br>";
			}
			if( ! is_numeric( $_POST["ano_portaria"] ) )
			{
				$msg .= "O Ano da Portaria deve ser um valor numérico<br>";
			}
			if( ! is_numeric( str_replace( ",", "", str_replace( ".", "", $_POST["valor_referencia"] ) ) ) )
			{
				$msg .= "O valor de Referencia deve ser um valor numerico<br>";
			}
			$this->mensagem = $msg;
			$this->id_licitacao = $_POST["id_licitacao"];
			$this->pregoeiro = $_POST["pregoeiro"];
			$this->equipe1 = $_POST["equipe1"];
			$this->equipe2 = $_POST["equipe2"];
			$this->equipe3 = $_POST["equipe3"];
			$this->valor_referencia = str_replace(",",".",$_POST["valor_referencia"]);
			$this->ano_processo = $_POST["ano_processo"];
			$this->mes_processo = $_POST["mes_processo"];
			$this->processo = $_POST["processo"];
			$this->portaria = $_POST["portaria"];
			$this->ano_portaria = $_POST["ano_portaria"];
			$this->seq_processo = $_POST["processo"];
			$this->seq_portaria = $_POST["portaria"];
		}
		
		if( ! $msg )
		{
			if( $this->pregoeiro && $this->equipe1 && $this->equipe2 && $this->equipe3 ) 
			{
				
				$db = new clsBanco();
				$db->Consulta( "SELECT 1 FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
				if( $db->Num_Linhas() )
				{
					$db->Consulta( "UPDATE compras_pregao_execucao SET ref_pregoeiro = '{$this->pregoeiro}', ref_equipe1 = '{$this->equipe1}', ref_equipe2 = '{$this->equipe2}', ref_equipe3 = '{$this->equipe3}', mes_processo = '{$this->mes_processo}', ano_processo = '{$this->ano_processo}', seq_processo = '{$this->seq_processo}', seq_portaria = '{$this->seq_portaria}', ano_portaria='{$this->ano_portaria}', valor_referencia='{$this->valor_referencia}' WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
				}
				else 
				{
					$db->Consulta( "INSERT INTO compras_pregao_execucao ( ref_cod_compras_licitacoes, ref_pregoeiro, ref_equipe1, ref_equipe2, ref_equipe3, mes_processo, ano_processo, seq_processo, seq_portaria, ano_portaria, valor_referencia ) VALUES('{$this->id_licitacao}', '{$this->pregoeiro}', '{$this->equipe1}', '{$this->equipe2}', '{$this->equipe3}', '{$this->mes_processo}', '{$this->ano_processo}', '{$this->seq_processo}', '{$this->seq_portaria}', '{$this->ano_portaria}', '{$this->valor_referencia}')" );
				}
				echo "<script>top.location='$this->pagina_anterior'</script>";
				return true;
			}
			else 
			{
				$this->mensagem = "Você precisa selecionar a equipe completa";
			}
		}
	}

	function Excluir()
	{
		// exclui essas paradas para que ele deixe de ser uma executada
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
		if( $db->Num_Linhas() )
		{
			$db->Consulta( "DELETE FROM compras_pregao_execucao WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
			die( "<script>document.location.href='licitacoes_finalizadas_lst.php';</script>" );
		}
		else 
		{
			$this->mensagem = "Codigo de licitação inválido";
		}
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
