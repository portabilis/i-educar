<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006 Prefeitura Municipal de Itajaí
 * <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matrícula');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_matricula;
  var $ref_cod_reserva_vaga;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $aprovado;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ano;
  var $data_matricula;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $ref_cod_escola;
  var $ref_cod_turma;

  var $semestre;
  var $is_padrao;
  var $dependencia;

  var $ref_cod_candidato_reserva_vaga;
  var $ref_cod_candidato_fila_unica;

  function Inicializar()
  {
    //$retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_turma_copiar_enturmacoes = $_GET['ref_cod_turma_copiar_enturmacoes'];
    $this->cod_matricula = $_GET['cod_matricula'];
    $this->ref_cod_aluno = $_GET['ref_cod_aluno'];
    $this->ref_cod_candidato_reserva_vaga = $_GET['ref_cod_candidato_reserva_vaga'];
    $this->ref_cod_candidato_fila_unica = $_GET['cod_candidato_fila_unica'];

    $retorno = ($this->ref_cod_turma_copiar_enturmacoes ? 'Enturmar' : 'Novo');

    $obj_aluno = new clsPmieducarAluno($this->ref_cod_aluno);

    if (! $obj_aluno->existe() and !$this->ref_cod_turma_copiar_enturmacoes) {
      header('Location: educar_aluno_lst.php');
      die;
    }
    if ($this->ref_cod_turma_copiar_enturmacoes){
      $this->nome_url_sucesso = Portabilis_String_Utils::toLatin1('Gravar enturmações');
      $url = 'educar_matriculas_turma_cad.php?ref_cod_turma=' . $this->ref_cod_turma_copiar_enturmacoes;
    }else
      $url = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

    if (is_numeric($this->cod_matricula)) {
      if ($obj_permissoes->permissao_excluir(627, $this->pessoa_logada, 7)) {
        $this->Excluir();
      }
    }

    $this->url_cancelar = $url;

    $nomeMenu = $retorno == "Editar" ? $retorno : "Nova";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""        => "{$nomeMenu} matrícula"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    // primary keys
    $this->campoOculto("ref_cod_turma_copiar_enturmacoes", $this->ref_cod_turma_copiar_enturmacoes);
    $this->campoOculto("cod_matricula", $this->cod_matricula);
    $this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);
    $this->campoOculto("ref_cod_candidato_reserva_vaga", $this->ref_cod_candidato_reserva_vaga);
    $this->campoOculto("ref_cod_candidato_fila_unica", $this->ref_cod_candidato_fila_unica);

    if ($this->ref_cod_aluno){
      $obj_aluno = new clsPmieducarAluno();
      $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_aluno)) {
        $det_aluno      = array_shift($lst_aluno);
        $this->nm_aluno = $det_aluno['nome_aluno'];
        $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
      }

      /*
       * Verifica se existem matrículas para o aluno para apresentar o campo
       * transferência, necessário para o relatório de movimentação mensal.
       */
      $obj_matricula = new clsPmieducarMatricula();
      $lst_matricula = $obj_matricula->lista(NULL, NULL, NULL, NULL, NULL, NULL,
        $this->ref_cod_aluno);
    }
    if ($this->ref_cod_turma_copiar_enturmacoes)
      $this->nome_url_sucesso = Portabilis_String_Utils::toLatin1('Gravar enturmações');
    // inputs

    $anoLetivoHelperOptions = array('situacoes' => array('em_andamento', 'nao_iniciado'));

    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola', 'curso', 'serie', 'turma'));
    $this->inputsHelper()->date('data_matricula', array('label' => Portabilis_String_Utils::toLatin1('Data da matrícula'), 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y') ));
    $this->inputsHelper()->hidden('ano_em_andamento', array('value' => '1'));

    if($GLOBALS['coreExt']['Config']->app->matricula->dependencia == 1)
      $this->inputsHelper()->checkbox('dependencia',
                                      array('label' => Portabilis_String_Utils::toLatin1('Matrícula de dependência?'),
                                            'value' => $this->dependencia));

    if (is_numeric($this->ref_cod_curso)) {
      $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
      $det_curso = $obj_curso->detalhe();

      if (is_numeric($det_curso['ref_cod_tipo_avaliacao'])) {
        $this->campoOculto('apagar_radios', $det_curso['padrao_ano_escolar']);
        $this->campoOculto('is_padrao', $det_curso['padrao_ano_escolar']);
      }
    }

      $script = array('/modules/Cadastro/Assets/Javascripts/Matricula.js');
      Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->acao_enviar = 'formUtils.submit()';
  }

  protected function getCurso($id) {
    $curso = new clsPmieducarCurso($id);
    return $curso->detalhe();
  }
  function Enturmar(){
    $enturmacoes_turma_dest = Portabilis_Utils_Database::fetchPreparedQuery("
                                                                  select * from pmieducar.matricula_turma
                                                                  where ref_cod_turma = {$this->ref_cod_turma} and ativo = 1");
    $qtq_alunos = count($enturmacoes_turma_dest);
    $db = new clsBanco();
    $max_aluno = $db->CampoUnico("select max_aluno from pmieducar.turma where cod_turma = $this->ref_cod_turma");
    $saldo_turma = $max_aluno - $qtq_alunos;
//echo $this->ref_cod_turma;die;
    $enturmacoes = Portabilis_Utils_Database::fetchPreparedQuery("
                                                                  select * from pmieducar.matricula_turma
                                                                  where ref_cod_turma = {$this->ref_cod_turma_copiar_enturmacoes} and ativo = 1");
    $qtd_alunos_new = count($enturmacoes);
    if ($qtd_alunos_new < $saldo_turma){
      foreach ($enturmacoes as $enturmar) {
         //echo $enturmar['ref_cod_matricula']."fd".$this->ref_cod_turma;die;
        $dado_matricula_old = Portabilis_Utils_Database::fetchPreparedQuery("
                                                                  select * from pmieducar.matricula where cod_matricula = {$enturmar['ref_cod_matricula']} limit 1");
        if (!$existe){
        $data = date( "Y-m-d");
        $datah = date( "Y-m-d H:i:s");
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        //print_r($dado_matricula_old[0]['ref_ref_cod_escola']);die;
        $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);
        $obj = new clsPmieducarMatricula(NULL, NULL,
          $dado_matricula_old[0]['ref_ref_cod_escola'], $dado_matricula_old[0]['ref_ref_cod_serie'], NULL,
          $this->pessoa_logada, $dado_matricula_old[0]['ref_cod_aluno'], 3, NULL, NULL, 1, $dado_matricula_old[0]['ano'],
          1, NULL, NULL, NULL, NULL, $dado_matricula_old[0]['ref_cod_curso'],
          NULL, 1, $datah);
        $matricula_new = $obj->cadastra();
        $db = new clsBanco();
        $existe = $db->CampoUnico("select 1 from pmieducar.matricula_turma, pmieducar.matricula
                                      where ref_cod_matricula = cod_matricula
                                      and ref_cod_turma = {$this->ref_cod_turma}
                                      and ref_cod_aluno = {$dado_matricula_old[0]['ref_cod_aluno']}");
          $db = new clsBanco();
          $db->CampoUnico("insert into pmieducar.matricula_turma
                           (ref_cod_matricula,
                            ref_cod_turma,
                            sequencial,
                            ref_usuario_exc,
                            ref_usuario_cad,
                            data_cadastro,
                            ativo,
                            data_enturmacao)
                           values
                           ({$matricula_new}, {$this->ref_cod_turma}, {$enturmar['sequencial']}, NULL,
                            {$enturmar['ref_usuario_cad']}, '{$datah}', {$enturmar['ativo']}, '{$data}')");
        }
      }
      header("Location: educar_matriculas_turma_cad.php?ref_cod_turma= {$this->ref_cod_turma}");
        die;
    }else{
      $this->mensagem = Portabilis_String_Utils::toLatin1("A turma não tem saldo de vagas suficiente.");
      //header("Location: educar_matricula_cad.php?ref_cod_turma_copiar_enturmacoes= {$this->ref_cod_turma_copiar_enturmacoes}");
      return FALSE;
    }
  }

  function Novo()
  {
    $dependencia = $this->dependencia == 'on';

    if ($dependencia && !$this->verificaQtdeDependenciasPermitida()) {
      return false;
    }

    if ($this->verificaAlunoFalecido()) {
      $this->mensagem = Portabilis_String_Utils::toLatin1("Não é possível matricular alunos falecidos.");
    }

    if (!$this->permiteMatriculaSerieDestino() && $this->bloqueiaMatriculaSerieNaoSeguinte()) {
      $this->mensagem = Portabilis_String_Utils::toLatin1("Não é possível matricular alunos em séries fora da sequência de enturmação.");
      return false;
    }

    $db = new clsBanco();
    $somente_do_bairro = $db->CampoUnico("SELECT matricula_apenas_bairro_escola FROM pmieducar.instituicao where cod_instituicao = {$this->ref_cod_instituicao}");
    if ($somente_do_bairro == 't'){
      $db = new clsBanco();
      $bairro_escola = $db->CampoUnico("select Upper(bairro) from cadastro.endereco_externo where idpes = (select idpes from cadastro.juridica where idpes = (select ref_idpes from pmieducar.escola where cod_escola = {$this->ref_cod_escola}))");
      $db = new clsBanco();
      $bairro_aluno = $db->CampoUnico("select Upper(nome) from public.bairro where idbai = (select idbai from cadastro.endereco_pessoa where idpes = (select ref_idpes from pmieducar.aluno where cod_aluno = {$this->ref_cod_aluno}))");
    if (strcasecmp($bairro_aluno, $bairro_escola) != 0){
      $this->mensagem = Portabilis_String_Utils::toLatin1("O aluno deve morar no mesmo bairro da escola");
      return FALSE;}
    }
    $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
    $this->nome_url_cancelar = 'Cancelar';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);

    //novas regras matricula aluno
    $this->ano = $_POST['ano'];

    $anoLetivoEmAndamentoEscola = new clsPmieducarEscolaAnoLetivo();
    $anoLetivoEmAndamentoEscola = $anoLetivoEmAndamentoEscola->lista($this->ref_cod_escola,
                                                                     $this->ano,
                                                                     null,
                                                                     null,
                                                                     1, /*somente em andamento */
                                                                     null,
                                                                     null,
                                                                     null,
                                                                     null,
                                                                     1
                                                                     );
    $objEscolaSerie = new clsPmieducarEscolaSerie();
    $dadosEscolaSerie = $objEscolaSerie->lista($this->ref_cod_escola, $this->ref_cod_serie);
    if(! $this->existeVagasDisponíveis() && $dadosEscolaSerie[0]['bloquear_enturmacao_sem_vagas'])
      return false;

    if(is_array($anoLetivoEmAndamentoEscola)) {
      require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
      $db = new clsBanco();

      $db->Consulta("SELECT ref_ref_cod_serie,
                            ref_cod_curso
                       FROM pmieducar.matricula
                      WHERE ano = $this->ano
                        AND ativo = 1
                        AND ref_ref_cod_escola = $this->ref_cod_escola
                        AND ref_cod_curso = $this->ref_cod_curso
                        AND ref_cod_aluno = $this->ref_cod_aluno
                        AND aprovado = 3
                        AND dependencia = FALSE ");

      $db->ProximoRegistro();
      $m = $db->Tupla();
      if (is_array($m) && count($m) && !$dependencia) {

        $curso = $this->getCurso($this->ref_cod_curso);

        if ($m['ref_ref_cod_serie'] == $this->ref_cod_serie) {
          $this->mensagem .= "Este aluno j&aacute; est&aacute; matriculado nesta s&eacute;rie e curso, n&atilde;o &eacute; possivel matricular um aluno mais de uma vez na mesma s&eacute;rie.<br />";

          return false;
        }

        elseif ($curso['multi_seriado'] != 1) {
          $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);
          $serie = $serie->detalhe();

          if (is_array($serie) && count($serie))
            $nomeSerie = $serie['nm_serie'];
          else
            $nomeSerie = '';

          $this->mensagem .= "Este aluno j&aacute; est&aacute; matriculado no(a) '$nomeSerie' deste curso e escola. Como este curso n&atilde;o &eacute; multi seriado, n&atilde;o &eacute; possivel manter mais de uma matricula em andamento para o mesmo curso.<br />";

          return false;
        }
      }

      else
      {
        $db->Consulta("select ref_ref_cod_escola, ref_cod_curso, ref_ref_cod_serie, ano from pmieducar.matricula where ativo = 1 and ref_ref_cod_escola != $this->ref_cod_escola and ref_cod_aluno = $this->ref_cod_aluno AND dependencia = FALSE and aprovado = 3 and not exists (select 1 from pmieducar.transferencia_solicitacao as ts where ts.ativo = 1 and ts.ref_cod_matricula_saida = matricula.cod_matricula )");

        $db->ProximoRegistro();
        $m = $db->Tupla();
        if (is_array($m) && count($m) && !$dependencia){

          $mesmoCursoAno = ($m['ref_cod_curso'] == $this->ref_cod_curso && $m['ano'] == $this->ano);
          $cursoADeferir = new clsPmieducarCurso($this->ref_cod_curso);
          $cursoDeAtividadeComplementar = $cursoADeferir->cursoDeAtividadeComplementar();

          if (($mesmoCursoAno || $GLOBALS['coreExt']['Config']->app->matricula->multiplas_matriculas == 0) && !$cursoDeAtividadeComplementar){

            require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
            require_once 'include/pessoa/clsJuridica.inc.php';

            $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);
            $serie = $serie->detalhe();
            if (is_array($serie) && count($serie))
              $serie = $serie['nm_serie'];
            else
              $serie = '';

            $escola = new clsPmieducarEscola($m['ref_ref_cod_escola']);
            $escola = $escola->detalhe();
            if (is_array($escola) && count($escola))
            {
              $escola = new clsJuridica($escola['ref_idpes']);
              $escola = $escola->detalhe();
              if (is_array($escola) && count($escola))
                $escola = $escola['fantasia'];
              else
                $escola = '';
            }
            else
              $escola = '';

            $curso = new clsPmieducarCurso($m['ref_cod_curso']);
            $curso = $curso->detalhe();
            if (is_array($curso) && count($curso))
              $curso = $curso['nm_curso'];
            else
              $curso = '';

            $this->mensagem .= "Este aluno j&aacute; est&aacute; matriculado no(a) '$serie' do curso '$curso' na escola '$escola', para matricular este aluno na sua escola solicite transfer&ecirc;ncia ao secret&aacute;rio(a) da escola citada.<br />";

            return false;
          }
        }
    }
      $serie = new clsPmieducarSerie($this->ref_cod_serie);
      $detSerie = $serie->detalhe();

      $alertaFaixaEtaria = $detSerie['alerta_faixa_etaria'] == "t";
      $bloquearMatriculaFaixaEtaria = $detSerie['bloquear_matricula_faixa_etaria'] == "t";

      $verificarDataCorte = $alertaFaixaEtaria || $bloquearMatriculaFaixaEtaria;

      @session_start();
      $reload = $_SESSION['reload_faixa_etaria'];
      @session_write_close();

      if ($verificarDataCorte && !$reload) {

        $instituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
        $instituicao = $instituicao->detalhe();

        $dataCorte = $instituicao["data_base_matricula"];
        $idadeInicial = $detSerie['idade_inicial'];
        $idadeFinal = $detSerie['idade_final'];

        $objAluno = new clsPmieducarAluno($this->ref_cod_aluno);
        $detAluno = $objAluno->detalhe();

        $objPes = new clsPessoaFisica($detAluno["ref_idpes"]);
        $detPes = $objPes->detalhe();

        $dentroPeriodoCorte = $serie->verificaPeriodoCorteEtarioDataNascimento($detPes["data_nasc"], $this->ano);

        if ($bloquearMatriculaFaixaEtaria && !$dentroPeriodoCorte) {
          $this->mensagem = Portabilis_String_Utils::toLatin1('Não foi possível realizar a matrícula, pois a idade do aluno está fora da faixa etária da série');
          return FALSE;
        } else if ($alertaFaixaEtaria && !$dentroPeriodoCorte) {
            echo "<script type=\"text/javascript\">
                    var msg = '".Portabilis_String_Utils::toLatin1('A idade do aluno encontra-se fora da faixa etária pré-definida na série, deseja continuar com a matrícula?')."';
                    if (!confirm(msg)) {
                      window.location = 'educar_aluno_det.php?cod_aluno=".$this->ref_cod_aluno."';
                    } else {
                      parent.document.getElementById('formcadastro').submit();
                    }
                  </script>";
          //Permite que o usuário possa salvar a matrícula na próxima tentativa
          $reload = 1;
          @session_start();
          $_SESSION['reload_faixa_etaria'] = $reload;
          @session_write_close();

          return TRUE;
        }
      }

      $objAluno = new clsPmieducarAluno();
      $alunoInep = $objAluno->verificaInep($this->ref_cod_aluno);

      $objSerie = new clsPmieducarSerie($this->ref_cod_serie);
      $serieDet = $objSerie->detalhe();

      $exigeInep = $serieDet['exigir_inep'] == "t";

      if (!$alunoInep && $exigeInep) {
        $this->mensagem = 'N&atilde;o foi poss&iacute;vel realizar matr&iacute;cula, necess&aacute;rio inserir o INEP no cadastro do aluno.';
        return FALSE;
      }

      $obj_reserva_vaga = new clsPmieducarReservaVaga();
      $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
        $this->ref_cod_serie, NULL, NULL,$this->ref_cod_aluno, NULL, NULL,
        NULL, NULL, 1);

      // Verifica se existe reserva de vaga para o aluno
      if (is_array($lst_reserva_vaga)) {
        $det_reserva_vaga           = array_shift($lst_reserva_vaga);
        $this->ref_cod_reserva_vaga = $det_reserva_vaga['cod_reserva_vaga'];

        $obj_reserva_vaga = new clsPmieducarReservaVaga($this->ref_cod_reserva_vaga,
          NULL, NULL, $this->pessoa_logada, NULL, NULL, NULL, NULL, 0);

        $editou = $obj_reserva_vaga->edita();
        if (! $editou) {
          $this->mensagem = 'Edição não realizada.<br />';
          return FALSE;
        }
      }

      $vagas_restantes = 1;

      if (! $this->ref_cod_reserva_vaga) {
        $obj_turmas = new clsPmieducarTurma();
        $lst_turmas = $obj_turmas->lista(NULL, NULL, NULL, $this->ref_cod_serie,
          $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, TRUE);

        if (is_array($lst_turmas)) {
          $total_vagas = 0;
          foreach ($lst_turmas as $turmas) {
            $total_vagas += $turmas['max_aluno'];
          }
        }
        else {
          $this->mensagem = 'A s&eacute;rie selecionada n&atilde;o possui turmas cadastradas.<br />';
          return FALSE;
        }

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(NULL, NULL, $this->ref_cod_escola,
          $this->ref_cod_serie, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, 1,
          $this->ano, $this->ref_cod_curso, $this->ref_cod_instituicao, 1);

        if (is_array($lst_matricula)) {
          $matriculados = count($lst_matricula);
        }

        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
          $this->ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1,
          $this->ref_cod_instituicao, $this->ref_cod_curso);

        if (is_array($lst_reserva_vaga)) {
          $reservados = count($lst_reserva_vaga);
        }

        $vagas_restantes = $total_vagas - ($matriculados + $reservados);
      }
      if ($vagas_restantes <= 0) {
        echo sprintf('
          <script>
            var msg = \'\';
            msg += \'Excedido o n\u00famero de total de vagas para Matr\u00cdcula!\\n\';
            msg += \'N\u00famero total de matriculados: %d\\n\';
            msg += \'N\u00famero total de vagas reservadas: %d\\n\';
            msg += \'N\u00famero total de vagas: %d\\n\';
            msg += \'Deseja mesmo assim realizar a Matr\u00cdcula?\';
            if (! confirm(msg)) {
              window.location = \'educar_aluno_det.php?cod_aluno=%d\';
            }
          </script>',
          $matriculados, $reservados, $total_vagas, $this->ref_cod_aluno
        );
      }

      $objInstituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
      $detInstituicao = $objInstituicao->detalhe();
      $controlaEspacoUtilizacaoAluno = $detInstituicao["controlar_espaco_utilizacao_aluno"];

      //se o parametro de controle de utilização de espaço estiver setado como verdadeiro
      if($controlaEspacoUtilizacaoAluno){
          $objTurma = new clsPmieducarTurma($this->ref_cod_turma);
          $maximoAlunosSala = $objTurma->maximoAlunosSala();
          $excedeuLimiteMatriculas = (($matriculados + $reservados) >= $maximoAlunosSala);

          if($excedeuLimiteMatriculas){
             echo sprintf('
              <script>
                var msg = \'\';
                msg += \'A sala n\u00e3o comporta mais alunos!\\n\';
                msg += \'N\u00famero total de matriculados: %d\\n\';
                msg += \'N\u00famero total de vagas reservadas: %d\\n\';
                msg += \'N\u00famero total de vagas: %d\\n\';
                msg += \'M\u00e1ximo de alunos que a sala comporta: %d\\n\';
                msg += \'N\u00e3o ser\u00e1 poss\u00edvel efetuar a matr\u00edcula do aluno.\';
                alert(msg);
                window.location = \'educar_aluno_det.php?cod_aluno=%d\';
              </script>',
              $matriculados, $reservados, $total_vagas, $maximoAlunosSala, $this->ref_cod_aluno
            );
            return false;
          }
      }

      $obj_matricula_aluno = new clsPmieducarMatricula();
      $lst_matricula_aluno = $obj_matricula_aluno->lista(NULL, NULL, NULL, NULL,
        NULL, NULL, $this->ref_cod_aluno);

      if ($this->is_padrao == 1) {
        $this->semestre =  NULL;
      }

      if (! $this->removerFlagUltimaMatricula($this->ref_cod_aluno)) {
        return false;
      }

      $db->Consulta("SELECT *
                      FROM pmieducar.matricula m
                      WHERE m.ano = {$this->ano}
                      AND m.aprovado = 3
                      AND m.ativo = 1
                      AND m.ref_cod_aluno = {$this->ref_cod_aluno}
                      AND m.ref_ref_cod_serie = {$this->ref_cod_serie}
                      AND m.ref_ref_cod_escola = {$this->ref_cod_escola}
                      AND dependencia ");

      $db->ProximoRegistro();
      $m = $db->Tupla();
      if (is_array($m) && count($m) && $dependencia) {
        $this->mensagem .= "Esse aluno j&aacute; tem uma matr&iacute;cula de depend&ecirc;ncia nesta escola e s&eacute;rie.";
        return false;
      }

            @session_start();
      $reloadReserva = $_SESSION['reload_reserva_vaga'];
      @session_write_close();

      $obj_CandidatoReservaVaga = new clsPmieducarCandidatoReservaVaga();
      $lst_CandidatoReservaVaga = $obj_CandidatoReservaVaga->lista($this->ano,
                                                                    NULL,
                                                                    NULL,
                                                                    NULL,
                                                                    $this->ref_cod_serie,
                                                                    NULL,
                                                                    NULL,
                                                                    $this->ref_cod_aluno,
                                                                    TRUE);
      $count = count($lst_CandidatoReservaVaga);
      $countEscolasDiferentes = 0;
      $countEscolasIguais = 0;

      if (is_array($lst_CandidatoReservaVaga)){
        for ($i = 0; $i < $count; $i++){
          if($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] != $this->ref_cod_escola){
            $countEscolasDiferentes = $countEscolasDiferentes + 1;
          }elseif ($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] == $this->ref_cod_escola) {
            $countEscolasIguais = $countEscolasIguais + 1;
          }
        }

        if(($countEscolasDiferentes > 0) && (!$reloadReserva)){
          echo "<script type=\"text/javascript\">
                  var msg = '".Portabilis_String_Utils::toLatin1('O aluno possui uma reserva de vaga em outra escola, deseja matricula-lo assim mesmo?')."';
                  if (!confirm(msg)) {
                    window.location = 'educar_aluno_det.php?cod_aluno=".$this->ref_cod_aluno."';
                  } else {
                    parent.document.getElementById('formcadastro').submit();
                  }
                </script>";
            $reloadReserva = 1;
            @session_start();
            $_SESSION['reload_reserva_vaga'] = $reloadReserva;
            @session_write_close();
          return TRUE;

        }else if(($countEscolasDiferentes > 0) && ($reloadReserva == 1)){
          $updateCandidatoReservaVaga = $obj_CandidatoReservaVaga->atualizaDesistente($this->ano,
                                                                                      $this->ref_cod_serie,
                                                                                      $this->ref_cod_aluno,
                                                                                      $this->ref_cod_escola);

        }
      }

      $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);
      $obj = new clsPmieducarMatricula(NULL, $this->ref_cod_reserva_vaga,
        $this->ref_cod_escola, $this->ref_cod_serie, NULL,
        $this->pessoa_logada, $this->ref_cod_aluno, 3, NULL, NULL, 1, $this->ano,
        1, NULL, NULL, NULL, NULL, $this->ref_cod_curso,
        NULL, $this->semestre,$this->data_matricula);

      $obj->dependencia = $dependencia;

      $cadastrou = $obj->cadastra();

      $this->cod_matricula = $cadastrou;

      if ($cadastrou) {

        if ($countEscolasIguais > 0){
          $obj_crv = new clsPmieducarCandidatoReservaVaga($this->ref_cod_candidato_reserva_vaga);
          $obj_crv->vinculaMatricula($this->ref_cod_escola, $this->cod_matricula, $this->ref_cod_aluno);
        } else if ($this->ref_cod_candidato_fila_unica) {
            $obj_cfu = new clsPmieducarCandidatoFilaUnica($this->ref_cod_candidato_fila_unica);
            $obj_cfu->vinculaMatricula($this->cod_matricula);
        }

        $this->enturmacaoMatricula($this->cod_matricula, $this->ref_cod_turma);

        $this->verificaSolicitacaoTransferencia();

        #TODO set in $_SESSION['flash'] 'Aluno matriculado com sucesso'
        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        header('Location: educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
        #die();
        #return true;
      }

      $this->mensagem = 'Cadastro n&atilde;o realizado.<br />';
      return FALSE;
    }
    else {
      $this->mensagem = Portabilis_String_Utils::toLatin1('O ano (letivo) selecionado não está em andamento na escola selecionada.<br />');
      return FALSE;
    }
  }

  function permiteDependenciaAnoConcluinte() {

    $instituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
    $instituicao = $instituicao->detalhe();
    $serie = new clsPmieducarSerie($this->ref_cod_serie);
    $serie = $serie->detalhe();

    $reprovaDependenciaAnoConcluinte = $instituicao['reprova_dependencia_ano_concluinte'];
    $anoConcluinte = $serie['concluinte'] == 2;

    return !(dbBool($reprovaDependenciaAnoConcluinte) && $anoConcluinte);
  }

  function verificaQtdeDependenciasPermitida() {
    $matriculasDependencia =
      Portabilis_Utils_Database::fetchPreparedQuery("SELECT *
                                                       FROM pmieducar.matricula
                                                      WHERE matricula.ano = {$this->ano}
                                                        AND matricula.ref_cod_aluno = {$this->ref_cod_aluno}
                                                        AND matricula.dependencia = TRUE
                                                        AND matricula.aprovado = 3
                                                        AND matricula.ativo = 1");

    $matriculasDependencia = count($matriculasDependencia);

    $db = new clsBanco();
    $matriculasDependenciaPermitida =
      $db->CampoUnico("SELECT regra_avaliacao.qtd_matriculas_dependencia
                         FROM pmieducar.serie
                        INNER JOIN modules.regra_avaliacao ON (regra_avaliacao.id = serie.regra_avaliacao_id)
                        WHERE serie.cod_serie = {$this->ref_cod_serie}");

    if ($matriculasDependencia >= $matriculasDependenciaPermitida) {
      $this->mensagem = Portabilis_String_Utils::toLatin1("A regra desta série limita a quantidade de matrículas de dependência para {$matriculasDependenciaPermitida}.");
      return false;
    }

    return true;
  }

  function verificaAlunoFalecido() {

    $aluno = new clsPmieducarAluno($this->ref_cod_aluno);
    $aluno = $aluno->detalhe();

    $pessoa = new clsPessoaFisica($aluno["ref_idpes"]);
    $pessoa = $pessoa->detalhe();

    $falecido = dbBool($pessoa['falecido']);

    return $falecido;
  }

  function verificaSolicitacaoTransferencia() {
    $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
    $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, $this->ref_cod_aluno, FALSE);

    if (!is_array($lst_transferencia)) return;

    foreach ($lst_transferencia as $transferencia) {
      $obj_matricula = new clsPmieducarMatricula($transferencia['ref_cod_matricula_saida']);
      $det_matricula = $obj_matricula->detalhe();

      // Caso a solicitação em aberto seja para a mesma série selecionada
      if ($det_matricula['ref_ref_cod_serie'] == $this->ref_cod_serie &&
          $det_matricula['ano'] == $this->ano) {

        $cod_transferencia = $transferencia['cod_transferencia_solicitacao'];
        $cod_matricula_transferencia = $det_matricula['cod_matricula'];

        $this->copiaNotasFaltas($cod_matricula_transferencia, $this->cod_matricula);
        $this->atendeSolicitacaoTransferencia($cod_transferencia, $this->cod_matricula);
        break;
      }
    }
  }

  function bloqueiaMatriculaSerieNaoSeguinte() {
    $instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $instituicao = $instituicao->detalhe();

    $bloqueia = dbBool($instituicao['bloqueia_matricula_serie_nao_seguinte']);

    return $bloqueia;
  }

  function permiteMatriculaSerieDestino() {
    $objMatricula      = new clsPmieducarMatricula;
    $objSequenciaSerie = new clsPmieducarSequenciaSerie;

    $dadosUltimaMatricula    = $objMatricula->getDadosUltimaMatricula($this->ref_cod_aluno);
    $situacaoUltimaMatricula = $dadosUltimaMatricula[0]['aprovado'];
    $serieUltimaMatricula    = $dadosUltimaMatricula[0]['ref_ref_cod_serie'];

    $aprovado = array(1, 12, 13);
    $reprovado = array(2, 14);

    if (!$dadosUltimaMatricula) {
      return true;
    }

    if (in_array($situacaoUltimaMatricula, $aprovado)){
      $serieNovaMatricula = $objSequenciaSerie->lista($serieUltimaMatricula);
      $serieNovaMatricula = $serieNovaMatricula[0]['ref_serie_destino'];
    }else if (in_array($situacaoUltimaMatricula, $reprovado))
      $serieNovaMatricula = $serieUltimaMatricula;

    if ($this->ref_cod_serie == $serieNovaMatricula){
      return true;
    }

    return false;
  }

  function copiaNotasFaltas($matriculaAntiga, $matriculaNova) {
    $db = new clsBanco();
    $db->Consulta("SELECT modules.copia_notas_transf({$matriculaAntiga},{$matriculaNova});");
  }

  function atendeSolicitacaoTransferencia($codTranferencia, $codMatriculaEntrada) {
    $obj_transferencia = new clsPmieducarTransferenciaSolicitacao($codTranferencia, NULL, $this->pessoa_logada, NULL, $codMatriculaEntrada, NULL, NULL, NULL, NULL, 0);
    $obj_transferencia->edita();
  }

  function desativaEnturmacoesMatricula($matriculaId) {
    $result = true;

    $enturmacoes = new clsPmieducarMatriculaTurma();
    $enturmacoes = $enturmacoes->lista($matriculaId, NULL, NULL, NULL, NULL,
                                       NULL, NULL, NULL, 1);

    if ($enturmacoes) {
      foreach ($enturmacoes as $enturmacao) {
        $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                     $enturmacao['ref_cod_turma'],
                                                     $this->pessoa_logada, null,
                                                     null, null, 0, null,
                                                     $enturmacao['sequencial']);
        $enturmacao->removerSequencial = TRUE;
        $detEnturmacao = $enturmacao->detalhe();
        $detEnturmacao = $detEnturmacao['data_enturmacao'];
        $enturmacao->data_enturmacao = $detEnturmacao;

        if ($result && ! $enturmacao->edita()){
          $result = false;
        }

      }
    }

    if(! $result) {
      $this->mensagem = "N&atilde;o foi poss&iacute;vel desativar as " .
                        "enturma&ccedil;&otilde;es da matr&iacute;cula.";
    }

    return $result;
  }


  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(627, $this->pessoa_logada, 7,
      'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);

    if (! $this->desativaEnturmacoesMatricula($this->cod_matricula))
      return false;

    $obj_matricula = new clsPmieducarMatricula( $this->cod_matricula );
    $det_matricula = $obj_matricula->detalhe();
    $ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

    $obj_sequencia = new clsPmieducarSequenciaSerie();
    $lst_sequencia = $obj_sequencia->lista(
      NULL, $ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, 1
    );

    // Verifica se a série da matrícula cancelada é sequência de alguma outra série
    if (is_array($lst_sequencia)) {
      $det_sequencia    = array_shift($lst_sequencia);
      $ref_serie_origem = $det_sequencia['ref_serie_origem'];

      $obj_matricula = new clsPmieducarMatricula();
      $lst_matricula = $obj_matricula->lista(
        NULL, NULL, NULL, $ref_serie_origem, NULL, NULL,$this->ref_cod_aluno,
        NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0
      );

      // Verifica se o aluno tem matrícula na série encontrada
      if (is_array($lst_matricula)) {
        $det_matricula     = array_shift($lst_matricula);
        $ref_cod_matricula = $det_matricula['cod_matricula'];

        $obj = new clsPmieducarMatricula(
          $ref_cod_matricula, NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL,
          NULL, NULL, NULL, 1, NULL, 1
        );

        $editou1 = $obj->edita();
        if (! $editou1) {
          $this->mensagem = 'N&atilde;o foi poss&iacute;vel editar a "&Uacute;ltima Matr&iacute;cula da Sequ&ecirc;ncia".<br />';
          return FALSE;
        }
      }
    }

    $obj = new clsPmieducarMatricula(
      $this->cod_matricula, NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL,
      NULL, NULL, NULL, 0
    );

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br />';
      header('Location: educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
      die();
    }

    $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br />';
    return FALSE;
  }

  protected function removerFlagUltimaMatricula($alunoId) {
    $matriculas = new clsPmieducarMatricula();
    $matriculas = $matriculas->lista(NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_cod_aluno,
                                     NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1);


    foreach ($matriculas as $matricula) {
      if (!$matricula['aprovado']==3){
        $matricula = new clsPmieducarMatricula($matricula['cod_matricula'], NULL, NULL, NULL,
                                               $this->pessoa_logada, NULL, $alunoId, NULL, NULL,
                                               NULL, 1, NULL, 0);
        if (! $matricula->edita()) {
          $this->mensagem = 'Erro ao remover flag ultima matricula das matriculas anteriores.';
          return false;
        }
      }
    }

    return true;
  }

