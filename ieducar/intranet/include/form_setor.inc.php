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


	// opcao para selecionar o proximo setor manualmente
	$objSetor = new clsSetor();
	$listaSetores = array( "Selecione" );
	$lista = $objSetor->lista( null, null, null, null, null, null, null, null, null, 1, 0, null, null, "sgl_setor ASC" );
	if( is_array( $lista ) && count( $lista ) )
	{
		foreach ( $lista AS $linha )
		{
			$listaSetores[$linha["cod_setor"]] = $linha["sgl_setor"];
		}
	}
	$this->campoLista( "setor_0", "Setor", $listaSetores, $this->setor_0,  "oproDocumentoNextLvl( this.value, '1' )", true );

	$listaVazia = array( "-----------" );
	if( $this->setor_0 )
	{
		$lista1 = array( "Selecione" );
		$lista = $objSetor->lista( $this->setor_0, null, null, null, null, null, null, null, null, 1, null, null, null, "sgl_setor ASC" );
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $linha )
			{
				$lista1[$linha["cod_setor"]] = $linha["sgl_setor"];
			}
		}
		else
		{
			$lista1 = array( "Nenhum setor filho para este setor" );
		}
	}
	else
	{
		$lista1 = $listaVazia;
	}
	$this->campoLista( "setor_1", "Setor", $lista1, $this->setor_1, "oproDocumentoNextLvl( this.value, '2' )", true, false, false, ! $this->setor_0 );
	if( $this->setor_1 )
	{
		$lista2 = array( "Selecione" );
		$lista = $objSetor->lista( $this->setor_1, null, null, null, null, null, null, null, null, 1, null, null, null, "sgl_setor ASC" );
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $linha )
			{
				$lista2[$linha["cod_setor"]] = $linha["sgl_setor"];
			}
		}
		else
		{
			$lista2 = array( "Nenhum setor filho para este setor" );
		}
	}
	else
	{
		$lista2 = $listaVazia;
	}
	$this->campoLista( "setor_2", "Setor", $lista2, $this->setor_2, "oproDocumentoNextLvl( this.value, '3' )", true, false, false, ! $this->setor_1 );
	if( $this->setor_2 )
	{
		$lista3 = array( "Selecione" );
		$lista = $objSetor->lista( $this->setor_2, null, null, null, null, null, null, null, null, 1, null, null, null, "sgl_setor ASC" );
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $linha )
			{
				$lista3[$linha["cod_setor"]] = $linha["sgl_setor"];
			}
		}
		else
		{
			$lista3 = array( "Nenhum setor filho para este setor" );
		}
	}
	else
	{
		$lista3 = $listaVazia;
	}
	$this->campoLista( "setor_3", "Setor", $listaVazia, $this->setor_3, "oproDocumentoNextLvl( this.value, '4' )", true, false, false, ! $this->setor_2 );
	if( ! $this->setor_3 )
	{
		$lista4 = array( "Selecione" );
		$lista = $objSetor->lista( $this->setor_3, null, null, null, null, null, null, null, null, 1, null, null, null, "sgl_setor ASC" );
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $linha )
			{
				$lista4[$linha["cod_setor"]] = $linha["sgl_setor"];
			}
		}
		else
		{
			$lista4 = array( "Nenhum setor filho para este setor" );
		}
	}
	else
	{
		$lista4 = $listaVazia;
	}
	$this->campoLista( "setor_4", "Setor", $listaVazia, $this->setor_4, false, false, false, false, ! $this->setor_3 );
?>