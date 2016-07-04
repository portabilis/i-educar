<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'Portabilis/Business/Professor.php';
require_once 'App/Model/IedFinder.php';
require_once 'ComponenteCurricular/Model/CodigoEducacenso.php';

class EducacensoExportController extends ApiCoreController
{

  var $pessoa_logada;

  var $ref_cod_escola;
  var $ref_cod_escola_;
  var $ref_cod_serie;
  var $ref_cod_serie_;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;
  var $hora_fim_intervalo_;

  var $ano;
  var $ref_cod_instituicao;
  var $msg = "";
  var $error = false;

  var $turma_presencial_ou_semi;

  const TECNOLOGO = 1;
  const LICENCIATURA = 2;
  const BACHARELADO = 3;


  protected function educacensoExport() {

    $escola   = $this->getRequest()->escola;
    $ano      = $this->getRequest()->ano;
    $data_ini = $this->getRequest()->data_ini;
    $data_fim = $this->getRequest()->data_fim;

    $conteudo = $this->exportaDadosCensoPorEscola($escola,
                  $ano,
                  Portabilis_Date_Utils::brToPgSQL($data_ini),
                  Portabilis_Date_Utils::brToPgSQL($data_fim));

    if($this->error){
      return array("error" => true,
                   "mensagem" => $this->msg);
    }

    return array('conteudo' => $conteudo);
  }

