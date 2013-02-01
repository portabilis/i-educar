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
require_once ("include/clsEmail.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Empresas" );
		$this->processoAp = "239";
	}
}

class indice extends clsCadastro
{
	var $cod_empresa;
	var $cnpj;
	var $nm_empresa;
	var $email;
	var $data_hora;
	var $endereco;
	var $ref_sigla_uf;
	var $cidade;
	var $bairro;
	var $telefone;
	var $fax;
	var $cep;
	var $nome_contato;
	var $senha;
	var $re_senha;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";

		if (@$_GET['cod_empresa'])
		{
			$retorno = "Editar";
			$this->cod_empresa = @$_GET['cod_empresa'];
			$db = new clsBanco();
			$db->Consulta( "SELECT cnpj, nm_empresa, email, data_hora, endereco, ref_sigla_uf, cidade, bairro, telefone, fax, cep, nome_contato, senha FROM compras_editais_empresa WHERE cod_compras_editais_empresa = '{$this->cod_empresa}'" );
			if ( $db->ProximoRegistro() )
			{
				list( $this->cnpj, $this->nm_empresa, $this->email, $this->data_hora, $this->endereco, $this->ref_sigla_uf, $this->cidade, $this->bairro, $this->telefone,$this-> fax, $this->cep,$this-> nome_contato, $this->senha ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ( $retorno == "Editar" ) ? "licitacoes_edital_empresa_det.php?cod_empresa=$this->cod_empresa" : "licitacoes_edital_empresa_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_empresa", $this->cod_empresa );
		$this->campoTexto( "nm_empresa", "Nome", $this->nm_empresa, 60, 255, true );
		$this->campoIdFederal("cnpj", "CNPJ", $this->cnpj, true );
		$this->campoTexto("email", "e-mail", $this->email, 60, 255, true );
		$this->campoTexto("endereco", "Endereco", $this->endereco, 60, 255, true );
		$estados = array();
		$db = new clsBanco();
		$db->Consulta( "SELECT sigla_uf, nome FROM public.uf ORDER BY nome ASC" );
		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome ) = $db->Tupla();
			$estados[$cod] = $nome;
		}
		$this->campoLista( "ref_sigla_uf", "Estado", $estados, $this->ref_sigla_uf );
		$this->campoTexto("cidade", "Cidade", $this->cidade, 60, 255, true );
		$this->campoTexto("bairro", "Bairro", $this->bairro, 60, 255, true );
		$this->campoTexto("telefone", "Telefone", $this->telefone, 20, 255, true );
		$this->campoTexto("fax", "Fax", $this->fax, 20, 255, true );
		$this->campoCep("cep", "Cep", number_format( $this->cep / 1000, 3, "-", "" ), true );
		$this->campoTexto("nome_contato", "Nome para contato", $this->nome_contato, 60, 255, true );
		$this->campoSenha("senha", "Senha", $this->senha, true );
		if( ! isset( $this->re_senha ) ) $this->re_senha = $this->senha;
		$this->campoSenha("re_senha", "Repita a Senha", $this->re_senha, true );
	}

	function Novo()
	{
		global $HTTP_POST_FILES;

		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$db = new clsBanco();

		$this->cep = idFederal2int($this->cep);
		
		if( $this->senha == $this->re_senha )
		{
			$db->Consulta( "INSERT INTO compras_editais_empresa( nm_empresa, cnpj, email, data_hora, endereco, ref_sigla_uf, cidade, bairro, telefone, fax, cep, nome_contato, senha ) VALUES( '{$this->nm_empresa}', '{$this->cnpj}', '{$this->email}', NOW(), '{$this->endereco}', '{$this->ref_sigla_uf}', '{$this->cidade}', '{$this->bairro}', '{$this->telefone}','{$this-> fax}', '{$this->cep}','{$this-> nome_contato}', '{$this->senha}' )" );

			$nome = $db->CampoUnico( "SELECT nome FROM cadastro.pessoa WHERE idpes = {$this->id_pessoa}" );
			$data_atual = date( "d/m/Y H:i", time() );
			$conteudo = "Cadastrando empresa_edital:<br>\nUsuario: {$this->id_pessoa} - {$nome}<br>\nhorario: $data_atual<br>\n<br>\n";
			$conteudo .= "Query:<br>\nINSERT INTO compras_editais_empresa( nm_empresa, cnpj, email, data_hora, endereco, ref_sigla_uf, cidade, bairro, telefone, fax, cep, nome_contato, senha ) VALUES( '{$this->nm_empresa}', '{$this->cnpj}', '{$this->email}', NOW(), '{$this->endereco}', '{$this->ref_sigla_uf}', '{$this->cidade}', '{$this->bairro}', '{$this->telefone}','{$this-> fax}', '{$this->cep}','{$this-> nome_contato}', '{$this->senha}' )";

			header( "location: licitacoes_edital_empresa_lst.php" );
			die();
		}
		else
		{
			$this->mensagem = "As senhas n&atilde;o conferem.";
		}
		return false;
	}

	function Editar()
	{
		global $HTTP_POST_FILES;

		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		if( $this->senha == $this->re_senha )
		{
			$db = new clsBanco();
			$cep = str_replace( "-", "", $this->cep );
			$db->Consulta( "UPDATE compras_editais_empresa SET nm_empresa = '{$this->nm_empresa}', cnpj = '{$this->cnpj}', email = '{$this->email}', data_hora = NOW(), endereco = '{$this->endereco}', ref_sigla_uf = '{$this->ref_sigla_uf}', cidade = '{$this->cidade}', bairro = '{$this->bairro}', telefone = '{$this->telefone}', fax = '{$this->fax}', cep = '{$cep}', nome_contato = '{$this->nome_contato}', senha = '{$this->senha}' WHERE cod_compras_editais_empresa='{$this->cod_empresa}'" );

			header( "location: licitacoes_edital_empresa_lst.php" );
			die();
		}
		else
		{
			$this->mensagem = "As senhas n&atilde;o conferem.";
		}
		return false;
	}

	function Excluir()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_editais_editais_empresas WHERE ref_cod_compras_editais_empresa = $this->cod_empresa" );
		if( ! $db->ProximoRegistro() )
		{
			$db->Consulta( "DELETE FROM compras_editais_empresa WHERE cod_compras_editais_empresa=$this->cod_empresa" );
			header( "location: licitacoes_edital_empresa_lst.php" );
			die();
			return true;
		}
		else
		{
			$this->mensagem = "Esta empresa esta associada a logs de downloads e n&atilde;o pode ser deletada.";
		}
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