function enturmacaoMatricula($matriculaId, $turmaDestinoId) {

    $enturmacaoExists = new clsPmieducarMatriculaTurma();
    $enturmacaoExists = $enturmacaoExists->lista($matriculaId,
                                                 $turmaDestinoId,
                                                NULL,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                NULL,
                                                 1);

    $enturmacaoExists = is_array($enturmacaoExists) && count($enturmacaoExists) > 0;
    if (! $enturmacaoExists) {
      $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                   $turmaDestinoId,
                                                  $this->pessoa_logada,
                                                   $this->pessoa_logada,
                                                   NULL,
                                                   NULL,
                                                   1);
      $enturmacao->data_enturmacao = $this->data_matricula;
      return $enturmacao->cadastra();
    }
    return false;
  }

  function existeVagasDisponíveis(){
    $dependencia = $this->dependencia == 'on';
    if(!$dependencia){
      // Caso quantidade de matrículas naquela turma seja maior ou igual que a capacidade da turma deve bloquear
      if($this->_getQtdMatriculaTurma() >= $this->_getMaxAlunoTurma()){
        $this->mensagem .= Portabilis_String_Utils::toLatin1("Não existem vagas disponíveis para essa turma!") . '<br/>';
        return false;
      }

      // Caso a capacidade de alunos naquele turno seja menor ou igual ao ao número de alunos matrículados + alunos na reserva de vaga externa deve bloquear
      if ($this->_getMaxAlunoTurno() <= ($this->_getQtdAlunosFila() + $this->_getQtdMatriculaTurno() )){
        $this->mensagem .= Portabilis_String_Utils::toLatin1("Não existem vagas disponíveis para essa série/turno!") . '<br/>';
        return false;
      }
    }

    return true;
  }

  function _getQtdMatriculaTurma(){
    $obj_mt = new clsPmieducarMatriculaTurma();
    $lst_mt = $obj_mt->enturmacoesSemDependencia($this->ref_cod_turma);
    return $lst_mt[0];
  }

  function _getMaxAlunoTurma(){
    $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
    $det_t = $obj_t->detalhe();
    return $det_t['max_aluno'];
  }

  function _getMaxAlunoTurno(){
    $obj_t = new clsPmieducarTurma();
    $det_t = $obj_t->detalhe();

    $lista_t = $obj_t->lista($int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null,
    $int_ref_ref_cod_serie = $this->ref_cod_serie, $int_ref_ref_cod_escola = $this->ref_cod_escola, $int_ref_cod_infra_predio_comodo = null,
    $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null,
    $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null,
    $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null,
    $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null,
    $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null,
    $bool_verifica_serie_multiseriada = false, $bool_tem_alunos_aguardando_nota = null, $visivel = null, $turma_turno_id = $det_t['turma_turno_id'], $tipo_boletim = null, $ano = $this->ano, $somenteAnoLetivoEmAndamento = FALSE);

    $max_aluno_turmas = 0;

    foreach ($lista_t as $reg) {
      $max_aluno_turmas += $reg['max_aluno'];
    }

    return $max_aluno_turmas;
  }

  function _getQtdAlunosFila(){
    $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
    $det_t = $obj_t->detalhe();

    $sql = 'SELECT count(1) as qtd
              FROM pmieducar.matricula
              WHERE ano = $1
              AND ref_ref_cod_escola = $2
              AND ref_cod_curso = $3
              AND ref_ref_cod_serie = $4
              AND turno_pre_matricula = $5
              AND aprovado = 11 ';

    return (int) Portabilis_Utils_Database::selectField($sql, array($this->ano, $this->ref_cod_escola, $this->ref_cod_curso, $this->ref_cod_serie, $det_t['turma_turno_id']));
  }

  function _getQtdMatriculaTurno(){
    $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
    $det_t = $obj_t->detalhe();

    $obj_mt = new clsPmieducarMatriculaTurma();
    $lst_mt = $obj_mt->lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
              $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
              $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
              $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = 1,
              $int_ref_cod_serie = $this->ref_cod_serie, $int_ref_cod_curso = $this->ref_cod_curso, $int_ref_cod_escola = $this->ref_cod_escola,
              $int_ref_cod_instituicao = NULL, $int_ref_cod_aluno = NULL, $mes = NULL,
              $aprovado = NULL, $mes_menor_que = NULL, $int_sequencial = NULL,
              $int_ano_matricula = NULL, $tem_avaliacao = NULL, $bool_get_nome_aluno = FALSE,
              $bool_aprovados_reprovados = NULL, $int_ultima_matricula = NULL,
              $bool_matricula_ativo = NULL, $bool_escola_andamento = FALSE,
              $mes_matricula_inicial = FALSE, $get_serie_mult = FALSE,
              $int_ref_cod_serie_mult = NULL, $int_semestre = NULL,
              $pegar_ano_em_andamento = FALSE, $parar=NULL, $diario = FALSE,
              $int_turma_turno_id = $det_t['turma_turno_id'], $int_ano_turma = $det_t['ano'], $dependencia = 'f');
    return count($lst_mt);
  }


}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
