<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
    
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Diária Grupo" );
        $this->processoAp = "297";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    var $cod_diaria_grupo, 
        $desc_grupo;

    function Inicializar()
    {
        $retorno = "Novo";
        
        if ( isset( $_GET['cod_diaria_grupo'] ) )
        {
            $this->cod_diaria_grupo = $_GET['cod_diaria_grupo'];
            $db = new clsBanco();
            $db->Consulta( "SELECT desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo={$this->cod_diaria_grupo}" );
            if ($db->ProximoRegistro())
            {
                list( $this->desc_grupo ) = $db->Tupla();
                $this->fexcluir = true;
                $retorno = "Editar";
            }
        }

        $this->url_cancelar = ( $retorno == "Editar" ) ? "diaria_grupo_det.php?cod_diaria_grupo={$this->cod_diaria_grupo}" : "diaria_grupo_lst.php";
        $this->nome_url_cancelar = "Cancelar";

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""        => "{$nomeMenu} grupo de di&aacute;rias"             
    ));
    $this->enviaLocalizacao($localizacao->montar());        

        return $retorno;
    }

    function Gerar()
    {
        @session_start();
        $this->pessoaFj = $_SESSION['id_pessoa'];
        session_write_close();
        
        $this->campoOculto( "pessoaFj", $this->pessoaFj );
        $this->campoOculto( "cod_diaria_grupo", $this->cod_diaria_grupo );
        
        // Campo listaPesq de Funcionario
        
        $this->campoTexto( "desc_grupo", "Grupo", $this->desc_grupo, 70, 255, false, false, true );

    }

    function Novo() 
    {
        @session_start();
        $this->ref_funcionario_cadastro = $_SESSION['id_pessoa'];
        session_write_close();
        
        $campos = "";
        $values = "";
        $db = new clsBanco();

            if( $this->desc_grupo )
            {
                $campos .= "desc_grupo";
                $values .= "'{$this->desc_grupo}'";
            
            $db->Consulta( "INSERT INTO pmidrh.diaria_grupo( $campos) VALUES( $values)" );

            header( "location: diaria_grupo_lst.php" );
            die();
            return true;

            }
            else 
            {
                $this->mensagem = "Preencha corretamente o campo Grupo";
            }
        
        return false;
    }

    function Editar() 
    {
        @session_start();
        $this->ref_funcionario_cadastro = $_SESSION['id_pessoa'];
        session_write_close();
        
        $set = "";
        $db = new clsBanco();
        
        if( is_numeric( $this->cod_diaria_grupo ) )
        {
                    
            if( $this->desc_grupo )
            {
                $campos .= " desc_grupo = '{$this->desc_grupo}'";

                    
            $db->Consulta( "UPDATE pmidrh.diaria_grupo SET $campos WHERE cod_diaria_grupo = '{$this->cod_diaria_grupo}'" );
            header( "location: diaria_grupo_lst.php" );
            die();
            return true;
        }
        else 
        {
            $this->mensagem = "Preencha corretamente o campo Grupo de Diaria";
        }
    }
    else 
    {
        $this->mensagem = "Logue-se novamente pra realizar esta operacao";
    }
    return true;
}

    function Excluir()
    {
        if( is_numeric( $this->cod_diaria_grupo ) )
        {
            $db = new clsBanco();
            $ref_cod_diaria = $db->CampoUnico( "SELECT ref_cod_diaria_grupo FROM pmidrh.diaria WHERE ref_cod_diaria_grupo = {$this->cod_diaria_grupo}" );

            $ref_cod_diaria_valores = $db->CampoUnico( "SELECT ref_cod_diaria_grupo FROM pmidrh.diaria_valores WHERE ref_cod_diaria_grupo = {$this->cod_diaria_grupo}" );

            if (!isset($ref_cod_diaria) )
            {
            if (!isset($ref_cod_diaria_valores))
            {
                $db->Consulta( "DELETE FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = {$this->cod_diaria_grupo}" );
                header( "location: diaria_grupo_lst.php" );
                die();
                return true;
            }
            else
            {
            $this->mensagem = "Código da diária está sendo utilizado na tabela diaria_valores.";
            }
            }
            else
            $this->mensagem = "Código da diária está sendo utilizado na tabela diaria.";
        }
        return false;
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>