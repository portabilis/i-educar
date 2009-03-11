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
require_once ("include/Geral.inc.php");


class clsEndereco
{
	var $idpes;
	var $tipo;
	var $idtlog;
	var $logradouro;
	var $idlog;
	var $numero;
	var $letra;
	var $complemento;
	var $bairro;
	var $idbai;
	var $cep;
	var $cidade;
	var $idmun;
	var $sigla_uf;
	var $reside_desde;
	var $bloco;
	var $apartamento;
	var $andar;

	function clsEndereco($idpes=false)
	{
		$this->idpes = $idpes;
	}
	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cep, idlog, numero, letra, complemento, idbai, bloco, andar, apartamento, logradouro, bairro, cidade, sigla_uf, idtlog FROM cadastro.v_endereco WHERE idpes = '{$this->idpes}'");
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$this->bairro = $tupla['bairro'];
				$this->idbai = $tupla['idbai'];
				$this->cidade = $tupla['cidade'];
				$this->sigla_uf = $tupla['sigla_uf'];
				$this->complemento = $tupla['complemento'];
				$this->bloco = $tupla['bloco'];
				$this->apartamento = $tupla['apartamento'];
				$this->andar = $tupla['andar'];
				$this->letra = $tupla['letra'];
				$this->numero = $tupla['numero'];
				$this->logradouro = $tupla['logradouro'];
				$this->idlog =  $tupla['idlog'];
				$this->idtlog = $tupla['idtlog'];
				$this->cep = $tupla['cep'];
				return $tupla;

			}
		
		}
		return false;
	}

	function edita()
	{

	}
}
?>