  protected function exportaDadosCensoPorEscola($escolaId, $ano, $data_ini, $data_fim){

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(846, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $export = $this->exportaDadosRegistro00($escolaId, $ano);
    $export .= $this->exportaDadosRegistro10($escolaId);
    foreach ($this->getTurmas($escolaId, $ano) as $turmaId => $turmaNome) {
      $export .= $this->exportaDadosRegistro20($escolaId, $turmaId, $data_ini, $data_fim);
    }
    foreach ($this->getServidores($escolaId, $ano, $data_ini, $data_fim) as $servidor) {

      $registro30 = $this->exportaDadosRegistro30($servidor['id'], $escolaId);
      $registro40 = $this->exportaDadosRegistro40($servidor['id'], $escolaId);
      $registro50 = $this->exportaDadosRegistro50($servidor['id'], $escolaId);
      $registro51 = $this->exportaDadosRegistro51($servidor['id'], $escolaId, $data_ini, $data_fim);
      if(!empty($registro30) && !empty($registro40) && !empty($registro50))
        $export .= $registro30 . $registro40 . $registro50 . $registro51;
    }

    foreach ($this->getAlunos($escolaId, $ano, $data_ini, $data_fim) as $alunoId) {
      $registro60 = $this->exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
      $registro70 = $this->exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
      $registro80 = $this->exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
      if(!empty($registro60) && !empty($registro70) && !empty($registro80))
        $export .= $registro60 . $registro70 . $registro80;
    }
    $export .= $this->exportaDadosRegistro99();
    return $export;
  }

  protected function getTurmas($escolaId, $ano){
    return App_Model_IedFinder::getTurmas($escolaId, NULL, $ano);
  }

  protected function getServidores($escolaId, $ano, $data_ini, $data_fim){
    $sql = 'SELECT distinct cod_servidor as id
              from pmieducar.servidor
             inner join modules.professor_turma on(servidor.cod_servidor = professor_turma.servidor_id)
             inner join pmieducar.turma on(professor_turma.turma_id = turma.cod_turma)
             where turma.ref_ref_cod_escola = $1
               and servidor.ativo = 1
               and professor_turma.ano = $2
               and turma.ativo = 1
               and (SELECT 1
                      FROM pmieducar.matricula_turma mt
                     INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
                      WHERE mt.ref_cod_turma = turma.cod_turma
                      AND mt.ativo = 1
                      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
                      LIMIT 1) IS NOT NULL';

    return Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim)));
  }

  protected function getAlunos($escolaId, $ano, $data_ini, $data_fim){
    $sql =
     'SELECT
      distinct(a.cod_aluno) as id

      FROM  pmieducar.aluno a
      INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
      INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
      INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
      INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)

      WHERE e.cod_escola = $1
      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
      AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
      AND m.ano = $2
    ';
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim)));
  }

  protected function exportaDadosRegistro00($escolaId, $ano){
    $sql =
    	' SELECT
        \'00\' as r00s1,
        ece.cod_escola_inep as r00s2,

      gestor_f.cpf as r00s3,
      gestor_p.nome as r00s4,
      e.cargo_gestor as r00s5,
      gestor_p.email as r00s6,

      e.situacao_funcionamento as r00s7,

        (SELECT min(ano_letivo_modulo.data_inicio)
          FROM pmieducar.ano_letivo_modulo
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) as r00s8,

        (SELECT max(ano_letivo_modulo.data_fim)
          FROM pmieducar.ano_letivo_modulo
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) as r00s9,

        p.nome as r00s10,
        e.latitude as r00s11,
        e.longitude as r00s12,
        ep.cep as r00s13,
        l.idtlog || l.nome as r00s14,
        ep.numero as r00s15,
        ep.complemento as r00s16,
        b.nome as r00s17,
        uf.cod_ibge as r00s18,
        m.cod_ibge as r00s19,
        d.cod_ibge as r00s20,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.ddd)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(ddd_telefone)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s21,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(telefone)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s22,


        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3),
          (SELECT min(fax)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s24,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 4),
          (SELECT min(fax)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s25,

        (SELECT COALESCE(p.email,(SELECT email FROM pmieducar.escola_complemento where ref_cod_escola = e.cod_escola))) as r00s26,

        e.orgao_regional as r00s27,
        e.dependencia_administrativa as r00s28,
        b.zona_localizacao as r00s29,
        0 as r00s32,
        0 as r00s33,
        0 as r00s34,
        0 as r00s35,
        0 as r00s36,
        e.regulamentacao as r00s39,
        0 as r00s40


        FROM pmieducar.escola e
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        INNER JOIN cadastro.pessoa p ON (e.ref_idpes = p.idpes)
        INNER JOIN cadastro.juridica j ON (j.idpes = p.idpes)
        INNER JOIN cadastro.pessoa gestor_p ON (gestor_p.idpes = e.ref_idpes_gestor)
        INNER JOIN cadastro.fisica gestor_f ON (gestor_f.idpes = gestor_p.idpes)
        INNER JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
        INNER JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
        INNER JOIN public.bairro b ON (clb.idbai = b.idbai)
        INNER JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
        INNER JOIN public.distrito d ON (d.iddis = b.iddis)
        INNER JOIN public.municipio m ON (d.idmun = m.idmun)
        INNER JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
        INNER JOIN public.pais ON (pais.idpais = uf.idpais)
        INNER JOIN public.logradouro l ON (l.idlog = cl.idlog)
        WHERE e.cod_escola = $1
    ';
    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($escolaId, $ano))));
    if ($r00s1){
      $d = '|';
      $return = '';

      $r00s2 = substr($r00s2, 0, 8);
      $r00s3 = $this->cpfToCenso($r00s3);
      $r00s4 = $this->upperAndUnaccent($r00s4);
      $r00s6 = strtoupper($r00s6);

      $r00s8 = Portabilis_Date_Utils::pgSQLToBr($r00s8);
      $r00s9 = Portabilis_Date_Utils::pgSQLToBr($r00s9);

      $r00s10 = $this->upperAndUnaccent($r00s10);
      $r00s14 = $this->upperAndUnaccent($r00s14);
      $r00s15 = $this->upperAndUnaccent($r00s15);
      $r00s16 = $this->upperAndUnaccent($r00s16);
      $r00s17 = $this->upperAndUnaccent($r00s17);
      $r00s26 = $this->upperAndUnaccent($r00s26);
      $r00s27 = str_pad($r00s27, 5, "0", STR_PAD_LEFT);

      if($r00s28 <> 4)
        $r00s32 = $r00s33 = $r00s34 = $r00s35 = $r00s36 = '';

      for ($i=1; $i <= 42 ; $i++)
        $return .= ${'r00s'.$i}.$d;

      $return = substr_replace($return, "", -1);

      return $return."\n";
    }else{
      $this->msg .= "Dados para formular o registro 00 da escola {$escolaId} não encontrados. Verifique se a escola possuí endereço normalizado, código do INEP e dados do gestor cadastrados.<br/>";
      $this->error = true;
    }
  }

  protected function exportaDadosRegistro10($escolaId){
    $sql =
    	'SELECT
      \'10\' as r10s1,
      ece.cod_escola_inep as r10s2,

      e.local_funcionamento,
      e.condicao as r10s12,
      e.codigo_inep_escola_compartilhada,
      e.agua_consumida as r10s20,
      e.agua_rede_publica as r10s21,
      e.agua_poco_artesiano as r10s22,
      e.agua_cacimba_cisterna_poco as r10s23,
      e.agua_fonte_rio as r10s24,
      e.agua_inexistente as r10s25,
      e.energia_rede_publica as r10s26,
      e.energia_gerador as r10s27,
      e.energia_outros as r10s28,
      e.energia_inexistente as r10s29,
      e.esgoto_rede_publica as r10s30,
      e.esgoto_fossa as r10s31,
      e.esgoto_inexistente as r10s32,
      e.lixo_coleta_periodica as r10s33,
      e.lixo_queima as r10s34,
      e.lixo_joga_outra_area as r10s35,
      e.lixo_recicla as r10s36,
      e.lixo_enterra as r10s37,
      e.lixo_outros as r10s38,
      e.dependencia_sala_diretoria as r10s39,
      e.dependencia_sala_professores as r10s40,
      e.dependencia_sala_secretaria as r10s41,
      e.dependencia_laboratorio_informatica as r10s42,
      e.dependencia_laboratorio_ciencias as r10s43,
      e.dependencia_sala_aee as r10s44,
      e.dependencia_quadra_coberta as r10s45,
      e.dependencia_quadra_descoberta as r10s46,
      e.dependencia_cozinha as r10s47,
      e.dependencia_biblioteca as r10s48,
      e.dependencia_sala_leitura as r10s49,
      e.dependencia_parque_infantil as r10s50,
      e.dependencia_bercario as r10s51,
      e.dependencia_banheiro_fora as r10s52,
      e.dependencia_banheiro_dentro as r10s53,
      e.dependencia_banheiro_infantil as r10s54,
      e.dependencia_banheiro_deficiente as r10s55,
      e.dependencia_vias_deficiente as r10s56,
      e.dependencia_banheiro_chuveiro as r10s57,
      e.dependencia_refeitorio as r10s58,
      e.dependencia_dispensa as r10s59,
      e.dependencia_aumoxarifado as r10s60,
      e.dependencia_auditorio as r10s61,
      e.dependencia_patio_coberto as r10s62,
      e.dependencia_patio_descoberto as r10s63,
      e.dependencia_alojamento_aluno as r10s64,
      e.dependencia_alojamento_professor as r10s65,
      e.dependencia_area_verde as r10s66,
      e.dependencia_lavanderia as r10s67,
      e.dependencia_nenhuma_relacionada as r10s68,
      e.dependencia_numero_salas_existente as r10s69,
      e.dependencia_numero_salas_utilizadas as r10s70,

      e.televisoes as r10s71,
      e.videocassetes as r10s72,
      e.dvds as r10s73,
      e.antenas_parabolicas as r10s74,
      e.copiadoras as r10s75,
      e.retroprojetores as r10s76,
      e.impressoras as r10s77,
      e.aparelhos_de_som as r10s78,
      e.projetores_digitais  as r10s79,
      e.faxs as r10s80,
      e.maquinas_fotograficas as r10s81,
      e.computadores as r10s82,
      e.impressoras_multifuncionais as r10s83,
      e.computadores_administrativo as r10s84,
      e.computadores_alunos as r10s85,
      e.acesso_internet as r10s86,
      e.banda_larga as r10s87,

      total_funcionario as r10s88,
      1 as r10s89,
      atendimento_aee as r10s90,
      atividade_complementar as r10s91,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 1
        LIMIT 1
      ) as r10s92,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 2
        LIMIT 1
      ) as r10s93,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 3
        LIMIT 1
      ) as r10s94,

      fundamental_ciclo as r10s96,
      localizacao_diferenciada as r10s97,
      didatico_nao_utiliza as r10s98,
      didatico_quilombola as r10s99,
      didatico_indigena as r10s100,
      educacao_indigena as r10s101,
      lingua_ministrada,
      codigo_lingua_indigena as r10s104,
      espaco_brasil_aprendizado as r10s105,
      abre_final_semana as r10s106,
      proposta_pedagogica as r10s107

      FROM pmieducar.escola e
      INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
      INNER JOIN cadastro.pessoa p ON (p.idpes = e.ref_idpes_gestor)
      INNER JOIN cadastro.fisica f ON (f.idpes = p.idpes)
      WHERE e.cod_escola = $1
    ';

    $exclusivamente = 2;

    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($escolaId))));
    if($r10s1){
      $d = '|';
      $return = '';

      for($i = 3; $i <=11; $i++){
        if ($local_funcionamento == $i)
          ${'r10s'.$i} = 1;
        else
          ${'r10s'.$i} = 0;
      }

      if($codigo_inep_escola_compartilhada !=null){
        $r10s13 = 1;
        $r10s14 = $codigo_inep_escola_compartilhada;
      }else
        $r10s13 = 0;

      if($r10s3 == 0)
        $r10s13 = NULL;

      if($r10s3 <> 1 && $r10s8 <> 1)
        $r10s12 = NULL;

      if($r10s3 == 1){
        if(is_null($r10s12)){
          $this->msg .= "Dados para formular o registro 10 campo 12 da escola {$escolaId} com problemas. Obrigatório quando o campo 3 for igual a 1 <br/>";
          $this->error = true;
        }
      }

      $r10s96 = ($r10s96 == 1 && ($r10s92 == 1 || $r10s93 == 1)) ? 1 : (($r10s92 == 1 || $r10s93 == 1) ? 0 : NULL);

      if ($r10s90 == $exclusivamente || $r10s91 == $exclusivamente) {
        $r10s92 = $r10s93 = $r10s94 = $r10s95 = '';
      } else {
        $r10s92 = ($r10s92 ? $r10s92 : 0);
        $r10s93 = ($r10s93 ? $r10s93 : 0);
        $r10s94 = ($r10s94 ? $r10s94 : 0);
        $r10s95 = ($r10s95 ? $r10s95 : 0);
      }

      $r10s98 = 1;
      $r10s99 = $r10s100 = 0;

      if($lingua_ministrada && $r10s101){
        $r10s102 = 1;
        $r10s104 = $lingua_ministrada;
      }elseif ($r10s124)
        $r10s103 = 1;

      for ($i=1; $i <= 107 ; $i++){
        if($i>=71 && $i<=85)
          $return .= (${'r10s'.$i} == 0 ? '' : ${'r10s'.$i}).$d;
        else
          $return .= ${'r10s'.$i}.$d;
      }
      $return = substr_replace($return, "", -1);

      return $return."\n";
    }else{
      $this->msg .= "Dados para formular o registro 10 da escola {$escolaId} não encontrados. Verifique se a escola possuí código do INEP cadastrado. <br/>";
      $this->error = true;
    }
  }

  protected function exportaDadosRegistro20($escolaId, $turmaId, $data_ini, $data_fim){
    $sql =
    	' SELECT
        \'20\' as r20s1,
        ece.cod_escola_inep as r20s2,
        t.cod_turma as r20s4,
        t.nm_turma as r20s5,
        1 as r20s6,
        substring(t.hora_inicial,1,2) as r20s7,
        substring(t.hora_inicial,4,2) as r20s8,
        substring(t.hora_final,1,2) as r20s9,
        substring(t.hora_final,4,2) as r20s10,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 1
          LIMIT 1
        ) as r20s11,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 2
          LIMIT 1
        ) as r20s12,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 3
          LIMIT 1
        ) as r20s13,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 4
          LIMIT 1
        ) as r20s14,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 5
          LIMIT 1
        ) as r20s15,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 6
          LIMIT 1
        ) as r20s16,
        (SELECT 1
          FROM turma_dia_semana
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 7
          LIMIT 1
        ) as r20s17,
        t.tipo_atendimento as r20s18,
        t.turma_mais_educacao as r20s19,

        t.atividade_complementar_1 as r20s20,
        t.atividade_complementar_2 as r20s21,
        t.atividade_complementar_3 as r20s22,
        t.atividade_complementar_4 as r20s23,
        t.atividade_complementar_5 as r20s24,
        t.atividade_complementar_6 as r20s25,
        t.aee_braille as r20s26,
        t.aee_recurso_optico as r20s27,
        t.aee_estrategia_desenvolvimento as r20s28,
        t.aee_tecnica_mobilidade as r20s29,
        t.aee_libras as r20s30,
        t.aee_caa as r20s31,
        t.aee_curricular as r20s32,
        t.aee_soroban as r20s33,
        t.aee_informatica as r20s34,
        t.aee_lingua_escrita as r20s35,
        t.aee_autonomia as r20s36,
        c.modalidade_curso as r20s37,
        t.etapa_id as r20s38,
        t.cod_curso_profissional as r20s39,
        t.turma_sem_professor as r20s66,
        s.cod_serie as serieId

        FROM pmieducar.turma t
        INNER JOIN pmieducar.serie s ON (t.ref_ref_cod_serie = s.cod_serie)
        INNER JOIN pmieducar.curso c ON (s.ref_cod_curso = c.cod_curso)
        INNER JOIN pmieducar.escola e ON (t.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        WHERE t.cod_turma = $1
        AND (SELECT 1
              FROM pmieducar.matricula_turma mt
             INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
              WHERE mt.ref_cod_turma = t.cod_turma
              AND mt.ativo = 1
              AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($2) AND DATE($3)
              LIMIT 1) IS NOT NULL';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($turmaId, $data_ini, $data_fim))));
    if ($r20s1){

      $r20s5 = $this->upperAndUnaccent($r20s5);

      //Dias da semana  e tipo de atendimento não podem ser nullos, 1 ou 0
      for($i = 11; $i <=18; $i++)
        ${'r20s'.$i} = ${'r20s'.$i};

      // Atribui 0 (Não lecionado) para todas as disciplinas por padrão.
      $r20s40 = $r20s41 = $r20s42 = $r20s43 = $r20s44 = $r20s45 = $r20s46 = $r20s47 = $r20s48 = $r20s49 =
      $r20s50 = $r20s51 = $r20s52 = $r20s53 = $r20s54 = $r20s55 = $r20s56 = $r20s57 = $r20s58 = $r20s59 = $r20s60 =
      $r20s61 = $r20s62 = $r20s63 = $r20s64 = $r20s65 = 0;

      // Se a turma não presta atendimento educacional especializado AEE esses campos precisam ser nulos
      if ($r20s18 != 5)
        $r20s26 = $r20s27 = $r20s28 = $r20s29 = $r20s30 = $r20s31 = $r20s32 = $r20s33 = $r20s34 = $r20s35 = $r20s36 = NULL;

      if(!((($r20s38 >= 4 && $r20s38 <= 38) || $r20s38 == 41 || $r20s38 == 56 ) && in_array($r20s18, array(0, 2, 3))))
        $r20s19 = NULL;


      $coddigoEducacensoToSeq =
      			 array( 1 => '40', 2 => '41', 3 => '42', 4 => '43', 5 => '44', 6 => '45', 7 => '46',
      			 			  8 => '47', 30 => '48', 9 => '49', 10 => '50', 11 => '51', 12 => '52', 13 => '53',
      			 			  14 => '54', 28 => '55', 29 => '56', 16 => '57', 17 => '58', 20 => '59', 21 => '60',
      			 			  23 => '61', 25 => '62', 26 => '63', 27 => '64', 99 => '65');
      try{
        $componentesTurma = App_Model_IedFinder::getComponentesTurma($serieid, $escolaId, $turmaId);
      }catch(Exception $e){
        $componentesTurma = array();
      }

      foreach($componentesTurma as $componente){
        // Só serão consideradas disciplinas tipificadas com o código do Educacenso
        if($componente->codigo_educacenso){
          // Pega o código educacenso
          $codigoEducacenso = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
          $codigoEducacenso = $codigoEducacenso->getKey($componente->codigo_educacenso);

          // Código da disciplina no i-Educar
          $codigoSistema = $componente->id;

          // Verifica se é disciplina padrão ano letivo. Se for, será considerado que existe professor
          // vinculado a disciplina na sala de aula

          $professorVinculado = (bool)Portabilis_Utils_Database::selectField
          ('SELECT 1
              from modules.professor_turma
            inner join modules.professor_turma_disciplina on(professor_turma_disciplina.professor_turma_id = professor_turma.id)
             where professor_turma.turma_id = $1
               and professor_turma_disciplina.componente_curricular_id = $2
               and (SELECT 1
                      FROM pmieducar.matricula_turma mt
                     INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
                     WHERE mt.ref_cod_turma = professor_turma.turma_id
                       AND mt.ativo = 1
                       AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
                     LIMIT 1) IS NOT NULL
               ',
               array('params' => array($turmaId, $codigoSistema, $data_ini, $data_fim)));

          if (array_key_exists($codigoEducacenso, $coddigoEducacensoToSeq)){
            if(${ 'r20s'. $coddigoEducacensoToSeq[$codigoEducacenso]} == 1){
              continue;
            }elseif(${ 'r20s'. $coddigoEducacensoToSeq[$codigoEducacenso]} == 2){
              if(!$professorVinculado){
                continue;
              }
            }
          	${ 'r20s'. $coddigoEducacensoToSeq[$codigoEducacenso]} = ($professorVinculado ? 1 : 2);
        	}
        }

      }

      $this->turma_presencial_ou_semi = $r20s6;
      $d = '|';
      $return = '';

      for ($i=1; $i <= 66 ; $i++)
        $return .= ${'r20s'.$i}.$d;

      $return = substr_replace($return, "", -1);

      return $return."\n";
    }
  }

  protected function exportaDadosRegistro30($servidorId, $escolaId){
    $sql =
    	' SELECT
        \'30\' as r30s1,
        ece.cod_escola_inep as r30s2,
        ecd.cod_docente_inep as r30s3,
        s.cod_servidor as r30s4,
        p.nome as r30s5,
        p.email as r30s6,
        null as r30s7,
        fis.data_nasc as r30s8,
        fis.sexo as r30s9,
        r.raca_educacenso as r30s10,
        (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae) as r30s12,
        (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_pai) as r30s13,
        coalesce(fis.nacionalidade,1) as r30s14,
        (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) as r30s15,
        uf.cod_ibge as r30s16,
        m.cod_ibge as r30s17


        FROM  pmieducar.servidor s
        INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
        INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
        INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN cadastro.fisica_raca rc ON (rc.ref_idpes = fis.idpes)
        LEFT JOIN cadastro.raca r ON (r.cod_raca = rc.ref_cod_raca)
        LEFT JOIN public.municipio m ON (m.idmun = fis.idmun_nascimento)
        LEFT JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
        LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
        WHERE s.cod_servidor = $1
          AND e.cod_escola = $2
        LIMIT 1
    ';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId, $escolaId))));
    if ($r30s1){
      $r30s5 = $this->upperAndUnaccent($r30s5);
      $r30s6 = strtoupper($r30s6);
      $r30s8 = Portabilis_Date_Utils::pgSQLToBr($r30s8);
      $r30s9 = $r30s9 == 'M' ? 1 : 2;
      $r30s10 = is_numeric($r30s10) ? $r30s10 : 0;

      $r30s11 = ($r30s12 || $r30s13) ? 1 : 0;

      $r30s12 = $this->upperAndUnaccent($r30s12);
      $r30s13 = $this->upperAndUnaccent($r30s13);

      if($r30s14 == '1' || $r30s14 == '2')
        $r30s15 = 76;

      if($r30s14 == "1"){
        if(is_null($r30s16) || is_null($r30s17)){
          $this->msg .= "Dados para formular o registro 30 da escola {$escolaId} não encontrados. Verifique se os municípios e UFs dos servidores brasileiros possuem código INEP cadastrados.<br/>";
          $this->error = true;
        }
      }

      $sql = 'select distinct(deficiencia_educacenso) as id from cadastro.fisica_deficiencia,
              cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1
              and deficiencia_educacenso is not null';

      $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($r30s4)));

      $r30s19 = $r30s20 = $r30s21 = $r30s22 = $r30s23 = $r30s24 = $r30s25 = $r30s26 = null;

      $deficienciaToSeq = array( 1 => '19',
                                 2 => '20',
                                 3 => '21',
                                 4 => '22',
                                 5 => '23',
                                 6 => '24',
                                 7 => '25',
                                 8 => '26' );
      $r30s18 = 0;

      foreach ($deficiencias as $deficiencia_educacenso) {
        $deficiencia_educacenso = $deficiencia_educacenso['id'];
        if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)){
          ${ 'r30s'. $deficienciaToSeq[$deficiencia_educacenso] } = 1;
          $r30s18 = 1;
        }
      }

      if($r30s18 = 0)
        $r30s19 = $r30s20 = $r30s21 = $r30s22 = $r30s23 = $r30s24 = $r30s25 = $r30s26 = NULL;

      $r30s7 = null;

      $d = '|';
      $return = '';
      $numeroRegistros = 26;

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r30s'.$i}.$d;

      $return = substr_replace($return, "", -1);

      return $return."\n";
    }
  }

  protected function exportaDadosRegistro40($servidorId, $escolaId){
    $sql =
    'SELECT

		\'40\' as r40s1,
		ece.cod_escola_inep as r40s2,
    ecd.cod_docente_inep as r40s3,
		s.cod_servidor as r40s4,
		fis.cpf as r40s5,
		b.zona_localizacao as r40s6,
		ep.cep as r40s7,
		l.idtlog || l.nome as r40s8,
    ep.numero as r40s9,
		ep.complemento as r40s10,
		b.nome as r40s11,
		uf.cod_ibge as r40s12,
		m.cod_ibge as r40s13

		FROM 	pmieducar.servidor s
		INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
		INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
    INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
    INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
    INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
		INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
		 LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
		 LEFT JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
		 LEFT JOIN public.bairro b ON (clb.idbai = b.idbai)
		 LEFT JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
		 LEFT JOIN public.distrito d ON (d.iddis = b.iddis)
		 LEFT JOIN public.municipio m ON (d.idmun = m.idmun)
		 LEFT JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
		 LEFT JOIN public.pais ON (pais.idpais = uf.idpais)
		 LEFT JOIN public.logradouro l ON (l.idlog = cl.idlog)
     LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
		WHERE s.cod_servidor = $1
      AND e.cod_escola = $2
		LIMIT 1
    ';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId, $escolaId))));
    if ($r40s1){
      $r40s5 = $this->cpfToCenso($r40s5);

      $r40s8  = $this->upperAndUnaccent($r40s8);
      $r40s9  = $this->upperAndUnaccent($r40s9);
      $r40s10 = $this->upperAndUnaccent($r40s10);
      $r40s11 = $this->upperAndUnaccent($r40s11);

      $d = '|';
      $return = '';
      $numeroRegistros = 13;

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r40s'.$i}.$d;

      $return = substr_replace($return, "", -1);

      return $return."\n";
    }
  }

  protected function exportaDadosRegistro50($servidorId, $escolaId){

  	$sql =
  	'SELECT

		\'50\' as r50s1,
		ece.cod_escola_inep as r50s2,
    ecd.cod_docente_inep as r50s3,
		s.cod_servidor as r50s4,
		esc.escolaridade as r50s5,
		situacao_curso_superior_1 as r50s6,
		formacao_complementacao_pedagogica_1 as r50s7,
    (SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_1) as r50s8,
		(SELECT grau_academico FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_1) as grau_academico_curso_superior_1,
		ano_inicio_curso_superior_1 as r50s9,
		ano_conclusao_curso_superior_1 as r50s10,
		tipo_instituicao_curso_superior_1 as r50s11,
		(SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_1) as r50s12,
		situacao_curso_superior_2 as r50s13,
		formacao_complementacao_pedagogica_2 as r50s14,
    (SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_2) as r50s15,
    (SELECT grau_academico FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_2) as grau_academico_curso_superior_2,
		ano_inicio_curso_superior_2 as r50s16,
		ano_conclusao_curso_superior_2 as r50s17,
		tipo_instituicao_curso_superior_2 as r50s18,
		(SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_2) as r50s19,
		situacao_curso_superior_3 as r50s20,
		formacao_complementacao_pedagogica_3 as r50s21,
		(SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_3) as r50s22,
    (SELECT grau_academico FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_3) as grau_academico_curso_superior_3,
		ano_inicio_curso_superior_3 as r50s23,
		ano_conclusao_curso_superior_3 as r50s24,
		tipo_instituicao_curso_superior_3 as r50s25,
		(SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_3) as r50s26,
		pos_especializacao as r50s27,
		pos_mestrado as r50s28,
		pos_doutorado as r50s29,
		pos_nenhuma as r50s30,
		curso_creche as r50s31,
		curso_pre_escola as r50s32,
		curso_anos_iniciais as r50s33,
		curso_anos_finais as r50s34,
		curso_ensino_medio as r50s35,
		curso_eja as r50s36,
		curso_educacao_especial as r50s37,
		curso_educacao_indigena as r50s38,
		curso_educacao_campo as r50s39,
		curso_educacao_ambiental as r50s49,
		curso_educacao_direitos_humanos as r50s41,
		curso_genero_diversidade_sexual as r50s42,
		curso_direito_crianca_adolescente as r50s43,
		curso_relacoes_etnicorraciais as r50s44,
		curso_outros as r50s45,
		curso_nenhum as r50s46

		FROM 	pmieducar.servidor s
		INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
		INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
    INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
    INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
    INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
		INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
		LEFT JOIN cadastro.escolaridade esc ON (esc.idesco = s.ref_idesco)
    LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
		WHERE s.cod_servidor = $1
      AND e.cod_escola = $2
		LIMIT 1
  	';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId, $escolaId))));
      if ($r50s1){
      $d = '|';
      $return = '';
      $numeroRegistros = 46;

      if($grau_academico_curso_superior_1 == self::BACHARELADO || $grau_academico_curso_superior_1 == self::TECNOLOGO){
        if(is_null($r50s7)){
          $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 7 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
          $this->error = true;
        }
      }elseif($grau_academico_curso_superior_1 == self::LICENCIATURA){
        $r50s7 = NULL;
      }

      if($grau_academico_curso_superior_2 == self::BACHARELADO || $grau_academico_curso_superior_2 == self::TECNOLOGO){
        if(is_null($r50s14)){
          $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 14 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
          $this->error = true;
        }
      }elseif($grau_academico_curso_superior_2 == self::LICENCIATURA){
        $r50s14 = NULL;
      }

      if($grau_academico_curso_superior_3 == self::BACHARELADO || $grau_academico_curso_superior_3 == self::TECNOLOGO){
        if(is_null($r50s21)){
          $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 21 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
          $this->error = true;
        }
      }elseif($grau_academico_curso_superior_3 == self::LICENCIATURA){
        $r50s21 = NULL;
      }

      if($r50s6 != 1){ $r50s7 = NULL;}
      if($r50s13 != 1){ $r50s14 = NULL;}
      if($r50s20 != 1){ $r50s21 = NULL;}

      $cont= 0;
      for ($i=1; $i <= $numeroRegistros; $i++){
        if($i >= 31)
        	$return .= (${'r50s'.$i} == 1 ? 1 : 0).$d;
        else
          $return .= ${'r50s'.$i}.$d;
      }

      $return = substr_replace($return, "", -1);

      return $return."\n";
    }
  }

  protected function exportaDadosRegistro51($servidorId, $escolaId, $data_ini, $data_fim){

  	$sql =
  	 'SELECT

			\'51\' as r51s1,
			ece.cod_escola_inep as r51s2,
      ecd.cod_docente_inep as r51s3,
			s.cod_servidor as r51s4,
			t.cod_turma as r51s6,
			pt.funcao_exercida as r51s7,
			pt.tipo_vinculo as r51s8,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id
				ORDER BY codigo_educacenso
				OFFSET 0
				LIMIT 1
			) as r51s9,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 1
				LIMIT 1
			) as r51s10,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 2
				LIMIT 1
			) as r51s11,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 3
				LIMIT 1
			) as r51s12,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 4
				LIMIT 1
			) as r51s13,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 5
				LIMIT 1
			) as r51s14,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 6
				LIMIT 1
			) as r51s15,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 7
				LIMIT 1
			) as r51s16,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 8
				LIMIT 1
			) as r51s17,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 9
				LIMIT 1
			) as r51s18,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 10
				LIMIT 1
			) as r51s19,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 11
				LIMIT 1
			) as r51s20,

			(
			SELECT distinct(cc.codigo_educacenso)

				FROM modules.componente_curricular cc
				INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

				WHERE	ptd.professor_turma_id = pt.id

				ORDER BY codigo_educacenso
				OFFSET 12
				LIMIT 1
			) as r51s21


			FROM 	pmieducar.servidor s
			INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
			INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
			INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
			INNER JOIN pmieducar.turma t ON (pt.turma_id = t.cod_turma)
      INNER JOIN pmieducar.escola e ON (t.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
      LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
			WHERE s.cod_servidor = $1
			AND e.cod_escola = t.ref_ref_cod_escola
      AND e.cod_escola = $2
      AND t.ativo = 1
      and (SELECT 1
             FROM pmieducar.matricula_turma mt
            INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
            WHERE mt.ref_cod_turma = t.cod_turma
              AND mt.ativo = 1
              AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
            LIMIT 1) IS NOT NULL
  	';


    // Transforma todos resultados em variáveis
		$d = '|';
    $return = '';
    $numeroRegistros = 21;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($servidorId, $escolaId, $data_ini, $data_fim))) as $reg) {
    	extract($reg);
	    for ($i=1; $i <= $numeroRegistros ; $i++)
	    	$return .= ${'r51s'.$i}.$d;

      $return = substr_replace($return, "", -1);
	    $return .= "\n";
    }
    return $return;
  }

  protected function exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim, $alunoId){

    $sql =
     'SELECT

      distinct(a.cod_aluno) as r60s4,
      p.idpes,
      \'60\' as r60s1,
      ece.cod_escola_inep as r60s2,
      eca.cod_aluno_inep as r60s3,
      p.nome as r60s5,
      fis.data_nasc as r60s6, /*tratar formato*/
      fis.sexo as r60s7, /*tratar na aplicação formato*/
      r.raca_educacenso as r60s8,
      /*se não tiver r60s10 e 11 é 0 se tiver um dos dois é 1*/
      COALESCE( a.nm_mae,(SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae)) as r60s10,
      COALESCE(a.nm_pai, (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_pai)) as r60s11,
      COALESCE(fis.nacionalidade,1) as r60s12,
      (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) as r60s13,
      uf.cod_ibge as r60s14,
      mun.cod_ibge as r60s15,
      recurso_prova_inep_aux_ledor as rs60s30,
      recurso_prova_inep_aux_transcricao as rs60s31,
      recurso_prova_inep_guia_interprete as rs60s32,
      recurso_prova_inep_interprete_libras as rs60s33,
      recurso_prova_inep_leitura_labial as rs60s34,
      recurso_prova_inep_prova_ampliada_16 as rs60s35,
      recurso_prova_inep_prova_ampliada_20 as rs60s36,
      recurso_prova_inep_prova_ampliada_24 as rs60s37,
      recurso_prova_inep_prova_braille as rs60s38

      FROM  pmieducar.aluno a
      INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
      INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
      INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
      INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
      LEFT JOIN cadastro.fisica_raca rc ON (rc.ref_idpes = fis.idpes)
      LEFT JOIN cadastro.raca r ON (r.cod_raca = rc.ref_cod_raca)
      LEFT JOIN public.municipio mun ON (mun.idmun = fis.idmun_nascimento)
      LEFT JOIN public.uf ON (uf.sigla_uf = mun.sigla_uf)
      LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

      WHERE e.cod_escola = $1
      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
      AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
      AND m.ano = $2
      AND a.cod_aluno = $5
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 39;

    $sqlDeficiencias = 'select distinct(deficiencia_educacenso) as id from cadastro.fisica_deficiencia,
                        cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1
                        and deficiencia_educacenso is not null';

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
      extract($reg);

      $r60s5 = $this->upperAndUnaccent($r60s5);

      $r60s6 = Portabilis_Date_Utils::pgSQLToBr($r60s6);
      $r60s7 = $r60s7 == 'M' ? 1 : 2;
      $r60s8 = is_numeric($r60s8) ? $r60s8 : 0;
      $r60s9 = (int) !(is_null($r60s10) && is_null($r60s11));

      $r60s10 = $this->upperAndUnaccent($r60s10);
      $r60s11 = $this->upperAndUnaccent($r60s11);

      if($r60s12 == '1' || $r60s12 == '2')
        $r60s13 = 76;

      $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sqlDeficiencias, array( 'params' => array($idpes)));

      // Reseta deficiências (DEFAULT NULL)
      $r60s16 = 0;
      $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 =
                 $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = NULL;

      // Caso não exista nenhum curso seta seq 40 como 1
      $r60s39 = (int) is_null($r60s30) && is_null($r60s31) && is_null($r60s32) && is_null($r60s33) && is_null($r60s34)
                && is_null($r60s35) && is_null($r60s36) && is_null($r60s37) && is_null($r60s38);

      // Define 'tipodeficiencia' => 'seqleiaute'
      $deficienciaToSeq = array(  1 => '17',
                                  2 => '18',
                                  3 => '19',
                                  4 => '20',
                                  5 => '21',
                                  6 => '22',
                                  7 => '23',
                                  8 => '24',
                                  9 => '25',
                                 10 => '26',
                                 11 => '27',
                                 12 => '28',
                                 13 => '29');

      // Se tiver alguma deficiência, a seq 16 deve ser 1
      if (count($deficiencias)>0){
        $r60s16 = 1;
        $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 =
                  $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = 0;

        foreach ($deficiencias as $deficiencia_educacenso) {
          $deficiencia_educacenso = $deficiencia_educacenso['id'];
          if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)){
            ${ 'r60s'. $deficienciaToSeq[$deficiencia_educacenso] } = 1;
          }
        }
      }
      // Se o aluno não tiver deficiências não pode ser informado recursos para provas
      if ($r60s16)
        $r60s39 = NULL;
      else
        $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 =
                  $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = NULL;

      if($r60s16 == 0){
        for($i=30; $i <= 39; $i++){
          ${'r60s'.$i} = NULL;
        }
      }else{
        for($i=30; $i <= 38; $i++){
          ${'r60s'.$i} = 0;
        }
        $r60s39 = 1;
      }

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r60s'.$i}.$d;

      $return = substr_replace($return, "", -1);
      $return .= "\n";
    }

    return $return;
  }

