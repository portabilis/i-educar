<?php


/**
 * Os parâmetros passados para esta página de listagem devem estar dentro da classe clsParametrosPesquisas.inc.php
 *
 * @author Adriano Erik Weiguert Nagasava
 *
 */

use Illuminate\Support\Facades\Session;

$desvio_diretorio = "";
require_once ( "include/clsBase.inc.php" );
require_once ( "include/clsListagem.inc.php" );
require_once ( "include/Geral.inc.php" );
require_once ( "include/pessoa/clsPessoa_.inc.php" );
class clsIndex extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Pesquisa por Pessoa!" );
        $this->processoAp         = "0";
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsListagem
{
  var $cpf;
  var $cnpj;
  var $matricula;
  var $campo_busca;
  var $chave_campo;

  function Gerar()
  {
    $this->nome = "form1";

    $show = $_REQUEST['show'];
    $this->campoOculto("show",$show);

    if ($show == "todos") {
      $show = false;
    } else {
      $show = 1;
    }

    $this->chave_campo = $_GET['chave_campo'];

    if ($_GET["campos"]) {
      $parametros       = new clsParametrosPesquisas();
      $parametros->deserializaCampos( $_GET["campos"] );
      Session::put('campos', $parametros->geraArrayComAtributos());
      unset( $_GET["campos"] );
    } else {
      $parametros = new clsParametrosPesquisas();
      $parametros->preencheAtributosComArray( Session::get('campos') );
    }

    $submit = false;

    foreach ($_GET as $key => $value) {
      $this->$key = $value;
    }

    if ($parametros->getPessoa() == 'F' || $parametros->getPessoa() == '') {
      $this->addCabecalhos(array("CPF", "Nome"));

      // Filtros de Busca
      $this->campoTexto( "campo_busca", "Pessoa", $this->campo_busca, 35, 255, false, false, false, "Código/Nome" );
      $this->campoCpf( "cpf", "CPF", ($this->cpf)?int2CPF(idFederal2int($this->cpf)):"" );

      $chave_busca = @$_GET['campo_busca'];
      $cpf = @$_GET['cpf'];
      $busca = @$_GET['busca'];

      // Paginador
      $limite      = 10;
      $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

      if(is_numeric($this->chave_campo))
        $chave = "[$this->chave_campo]";
      else
        $chave = "";

      if ($busca == 'S') {
        if ($parametros->getPessoaNovo() == 'S') {
          if ($parametros->getPessoaTela() == "window") {
                        $this->acao = "set_campo_pesquisa(\"".$parametros->getPessoaCampo()."\", \"0\", \"submit\")";
            $this->nome_acao = "Novo";
          } elseif ($parametros->getPessoaTela() == "frame") {
            $this->acao = "go( \"pesquisa_pessoa_cad.php?pessoa=F&cod=0&ref_cod_sistema=".$parametros->getCodSistema()."&pessoa_cpf=".$parametros->getPessoaCPF()."\" )";
            $this->nome_acao = "Novo";
          }
        }

        if (is_numeric($chave_busca)) {
          $obj_pessoa = new clsPessoaFisica();
          $lst_pessoa = $obj_pessoa->lista( null, ( ( $cpf ) ? idFederal2int( $cpf ) : null ), $iniciolimit, $limite, false, $parametros->getCodSistema(), $chave_busca );
        } else {
          $obj_pessoa = new clsPessoaFisica();
          $lst_pessoa = $obj_pessoa->lista( $chave_busca, ( ( $cpf ) ? idFederal2int( $cpf ) : null ), $iniciolimit, $limite, false, $parametros->getCodSistema() );
        }
      } else {
                $obj_pessoa = new clsPessoaFisica();
                $lst_pessoa = $obj_pessoa->lista( null, null, $iniciolimit, $limite, false, $parametros->getCodSistema() );
      }

            if ($lst_pessoa) {
        foreach ($lst_pessoa as $pessoa) {
          $funcao = " set_campo_pesquisa(";
          $virgula = "";
          $cont = 0;
          $pessoa["cpf"] = ( is_numeric( $pessoa["cpf"] ) ) ? int2CPF( $pessoa["cpf"] ) : null;

                    foreach ($parametros->getCampoNome() as $campo) {
                        if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                            $funcao .= "{$virgula} '{$campo}{$chave}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                            $campoTexto = addslashes($pessoa[$parametros->getCampoValor( $cont )]);
                            $funcao .= "{$virgula} '{$campo}{$chave}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$campoTexto}'";
                            $virgula = ",";
                        }
                        $cont++;
                    }
                    if ( $parametros->getSubmit() )
                        $funcao .= "{$virgula} 'submit' )";
                    else
                        $funcao .= " )";
                    if ( $parametros->getPessoaEditar() == "S" ) {
                        if ( $parametros->getPessoaTela() == "frame" ) {
                            $this->addLinhas( array( "<a href='pesquisa_pessoa_cad.php?pessoa=F&cod={$pessoa["idpes"]}&ref_cod_sistema=".$parametros->getCodSistema()."'>{$pessoa["cpf"]}</a>", "<a href='pesquisa_pessoa_cad.php?pessoa=F&cod={$pessoa["idpes"]}&ref_cod_sistema=".$parametros->getCodSistema()."'>{$pessoa["nome"]}</a>" ) );
                        }
                        else {
                            $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cpf"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                        }
                    }
                    else {
                        $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cpf"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                    }
                    $total = $pessoa['total'];
                }
            }
            else {
                $this->addLinhas( array( "Não existe nenhum resultado a ser apresentado." ) );
            }
        }
        elseif ( $parametros->getPessoa() == 'J' )
        {

            $this->addCabecalhos( array( "CNPJ", "Nome" ) );

            // Filtros de Busca
            $this->campoTexto( "campo_busca", "Pessoa", $this->campo_busca, 35, 255, false, false, false, "Código/Nome" );
            if( $this->cnpj )
            {
                if( is_numeric($this->cnpj) )
                {
                    $this->cnpj = int2CNPJ($this->cnpj);
                }
            }
            else
            {
                $this->cnpj = "";
            }
            $this->campoCnpj( "cnpj", "CNPJ", $this->cnpj );

            $chave_busca = @$_GET['campo_busca'];
            $cnpj        = @$_GET['cnpj'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

            if ( $busca == 'S' ) {
                if ( $parametros->getPessoaNovo() == 'S' ) {
                    if ( $parametros->getPessoaTela() == "window" ) {
                        $this->acao      = "set_campo_pesquisa( \"".$parametros->getPessoaCampo()."\", \"0\", \"submit\" )";
                        $this->nome_acao = "Novo";
                    }
                    elseif ( $parametros->getPessoaTela() == "frame" ) {
                        $this->acao      = "go( \"pesquisa_pessoa_cad.php?pessoa=J&cod=0\" )";
                        $this->nome_acao = "Novo";
                    }
                }
                if ( is_numeric( $chave_busca ) ) {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista( ( ( $cnpj ) ? idFederal2int( $cnpj ) : null ), false, false, $iniciolimit, $limite, false, false, false, $chave_busca );
                }
                else {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista( ( ( $cnpj ) ? idFederal2int( $cnpj ) : null ), $chave_busca, false, $iniciolimit, $limite );
                }
            }
            else {
                $obj_pessoa = new clsPessoaJuridica();
                $lst_pessoa = $obj_pessoa->lista( null, null, null, $iniciolimit, $limite );
            }
            if ( $lst_pessoa ) {
                foreach ( $lst_pessoa as $pessoa ) {
                    $funcao         = " set_campo_pesquisa(";
                    $virgula        = "";
                    $cont           = 0;
                    $pessoa["cnpj"] = ( is_numeric( $pessoa["cnpj"] ) ) ? int2CNPJ( $pessoa["cnpj"] ) : null;
                    foreach ( $parametros->getCampoNome() as $campo ) {
                        if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        $cont++;
                    }
                    if ( $parametros->getSubmit() )
                        $funcao .= "{$virgula} 'submit' )";
                    else
                        $funcao .= " )";
                    if ( $campos["edita"]["permitir"] == "S" ) {
                        if ( $parametros->getPessoaTela() == "frame" ) {
                            $this->addLinhas( array( "<a href='pesquisa_pessoa_cad.php?pessoa=J&cod={$pessoa["idpes"]}'>{$pessoa["cnpj"]}</a>", "<a href='pesquisa_pessoa_cad.php?pessoa=J&cod={$pessoa["idpes"]}'>{$pessoa["nome"]}</a>" ) );
                        }
                        else {
                            $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cnpj"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                        }
                    }
                    else {
                        $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cnpj"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                    }
                    $total = $pessoa['total'];
                }
            }
            else {
                $this->addLinhas( array( "Não existe nenhum resultado a ser apresentado." ) );
            }
        }
        elseif ( $parametros->getPessoa() == 'FJ' )
        {

            $this->addCabecalhos( array( "CNPJ/CPF", "Nome" ) );

            // Filtros de Busca
            $this->campoTexto( "campo_busca", "Pessoa", $this->campo_busca, 50, 255, false, false, false, "Código/Nome" );
            $this->campoIdFederal( "id_federal", "CNPJ/CPF", ($this->id_federal)?int2IdFederal($this->id_federal):"" );

            $chave_busca = @$_GET['campo_busca'];
            $id_federal  = @$_GET['id_federal'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;
            if ( $busca == 'S' ) {
                if ( $parametros->getPessoaNovo() == 'S' ) {
                    if ( $parametros->getPessoaTela() == "window" ) {
                        $this->acao      = "set_campo_pesquisa( \"".$parametros->getPessoaCampo()."\", \"0\", \"submit\" )";
                        $this->nome_acao = "Novo";
                    }
                    elseif ( $parametros->getPessoaTela() == "frame" ) {
                        $this->acao      = "go( \"pesquisa_pessoa_cad.php?pessoa=FJ&cod=0&ref_cod_sistema=".$parametros->getCodSistema()."&pessoa_cpf=".$parametros->getPessoaCPF()."\" )";
                        $this->nome_acao = "Novo";
                    }
                }
                if ( is_numeric( $chave_busca ) ) {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida( $chave_busca, null, idFederal2int( $id_federal ), $iniciolimit, $limite, null,"nome ASC", $parametros->getCodSistema() );
                }
                else {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida( null, $chave_busca, idFederal2int( $id_federal ), $iniciolimit, $limite, null,"nome ASC", $parametros->getCodSistema() );
                }
            }
            else {
                $obj_pessoa = new clsPessoaFj();
                $lst_pessoa = $obj_pessoa->lista_rapida( null, null, null, $iniciolimit, $limite,null,"nome ASC", $parametros->getCodSistema() );
            }
            if ( $lst_pessoa ) {
                foreach ( $lst_pessoa as $pessoa ) {
                    $funcao               = " set_campo_pesquisa(";
                    $virgula              = "";
                    $cont                 = 0;
                    foreach ( $parametros->getCampoNome() as $campo ) {
                        if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        $cont++;
                    }
                    if ( $parametros->getSubmit() )
                        $funcao .= "{$virgula} 'submit' )";
                    else
                        $funcao .= " )";
                    $pessoa['cnpj'] = ($pessoa['tipo'] == 'J' && $pessoa['cnpj']) ? int2CNPJ($pessoa['cnpj']) : null;
                    $pessoa['cpf'] = ($pessoa['tipo'] == 'F' && $pessoa['cpf']) ?  int2CPF($pessoa['cpf']) : null;
                    $obj_pes = new clsPessoa_( $pessoa["idpes"] );
                    $det_pes = $obj_pes->detalhe();
                    if ( $parametros->getPessoaEditar() == "S" ) {
                        if ( $parametros->getPessoaTela() == "frame" ) {

                            if ( $det_pes["tipo"] == "J" )
                                $this->addLinhas( array( "<a href='pesquisa_pessoa_cad.php?pessoa={$det_pes["tipo"]}&cod={$pessoa["idpes"]}'>{$pessoa["cnpj"]}</a>", "<a href='pesquisa_pessoa_cad.php?pessoa={$det_pes["tipo"]}&cod={$pessoa["idpes"]}'>{$pessoa["nome"]}</a>" ) );
                            elseif ( $det_pes["tipo"] == "F" )
                                $this->addLinhas( array( "<a href='pesquisa_pessoa_cad.php?pessoa={$det_pes["tipo"]}&cod={$pessoa["idpes"]}&ref_cod_sistema=".$parametros->getCodSistema()."'>{$pessoa["cpf"]}</a>", "<a href='pesquisa_pessoa_cad.php?pessoa={$det_pes["tipo"]}&cod={$pessoa["idpes"]}&ref_cod_sistema=".$parametros->getCodSistema()."'>{$pessoa["nome"]}</a>" ) );
                        }
                        else {
                            if($det_pes["tipo"] == "J" )
                                $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cnpj"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                            else
                                $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cpf"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                        }
                    }
                    else {
                        if($det_pes["tipo"] == "J" )
                            $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cnpj"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                        else
                            $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["cpf"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                    }
                    $total = $pessoa['_total'];
                }
            }
            else {
                $this->addLinhas( array( "Não existe nenhum resultado a ser apresentado." ) );
            }
        }
        else if( $parametros->getPessoa() == 'FUNC' )
        {
            $this->addCabecalhos( array( "Matricula", "Nome" ) );

            // Filtros de Busca
            $this->campoTexto( "campo_busca", "Pessoa", $this->campo_busca, 50, 255, false, false, false, "Código/Nome" );
            $this->campoNumero( "matricula", "Matricula", $this->matricula, 15, 255 );

            $chave_busca = @$_GET['campo_busca'];
            $cpf         = @$_GET['cpf'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

            if ( $busca == 'S' )
            {
                if ( $parametros->getPessoaNovo() == 'S' )
                {
                    if ( $parametros->getPessoaTela() == "window" )
                    {
                        $this->acao      = "set_campo_pesquisa( \"".$parametros->getPessoaCampo()."\", \"0\", \"submit\" )";
                        $this->nome_acao = "Novo";
                    }
                    elseif ( $parametros->getPessoaTela() == "frame" )
                    {
                        $this->acao      = "go( \"pesquisa_pessoa_cad.php?pessoa=F&cod=0&ref_cod_sistema=".$parametros->getCodSistema()."&pessoa_cpf=".$parametros->getPessoaCPF()."\" )";
                        $this->nome_acao = "Novo";
                    }
                }
                if ( is_numeric( $chave_busca ) )
                {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista($this->matricula,false,$show,false,false,false,false,$iniciolimit,$limite,false,false,$this->campo_busca);
                }
                else
                {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista($this->matricula,$this->campo_busca,$show,false,false,false,false,$iniciolimit,$limite);
                }
            }
            else
            {
                $obj_funcionario = new clsFuncionario();
                $lst_pessoa = $obj_funcionario->lista(false,false,$show,false,false,false,false,$iniciolimit,$limite);
            }
            if ( $lst_pessoa )
            {
                foreach ( $lst_pessoa as $pessoa )
                {
                    $funcao        = " set_campo_pesquisa(";
                    $virgula       = "";
                    $cont          = 0;
                    $pessoa["cpf"] = ( is_numeric( $pessoa["cpf"] ) ) ? int2CPF( $pessoa["cpf"] ) : null;
                    foreach ( $parametros->getCampoNome() as $campo )
                    {
                        if ( $parametros->getCampoTipo( $cont ) == "text" )
                        {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        elseif ( $parametros->getCampoTipo( $cont ) == "select" )
                        {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                            $virgula = ",";
                        }
                        $cont++;
                    }
                    if ( $parametros->getSubmit() )
                        $funcao .= "{$virgula} 'submit' )";
                    else
                        $funcao .= " )";
                    if ( $parametros->getPessoaEditar() == "S" )
                    {
                        if ( $parametros->getPessoaTela() == "frame" )
                        {
                            $this->addLinhas( array( "<a href='pesquisa_pessoa_cad.php?pessoa=F&cod={$pessoa["idpes"]}'>{$pessoa["matricula"]}</a>", "<a href='pesquisa_pessoa_cad.php?pessoa=F&cod={$pessoa["idpes"]}&ref_cod_sistema=".$parametros->getCodSistema()."'>{$pessoa["nome"]}</a>" ) );
                        }
                        else
                        {
                            $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["matricula"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                        }
                    }
                    else
                    {
                        $this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["matricula"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa["nome"]}</a>" ) );
                    }
                    $total = $pessoa['_total'];
                }
            }
            else
            {
                $this->addLinhas( array( "Não existe nenhum resultado a ser apresentado." ) );
            }
        }

        // Paginador
        $this->addPaginador2( "pesquisa_pessoa_lst.php", $total, $_GET, $this->nome, $limite );

        // Define Largura da Página
        $this->largura = "100%";
    }
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
