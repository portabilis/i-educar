<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  *                                      *
  * @author Prefeitura Municipal de ItajaÃ­                 *
  * @updated 29/03/2007                          *
  *   Pacote: i-PLB Software PÃºblico Livre e Brasileiro          *
  *                                    *
  * Copyright (C) 2006  PMI - Prefeitura Municipal de ItajaÃ­       *
  *           ctima@itajai.sc.gov.br                 *
  *                                    *
  * Este  programa  Ã©  software livre, vocÃª pode redistribuÃ­-lo e/ou   *
  * modificÃ¡-lo sob os termos da LicenÃ§a PÃºblica Geral GNU, conforme   *
  * publicada pela Free  Software  Foundation,  tanto  a versÃ£o 2 da   *
  * LicenÃ§a   como  (a  seu  critÃ©rio)  qualquer  versÃ£o  mais  nova.  *
  *                                    *
  * Este programa  Ã© distribuÃ­do na expectativa de ser Ãºtil, mas SEM   *
  * QUALQUER GARANTIA. Sem mesmo a garantia implÃ­cita de COMERCIALI-   *
  * ZAÃÃO  ou  de ADEQUAÃÃO A QUALQUER PROPÃSITO EM PARTICULAR. Con-   *
  * sulte  a  LicenÃ§a  PÃºblica  Geral  GNU para obter mais detalhes.   *
  *                                    *
  * VocÃª  deve  ter  recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU   *
  * junto  com  este  programa. Se nÃ£o, escreva para a Free Software   *
  * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA   *
  * 02111-1307, USA.                           *
  *                                    *
  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "App/Model/MatriculaSituacao.php" );

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo( "{$this->_instituicao} i-Educar - Bloqueio do ano letivo" );
    $this->processoAp = "21251";
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

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $sequencial;

  function Inicializar()
  {
    $retorno = "Editar";
    

    $this->ref_cod_matricula=$_GET["ref_cod_matricula"];
    $this->ref_cod_turma=$_GET["ref_cod_turma"];
    $this->sequencial=$_GET["sequencial"];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada,3, "educar_matricula_historico_lst.php?ref_cod_matricula=".$this->ref_cod_matricula);
    $this->fexcluir = $obj_permissoes->permissao_excluir(578,$this->pessoa_logada,3);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Histórico de enturmações da matrícula"
    ));
    $this->enviaLocalizacao($localizacao->montar());
    $this->url_cancelar = "educar_matricula_historico_lst.php?ref_cod_matricula=".$this->ref_cod_matricula;
    return $retorno;
  }

  function Gerar()
  {
    $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
    $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

    $enturmacao = new clsPmieducarMatriculaTurma($this->ref_cod_matricula);
    $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
    $enturmacao->ref_cod_turma = $this->ref_cod_turma;
    $enturmacao->sequencial = $this->sequencial;
    $enturmacao = $enturmacao->detalhe();

    $matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
    $matricula = $matricula->detalhe();

    $instituicao = new clsPmieducarInstituicao($matricula['ref_cod_instituicao']);
    $instituicao = $instituicao->detalhe();

    $escola = new clsPmieducarEscola($matricula['ref_ref_cod_escola']);
    $escola = $escola->detalhe();

    $this->campoRotulo('ano', 'Ano', $matricula['ano']);
    $this->campoRotulo('nm_instituicao', 'Instituição', $instituicao['nm_instituicao']);
    $this->campoRotulo('nm_escola', 'Escola', $escola['nome']);
    $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $enturmacao['nome']);
    $this->campoRotulo('sequencial', 'Sequencial', $enturmacao['sequencial']);

    switch ($matricula['aprovado']) {
      case 1:
        $situacao = 'Aprovado';
        break;
      case 2:
        $situacao = 'Reprovado';
        break;
      case 3:
        $situacao = 'Cursando';
        break;
      case 4:
        $situacao = 'Transferido';
        break;
      case 5:
        $situacao = 'Reclassificado';
        break;
      case 6:
        $situacao = 'Abandono';
        break;
      case 7:
        $situacao = 'Em Exame';
        break;
      case 12:
        $situacao = 'Aprovado com dependência';
        break;
      case 13:
        $situacao = 'Aprovado pelo conselho';
        break;
      case 14:
        $situacao = 'Reprovado por faltas';
        break;
      default:
        $situacao = '';
        break;
    }
    $this->campoRotulo('situacao', 'Situação', $situacao);

    $this->inputsHelper()->date('data_enturmacao', array('label' => 'Data enturmação', 'value' => dataToBrasil($enturmacao['data_enturmacao']), 'placeholder' => ''));
    $this->inputsHelper()->date('data_exclusao', array('label' => 'Data de saí­da', 'value' => dataToBrasil($enturmacao['data_exclusao']), 'placeholder' => '', 'required' => false));
  }

  function Editar()
  {
    

    $enturmacao = new clsPmieducarMatriculaTurma();
    $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
    $enturmacao->ref_cod_turma = $this->ref_cod_turma;
    $enturmacao->sequencial = $this->sequencial;
    $enturmacao->ref_usuario_exc = $this->pessoa_logada;
    $enturmacao->data_enturmacao = dataToBanco($this->data_enturmacao);
    $enturmacao->data_exclusao = dataToBanco($this->data_exclusao);

    $dataSaidaEnturmacaoAnterior = $enturmacao->getDataSaidaEnturmacaoAnterior($this->ref_cod_matricula, $this->sequencial);

    $matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
    $matricula = $matricula->detalhe();
    $dataSaidaMatricula = "";
    if($matricula['data_cancel']){
      $dataSaidaMatricula = date("Y-m-d", strtotime($matricula['data_cancel']));
    }

    //echo $enturmacao->data_exclusao . "<br>";
    //echo $dataSaidaMatricula;
    //die();

    $seqUltimaEnturmacao = $enturmacao->getUltimaEnturmacao($this->ref_cod_matricula);

    if ($enturmacao->data_exclusao && ($enturmacao->data_exclusao < $enturmacao->data_enturmacao)) {
      $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br> A data de sa&iacute;da n&atilde;o pode ser anterior a data de enturma&ccedil;&atilde;o.";
      return false;
    }

   if ($dataSaidaEnturmacaoAnterior && ($enturmacao->data_enturmacao < $dataSaidaEnturmacaoAnterior)) {
      $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br> A data de enturma&ccedil;&atilde;o n&atilde;o pode ser anterior a data de sa&iacute;da da enturma&ccedil;&atilde;o antecessora.";
      return false;
    }

    if ($dataSaidaMatricula &&

        ($enturmacao->data_exclusao > $dataSaidaMatricula) &&

        (App_Model_MatriculaSituacao::TRANSFERIDO    == $matricula['aprovado'] ||
         App_Model_MatriculaSituacao::ABANDONO       == $matricula['aprovado'] ||
         App_Model_MatriculaSituacao::RECLASSIFICADO == $matricula['aprovado']) &&
         ($this->sequencial == $seqUltimaEnturmacao)

        ) {

      $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br> A data de sa&iacute;da n&atilde;o pode ser posterior a data de sa&iacute;da da matricula.";
      return false;
    }

    $editou = $enturmacao->edita();
    if( $editou )
    {
      if(is_null($dataSaidaMatricula) || empty($dataSaidaMatricula)){
        $dataSaidaMatricula = $enturmacao->data_exclusao;

        $matricula_get = new clsPmieducarMatricula($this->ref_cod_matricula, NULL, NULL, NULL, NULL, $matricula['ref_usuario_cad'], $matricula['ref_cod_aluno'], $matricula['aprovado'],
                                                   NULL, NULL, NULL, $matricula['ano'], $matricula['ultima_matricula'], NULL, NULL, NULL, NULL, $matricula['ref_cod_curso'], NULL, NULL,
                                                   NULL, $dataSaidaMatricula, NULL);
        $matricula_get->edita();
      }

      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      $this->simpleRedirect("educar_matricula_historico_lst.php?ref_cod_matricula=".$this->ref_cod_matricula);
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    return false;
  }

  function Excluir()
  {
    

    $enturmacao = new clsPmieducarMatriculaTurma();
    $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
    $enturmacao->ref_cod_turma = $this->ref_cod_turma;
    $enturmacao->sequencial = $this->sequencial;
    $enturmacao->ref_usuario_exc = $this->pessoa_logada;
    $excluiu = $enturmacao->excluir();
    if( $excluiu )
    {
      $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
      $this->simpleRedirect("educar_matricula_historico_lst.php?ref_cod_matricula=".$this->ref_cod_matricula);
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    return false;
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
