<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1);
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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/clsPmieducarUsuario.inc.php");
require_once ("include/pmieducar/clsPmieducarEscolaUsuario.inc.php");
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'include/pmieducar/clsPmieducarFuncionarioVinculo.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Cadastro de usu&aacute;rios" );
        $this->processoAp = "555";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{

    var $pessoa_logada;

    var $ref_pessoa;
    var $ref_cod_setor_new;

    //dados do funcionario
    var $nome;
    var $matricula;
    var $_senha;
    var $ativo;
    var $ref_cod_funcionario_vinculo;
    var $tempo_expira_conta;
    var $ramal;
    var $super;
    var $proibido;
    var $matricula_permanente;
    var $matricula_interna;
    var $escola;

    //senha carregada do banco (controle de criptografia)
    var $confere_senha;

    //setor e subsetores
    var $setor_0;
    var $setor_1;
    var $setor_2;
    var $setor_3;
    var $setor_4;

    function Inicializar()
    {
        $retorno = "Novo";
        

        $this->ref_pessoa = $_POST["ref_pessoa"];
        if( $_GET["ref_pessoa"] )
        {
            $this->ref_pessoa = $_GET["ref_pessoa"];
        }


        if( is_numeric( $this->ref_pessoa ) )
        {

            $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);
            $det_funcionario = $obj_funcionario->detalhe();
            if( $det_funcionario )
            {
                foreach ($det_funcionario as $campo => $valor) {
                    $this->$campo = $valor;
                }
                $this->_senha = $this->senha;
                $this->confere_senha = $this->_senha;
                $this->fexcluir = true;
                $retorno = "Editar";
            }

            $obj_menu_funcionario = new clsPortalMenuFuncionario($this->ref_pessoa, null, null, 0);
            $det_menu_funcionario = $obj_menu_funcionario->detalhe();
            if( $det_menu_funcionario )
            {
                $this->super = true;
            }
            $this->status = $this->ativo;
            $obj = new clsPmieducarUsuario( $this->ref_pessoa);
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $obj_permissoes = new clsPermissoes();
                $this->fexcluir = $obj_permissoes->permissao_excluir( 555, $this->pessoa_logada,7, "educar_usuario_lst.php", true );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_usuario_det.php?ref_pessoa={$this->ref_pessoa}" : "educar_usuario_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_configuracoes_index.php"    => "Configurações",
             ""                                  => "{$nomeMenu} usu&aacute;rio"
        ));
        $this->enviaLocalizacao($localizacao->montar());
        return $retorno;
    }

    function Gerar()
    {

        $obj_permissao = new clsPermissoes();

        $this->campoOculto("ref_pessoa", $this->ref_pessoa);

        if( is_numeric($this->ref_pessoa) )
        {
            $this->campoOculto("confere_senha", $this->confere_senha);
        }

        //--------------------------------------------------------------------
        if( $_POST )
        {
            foreach( $_POST AS $campo => $val )
            {
                $this->$campo = ( $this->$campo ) ? $this->$campo : $val;
            }
        }

         //--------------------------------------------------------------------
        $this->ref_cod_setor_new = 0;
        if( ! $this->ref_cod_setor_new && is_numeric( $this->ref_pessoa ) )
        {
            $objFuncionario = new clsPortalFuncionario( $this->ref_pessoa );
            $detFunc = $objFuncionario->detalhe();
            $this->ref_cod_setor_new = $detFunc["ref_cod_setor_new"];
        }

        if( $this->ref_cod_setor_new )
        {
            $objSetor = new clsSetor();
            $parentes = $objSetor->getNiveis( $this->ref_cod_setor_new );
            for( $i = 0; $i < 5; $i++ )
            {
                if( isset( $parentes[$i] ) && $parentes[$i] )
                {
                    $nmvar = "setor_{$i}";
                    $this->$nmvar = $parentes[$i];
                }
            }
        }
         //--------------------------------------------------------------------
        if( $_GET["ref_pessoa"] )
        {
            $obj_funcionario = new clsPessoaFj($this->ref_pessoa);
            $det_funcionario = $obj_funcionario->detalhe();

            $this->nome = $det_funcionario["nome"];

            $this->campoRotulo("nome", "Nome", $this->nome);
        }
        else
        {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit( 1 );
            $parametros->setPessoa( "F" );
            $parametros->setPessoaNovo( 'S' );
            $parametros->setPessoaEditar( 'N' );
            $parametros->setPessoaTela( "frame" );
            $parametros->setPessoaCPF('N');
            $parametros->adicionaCampoTexto("nome", "nome");
            $parametros->adicionaCampoTexto("nome_busca", "nome");
            $parametros->adicionaCampoTexto("ref_pessoa", "idpes");
            $this->campoTextoPesquisa("nome_busca", "Nome", $this->nome, 30, 255, true, "pesquisa_pessoa_lst.php", false, false, "", "", $parametros->serializaCampos()."&busca=S", true );
            $this->campoOculto("nome", $this->nome);
            $this->campoOculto("ref_pessoa", $this->ref_pessoa);
        }

        $this->campoTexto("matricula", "Matr&iacute;cula", $this->matricula, 12, 12, true);
        $this->campoSenha("_senha", "Senha", $this->_senha, true);
        $this->campoEmail("email", "E-mail usuário", $this->email, 50, 50, false, false, false, 'Utilizado para redefinir a senha, caso o usúario esqueça<br />Este campo pode ser gravado em branco, neste caso será solicitado um e-mail ao usuário, após entrar no sistema.');

        $this->campoTexto('matricula_interna', 'Matr&iacute;cula interna', $this->matricula_interna, 30, 30, false, false, false , 'Utilizado somente para registro, caso a institui&ccedil;&atilde;o deseje que a matr&iacute;cula interna deste funcion&aacute;rio seja registrada no sistema.');

        $obj_setor = new clsSetor();
        $lst_setor = $obj_setor->lista(null, null, null, null, null, null, null, null, null, 1, 0);

        $opcoes = array("" => "Selecione");

        if( is_array($lst_setor) && count($lst_setor) )
        {
            foreach ($lst_setor as $setor) {
                $opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
            }
        }
        $this->campoLista("setor_0", "Setor", $opcoes, $this->setor_0, "oproDocumentoNextLvl( this.value, '1' )", NULL, NULL, NULL, NULL, FALSE);

        $lst_setor = $obj_setor->lista($this->setor_0);

        $opcoes = array("" => "Selecione");

        if( is_array($lst_setor) && count($lst_setor) )
        {
            foreach($lst_setor as $setor)
            {
                $opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
            }
        }
        else
        {
            $opcoes[""] = "---------";
        }
        $this->campoLista("setor_1", "Subsetor 1", $opcoes, $this->setor_1, "oproDocumentoNextLvl(this.value, '2')", false, "", "", $this->setor_0 == "" ? true : false, false);

        $opcoes = array("" => "Selecione");

        $lst_setor = $obj_setor->lista($this->setor_1);

        if( is_array($lst_setor) && count($lst_setor) )
        {
            foreach ($lst_setor as $setor)
            {
                $opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
            }
        }
        else
        {
            $opcoes[""] = "---------";
        }
        $this->campoLista("setor_2", "Subsetor 2", $opcoes, $this->setor_2, "oproDocumentoNextLvl(this.value, '3')", false, "", "", $this->setor_1 == "" ? true : false, false);

        $opcoes = array("" => "Selecione");

        $lst_setor = $obj_setor->lista($this->setor_2);

        if( is_array($lst_setor) && count($lst_setor) )
        {
            foreach ($lst_setor as $setor)
            {
                $opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
            }
        }
        else
        {
            $opcoes[""] = "---------";
        }
        $this->campoLista("setor_3", "Subsetor 3", $opcoes, $this->setor_3, "oproDocumentoNextLvl(this.value, '4')", false, "", "", $this->setor_2 == "" ? true : false, false);

        $opcoes = array("" => "Selecione");

        $lst_setor = $obj_setor->lista($this->setor_3);

        if( is_array($lst_setor) && count($lst_setor) )
        {
            foreach ($lst_setor as $setor)
            {
                $opcoes[$setor["cod_setor"]] = $setor["sgl_setor"];
            }
        }
        else
        {
            $opcoes[""] = "---------";
        }
        $this->campoLista("setor_4", "Subsetor 4", $opcoes, $this->setor_4, "oproDocumentoNextLvl(this.value, '5')", false, "", "", $this->setor_3 == "" ? true : false, false);

        $opcoes = array(0 => "Inativo", 1 => "Ativo");
        if (!$this->ref_cod_pessoa_fj == '')
            $this->campoLista("ativo", "Status", $opcoes, $this->status);
        else
            $this->campoLista("ativo", "Status", $opcoes, 1);

        $objFuncionarioVinculo = new clsPmieducarFuncionarioVinculo;
        $opcoes = ['' => 'Selecione'] + $objFuncionarioVinculo->lista();
        $this->campoLista("ref_cod_funcionario_vinculo", "V&iacute;nculo", $opcoes, $this->ref_cod_funcionario_vinculo);

        $opcoes = array("" => "Selecione",
                         5 => "5",
                         6 => "6",
                         7 => "7",
                         10 => "10",
                         14 => "14",
                         20 => "20",
                         21 => "21",
                         28 => "28",
                         30 => "30",
                         35 => "35",
                         60 => "60",
                         90 => "90",
                        120 => "120",
                        150 => "150",
                        180 => "180",
                        210 => "210",
                        240 => "240",
                        270 => "270",
                        300 => "300",
                        365 => "365"
                        );

        $this->campoLista("tempo_expira_conta", "Dias p/ expirar a conta", $opcoes, $this->tempo_expira_conta);

        $tempoExpiraSenha = $GLOBALS['coreExt']['Config']->app->user_accounts->default_password_expiration_period;

        if (is_numeric($tempoExpiraSenha))
            $this->campoOculto("tempo_expira_senha", $tempoExpiraSenha);
        else {
            $opcoes = array('' => 'Selecione', 5 => '5', 30 => '30', 60 => '60', 90 => '90', 120 => '120', 180 => '180');
            $this->campoLista("tempo_expira_senha", "Dias p/ expirar a senha", $opcoes, $this->tempo_expira_senha);
        }

        $this->campoTexto("ramal", "Ramal", $this->ramal, 11, 30);

        $opcoes = array(null => "Não", 'S' => "Sim");
        $this->campoLista("super", "Super usu&aacute;rio", $opcoes, $this->super, '',false,'','',false,false);

        $opcoes = array(null => "Não", 1 => "Sim");
        $this->campoLista("proibido", "Banido", $opcoes, $this->proibido, '',false,'','',false,false);

        $opcoes = array(null => "Não", 1 => "Sim");
        $this->campoLista("matricula_permanente", "Matr&iacute;cula permanente", $opcoes, $this->matricula_permanente, '',false,'','',false,false);

        $opcoes = array( "" => "Selecione" );
        if( class_exists( "clsPmieducarTipoUsuario" ) )
        {
            $objTemp = new clsPmieducarTipoUsuario();
            $objTemp->setOrderby('nm_tipo ASC');

            $obj_libera_menu = new clsMenuFuncionario($this->pessoa_logada,false,false,0);
            $obj_super_usuario = $obj_libera_menu->detalhe();

            // verifica se pessoa logada é super-usuario
            if ($obj_super_usuario) {
                $lista = $objTemp->lista(null,null,null,null,null,null,null,null,1);
            }else{
                $lista = $objTemp->lista(null,null,null,null,null,null,null,null,1,$obj_permissao->nivel_acesso($this->pessoa_logada));
            }

            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                    $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
                }
            }
        }
        else
        {
            echo "<!--\nErro\nClasse clsPmieducarTipoUsuario n&atilde;o encontrada\n-->";
            $opcoes = array( "" => "Erro na geração" );
        }
        $tamanho = sizeof($opcoes_);
        echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";
        foreach ($opcoes_ as $key => $valor)
            echo "cod_tipo_usuario[{$key}] = {$valor};\n";
        echo "</script>";

        $this->campoLista( "ref_cod_tipo_usuario", "Tipo Usu&aacute;rio", $opcoes, $this->ref_cod_tipo_usuario,"",null,null,null,null,true );

        $nivel = $obj_permissao->nivel_acesso($this->ref_pessoa);

        $this->campoOculto("nivel_usuario_",$nivel);

        $this->inputsHelper()->dynamic(array('instituicao'));
        $this->inputsHelper()->multipleSearchEscola(null, array('label' => 'Escola(s)',
                                                                'required' => false));

        $scripts = array('/modules/Cadastro/Assets/Javascripts/Usuario.js');

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $this->acao_enviar = "valida()";

    }

    function Novo()
    {
        

        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
          $this->mensagem = "Formato do e-mail inválido.";
          return false;
        }

        //setor recebe o id do ultimo subsetor selecionado
        $this->ref_cod_setor_new = 0;
        for( $i = 0; $i < 5; $i++ )
        {
            $nmvar = "setor_{$i}";
            if( is_numeric( $this->$nmvar ) && $this->$nmvar )
            {
                $this->ref_cod_setor_new = $this->$nmvar;
            }
        }

    if (! $this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula))
      return false;

    if (! $this->validatesPassword($this->matricula, $this->_senha))
      return false;

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, md5($this->_senha), $this->ativo, null, $this->ramal, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->tempo_expira_conta, "NOW()", "NOW()", $this->pessoa_logada, empty($this->proibido) ? 0 : 1, $this->ref_cod_setor_new, null, empty($this->matricula_permanente)? 0 : 1, 1, $this->email, $this->matricula_interna);
        if( $obj_funcionario->cadastra() )
        {

      $funcionario = $obj_funcionario->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("funcionario", $this->pessoa_logada, $this->ref_pessoa);
      $auditoria->inclusao($funcionario);

            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario( $this->ref_pessoa, null, $this->ref_cod_instituicao, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
            } else {
                $obj = new clsPmieducarUsuario( $this->ref_pessoa, null, null, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
            }

            if($obj->existe()){
        $detalheAntigo = $obj->detalhe();
                $cadastrou     = $obj->edita();
        $detalheNovo   = $obj->detalhe();
        $auditoria     = new clsModulesAuditoriaGeral("usuario", $this->pessoa_logada, $cadastrou);
        $auditoria->alteracao($detalheAntigo, $detalheNovo);
      }
            else{
                $cadastrou = $obj->cadastra();
        $usuario = new clsPmieducarUsuario($cadastrou);
        $usuario = $usuario->detalhe();
        $auditoria = new clsModulesAuditoriaGeral("usuario", $this->pessoa_logada, $cadastrou);
        $auditoria->inclusao($usuario);
      }

            $this->insereUsuarioEscolas($this->ref_pessoa, $this->escola);

            if( $cadastrou )
            {
                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect('educar_usuario_lst.php');
            }
        }
        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar -->";
        return false;
    }


    function Editar()
    {
        

        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
          $this->mensagem = "Formato do e-mail inválido.";
          return false;
        }

        $this->ref_cod_setor_new = 0;
        for( $i = 0; $i < 5; $i++ )
        {
            $nmvar = "setor_{$i}";
            if( is_numeric( $this->$nmvar ) && $this->$nmvar )
            {
                $this->ref_cod_setor_new = $this->$nmvar;
            }
        }

    if (! $this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula))
      return false;

    if (! $this->validatesPassword($this->matricula, $this->_senha))
      return false;

        //verifica se a senha ja esta criptografada
        if($this->_senha != $this->confere_senha)
        {
            $this->_senha = md5($this->_senha);
        }

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, $this->_senha, $this->ativo, null, $this->ramal, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->tempo_expira_conta, "NOW()", "NOW()", $this->pessoa_logada, empty($this->proibido) ? 0 : 1, $this->ref_cod_setor_new, null, empty($this->matricula_permanente) ? 0 : 1, null, $this->email, $this->matricula_interna);
    $detalheAntigo = $obj_funcionario->detalhe();
        if( $obj_funcionario->edita() )
        {

      $detalheNovo = $obj_funcionario->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("funcionario", $this->pessoa_logada, $this->ref_pessoa);
      $auditoria->alteracao($detalheAntigo, $detalheNovo);

            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario( $this->ref_pessoa, null, $this->ref_cod_instituicao, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
            } else {
                $obj = new clsPmieducarUsuario( $this->ref_pessoa, null, null, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
            }

      if($obj->existe()){
        $detalheAntigo = $obj->detalhe();
        $editou     = $obj->edita();
        $detalheNovo   = $obj->detalhe();
        $auditoria     = new clsModulesAuditoriaGeral("usuario", $this->pessoa_logada, $editou);
        $auditoria->alteracao($detalheAntigo, $detalheNovo);
      }
      else{
        $editou = $obj->cadastra();
        $usuario = new clsPmieducarUsuario($editou);
        $usuario = $usuario->detalhe();
        $auditoria = new clsModulesAuditoriaGeral("usuario", $this->pessoa_logada, $editou);
        $auditoria->inclusao($usuario);
      }

            $this->insereUsuarioEscolas($this->ref_pessoa, $this->escola);

            if($this->nivel_usuario_ == 8)
            {
                $obj_tipo = new clsPmieducarTipoUsuario($this->ref_cod_tipo_usuario);
                $det_tipo = $obj_tipo->detalhe();
                if($det_tipo['nivel'] != 8){
                    $obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
                    $lista_bibliotecas_usuario = $obj_usuario_bib->lista(null,$this->pessoa_logada);

                    if ($lista_bibliotecas_usuario) {

                        foreach ($lista_bibliotecas_usuario as $usuario)
                        {
                            $obj_usuario_bib = new clsPmieducarBibliotecaUsuario($usuario['ref_cod_biblioteca'],$this->pessoa_logada);
                            if(!$obj_usuario_bib->excluir()){
                                echo "<!--\nErro ao excluir usuarios biblioteca\n-->";
                                return false;
                            }
                        }
                    }
                }
            }

            if($this->ref_cod_instituicao != $this->ref_cod_instituicao_)
            {
                $obj_biblio = new clsPmieducarBiblioteca();
                $lista_biblio_inst = $obj_biblio->lista(null,$this->ref_cod_instituicao_);
                if($lista_biblio_inst)
                {
                    foreach ($lista_biblio_inst as $biblioteca) {
                        $obj_usuario_bib = new clsPmieducarBibliotecaUsuario($biblioteca['cod_biblioteca'],$this->pessoa_logada);
                        $obj_usuario_bib->excluir();
                    }
                }
            }

            if( $editou )
            {

                $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
                $this->simpleRedirect('educar_usuario_lst.php');
            }
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPortalFuncionario-->";
        return false;
    }

    function Excluir()
    {
        

    $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);
        $detalhe = $obj_funcionario->detalhe();
        if($obj_funcionario->excluir())
        {
      $auditoria = new clsModulesAuditoriaGeral("funcionario", $this->pessoa_logada, $this->ref_pessoa);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_usuario_lst.php');
        }
        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPortalFuncionario\n-->";
        return false;
    }


  function validatesUniquenessOfMatricula($pessoaId, $matricula) {
    $sql = "select 1 from portal.funcionario where lower(matricula) = lower('$matricula') and ref_cod_pessoa_fj != $pessoaId";
    $db = new clsBanco();

        if ($db->CampoUnico($sql) == '1') {
      $this->mensagem = "A matrícula '$matricula' já foi usada, por favor, informe outra.";
      return false;
    }
    return true;
  }

  function validatesPassword($matricula, $password) {
    $msg = '';

        if ($password == $matricula)
      $msg = 'Informe uma senha diferente da matricula.';
    elseif (strlen($password) < 8)
      $msg = 'Por favor informe uma senha segura, com pelo menos 8 caracteres.';

    if ($msg) {
      $this->mensagem = $msg;
      return false;
    }
    return true;
  }

  function excluiTodosVinculosEscola($codUsuario) {
    $usuarioEscola = new clsPmieducarEscolaUsuario();
    $usuarioEscola->excluirTodos($codUsuario);
  }

  function insereUsuarioEscolas($codUsuario, $escolas) {
    $this->excluiTodosVinculosEscola($codUsuario);
    foreach ($escolas as $e) {
        $usuarioEscola = new clsPmieducarEscolaUsuario();
        $usuarioEscola->ref_cod_usuario = $codUsuario;
        $usuarioEscola->ref_cod_escola = $e;
        $usuarioEscola->cadastra();
    }
  }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
