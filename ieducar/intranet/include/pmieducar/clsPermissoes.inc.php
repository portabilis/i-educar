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
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/clsMenuFuncionario.inc.php" );



class clsPermissoes
{
	function clsPermissoes(){}

	function permissao_cadastra($int_processo_ap, $int_idpes_usuario, $int_soma_nivel_acesso, $str_pagina_redirecionar = null, $super_usuario = null,$int_verifica_usuario_biblioteca = false)
	{

		// Verifica se é super usuário

		$obj_usuario = new clsFuncionario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();

		if($super_usuario != null && $detalhe_usuario['ativo'])
		{
			$obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,false,false,0);
			$detalhe_super_usuario = $obj_menu_funcionario->detalhe();
		}


		if( ! $detalhe_super_usuario )
		{
			$obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,false,false,$int_processo_ap);
			$detalhe = $obj_menu_funcionario->detalhe();
		}

		$nivel = $this->nivel_acesso( $int_idpes_usuario );
		$ok = false;
		if( ( $super_usuario && $detalhe_super_usuario ) || $nivel & $int_soma_nivel_acesso )
		{
			$ok = true;
		}
		if( ( ! $detalhe['cadastra'] && ! $detalhe_super_usuario ) )
		{
			$ok = false;
		}

		/**
		 * verificao se for usuario tipo biblioteca ou escola(com $int_verifica_usuario_biblioteca = true) verifica se possui cadastro na tabela usuario biblioteca
		 *
		 */

			if(($nivel == 8 || ($nivel == 4 && $int_verifica_usuario_biblioteca == true)) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario)
			{
				$ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? false : true;

				if(!$ok && $nivel == 8){
					header("Location: index.php?negado=1");
					die();
				}

			}
		/**
		 *
		 */
		if( ! $ok )
		{
			if($str_pagina_redirecionar)
			{

				header("Location: $str_pagina_redirecionar");
				die();
			}
			else
			{
				return false;
			}
		}

		return  true;
	}

	function permissao_excluir($int_processo_ap, $int_idpes_usuario, $int_soma_nivel_acesso, $str_pagina_redirecionar = null, $super_usuario = null,$int_verifica_usuario_biblioteca = false)
	{
		// Verifica se é super usuário

		$obj_usuario = new clsFuncionario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();

		if($super_usuario != null && $detalhe_usuario['ativo'])
		{
			$obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,false,false,0);
			$detalhe_super_usuario = $obj_menu_funcionario->detalhe();
		}


		if( ! $detalhe_super_usuario )
		{
			$obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,false,false,$int_processo_ap);
			$detalhe = $obj_menu_funcionario->detalhe();
		}

		$nivel = $this->nivel_acesso( $int_idpes_usuario );
		$ok = false;
		if( ( $super_usuario && $detalhe_super_usuario ) || $nivel & $int_soma_nivel_acesso )
		{
			$ok = true;
		}
		if( ( ! $detalhe['exclui'] && ! $detalhe_super_usuario ) )
		{
			$ok = false;
		}

		/**
		 * verificao se for usuario tipo biblioteca ou escola(com $int_verifica_usuario_biblioteca = true) verifica se possui cadastro na tabela usuario biblioteca
		 *
		 */

			if(($nivel == 8 || ($nivel == 4 && $int_verifica_usuario_biblioteca == true)) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario)
			{
				$ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? false : true;


				if(!$ok && $nivel == 8){
					header("Location: index.php?negado=1");
					die();
				}

			}
		/**
		 *
		 */
		if( ! $ok )
		{
			if($str_pagina_redirecionar)
			{
				header("Location: $str_pagina_redirecionar");
				die();
			}
			else
			{
				return false;
			}
		}
		return  true;
	}

	function nivel_acesso($int_idpes_usuario)
	{
		$obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();
		if($detalhe_usuario)
		{
			$obj_tipo_usuario = new clsPmieducarTipoUsuario($detalhe_usuario['ref_cod_tipo_usuario']);
			$detalhe_tipo_usuario = $obj_tipo_usuario->detalhe();
			return $detalhe_tipo_usuario['nivel'];
		}
		return false;
	}

	function getInstituicao($int_idpes_usuario)
	{
		$obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();
		if($detalhe_usuario)
		{
			return $detalhe_usuario['ref_cod_instituicao'];
		}
		return false;
	}

	function getEscola($int_idpes_usuario)
	{
		$obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();
		if($detalhe_usuario)
		{
			return $detalhe_usuario['ref_cod_escola'];
		}
		return false;
	}

	function getInstituicaoEscola($int_idpes_usuario)
	{
		$obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
		$detalhe_usuario = $obj_usuario->detalhe();
		if($detalhe_usuario)
		{
			return array( "instituicao" => $detalhe_usuario['ref_cod_instituicao'], "escola" => $detalhe_usuario['ref_cod_escola']);
		}
		return false;
	}

	function getBiblioteca( $int_idpes_usuario )
	{
		$obj_usuario = new clsPmieducarBibliotecaUsuario();
		$lst_usuario_biblioteca = $obj_usuario->lista(null, $int_idpes_usuario);
		if ( $lst_usuario_biblioteca )
			return $lst_usuario_biblioteca;
		else
			return 0;
	}

	function isSuperUsuario($int_idpes_usuario)
	{
		if($int_idpes_usuario)
		{
			$obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,false,false,0);
			$detalhe_super_usuario = $obj_menu_funcionario->detalhe();
			if($detalhe_super_usuario)
			return true;
		}
		return false;
	}
}
?>