protected function exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim, $alunoId){

    $sql =
     '  SELECT

        distinct(a.cod_aluno) as r70s4,
        \'70\' as r70s1,
        ece.cod_escola_inep as r70s2,
        eca.cod_aluno_inep as r70s3,
        fd.rg as r70s5,
        oer.codigo_educacenso as r70s6,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_exp_rg) as r70s7,
        fd.data_exp_rg as r70s8,
        tipo_cert_civil,
        num_termo as r70s11,
        num_folha as r70s12,
        num_livro as r70s13,
        data_emissao_cert_civil as r70s14,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_cert_civil) as r70s15,
        cartorio_cert_civil_inep as r70s17,
        certidao_nascimento as r70s18,
        fis.cpf as r70s19,
        fis.nis_pis_pasep as r70s21,
        b.zona_localizacao as r70s22,
        ep.cep as r70s23,
        l.idtlog || l.nome as r70s24,
        ep.numero as r70s25,
        ep.complemento as r70s26,
        b.nome as r70s27,
        uf.cod_ibge as r70s28,
        mun.cod_ibge as r70s29


        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
         LEFT JOIN cadastro.documento fd ON (fis.idpes = fd.idpes)
         LEFT JOIN cadastro.orgao_emissor_rg oer ON (fd.idorg_exp_rg = oer.idorg_rg)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        INNER JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
        INNER JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
        INNER JOIN public.bairro b ON (clb.idbai = b.idbai)
        INNER JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
        INNER JOIN public.distrito d ON (d.iddis = b.iddis)
        INNER JOIN public.municipio mun ON (d.idmun = mun.idmun)
        INNER JOIN public.uf ON (uf.sigla_uf = mun.sigla_uf)
        INNER JOIN public.pais ON (pais.idpais = uf.idpais)
        INNER JOIN public.logradouro l ON (l.idlog = cl.idlog)
        LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

        WHERE e.cod_escola = $1
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
        AND m.ano = $2
        AND a.cod_aluno = $5
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 29;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
      extract($reg);

      $r70s8 = Portabilis_Date_Utils::pgSQLToBr($r70s8);
      $r70s14 = Portabilis_Date_Utils::pgSQLToBr($r70s14);

      $r70s19 = $this->cpfToCenso($r70s19);

      $r70s24 = $this->upperAndUnaccent($r70s24);
      $r70s25 = $this->upperAndUnaccent($r70s25);
      $r70s26 = $this->upperAndUnaccent($r70s26);
      $r70s27 = $this->upperAndUnaccent($r70s27);

      if($r70s21 == 0){ $r70s21 = null; }
      if($r70s5 == 0){ $r70s5 = null; }

      // Validações referentes a certidões (Modelo antigo e novo, nascimento e casamento)
      $r70s9 = $r70s10 = NULL;
      if (is_null($tipo_cert_civil) && !empty($r70s18)){
        $r70s9 = 2;
        $r70s10 = NULL;
        $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = NULL;
        $r70s18 =  str_replace(' ', '',$r70s18);
      }elseif($tipo_cert_civil == 91){
        if (!(is_null($r70s11) || is_null($r70s15) || is_null($r70s17)))
          $r70s9 = $r70s10 = 1;
        else
          $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = NULL;

      }elseif ($tipo_cert_civil == 92) {
        if (!(is_null($r70s11) || is_null($r70s15) || is_null($r70s17))){
          $r70s9 = 1;
          $r70s10 = 2;
        }else
          $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = NULL;
      }else
        $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = NULL;
      // fim das validações de certidões //

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r70s'.$i}.$d;

      $return = substr_replace($return, "", -1);

      $return .= "\n";
    }

    return $return;
  }

  protected function exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim, $alunoId){

    $sql =
     '  SELECT

        \'80\' as r80s1,
        ece.cod_escola_inep as r80s2,
        eca.cod_aluno_inep as r80s3,
        a.cod_aluno as r80s4,
        t.cod_turma as r80s6,
        t.turma_unificada as r80s8,
        t.etapa_educacenso as r80s9,
        \'3\' as r80s10,
        ta.responsavel as transporte_escolar,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 1
        ) as r80s13,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 2
        ) as r80s14,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 3
        ) as r80s15,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 4
        ) as r80s16,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 5
        ) as r80s17,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 6
        ) as r80s18,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 7
        ) as r80s19,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 8
        ) as r80s20,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 9
        ) as r80s21,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 10
        ) as r80s22,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 11
        ) as r80s23,

        a.veiculo_transporte_escolar

        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = mt.ref_cod_turma)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN modules.transporte_aluno ta ON (ta.aluno_id = a.cod_aluno)
        LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

        WHERE e.cod_escola = $1
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
        AND m.ano = $2
        AND a.cod_aluno = $5
        AND m.ativo = 1
        AND mt.ativo = 1
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 24;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
      extract($reg);

      $r80s9 = NULL;

      for ($i=13; $i <= 23 ; $i++)
          ${'r80s'.$i} = 0;

      // validações transporte escolar

      // if ($transporte_escolar){
      //   $veiculo = false;
      //   for ($i=13; $i <= 23 ; $i++) {
      //     if (${'r80s'.$i} == 1)
      //       $veiculo = true;
      //   }
      //   if ($veiculo)
      //     $r80s11 = 1;
      //   elseif($veiculo_transporte_escolar){
      //     $r80s11 = 1;
      //     ${'r80s'.($veiculo_transporte_escolar + 12)} = 1;
      //   }
      //   $r80s12 = $transporte_escolar;
      // }else{
      //   for ($i=13; $i <= 23 ; $i++) {
      //     ${'r80s'.$i} = NULL;
      //   }
      // }

      if(is_null($transporte_escolar)){
        $r80s11 = NULL;
      }else{
        $r80s11 = (($transporte_escolar == 0 ) ? 0 : 1);
        if($r80s11){
          $r80s12 = $transporte_escolar;
        }
      }
      ${'r80s'.($veiculo_transporte_escolar + 12)} = 1;
      $utiliza_algum_veiculo = FALSE;
      for($i=13; $i<=23;$i++){
        $utiliza_algum_veiculo = (${'r80s'.$i} == 1) || $utiliza_algum_veiculo;
      }

      if(!$transporte_escolar){
        for($i=12; $i<=23;$i++){
          ${'r80s'.$i} = NULL;
        }
      }

      if($transporte_escolar && !$utiliza_algum_veiculo){
        $this->msg .= "Dados para formular o registro 80 campo 11 da escola {$escolaId} com problemas. Verifique se o campo tipo de veículo foi preenchido no aluno {$alunoId}.<br/>";
        $this->error = true;
      }

      if($this->turma_presencial_ou_semi == 1 || $this->turma_presencial_ou_semi == 2){
        if(is_null($r80s11)){
          $this->msg .= "Dados para formular o registro 80 campo 11 da escola {$escolaId} com problemas. Verifique se o campo transporte escolar foi preenchido para aluno {$alunoId}.<br/>";
          $this->error = true;
        }
      }

      // fim validações transporte escolar

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r80s'.$i}.$d;

      $return = substr_replace($return, "", -1);
      $return .= "\n";
    }

    return $return;
  }

  protected function exportaDadosRegistro99() {
    return "99|\n";
  }

  protected function cpfToCenso($cpf){
    $cpf = str_replace(array('.', '-'), '', int2CPF($cpf));
    return $cpf == '00000000000' ? NULL : $cpf;
  }

  protected function upperAndUnaccent($string){
    $string = Portabilis_String_Utils::toUtf8($string);
    $string = preg_replace(array("/(á|à|ã|â|ä)/",
                                 "/(Á|À|Ã|Â|Ä)/",
                                 "/(é|è|ê|ë)/",
                                 "/(É|È|Ê|Ë)/",
                                 "/(í|ì|î|ï)/",
                                 "/(Í|Ì|Î|Ï)/",
                                 "/(ó|ò|õ|ô|ö)/",
                                 "/(Ó|Ò|Õ|Ô|Ö)/",
                                 "/(ú|ù|û|ü)/",
                                 "/(Ú|Ù|Û|Ü)/",
                                 "/(ñ)/","/(Ñ)/",
                                 "/(ç)/","/(Ç)/", "/(ª)/"),
                            explode(" ","a A e E i I o O u U n N c C "), $string);

    return strtoupper($string);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'educacenso-export'))
      $this->appendResponse($this->educacensoExport());
    else
      $this->notImplementedOperationError();
  }
}