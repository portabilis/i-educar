<?php

/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponÃ­vel desde a versÃ£o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'App/Model/MatriculaSituacao.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $ref_cod_matricula;

  function Gerar()
  {
    $this->titulo = 'Lista de enturmações da matrí­cula';

    $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

    if (!$this->ref_cod_matricula) {
        $this->simpleRedirect('educar_matricula_historico_lst.php');
    }

    $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
    $det_matricula = $obj_matricula->detalhe();

    $situacao = App_Model_MatriculaSituacao::getSituacao($det_matricula['aprovado']);

    $this->ref_cod_curso = $det_matricula['ref_cod_curso'];

    $this->ref_cod_serie  = $det_matricula['ref_ref_cod_serie'];
    $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
    $this->ref_cod_turma  = $_GET['ref_cod_turma'];
    $this->ano_letivo     = $_GET['ano_letivo'];

    $this->addCabecalhos(array(
      'Sequencial',
      'Turma',
      'Turno do aluno',
      'Ativo',
      'Data de enturmação',
      'Data de saída',
      'Transferido',
      'Remanejado',
      'Reclassificado',
      'Abandono',
      'Falecido',
      'Usuário criou',
      'Usuário editou'
    ));

    // Busca dados da matricula
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_matricula = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($obj_aluno->lista($detalhe_matricula['ref_cod_aluno'],
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
      $det_escola = $obj_escola->detalhe();

    if ($det_escola['nome']) {
      $this->campoRotulo('nm_escola', 'Escola', $det_escola['nome']);
    }

    $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $det_aluno['nome_aluno']);
    $this->campoRotulo('matricula', 'Matrícula', $this->ref_cod_matricula);
    $this->campoRotulo('situacao', 'Situação', $situacao);
    $this->campoRotulo('data_saida', 'Data saída', dataToBrasil($detalhe_matricula['data_cancel']));

    //Paginador
    $this->limite = 20;
    $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

    $obj = new clsPmieducarMatriculaTurma();
    $obj->setOrderby( "sequencial ASC" );
    $obj->setLimite( $this->limite, $this->offset );

    $lista = $obj->lista($this->ref_cod_matricula);

    $total = $obj->_total;

    // monta a lista
    if( is_array( $lista ) && count( $lista ) )
    {
      foreach ( $lista AS $registro )
      {
        $ativo = $registro["ativo"] ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');
        $dataEnturmacao = dataToBrasil($registro["data_enturmacao"]);
        $dataSaida = dataToBrasil($registro["data_exclusao"]);
        $dataSaidaMatricula = dataToBrasil($detalhe_matricula["data_cancel"]);
        $transferido = $registro["transferido"] == 't' ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');
        $remanejado = $registro["remanejado"] == 't' ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');
        $abandono = $registro["abandono"] == 't' ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');
        $reclassificado = $registro["reclassificado"] == 't' ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');
        $falecido = $registro["falecido"] == 't' ? 'Sim' : Portabilis_String_Utils::toLatin1('Não');

        $usuarioCriou = new clsPessoa_($registro['ref_usuario_cad']);
        $usuarioCriou = $usuarioCriou->detalhe();

        $usuarioEditou = new clsPessoa_($registro['ref_usuario_exc']);
        $usuarioEditou = $usuarioEditou->detalhe();

        $turno = '';
        if ($registro['turno_id']) {
            $turno = Portabilis_Utils_Database::selectField('SELECT nome FROM pmieducar.turma_turno WHERE id = $1', [$registro['turno_id']]);
        }

        $this->addLinhas(
          array(
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$registro["sequencial"]}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$registro["nm_turma"]}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$turno}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$ativo}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$dataEnturmacao}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$dataSaida}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$transferido}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$remanejado}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$reclassificado}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$abandono}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$falecido}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$usuarioCriou['nome']}</a>",
          "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}  \">{$usuarioEditou['nome']}</a>",
          ));
      }
    }

    $this->addLinhas('<small>A coluna "Turno do aluno" permanecerá em branco quando o turno do aluno for o mesmo da turma.</small>');

    $this->addPaginador2( "educar_matricula_historico_lst.php", $total, $_GET, $this->nome, $this->limite );

    $this->acao = "go(\"educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}\")";
    $this->nome_acao = "Voltar";

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "Início",
         "educar_index.php"                  => "Escola",
         ""                                  => "Histórico de enturmações da matrí­cula"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}

// Instancia objeto de pÃ¡gina
$pagina = new clsIndexBase();

// Instancia objeto de conteÃºdo
$miolo = new indice();

// Atribui o conteÃºdo Ã   pÃ¡gina
$pagina->addForm($miolo);

// Gera o cÃ³digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('botao_busca').style.visibility = 'hidden';
</script>
