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
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Usu&aacute;rios!" );
        $this->processoAp = "555";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    function Gerar()
    {

        $this->titulo = "Usu&aacute;rios";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;
        
        $this->addCabecalhos( array( "Nome","Matrícula", "Matrícula Interna" ,"Status", "Tipo usu&aacute;rio", "N&iacute;vel de Acesso") );

        // Filtros de Busca
        $this->campoTexto("nm_pessoa", "Nome", $this->nm_pessoa, 42, 255);
        $this->campoTexto("matricula", "Matr&iacute;cula", $this->matricula, 20, 15);
        $this->campoTexto("matricula_interna", "Matr&iacute;cula Interna", $this->matricula_interna, 20, 30);

        $opcoes = array( "" => "Selecione" );
        if( class_exists( "clsPmieducarTipoUsuario" ) )
        {
            $objTemp = new clsPmieducarTipoUsuario();
            $objTemp->setOrderby('nm_tipo ASC');
            $lista = $objTemp->lista(null,null,null,null,null,null,null,null,1);
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                }
            }
        }
        else
        {
            echo "<!--\nErro\nClasse clsPmieducarTipoUsuario n&atilde;o encontrada\n-->";
            $opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
        }
        $this->campoLista( "ref_cod_tipo_usuario", "Tipo Usu&aacute;rio", $opcoes, $this->ref_cod_tipo_usuario,null,null,null,null,null,false);

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $detalhe = $obj_usuario->detalhe();

        // filtro de nivel de acesso
        $obj_tipo_usuario = new clsPmieducarTipoUsuario($detalhe['ref_cod_tipo_usuario']);
        $tipo_usuario = $obj_tipo_usuario->detalhe();

        $obj_super_usuario = new clsMenuFuncionario($this->pessoa_logada,false,false,0);
        $super_usuario_det = $obj_super_usuario->detalhe();


        if( $super_usuario_det )
        {
            $opcoes = array( "" => "Selecione", "1" => "Poli-Institucional", "2" => "Institucional", "4" => "Escolar", "8" => "Biblioteca");
        }
        elseif ($tipo_usuario["nivel"] == 1)
        {
            $opcoes = array( "" => "Selecione", "2" => "Institucional", "4" => "Escolar", "8" => "Biblioteca");
        }
        elseif ($tipo_usuario["nivel"] == 2)
        {
            $opcoes = array( "" => "Selecione", "4" => "Escolar", "8" => "Biblioteca");
        }
        elseif ($tipo_usuario["nivel"] == 4)
        {
            $opcoes = array( "" => "Selecione", "8" => "Biblioteca");
        }
        $this->campoLista( "ref_cod_nivel_usuario", "N&iacute;vel de Acesso", $opcoes, $this->ref_cod_nivel_usuario,null,null,null,null,null,false );

        $this->inputsHelper()->dynamic('instituicao',  array('required' =>  false, 'show-select' => true, 'value' => $this->ref_cod_instituicao));
        $this->inputsHelper()->dynamic('escola',  array('required' =>  false, 'show-select' => true, 'value' => $this->ref_cod_escola));
        $selectOptions = array(
        0 => 'Selecione',
        1  => 'Inativo',
        2  => 'Ativo'
        );

        $options = array('required'  => false,
                         'label'     => 'Status',
                         'value'     => $this->int_ativo,
                         'resources' => $selectOptions);

        $this->inputsHelper()->select( "int_ativo", $options);
        //gambiarra pois o inputsHelper está bugado
        switch ($this->int_ativo) {
   case 0:
         $this->int_ativo = null;
         break;
   case 1:
         $this->int_ativo = 0;
         break;
   case 2:
         $this->int_ativo = 1;
         break;
}
        // Paginador
        $limite = 10;
        $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $obj_func = new clsFuncionario();
        $obj_func->setOrderby("(nome) ASC");
        $obj_func->setLimite($limite, $iniciolimit);
        $lst_func = $obj_func->listaFuncionarioUsuario(
            $_GET["matricula"], 
            $_GET['nm_pessoa'],
            $_GET['matricula_interna'],
            $this->ref_cod_escola, 
            $this->ref_cod_instituicao, 
            $this->ref_cod_tipo_usuario, 
            $this->ref_cod_nivel_usuario,
            $this->int_ativo
            );

        if($lst_func)
        {
            foreach ( $lst_func AS $pessoa )
            {
                $ativo = ($pessoa['ativo'] == '1') ? "Ativo" : "Inativo";
                $total = $pessoa['_total'];
                $pessoa['nome']  = minimiza_capitaliza($pessoa['nome']);
                if ($pessoa["nivel"] == 1)
                {
                    $nivel = "Poli-Institucional";
                }
                elseif ($pessoa["nivel"] == 2)
                {
                    $nivel = "Institucional";
                }
                elseif ($pessoa["nivel"] == 4)
                {
                    $nivel = "Escolar";
                }
                elseif ($pessoa["nivel"] == 8)
                {
                    $nivel = "Biblioteca";
                }else{
                    $nivel = "";
                }
                $this->addLinhas( array(
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'><img src='imagens/noticia.jpg' border=0>{$pessoa['nome']}</a>",
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['matricula']}</a>",
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['matricula_interna']}</a>", 
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$ativo}</a>",
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['nm_tipo']}</a>", 
                "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$nivel}</a>", ) );
            }
        }

        $this->addPaginador2( "educar_usuario_lst.php", $total, $_GET, $this->nome, $limite );

        $obj_permissao = new clsPermissoes();
        if($obj_permissao->permissao_cadastra(555, $this->pessoa_logada,7,null,true)){
            $this->acao = "go(\"educar_usuario_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_configuracoes_index.php"                  => "Configurações",
         ""                                  => "Usuários"
        ));
        $this->enviaLocalizacao($localizacao->montar());        
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
