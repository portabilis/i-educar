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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/pmieducar/clsPmieducarEscolaUsuario.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Usu&aacute;rio" );
        $this->processoAp = "555";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_usuario;
    var $ref_cod_escola;
    var $ref_cod_instituicao;
    var $ref_funcionario_cad;
    var $ref_funcionario_exc;
    var $ref_cod_tipo_usuario;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        

        $this->titulo = "Usu&aacute;rio - Detalhe";

        $cod_pessoa = $this->cod_usuario = $_GET["ref_pessoa"];

        $obj_pessoa = new clsPessoa_($cod_pessoa);
        $det_pessoa = $obj_pessoa->detalhe();

        $this->addDetalhe( array("Nome", $det_pessoa["nome"]) );

        $obj_fisica_cpf = new clsFisica($cod_pessoa);
        $det_fisica_cpf = $obj_fisica_cpf->detalhe();
        $this->addDetalhe( array("CPF", int2CPF($det_fisica_cpf["cpf"])) );

        $obj_endereco = new clsEndereco($cod_pessoa);
        $det_endereco = $obj_endereco->detalhe();

        if($det_endereco["tipo_origem"] == "endereco_pessoa")
        {
            $this->addDetalhe( array("CEP", int2CEP($det_endereco["cep"])) );

            $obj_bairro = new clsBairro($det_endereco["idbai"]);
            $det_bairro = $obj_bairro->detalhe();

            $this->addDetalhe( array("Bairro", $det_bairro["nome"]) );

            //echo "det: {$det_bairro["idmun"]}";
            $obj_municipio = $det_bairro["idmun"];
            $det_municipio = $obj_municipio->detalhe();

            $this->addDetalhe( array("Cidade", $det_municipio["nome"]) );
            for($i = 1; $i <= 4; $i++)
            {
                $obj_fone_pessoa = new clsPessoaTelefone($cod_pessoa, $i);
                $det_fone_pessoa = $obj_fone_pessoa->detalhe();

                if($det_fone_pessoa)
                {
                    switch($i):
                    case 1:
                        $this->addDetalhe( array("Telefone 1", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 2:
                        $this->addDetalhe( array("Telefone 2", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 3:
                        $this->addDetalhe( array("Celular", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 4:
                        $this->addDetalhe( array("Fax", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    endswitch;
                }
            }
        }
        elseif ($det_endereco["tipo_origem"] == "endereco_externo")
        {
            $this->addDetalhe( array("CEP", int2CEP($det_endereco["cep"])) );
            $this->addDetalhe( array("Bairro", $det_endereco["bairro"]) );
            $this->addDetalhe( array("Cidade", $det_endereco["cidade"]) );
            for($i = 1; $i <= 4; $i++)
            {
                $obj_fone_pessoa = new clsPessoaTelefone($cod_pessoa, $i);
                $det_fone_pessoa = $obj_fone_pessoa->detalhe();

                if($det_fone_pessoa)
                {
                    switch($i):
                    case 1:
                        $this->addDetalhe( array("Telefone 1", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 2:
                        $this->addDetalhe( array("Telefone 2", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 3:
                        $this->addDetalhe( array("Celular", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    case 4:
                        $this->addDetalhe( array("Fax", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
                        break;
                    endswitch;
                }
            }
        }

        $obj_funcionario = new clsFuncionario($cod_pessoa);
        $det_funcionario = $obj_funcionario->detalhe();

        $this->addDetalhe( array("Ramal", $det_funcionario["ramal"]) );

        $this->addDetalhe( array("Site", $det_pessoa["url"]) );
        //$this->addDetalhe( array("E-mail", $det_pessoa["email"]) );
        $this->addDetalhe( array("E-mail usuário", $det_funcionario["email"]) );

        if (!empty($det_funcionario['matricula_interna']))
            $this->addDetalhe( array('Matr&iacute;cula interna', $det_funcionario['matricula_interna']));

        $obj_fisica = new clsFisica($cod_pessoa);
        $det_fisica = $obj_fisica->detalhe();

        $sexo = ($det_fisica["sexo"] == "M") ? "Masculino" : "Feminino";
        $this->addDetalhe( array("Sexo", $sexo) );

        $this->addDetalhe( array("Matrícula", $det_funcionario["matricula"]) );
        $this->addDetalhe( array("Sequencial", $det_funcionario["sequencial"]) );
        $ativo_f = ($det_funcionario["ativo"] == '1') ? "Ativo" : "Inativo";
        $this->addDetalhe( array("Status", $ativo_f) );

        $tmp_obj = new clsPmieducarUsuario( $this->cod_usuario );
        $registro = $tmp_obj->detalhe();

        if( class_exists( "clsPmieducarTipoUsuario" ) )
        {
            $obj_ref_cod_tipo_usuario = new clsPmieducarTipoUsuario( $registro["ref_cod_tipo_usuario"] );
            $det_ref_cod_tipo_usuario = $obj_ref_cod_tipo_usuario->detalhe();
            $registro["ref_cod_tipo_usuario"] = $det_ref_cod_tipo_usuario["nm_tipo"];
        }
        else
        {
            $registro["ref_cod_tipo_usuario"] = "Erro na gera&ccedil;&atilde;o";
            echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarTipoUsuario\n-->";
        }
        if( class_exists( "clsPmieducarInstituicao" ) )
        {
            $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
            $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
        }
        else
        {
            $registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
            echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
        }
        if( class_exists( "clsPmieducarEscolaUsuario" ) )
        {
            $escolasUsuario = new clsPmieducarEscolaUsuario();
            $escolasUsuario = $escolasUsuario->lista($cod_pessoa);

            foreach ($escolasUsuario as $escola) {
                $escolaDetalhe = new clsPmieducarEscola($escola['ref_cod_escola']);
                $escolaDetalhe = $escolaDetalhe->detalhe();
                $nomesEscola[] = $escolaDetalhe['nome'];
            }
            $nomesEscola = implode("<br>", $nomesEscola);
            $registro["ref_cod_escola"] = $nomesEscola;
        }
        else
        {
            $registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
            echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
        }

        if( $registro["ref_cod_tipo_usuario"] )
        {
            $this->addDetalhe( array( "Tipo Usu&aacute;rio", "{$registro["ref_cod_tipo_usuario"]}") );
        }
        if( $registro["ref_cod_instituicao"] )
        {
            $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
        }
        if( $registro["ref_cod_escola"] )
        {
            $this->addDetalhe( array( "Escolas", $registro["ref_cod_escola"]) );
        }

        $objPermissao = new clsPermissoes();
        if( $objPermissao->permissao_cadastra( 555, $this->pessoa_logada,7) )
        {
            $this->url_novo = "educar_usuario_cad.php";
            $this->url_editar = "educar_usuario_cad.php?ref_pessoa={$cod_pessoa}";
        }
        $this->url_cancelar = "educar_usuario_lst.php";
        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_configuracoes_index.php"  => "Configurações",
         ""                                  => "Detalhe do usu&aacute;rio"
    ));
    $this->enviaLocalizacao($localizacao->montar());
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
