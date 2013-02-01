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

class clsPessoaJuridica extends clsPessoaFj
{
	var $idpes;
	var $cnpj;
	var $fantasia;
	var $insc_estadual;
	var $capital_social;
	var $banco = 'gestao_homolog';
	var $schema_cadastro = "cadastro";

	function  clsPessoaJuridica($int_idpes = false, $numeric_cnpj = false, $str_fantasia = false, $numeric_insc_estadual=false, $numeric_capital_social = false )
	{
		$this->idpes = $int_idpes;
		$this->cnpj = $numeric_cnpj;
		$this->fantasia = $str_fantasia;
		$this->insc_estadual = $numeric_insc_estadual;
		$this->capital_social = $numeric_capital_social;
	}


	function lista($numeric_cnpj =false, $str_fantasia = false, $numeric_insc_estadual=false, $inicio_limit=false, $fim_limite = false, $str_ordenacao = false,$arrayint_idisin = false, $arrayint_idnotin = false, $int_idpes = false )
	{

		$objJuridica = new clsJuridica();
		$lista = $objJuridica->lista( $str_fantasia,false,$numeric_cnpj,$str_ordenacao,$inicio_limit,$fim_limite, $arrayint_idisin,$arrayint_idnotin, $int_idpes );
		if($lista)
		{
			foreach ($lista as $linha)
			{

			$this->fantasia = $linha['fantasia'];
			$this->cnpj = $linha['cnpj'];
			$this->insc_estadual = $linha['insc_estadual'];
			$this->capital_social = $linha['capital_social'];
			$this->idpes = $linha['idpes'];
			$this->total = $linha['total'];
			$tupla = parent::detalhe();
			$tupla['insc_estadual'] = $this->insc_estadual;
			$tupla[] = &$tupla['insc_estadual'];
			$tupla['capital_social'] = $this->capital_social;
			$tupla[] = &$tupla['capital_social'];
			$tupla['cnpj'] = $this->cnpj;
			$tupla[] = &$tupla['cnpj'];
			$tupla['fantasia'] = $this->fantasia;
			$tupla[] = &$tupla['fantasia'];
			$tupla['total'] = $this->total;
			$tupla[] = &$tupla['total'];

			$resultado[] = $tupla;

			}


		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
	}


	function detalhe()
	{
		if($this->idpes)
		{
			$tupla = parent::detalhe();

			$objJuridica = new clsJuridica($this->idpes);
			$detalhe = $objJuridica->detalhe();
			if($detalhe)
			{
				$this->fantasia = $detalhe['fantasia'];
				$this->cnpj = $detalhe['cnpj'];
				$this->insc_estadual = $detalhe['insc_estadual'];
				$this->capital_social = $detalhe['capital_social'];

				$tupla['insc_estadual'] = $this->insc_estadual;
				$tupla[] = &$tupla['insc_estadual'];
				$tupla['capital_social'] = $this->capital_social;
				$tupla[] = &$tupla['capital_social'];
				$tupla['cnpj'] = $this->cnpj;
				$tupla[] = &$tupla['cnpj'];
				$tupla['fantasia'] = $this->fantasia;
				$tupla[] = &$tupla['fantasia'];
			}
			return $tupla;
		}
		elseif($this->cnpj)
		{
			$objJuridica = new clsJuridica(false,$this->cnpj);
			$detalhe = $objJuridica->detalhe();
			if($detalhe)
			{
				$this->fantasia = $detalhe['fantasia'];
				$this->cnpj = $detalhe['cnpj'];
				$this->insc_estadual = $detalhe['insc_estadual'];
				$this->capital_social = $detalhe['capital_social'];
				$this->idpes = $detalhe['idpes'];
				$tupla = parent::detalhe();
				$tupla['insc_estadual'] = $this->insc_estadual;
				$tupla[] = &$tupla['insc_estadual'];
				$tupla['capital_social'] = $this->capital_social;
				$tupla[] = &$tupla['capital_social'];

				$tupla['cnpj'] = $this->cnpj;
				$tupla[] = &$tupla['cnpj'];
				$tupla['fantasia'] = $this->fantasia;
				$tupla[] = &$tupla['fantasia'];

			}
			return $tupla;
		}


		return false;
	}

	function queryRapida($int_idpes)
	{
		$this->idpes = $int_idpes;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ ) {
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] =  &$resultado[$pos];
			$pos++;
		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function queryRapidaCNPJ($int_CNPJ)
	{
		$this->cnpj = $int_CNPJ;
		$this->detalhe();
		$resultado = array();
		$pos = 0;
		for ($i = 1; $i< func_num_args(); $i++ )
		{
			$campo = func_get_arg($i);
			$resultado[$pos] = ($this->$campo) ? $this->$campo : "";
			$resultado[$campo] =  &$resultado[$pos];
			$pos++;
		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}
}
?>
