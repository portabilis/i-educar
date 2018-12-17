<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de ItajaÃ­                              *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software PÃºblico Livre e Brasileiro                   *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de ItajaÃ­            *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  Ã©  software livre, vocÃª pode redistribuÃ­-lo e/ou  *
    *   modificÃ¡-lo sob os termos da LicenÃ§a PÃºblica Geral GNU, conforme  *
    *   publicada pela Free  Software  Foundation,  tanto  a versÃ£o 2 da    *
    *   LicenÃ§a   como  (a  seu  critÃ©rio)  qualquer  versÃ£o  mais  nova.     *
    *                                                                        *
    *   Este programa  Ã© distribuÃ­do na expectativa de ser Ãºtil, mas SEM  *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implÃ­cita de COMERCIALI-    *
    *   ZAÃÃO  ou  de ADEQUAÃÃO A QUALQUER PROPÃSITO EM PARTICULAR. Con-     *
    *   sulte  a  LicenÃ§a  PÃºblica  Geral  GNU para obter mais detalhes.   *
    *                                                                        *
    *   VocÃª  deve  ter  recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU     *
    *   junto  com  este  programa. Se nÃ£o, escreva para a Free Software    *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'modules/Api/Model/ApiExternaController.php';
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ocorr&ecirc;ncia Disciplinar" );
        $this->processoAp = "578";
        $this->addEstilo("localizacaoSistema");
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_ocorrencia_disciplinar;
    var $ref_cod_matricula;
    var $ref_cod_tipo_ocorrencia_disciplinar;
    var $sequencial;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $observacao;
    var $data_exclusao;
    var $ativo;

    var $data_cadastro;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    var $hora_cadastro;

    function Inicializar()
    {
        $retorno = "Novo";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->ref_cod_matricula = $_GET["ref_cod_matricula"];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra( 21145, $this->pessoa_logada, 7,  "educar_matricula_lst.php" );

        $data = getdate();

        $data['mday'] = sprintf("%02d",$data['mday']);
        $data['mon'] = sprintf("%02d",$data['mon']);
        $data['hours'] = sprintf("%02d",$data['hours']);
        $data['minutes'] = sprintf("%02d",$data['minutes']);

        $this->data_cadastro = "{$data['mday']}/{$data['mon']}/{$data['year']}";
        $this->hora_cadastro = "{$data['hours']}:{$data['minutes']}";

        $this->sequencial=$_GET["sequencial"];
        $this->ref_cod_matricula=$_GET["ref_cod_matricula"];
        $this->ref_cod_tipo_ocorrencia_disciplinar=$_GET["ref_cod_tipo_ocorrencia_disciplinar"];

        if (is_numeric($this->ref_cod_matricula) &&
            is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) &&
            is_numeric($this->sequencial))
        {
            $obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial);
            $registro = $obj->detalhe();
            if ($registro)
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->hora_cadastro = dataFromPgToBr($this->data_cadastro,'H:i');
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);

              $obj_permissoes = new clsPermissoes();
              if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
              {
                  $this->fexcluir = true;
              }

                $retorno = "Editar";
            }
        }

        if( class_exists( "clsPmieducarMatricula" ) && is_numeric($this->ref_cod_matricula))
        {
            $obj_ref_cod_matricula = new clsPmieducarMatricula();
            $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
            $this->ref_cod_escola = $detalhe_aluno['ref_ref_cod_escola'];
            $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
            $det_escola = $obj_escola->detalhe();
            $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];
        }

        if (is_numeric($this->ref_cod_matricula))
            $this->url_cancelar = ($retorno == "Editar") ? "educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_tipo_ocorrencia_disciplinar={$registro["ref_cod_tipo_ocorrencia_disciplinar"]}&sequencial={$registro["sequencial"]}" : "educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Ocorrências disciplinares da matrícula"
    ));
    $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        /**
         * Busca infos aluno
         */


        if( class_exists( "clsPmieducarMatricula" ) && is_numeric($this->ref_cod_matricula))
        {
            $obj_ref_cod_matricula = new clsPmieducarMatricula();
            $detalhe_aluno = $obj_ref_cod_matricula->lista($this->ref_cod_matricula);
            if($detalhe_aluno)
                $detalhe_aluno = array_shift($detalhe_aluno);
            $obj_aluno = new clsPmieducarAluno();
            $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

            $this->campoRotulo("nm_pessoa","Nome do Aluno",$det_aluno['nome_aluno']);
        }else{
            $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));
            // FIXME #parameters
            $this->inputsHelper()->simpleSearchMatricula(null);
            $this->inputsHelper()->hidden('somente_andamento');
        }

        // primary keys
        $this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );
        $this->campoOculto( "ref_cod_tipo_ocorrencia_disciplinar", $this->ref_cod_tipo_ocorrencia_disciplinar );
        $this->campoOculto( "sequencial", $this->sequencial );
        $this->campoOculto( "cod_ocorrencia_disciplinar", $this->cod_ocorrencia_disciplinar);

        $this->campoData("data_cadastro","Data Atual",$this->data_cadastro,true);
        $this->campoHora("hora_cadastro","Horas",$this->hora_cadastro,true);

        // foreign keys
    /*  $opcoes = array( "" => "Selecione" );
        if( class_exists( "clsPmieducarMatricula" ) )
        {
            $objTemp = new clsPmieducarMatricula();
            $lista = $objTemp->lista();
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_matricula']}"] = "{$registro['ref_ref_cod_escola']}";
                }
            }
        }
        else
        {
            echo "<!--\nErro\nClasse clsPmieducarMatricula nao encontrada\n-->";
            $opcoes = array( "" => "Erro na geracao" );
        }
        $this->campoLista( "ref_cod_matricula", "Matricula", $opcoes, $this->ref_cod_matricula );
        */

        //$opcoes = array('' => 'Selecione um aluno clicando na lupa');
        //$this->campoListaPesq("nm_aluno", "Aluno", $opcoes,$this->ref_cod_matricula,"educar_pesquisa_matricula_lst.php","",false,"","",null,"","",true);
        //$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);



        $opcoes = array( "" => "Selecione" );
        if( class_exists( "clsPmieducarTipoOcorrenciaDisciplinar" ) )
        {
            $objTemp = new clsPmieducarTipoOcorrenciaDisciplinar();
            $lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_tipo_ocorrencia_disciplinar']}"] = "{$registro['nm_tipo']}";
                }
            }
        }
        else
        {
            echo "<!--\nErro\nClasse clsPmieducarTipoOcorrenciaDisciplinar nao encontrada\n-->";
            $opcoes = array( "" => "Erro na geracao" );
        }
        $this->campoLista( "ref_cod_tipo_ocorrencia_disciplinar", "Tipo Ocorr&ecirc;ncia Disciplinar", $opcoes, $this->ref_cod_tipo_ocorrencia_disciplinar );


        // text
        $this->campoMemo( "observacao", "Observac&atilde;o", $this->observacao, 60, 10, true );

        $this->campoCheck("visivel_pais",
                          Portabilis_String_Utils::toLatin1("Visí­vel aos pais"),
                          $this->visivel_pais,
                          Portabilis_String_Utils::toLatin1("Marque este campo, caso deseje que os pais do aluno possam visualizar tal ocorrência disciplinar."));

    }

    function Novo()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );

        $this->visivel_pais = is_null($this->visivel_pais) ? 0 : 1;

        $voltaListagem = is_numeric($this->ref_cod_matricula);

        $this->ref_cod_matricula = is_numeric($this->ref_cod_matricula) ? $this->ref_cod_matricula : $this->getRequest()->matricula_id;

    $obj_ref_cod_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
    $detalhe_mat = $obj_ref_cod_matricula->detalhe();
    $this->ref_cod_instituicao = $detalhe_mat['ref_cod_instituicao'];

        $obj = new clsPmieducarMatriculaOcorrenciaDisciplinar( $this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, null, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->getDataHoraCadastro(), $this->data_exclusao, $this->ativo, $this->visivel_pais);
        $cod_ocorrencia_disciplinar = $obj->cadastra();
        if( $cod_ocorrencia_disciplinar )
        {

            $ocorrenciaDisciplinar = new clsPmieducarMatriculaOcorrenciaDisciplinar();
            $ocorrenciaDisciplinar->cod_ocorrencia_disciplinar = $cod_ocorrencia_disciplinar;

            $ocorrenciaDisciplinar = $ocorrenciaDisciplinar->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("matricula_ocorrencia_disciplinar", $this->pessoa_logada, $cod_ocorrencia_disciplinar);
            $auditoria->inclusao($ocorrenciaDisciplinar);

            if(($this->visivel_pais) && ($this->possuiConfiguracaoNovoEducacao())){
                $resposta = json_decode($this->enviaOcorrenciaNovoEducacao($cod_ocorrencia_disciplinar));

                if(is_array($resposta->errors)){
                    echo Portabilis_String_Utils::toLatin1("Erro ao enviar ocorrencia disciplinar ao sistema externo: " . $resposta->errors[0]);
                    die;
                }

            }
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            if ($voltaListagem)
                header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
            else
                echo "<script language='javascript' type='text/javascript'>alert('Cadastro efetuado com sucesso.');</script>";
                echo "<script language='javascript' type='text/javascript'>window.location.href='educar_matricula_ocorrencia_disciplinar_cad.php'</script>";
                //header( "Location: educar_matricula_ocorrencia_disciplinar_cad.php");
            return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nis_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->observacao )\n-->";
        return false;
    }

    function Editar()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );

        $ocorrenciaDisciplinar = new clsPmieducarMatriculaOcorrenciaDisciplinar();
        $ocorrenciaDisciplinar->cod_ocorrencia_disciplinar = $this->cod_ocorrencia_disciplinar;
        $ocorrenciaDisciplinarDetalheAntes = $ocorrenciaDisciplinar->detalhe();

        $this->visivel_pais = is_null($this->visivel_pais) ? 0 : 1;

        $voltaListagem = is_numeric($this->ref_cod_matricula);

        $this->ref_cod_matricula = is_numeric($this->ref_cod_matricula) ? $this->ref_cod_matricula : $this->getRequest()->matricula_id;

        $obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->getDataHoraCadastro(), $this->data_exclusao, $this->ativo, $this->visivel_pais);

        $editou = $obj->edita();
        if( $editou )
        {
            $ocorrenciaDisciplinarDetalheDepois = $ocorrenciaDisciplinar->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("matricula_ocorrencia_disciplinar", $this->pessoa_logada, $this->cod_ocorrencia_disciplinar);
            $auditoria->alteracao($ocorrenciaDisciplinarDetalheAntes, $ocorrenciaDisciplinarDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            if ($voltaListagem)
                header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
            else
                header( "Location: educar_matricula_ocorrencia_disciplinar_cad.php");
            die();
            return true;
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
        return false;
    }

    function Excluir()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );

        $ocorrenciaDisciplinar = new clsPmieducarMatriculaOcorrenciaDisciplinar();
        $ocorrenciaDisciplinar->cod_ocorrencia_disciplinar = $this->cod_ocorrencia_disciplinar;
        $ocorrenciaDisciplinar = $ocorrenciaDisciplinar->detalhe();

        $this->data_cadastro = Portabilis_Date_Utils::brToPgSQL($this->data_cadastro);
        $obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("matricula_ocorrencia_disciplinar", $this->pessoa_logada, $this->cod_ocorrencia_disciplinar);
            $auditoria->exclusao($ocorrenciaDisciplinar);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
            die();
            return true;
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
        return false;
    }

  protected function getDataHoraCadastro() {
    return $this->data_cadastro = dataToBanco($this->data_cadastro) . " " . $this->hora_cadastro;
  }
  protected function enviaOcorrenciaNovoEducacao($cod_ocorrencia_disciplinar){

    $tmp_obj = new clsPmieducarConfiguracoesGerais( $this->ref_cod_instituicao );
    $instituicao = $tmp_obj->detalhe();

    $obj_tmp   = new clsPmieducarMatricula($this->ref_cod_matricula);
    $det_tmp   = $obj_tmp->detalhe();
    $cod_aluno = $det_tmp["ref_cod_aluno"];

    $cod_escola = $det_tmp["ref_ref_cod_escola"];

    $obj_tmp = new clsPmieducarTipoOcorrenciaDisciplinar($this->ref_cod_tipo_ocorrencia_disciplinar);
    $det_tmp = $obj_tmp->detalhe();

    $tipo_ocorrencia = $det_tmp["nm_tipo"];

    $params   = [
      'token'        => $GLOBALS['coreExt']['Config']->apis->access_key,
      'api_code'     => $cod_ocorrencia_disciplinar,
      'student_code' => $cod_aluno,
      'description'  => $this->observacao,
      'occurred_at'  => $this->data_cadastro,
      'unity_code'   => $cod_escola,
      'kind'         => $tipo_ocorrencia,
    ];

    $requisicao = new ApiExternaController([
      'url'            => $instituicao['url_novo_educacao'],
      'recurso'        => 'ocorrencias-disciplinares',
      'tipoRequisicao' => ApiExternaController::REQUISICAO_POST,
      'params'         => $params,
      'token_header' => $GLOBALS['coreExt']['Config']->apis->educacao_token_header,
      'token_key'    => $GLOBALS['coreExt']['Config']->apis->educacao_token_key,
    ]
    );


    return $requisicao->executaRequisicao();
  }

  protected function possuiConfiguracaoNovoEducacao(){
    $tmp_obj = new clsPmieducarConfiguracoesGerais( $this->ref_cod_instituicao );
    $instituicao = $tmp_obj->detalhe();

      return strlen($instituicao['url_novo_educacao']) > 0;
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
