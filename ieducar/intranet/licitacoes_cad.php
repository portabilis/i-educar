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
		$this->processoAp = "29";
	}
}

class indice extends clsCadastro
{
	var $id_licitacao;
	var $id_modalidade;
	var $id_pessoa;
	var $numero;
	var $objeto;
	var $data_c;
	var $hora;
	var $cod_licitacao_semasa;
	var $oculto;

	var $nome_;
	var $sobrenome;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";

		if (@$_GET['id_licitacao'])
		{
			$retorno = "Editar";
			$this->id_licitacao = @$_GET['id_licitacao'];
			$db = new clsBanco();
			$db->Consulta( "SELECT l.cod_compras_licitacoes, l.ref_cod_compras_modalidade, l.ref_ref_cod_pessoa_fj, l.numero, l.objeto, l.data_hora, l.cod_licitacao_semasa, l.oculto FROM compras_licitacoes l WHERE l.cod_compras_licitacoes={$this->id_licitacao}" );
			if ($db->ProximoRegistro())
			{
				list($this->id_licitacao, $this->id_modalidade, $this->id_pessoa, $this->numero, $this->objeto, $this->data_c, $this->cod_licitacao_semasa, $this->oculto) = $db->Tupla();
				$this->hora = date('H:i', strtotime(substr($this->data_c ,0,19)));
				$this->data_c = date('d/m/Y', strtotime(substr($this->data_c,0,19) ));

				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "licitacoes_det.php?id_licitacao=$this->id_licitacao" : "licitacoes_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_licitacao", $this->id_licitacao );
		$this->campoOculto( "id_pessoa", $this->id_pessoa );

		$objPessoa = new clsPessoaFisica();
		$db = new clsBanco();
		list($nome_) = $objPessoa->queryRapida($this->id_pessoa, "nome");
		$this->campoRotulo( "pessoa", "Responsável", $nome_);

		$lista = array();
		$db = new clsBanco();
		$db->Consulta( "SELECT cod_compras_modalidade, nm_modalidade FROM compras_modalidade" );
		while ($db->ProximoRegistro())
		{
			list($id, $nm) = $db->Tupla();
			$lista[$id] = $nm;
		}
		$this->id_modalidade = empty($this->id_modalidade) ? 2 : $this->id_modalidade;
		$this->campoLista("id_modalidade", "Modalidade",  $lista, $this->id_modalidade);

		$this->campoTexto( "numero", "Numero",  $this->numero, "8", "30", true );
		$this->campoMemo( "objeto", "Objeto",  $this->objeto, "50", "8", true );
		$this->campoData( "data_c", "Data",  $this->data_c, true );
		$this->campoHora( "hora", "Hora",  $this->hora, true, "", "", "hh:mm" );
		if($this->oculto == 'f')
		{
			$this->oculto = "";
		}
		$this->campoCheck("oculto","Ocultar",$this->oculto);
		$this->campoNumero( "cod_licitacao_semasa", "Número Licitação Semasa",  $this->cod_licitacao_semasa, 6, 20, false);

	}

	function Novo()
	{

		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$this->data_c = str_replace("%2F", "/", $this->data_c);

		if (empty($this->data_c) || empty($this->id_modalidade) || empty($this->numero) || empty($this->objeto) || empty($this->hora))
		{
			return false;
		}
		else
		{
			$db = new clsBanco();
			$data = explode("/",  $this->data_c);
			$this->data_c = "{$data[2]}-{$data[1]}-{$data[0]}";
			$campos  = "";
			$valores = "";

			if($this->cod_licitacao_semasa)
			{
				$campos = ",cod_licitacao_semasa";
				$valores = ",'$this->cod_licitacao_semasa'";
			}
			if($this->oculto == "on")
			{
				$campos = ",oculto";
				$valores = ",'true'";
			}

			$db->Consulta( "INSERT INTO compras_licitacoes (ref_cod_compras_modalidade, ref_ref_cod_pessoa_fj, numero, objeto, data_hora $campos) VALUES ({$this->id_modalidade}, {$this->id_pessoa}, '{$this->numero}', '{$this->objeto}', '{$this->data_c} {$this->hora}:00' $valores)" );


			echo "<script>document.location='licitacoes_lst.php';</script>";

			return true;
		}

	}

	function Editar()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$this->data_c = str_replace("%2F", "/", $this->data_c);

		if (empty($this->data_c) || empty($this->id_licitacao) || empty($this->id_modalidade) || empty($this->numero) || empty($this->objeto) || empty($this->hora))
		{
			return false;
		}
		else
		{
			$db = new clsBanco();
			$set = "";
			if($this->cod_licitacao_semasa)
			{
				$set = ",cod_licitacao_semasa = '$this->cod_licitacao_semasa'";
			}else
			{
				$set = ",cod_licitacao_semasa = null ";
			}
			if($this->oculto == "on")
			{
				$set = ",oculto = 'true' ";
			}else
			{
				$set = ",oculto = 'false' ";
			}
			$data = explode("/",  $this->data_c);
			$this->data_c = "{$data[2]}-{$data[1]}-{$data[0]}";
			$db->Consulta( "UPDATE compras_licitacoes SET ref_cod_compras_modalidade={$this->id_modalidade}, numero='{$this->numero}', objeto='{$this->objeto}', data_hora='{$this->data_c} {$this->hora}:00' $set WHERE cod_compras_licitacoes = {$this->id_licitacao} " );

			echo "<script>document.location='licitacoes_lst.php';</script>";

			return true;
		}
	}

	function Excluir()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		if (empty($this->id_pessoa) || empty($this->id_licitacao))
		{
			return false;
		}
		else
		{
			$db = new clsBanco();

			$db->Consulta( "SELECT 1 FROM compras_editais_editais WHERE ref_cod_compras_licitacoes = '{$this->id_licitacao}'" );
			if( ! $db->Num_Linhas() )
			{
				$db->Consulta( "DELETE FROM compras_licitacoes WHERE cod_compras_licitacoes=$this->id_licitacao AND ref_ref_cod_pessoa_fj = $this->id_pessoa" );
				header( "location: licitacoes_lst.php" );
				die();
				return true;
			}
			else
			{
				$this->mensagem = "Impossivel deletar licita&ccedil;&atilde;o.<br>Esta licita&ccedil;&atilde;o j&aacute; possui um edital publicado.";
			}
		}
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
