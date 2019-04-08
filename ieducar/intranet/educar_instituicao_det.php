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
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Institui&ccedil;&atilde;o" );
        $this->processoAp = "559";
        $this->addEstilo("localizacaoSistema");
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

    var $cod_instituicao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idtlog;
    var $ref_sigla_uf;
    var $cep;
    var $cidade;
    var $bairro;
    var $logradouro;
    var $numero;
    var $complemento;
    var $nm_responsavel;
    var $ddd_telefone;
    var $telefone;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $pessoa_logada;

    function Gerar()
    {
        $this->titulo = "Institui&ccedil;&atilde;o - Detalhe";


        $this->cod_instituicao=$_GET["cod_instituicao"];

        $tmp_obj = new clsPmieducarInstituicao( $this->cod_instituicao );
        $registro = $tmp_obj->detalhe();

        if( class_exists( "clsTipoLogradouro" ) )
        {
            $obj_ref_idtlog = new clsTipoLogradouro( $registro["ref_idtlog"] );
            $det_ref_idtlog = $obj_ref_idtlog->detalhe();
            $registro["ref_idtlog"] = $det_ref_idtlog["descricao"];
        }
        else
        {
            $registro["ref_idtlog"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsUrbanoTipoLogradouro\n-->";
        }

        $registro["cep"] = int2CEP( $registro["cep"] );
        $this->addDetalhe( array( "Código Instituição", "{$registro["cod_instituicao"]}") );
        $this->addDetalhe( array( "Nome da Instituição", "{$registro["nm_instituicao"]}") );
        $this->addDetalhe( array( "CEP", "{$registro["cep"]}") );
        $this->addDetalhe( array( "Logradouro", "{$registro["logradouro"]}") );
        $this->addDetalhe( array( "Bairro", "{$registro["bairro"]}") );
        $this->addDetalhe( array( "Cidade", "{$registro["cidade"]}") );
        $this->addDetalhe( array( "Tipo do Logradouro", "{$registro["ref_idtlog"]}") );
        $this->addDetalhe( array( "UF", "{$registro["ref_sigla_uf"]}") );
        $this->addDetalhe( array( "Número", "{$registro["numero"]}") );
        $this->addDetalhe( array( "Complemento", "{$registro["complemento"]}") );
        $this->addDetalhe( array( "DDD Telefone", "{$registro["ddd_telefone"]}") );
        $this->addDetalhe( array( "Telefone", "{$registro["telefone"]}") );
        $this->addDetalhe( array( "Nome do Responsável", "{$registro["nm_responsavel"]}") );

        $obj_permissoes = new clsPermissoes();
        if ( $obj_permissoes->permissao_cadastra( 559, $this->pessoa_logada, 3 ) ) {
            $this->url_editar = "educar_instituicao_cad.php?cod_instituicao={$registro["cod_instituicao"]}";
        }
        $this->url_cancelar = "educar_instituicao_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "Detalhe da institui&ccedil;&atilde;o"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->array_botao[] = 'Documentação padrão';
        $this->array_botao_url_script[] = "go(\"educar_documentacao_instituicao_cad.php?cod_instituicao={$registro["cod_instituicao"]}\")";
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
