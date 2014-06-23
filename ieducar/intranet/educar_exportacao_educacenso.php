<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'Portabilis/Business/Professor.php';
require_once 'App/Model/IedFinder.php';
require_once 'ComponenteCurricular/Model/CodigoEducacenso.php';

/**
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Exporta&ccedil;&atilde;o Educacenso');
    $this->processoAp = 846;
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
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


  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(585, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Exporta&ccedil;&atilde;o para o Educacenso"
    ));
    $this->enviaLocalizacao($localizacao->montar());    
    
    return 'Novo';
  }

  function Gerar()
  {
    $this->nome_url_sucesso = "Exportar";
    $this->acao_enviar      = 'document.formcadastro.submit()';

    $this->inputsHelper()->input('ano');
    $this->inputsHelper()->date('data_ini',array( 'label' => Portabilis_String_Utils::toLatin1('Data início')));
    $this->inputsHelper()->date('data_fim',array( 'label' => 'Data fim'));
    $escolas = Portabilis_Business_Professor::isProfessor($this->ref_cod_instituicao, $this->pessoa_logada) ? 
                  Portabilis_Business_Professor::escolasAlocado($this->ref_cod_instituicao, $this->pessoa_logada) : 
                                                                                  App_Model_IedFinder::getEscolas();
 
    if (is_array($escolas) && count($escolas)) {      

      $conteudo = '<br style="clear: left" />';
      $conteudo .= '<div style="margin-bottom: 10px; float: left">';
      $conteudo .= "  <label style='display: block; float: left;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(".'"escolas[]"'.");'/><b>Marcar todas</b></label>";
      $conteudo .= '</div>';
      $conteudo .= '<br style="clear: left" />';
      foreach ($escolas as $key => $value) {

        $conteudo .= '<div style="margin-bottom: 10px; float: left">';
        $conteudo .= "  <label style='display: block; float: left;'><input type=\"checkbox\" name=\"escolas[$key]\" id=\"escolas[]\" value=\"{$key}\">{$value}</label>";
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';
      }

      $escolas  = '<table cellspacing="0" cellpadding="0" border="0">';
      $escolas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
      $escolas .= '</table>';
    }

    $this->campoRotulo("escolas_", "<b>Escolas</b>",
      "<div id='escolas'>$escolas</div>", null, 'Selecione a(s) escola(s) que deseja exportar');

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();
    //Checa permissões
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(585, $this->pessoa_logada, 7,
      'educar_index.php');
    
    $conteudo = '';

    foreach ($this->escolas as $key => $escolaId) {  
      $conteudo .= $this->exportaDadosCensoPorEscola($escolaId, 
              $this->ano, 
              Portabilis_Date_Utils::brToPgSQL($this->data_ini), 
              Portabilis_Date_Utils::brToPgSQL($this->data_fim));
    }

    header('Content-type: text/plain');
    header('Content-Length: ' . strlen($conteudo));
    header('Content-Disposition: attachment; filename=exportacao.txt');
    echo $conteudo;
    die();
  }

  function exportaDadosCensoPorEscola($escolaId, $ano, $data_ini, $data_fim){
    $export = $this->exportaDadosRegistro00($escolaId, $ano);
    $export .= $this->exportaDadosRegistro10($escolaId);
    foreach ($this->getTurmas($escolaId, $ano) as $turmaId => $turmaNome) {
      $export .= $this->exportaDadosRegistro20($escolaId, $turmaId);
    }
    foreach ($this->getServidores($escolaId) as $servidor) {
      $export .= $this->exportaDadosRegistro30($servidor['id']);
      $export .= $this->exportaDadosRegistro40($servidor['id']);
      $export .= $this->exportaDadosRegistro50($servidor['id']);
      $export .= $this->exportaDadosRegistro51($servidor['id']);      
    }
    $export .= $this->exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim);
    $export .= $this->exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim);
    $export .= $this->exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim);
    return $export;
  }

  function getTurmas($escolaId, $ano){
    return App_Model_IedFinder::getTurmas($escolaId, NULL, $ano);
  }

  function getServidores($escolaId){
    $sql = 'SELECT cod_servidor as id
              FROM pmieducar.servidor
              INNER JOIN pmieducar.servidor_alocacao ON (ref_cod_servidor = cod_servidor)
              WHERE ref_cod_escola = $1';
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId)));
  }

  function exportaDadosRegistro00($escolaId, $ano){
    $sql = 
    	' SELECT 
        \'00\' as r00s1,
        ece.cod_escola_inep as r00s2,
        e.situacao_funcionamento as r00s3,

        (SELECT min(ano_letivo_modulo.data_inicio) 
          FROM pmieducar.ano_letivo_modulo 
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) as r00s4,

        (SELECT max(ano_letivo_modulo.data_fim) 
          FROM pmieducar.ano_letivo_modulo 
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) as r00s5,

        p.nome as r00s6,
        e.latitude as r00s7,
        e.longitude as r00s8,
        ep.cep as r00s9,
        l.idtlog || l.nome as r00s10,
        ep.numero as r00s11,
        b.nome as r00s13,
        uf.cod_ibge as r00s14,
        m.cod_ibge as r00s15,
        d.cod_ibge as r00s16,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.ddd)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(ddd_telefone) 
            FROM pmieducar.escola_complemento 
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s17,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(telefone) 
            FROM pmieducar.escola_complemento 
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) as r00s18,

        (SELECT COALESCE(p.email,(SELECT email FROM pmieducar.escola_complemento where ref_cod_escola = e.cod_escola))) as r00s22,

        e.dependencia_administrativa as r00s24,
        b.zona_localizacao as r00s25


        FROM pmieducar.escola e
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        INNER JOIN cadastro.pessoa p ON (e.ref_idpes = p.idpes)
        INNER JOIN cadastro.juridica j ON (j.idpes = p.idpes)
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

      for ($i=1; $i <= 35 ; $i++)
        $return .= ${'r00s'.$i}.$d;

      return $return."\n";
    }
  }

  function exportaDadosRegistro10($escolaId){
    $sql = 
    	'SELECT
      \'10\' as r10s1,
      ece.cod_escola_inep as r10s2,

      f.cpf as r10s3,
      p.nome as r10s4,
      e.cargo_gestor as r10s5,
      p.email as r10s6,
      e.local_funcionamento,
      e.condicao as r10s16,
      e.codigo_inep_escola_compartilhada,
      e.agua_consumida as r10s24,
      e.agua_rede_publica as r10s25,
      e.agua_poco_artesiano as r10s26,
      e.agua_cacimba_cisterna_poco as r10s27,
      e.agua_fonte_rio as r10s28,
      e.agua_inexistente as r10s29,
      e.energia_rede_publica as r10s30,
      e.energia_gerador as r10s31,
      e.energia_outros as r10s32,
      e.energia_inexistente as r10s33,
      e.esgoto_rede_publica as r10s34,
      e.esgoto_fossa as r10s35,
      e.esgoto_inexistente as r10s36,
      e.lixo_coleta_periodica as r10s37,
      e.lixo_queima as r10s38,
      e.lixo_joga_outra_area as r10s39,
      e.lixo_recicla as r10s40,
      e.lixo_enterra as r10s41,
      e.lixo_outros as r10s42,
      e.dependencia_sala_diretoria as r10s43,
      e.dependencia_sala_professores as r10s44,
      e.dependencia_sala_secretaria as r10s45,
      e.dependencia_laboratorio_informatica as r10s46,
      e.dependencia_laboratorio_ciencias as r10s47,
      e.dependencia_sala_aee as r10s48,
      e.dependencia_quadra_coberta as r10s49,
      e.dependencia_quadra_descoberta as r10s50,
      e.dependencia_cozinha as r10s51,
      e.dependencia_biblioteca as r10s52,
      e.dependencia_sala_leitura as r10s53,
      e.dependencia_parque_infantil as r10s54,
      e.dependencia_bercario as r10s55,
      e.dependencia_banheiro_fora as r10s56,
      e.dependencia_banheiro_dentro as r10s57,
      e.dependencia_banheiro_infantil as r10s58,
      e.dependencia_banheiro_deficiente as r10s59,
      e.dependencia_banheiro_chuveiro as r10s61,
      e.dependencia_refeitorio as r10s62,
      e.dependencia_dispensa as r10s63,
      e.dependencia_aumoxarifado as r10s64,
      e.dependencia_auditorio as r10s65,
      e.dependencia_patio_coberto as r10s66,
      e.dependencia_patio_descoberto as r10s67,
      e.dependencia_alojamento_aluno as r10s68,
      e.dependencia_alojamento_professor as r10s69,
      e.dependencia_area_verde as r10s70,
      e.dependencia_lavanderia as r10s71,
      e.dependencia_nenhuma_relacionada as r10s72,
      e.dependencia_numero_salas_existente as r10s73,
      e.dependencia_numero_salas_utilizadas as r10s74,

      e.televisoes as r10s75,
      e.videocassetes as r10s76,
      e.dvds as r10s77,
      e.antenas_parabolicas as r10s78,
      e.copiadoras as r10s79,
      e.retroprojetores as r10s80,
      e.impressoras as r10s81,
      e.aparelhos_de_som as r10s82,
      e.projetores_digitais  as r10s83,
      e.faxs as r10s84,
      e.maquinas_fotograficas as r10s85,
      e.computadores as r10s86,
      e.computadores_administrativo as r10s87,
      e.computadores_alunos as r10s88,
      e.acesso_internet as r10s89,
      e.banda_larga as r10s90,

      total_funcionario as r10s91,
      1 as r10s92,
      atendimento_aee as r10s93,
      atividade_complementar as r10s94,

      (SELECT 1 
        FROM pmieducar.curso 
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso)
        WHERE modalidade_curso = 1 AND escola_curso.ref_cod_escola = e.cod_escola
        LIMIT 1
      ) as r10s95,

      (SELECT 1 
        FROM pmieducar.curso 
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso)
        WHERE modalidade_curso = 2 AND escola_curso.ref_cod_escola = e.cod_escola 
        LIMIT 1
      ) as r10s96,

      (SELECT 1 
        FROM pmieducar.curso 
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso)
        WHERE modalidade_curso = 3 AND escola_curso.ref_cod_escola = e.cod_escola 
        LIMIT 1
      ) as r10s97,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 1
        LIMIT 1
      ) as r10s98,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 2
        LIMIT 1
      ) as r10s99,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 3
        LIMIT 1
      ) as r10s100,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 4
        LIMIT 1
      ) as r10s101,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 5
        LIMIT 1
      ) as r10s102,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 6
        LIMIT 1
      ) as r10s103,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 7
        LIMIT 1
      ) as r10s104,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 8
        LIMIT 1
      ) as r10s105,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 1
        LIMIT 1
      ) as r10s106,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 2
        LIMIT 1
      ) as r10s107,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 3
        LIMIT 1
      ) as r10s108,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 4
        LIMIT 1
      ) as r10s109,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 5
        LIMIT 1
      ) as r10s110,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 6
        LIMIT 1
      ) as r10s111,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 7
        LIMIT 1
      ) as r10s112,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 8
        LIMIT 1
      ) as r10s113,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 9
        LIMIT 1
      ) as r10s114,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 10
        LIMIT 1
      ) as r10s115,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 9
        LIMIT 1
      ) as r10s116,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 11
        LIMIT 1
      ) as r10s117,

      (SELECT 1 
        FROM modules.etapas_curso_educacenso
        INNER JOIN pmieducar.curso ON (curso.cod_curso = etapas_curso_educacenso.curso_id)
        INNER JOIN pmieducar.escola_curso ON (curso.cod_curso = escola_curso.ref_cod_curso) 
        WHERE escola_curso.ref_cod_escola = e.cod_escola
        AND etapas_curso_educacenso.etapa_id = 10
        LIMIT 1
      ) as r10s118,

      fundamental_ciclo as r10s119,
      localizacao_diferenciada as r10s120,
      didatico_nao_utiliza as r10s121,
      didatico_quilombola as r10s122,
      didatico_indigena as r10s123,
      educacao_indigena as r10s124,
      lingua_ministrada,
      espaco_brasil_aprendizado as r10s128,
      abre_final_semana as r10s129,
      codigo_lingua_indigena as r10s127,
      proposta_pedagogica as r10s130

      FROM pmieducar.escola e
      INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
      INNER JOIN cadastro.pessoa p ON (p.idpes = e.ref_idpes_gestor)
      INNER JOIN cadastro.fisica f ON (f.idpes = p.idpes)
      WHERE e.cod_escola = $1
    ';

    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($escolaId))));
    if($r10s1){
      $d = '|';
      $return = '';

      if ($local_funcionamento)
        ${'r10s'.$local_funcionamento} = 1;

      if($codigo_inep_escola_compartilhada !=null){
        $r10s17 = 1;
        $r10s18 = $codigo_inep_escola_compartilhada;
      }

      if($lingua_ministrada && $r10s124){
        $r10s125 = 1;
        $r10s127 = $lingua_ministrada;
      }elseif ($r10s124) {
        $r10s126 = 1;
      }

      $r10s3 = $this->cpfToCenso($r10s3);

      for ($i=1; $i <= 130 ; $i++){
        if($i>=75 && $i<=88)
          $return .= (${'r10s'.$i} == 0 ? '' : ${'r10s'.$i}).$d;
        else
          $return .= ${'r10s'.$i}.$d;
      }
      return $return."\n"; 
    }
  }

  function exportaDadosRegistro20($escolaId, $turmaId){
    $sql = 
    	' SELECT 
        \'20\' as r20s1,
        ece.cod_escola_inep as r20s2,
        t.cod_turma as r20s4,
        t.nm_turma as r20s5,
        substring(t.hora_inicial,1,2) as r20s6,
        substring(t.hora_inicial,4,2) as r20s7,
        substring(t.hora_final,1,2) as r20s8,
        substring(t.hora_final,4,2) as r20s9,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 1
          LIMIT 1
        ) as r20s10,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 2
          LIMIT 1
        ) as r20s11,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 3
          LIMIT 1
        ) as r20s12,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 4
          LIMIT 1
        ) as r20s13,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 5
          LIMIT 1
        ) as r20s14,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 6
          LIMIT 1
        ) as r20s15,
        (SELECT 1 
          FROM turma_dia_semana 
          WHERE ref_cod_turma = t.cod_turma
          AND dia_semana = 7
          LIMIT 1
        ) as r20s16,
        t.tipo_atendimento as r20s17,
        t.turma_mais_educacao as r20s18,

        t.atividade_complementar_1 as r20s19,
        t.atividade_complementar_2 as r20s20,
        t.atividade_complementar_3 as r20s21,
        t.atividade_complementar_4 as r20s22,
        t.atividade_complementar_5 as r20s23,
        t.atividade_complementar_6 as r20s24,
        t.aee_braille as r20s25,
        t.aee_recurso_optico as r20s26,
        t.aee_estrategia_desenvolvimento as r20s27,
        t.aee_tecnica_mobilidade as r20s28,
        t.aee_libras as r20s29,
        t.aee_caa as r20s30,
        t.aee_curricular as r20s31,
        t.aee_soroban as r20s32,
        t.aee_informatica as r20s33,
        t.aee_lingua_escrita as r20s34,
        t.aee_autonomia as r20s35,
        c.modalidade_curso as r20s36,
        t.etapa_id as r20s37,
        t.cod_curso_profissional as r20s38,
        t.turma_sem_professor as r20s65,
        s.cod_serie as serieId

        FROM pmieducar.turma t
        INNER JOIN pmieducar.serie s ON (t.ref_ref_cod_serie = s.cod_serie)
        INNER JOIN pmieducar.curso c ON (s.ref_cod_curso = c.cod_curso)
        INNER JOIN pmieducar.escola e ON (t.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        WHERE t.cod_turma = $1
    ';
    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($turmaId))));
    if ($r20s1){
      // Atribui 0 (Não lecionado) para todas as disciplinas por padrão.
      $r20s39 = $r20s40 = $r20s41 = $r20s42 = $r20s43 = $r20s44 = $r20s45 = $r20s46 = $r20s47 = $r20s48 = $r20s49 = 
      $r20s50 = $r20s51 = $r20s52 = $r20s53 = $r20s54 = $r20s55 = $r20s56 = $r20s57 = $r20s58 = $r20s59 = $r20s60 = 
      $r20s61 = $r20s62 = $r20s63 = $r20s64 = 0;


      $coddigoEducacensoToSeq = 
      			 array( 1 => '39', 2 => '40', 3 => '41', 4 => '42', 5 => '43', 6 => '44', 7 => '45', 
      			 			  8 => '46', 30 => '47', 9 => '48', 10 => '49', 11 => '50', 12 => '51', 13 => '52',
      			 			  14 => '53', 28 => '54', 29 => '55', 16 => '56', 17 => '57', 20 => '58', 21 => '59', 
      			 			  23 => '60', 25 => '61', 26 => '62', 27 => '63', 99 => '64');

      foreach(App_Model_IedFinder::getComponentesTurma($serieid, $escolaId, $turmaId) as $componente){
        // Só serão consideradas disciplinas tipificadas com o código do Educacenso
        if($componente->codigo_educacenso){
          // Pega o código educacenso
          $codigoEducacenso = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
          $codigoEducacenso = $codigoEducacenso->getKey($componente->codigo_educacenso);

          // Código da disciplina no i-Educar
          $codigoSistema = $componente->id;        

          // Verifica se é disciplina padrão ano letivo. Se for, será considerado que existe professor
          // vinculado a disciplina na sala de aula

          $padraoAnoLetivo = 
            !(bool)Portabilis_Utils_Database::selectField('SELECT 1 
                                                            FROM componente_curricular_turma 
                                                            WHERE turma_id = $1 
                                                            AND componente_curricular_id = $2 
                                                            LIMIT 1', 
                                                            array('params' => array($turmaId, $codigoSistema)));

          $professorVinculado = true;
          if (!$padraoAnoLetivo){
            $professorVinculado =
              Portabilis_Utils_Database::selectField('SELECT docente_vinculado
                                                              FROM componente_curricular_turma
                                                              WHERE turma_id = $1
                                                              AND componente_curricular_id = $2
                                                              LIMIT 1',
                                                              array('params' => array($turmaId, $codigoSistema)));
            $professorVinculado = $professorVinculado['docente_vinculado'];
          }
          
          if (array_key_exists($codigoEducacenso, $coddigoEducacensoToSeq)){
          	${ 'r20s'. $coddigoEducacensoToSeq[$codigoEducacenso] } = $professorVinculado ? 2 : 1;
        	}
        }
          
      }
      $d = '|';
      $return = '';

      for ($i=1; $i <= 65 ; $i++)
        $return .= ${'r20s'.$i}.$d;

      return $return."\n";
    }
  }
  
  function exportaDadosRegistro30($servidorId){
    $sql = 
    	' SELECT
        \'30\' as r30s1,
        ece.cod_escola_inep as r30s2,
        s.cod_servidor as r30s4,
        p.nome as r30s5,
        p.email as r30s6,
        fis.nis_pis_pasep as r30s7,
        fis.data_nasc as r30s8,
        fis.sexo as r30s9,
        r.raca_educacenso as r30s10,
        (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae) as r30s11,
        fis.nacionalidade as r30s12,
        (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) as r30s13,
        m.cod_ibge as r30s14,
        uf.cod_ibge as r30s15


        FROM  pmieducar.servidor s
        INNER JOIN portal.funcionario f ON (s.cod_servidor = f.ref_cod_pessoa_fj)
        INNER JOIN cadastro.fisica fis ON (fis.idpes = f.ref_cod_pessoa_fj)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
        INNER JOIN pmieducar.servidor_alocacao sa ON (sa.ref_cod_servidor = s.cod_servidor)
        INNER JOIN pmieducar.escola e ON (sa.ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN cadastro.fisica_raca rc ON (rc.ref_idpes = fis.idpes)
        LEFT JOIN cadastro.raca r ON (r.cod_raca = rc.ref_cod_raca)
        LEFT JOIN public.municipio m ON (m.idmun = fis.idmun_nascimento)
        LEFT JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
        WHERE s.cod_servidor = $1

        LIMIT 1
    ';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId))));
    if ($r30s1){
      $r30s8 = Portabilis_Date_Utils::pgSQLToBr($r30s8);
      $r30s9 = $r30s9 == 'M' ? 1 : 2;

      $sql = 'select distinct(deficiencia_educacenso) as id from cadastro.fisica_deficiencia,
              cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1 
              and deficiencia_educacenso is not null';

      $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($r30s4)));
      
      $r30s17 = $r30s18 = $r30s19 = $r30s20 = $r30s21 = $r30s22 = $r30s23 = $r30s24 = 0;
      
      $deficienciaToSeq = array( 1 => '17',
                                 2 => '18',
                                 3 => '19',
                                 4 => '20',
                                 5 => '21',
                                 6 => '22',
                                 7 => '23',
                                 8 => '24' );

      foreach ($deficiencias as $deficiencia_educacenso) {
        $deficiencia_educacenso = $deficiencia_educacenso['id'];
        if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)){
          ${ 'r30s'. $deficienciaToSeq[$deficiencia_educacenso] } = 1;
        }
      }
      
      $d = '|';
      $return = '';
      $numeroRegistros = 24;

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r30s'.$i}.$d;

      return $return."\n";
    }
  }

  function exportaDadosRegistro40($servidorId){
    $sql = 
    'SELECT

		\'40\' as r40s1,
		ece.cod_escola_inep as r40s2,
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
		INNER JOIN portal.funcionario f ON (s.cod_servidor = f.ref_cod_pessoa_fj)
		INNER JOIN cadastro.fisica fis ON (fis.idpes = f.ref_cod_pessoa_fj)
		INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
		INNER JOIN pmieducar.servidor_alocacao sa ON (sa.ref_cod_servidor = s.cod_servidor)
		INNER JOIN pmieducar.escola e ON (sa.ref_cod_escola = e.cod_escola)
		INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
		INNER JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
		INNER JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
		INNER JOIN public.bairro b ON (clb.idbai = b.idbai)
		INNER JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
		INNER JOIN public.distrito d ON (d.iddis = b.iddis)
		INNER JOIN public.municipio m ON (d.idmun = m.idmun)
		INNER JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
		INNER JOIN public.pais ON (pais.idpais = uf.idpais)
		INNER JOIN public.logradouro l ON (l.idlog = cl.idlog)
		WHERE s.cod_servidor = $1

		LIMIT 1    
    ';

    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId))));
    if ($r40s1){
      $r40s5 = $this->cpfToCenso($r40s5);

      $d = '|';
      $return = '';
      $numeroRegistros = 35;

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r40s'.$i}.$d;

      return $return."\n";
    }
  }

  function exportaDadosRegistro50($servidorId){

  	$sql = 
  	'SELECT

		\'50\' as r50s1,
		ece.cod_escola_inep as r50s2,
		s.cod_servidor as r50s4,
		esc.escolaridade as r50s5,
		situacao_curso_superior_1 as r50s6,
		formacao_complementacao_pedagogica_1 as r50s7,
		codigo_curso_superior_1 as r50s8,
		ano_inicio_curso_superior_1 as r50s9,
		ano_conclusao_curso_superior_1 as r50s10,
		tipo_instituicao_curso_superior_1 as r50s11,
		instituicao_curso_superior_1 as r50s12,
		situacao_curso_superior_2 as r50s13,
		formacao_complementacao_pedagogica_2 as r50s14,
		codigo_curso_superior_2 as r50s15,
		ano_inicio_curso_superior_2 as r50s16,
		ano_conclusao_curso_superior_2 as r50s17,
		tipo_instituicao_curso_superior_2 as r50s18,
		instituicao_curso_superior_2 as r50s19,
		situacao_curso_superior_3 as r50s20,
		formacao_complementacao_pedagogica_3 as r50s21,
		codigo_curso_superior_3 as r50s22,
		ano_inicio_curso_superior_3 as r50s23,
		ano_conclusao_curso_superior_3 as r50s24,
		tipo_instituicao_curso_superior_3 as r50s25,
		instituicao_curso_superior_3 as r50s26,
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
		INNER JOIN portal.funcionario f ON (s.cod_servidor = f.ref_cod_pessoa_fj)
		INNER JOIN cadastro.fisica fis ON (fis.idpes = f.ref_cod_pessoa_fj)
		INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
		INNER JOIN pmieducar.servidor_alocacao sa ON (sa.ref_cod_servidor = s.cod_servidor)
		INNER JOIN pmieducar.escola e ON (sa.ref_cod_escola = e.cod_escola)
		INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
		INNER JOIN cadastro.escolaridade esc ON (esc.idesco = s.ref_idesco)
		WHERE s.cod_servidor = $1

		LIMIT 1
  	';
    
    // Transforma todos resultados em variáveis
    extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array('return_only' => 'first-row', 'params' => array($servidorId))));
      if ($r50s1){
      $d = '|';
      $return = '';
      $numeroRegistros = 46;
      $r50s46 = (int) is_null($r50s31) && is_null($r50s32) && is_null($r50s33) && is_null($r50s34) && is_null($r50s35)
          					&& is_null($r50s36) && is_null($r50s37) && is_null($r50s38) && is_null($r50s39) && is_null($r50s40)
          					&& is_null($r50s41) && is_null($r50s42) && is_null($r50s43) && is_null($r50s44) && is_null($r50s45);
      $cont= 0;
      for ($i=1; $i <= $numeroRegistros; $i++){
      	$return .= ${'r50s'.$i}.$d;
      }

      return $return."\n";
    }
  }

  function exportaDadosRegistro51($servidorId){

  	$sql = 
  	 'SELECT

			\'51\' as r51s1,
			ece.cod_escola_inep as r51s2,
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
			INNER JOIN portal.funcionario f ON (s.cod_servidor = f.ref_cod_pessoa_fj)
			INNER JOIN cadastro.fisica fis ON (fis.idpes = f.ref_cod_pessoa_fj)
			INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
			INNER JOIN pmieducar.servidor_alocacao sa ON (sa.ref_cod_servidor = s.cod_servidor)
			INNER JOIN pmieducar.escola e ON (sa.ref_cod_escola = e.cod_escola)
			INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
			INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
			INNER JOIN pmieducar.turma t ON (pt.turma_id = t.cod_turma)
			WHERE s.cod_servidor = $1
			AND e.cod_escola = t.ref_ref_cod_escola
  	';

    
    // Transforma todos resultados em variáveis
		$d = '|';
    $return = '';
    $numeroRegistros = 21;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($servidorId))) as $reg) {
    	extract($reg);
	    for ($i=1; $i <= $numeroRegistros ; $i++)
	    	$return .= ${'r51s'.$i}.$d;
	    $return .= "\n";
    }

    return $return;
  }
  
  function exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim){

    $sql = 
     'SELECT

      distinct(a.cod_aluno) as r60s4,
      p.idpes,
      \'60\' as r60s1,
      ece.cod_escola_inep as r60s2,
      p.nome as r60s5,
      fis.nis_pis_pasep as r60s6,
      fis.data_nasc as r60s7, /*tratar formato*/
      fis.sexo as r60s8, /*tratar na aplicação formato*/
      r.raca_educacenso as r60s9,
      /*se não tiver r60s11 e 12 é 0 se tiver um dos dois é 1*/
      COALESCE( a.nm_mae,(SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae)) as r60s11,
      COALESCE(a.nm_pai, (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_pai)) as r60s12,
      fis.nacionalidade as r60s13,
      (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) as r60s14,
      uf.cod_ibge as r60s15,
      mun.cod_ibge as r60s16,
      recurso_prova_inep_aux_ledor as rs60s31,
      recurso_prova_inep_aux_transcricao as rs60s32,
      recurso_prova_inep_guia_interprete as rs60s33,
      recurso_prova_inep_interprete_libras as rs60s34,
      recurso_prova_inep_leitura_labial as rs60s35,
      recurso_prova_inep_prova_ampliada_16 as rs60s36,
      recurso_prova_inep_prova_ampliada_20 as rs60s37,
      recurso_prova_inep_prova_ampliada_24 as rs60s38,
      recurso_prova_inep_prova_braille as rs60s39

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

      WHERE e.cod_escola = $1
      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
      AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))      
      AND m.ano = $2
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 40;

    $sqlDeficiencias = 'select distinct(deficiencia_educacenso) as id from cadastro.fisica_deficiencia,
                        cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1 
                        and deficiencia_educacenso is not null';

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim))) as $reg) {
      extract($reg);
      
      $r60s7 = Portabilis_Date_Utils::pgSQLToBr($r60s7);
      $r60s8 = $r60s8 == 'M' ? 1 : 2;
      $r60s10 = (int) !(is_null($r60s11) && is_null($r60s12));

      $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sqlDeficiencias, array( 'params' => array($idpes)));
      
      // Reseta deficiências (DEFAULT 0)
      $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 = 
                            $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = $r60s30 = 0;
      
      // Caso não exista nenhum curso seta seq 40 como 1
      $r60s40 = (int) is_null($r60s31) && is_null($r60s32) && is_null($r60s33) && is_null($r60s34)
                && is_null($r60s35) && is_null($r60s36) && is_null($r60s37) && is_null($r60s38) && is_null($r60s39);
      // Define 'tipodeficiencia' => 'seqleiaute'
      $deficienciaToSeq = array(  1 => '18',
                                  2 => '19',
                                  3 => '20',
                                  4 => '21',
                                  5 => '22',
                                  6 => '23',
                                  7 => '24',
                                  8 => '25', 
                                  9 => '26', 
                                 10 => '27', 
                                 11 => '28', 
                                 12 => '29', 
                                 13 => '30');
      
      // Se tiver alguma deficiência, a seq 17 deve ser 1
      if (count($deficiencias)>0){
        $r60s17 = 1;

        foreach ($deficiencias as $deficiencia_educacenso) {
          $deficiencia_educacenso = $deficiencia_educacenso['id'];
          if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)){
            ${ 'r60s'. $deficienciaToSeq[$deficiencia_educacenso] } = 1;
          }
        }
      }

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r60s'.$i}.$d;
      $return .= "\n";
    }

    return $return;
  }  

function exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim){

    $sql = 
     '  SELECT

        distinct(a.cod_aluno) as r70s4,
        \'70\' as r70s1,
        ece.cod_escola_inep as r70s2,
        fd.rg as r70s5,
        oer.sigla as r70s7,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_exp_rg) as r70s8,
        fd.data_exp_rg as r70s9,
        tipo_cert_civil,
        num_termo as r70s12,
        num_folha as r70s13,
        num_livro as r70s14,
        data_emissao_cert_civil as r70s15,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_cert_civil) as r70s16,
        cartorio_cert_civil_inep as r70s18,
        certidao_nascimento as r70s19,
        fis.cpf as r70s20,
        fis.nis_pis_pasep as r70s22,
        a.justificativa_falta_documentacao as r70s23,
        b.zona_localizacao as r70s24,
        ep.cep as r70s25,
        l.idtlog || l.nome as r70s26,
        ep.numero as r70s27,
        ep.complemento as r70s28,        
        b.nome as r70s29,
        uf.cod_ibge as r70s30,
        mun.cod_ibge as r70s31


        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
        INNER JOIN cadastro.documento fd ON (fis.idpes = fd.idpes)
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

        WHERE e.cod_escola = $1
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
        AND m.ano = $2     
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 31;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim))) as $reg) {
      extract($reg);

      $r70s9 = Portabilis_Date_Utils::pgSQLToBr($r70s9);
      $r70s15 = Portabilis_Date_Utils::pgSQLToBr($r70s15);

      $r70s20 = $this->cpfToCenso($r70s20);

      // Validações referentes a certidões (Modelo antigo e novo, nascimento e casamento)
      $r70s10 = $r70s11 = NULL;
      if (is_null($tipo_cert_civil) && !empty($r70s19)){
        $r70s10 = 2;
        $r70s11 = 1;
        $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = NULL;
        $r70s19 =  str_replace(' ', '',$r70s19);
      }elseif($tipo_cert_civil == 91){
        if (!(is_null($r70s12) || is_null($r70s16) || is_null($r70s18)))
          $r70s10 = $r70s11 = 1;        
        else
          $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = $r70s19 = NULL;        
        
      }elseif ($tipo_cert_civil == 92) {
        if (!(is_null($r70s12) || is_null($r70s16) || is_null($r70s18))){
          $r70s10 = 1;
          $r70s11 = 2;  
        }else
          $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = $r70s19 = NULL;              
      }else
        $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = $r70s19 = NULL;
      // fim das validações de certidões //

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r70s'.$i}.$d;
      $return .= "\n";
    }

    return $return;
  }

function exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim){

    $sql = 
     '  SELECT

        \'80\' as r80s1,
        ece.cod_escola_inep as r80s2,
        a.cod_aluno as r80s4,
        t.cod_turma as r80s6,
        t.turma_unificada as r80s8,
        t.etapa_educacenso as r80s9,
        \'3\' as r80s10,
        ta.responsavel as transporte_escolar,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 1
        ) as r80s13,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 2
        ) as r80s14,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 3
        ) as r80s15,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 4
        ) as r80s16,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 5
        ) as r80s17,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 6
        ) as r80s18,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 7
        ) as r80s19,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 8
        ) as r80s20,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 9
        ) as r80s21,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 10
        ) as r80s22,
        (
          SELECT 1 
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 11
        ) as r80s23

        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = mt.ref_cod_turma)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN modules.transporte_aluno ta ON (ta.aluno_id = a.cod_aluno)

        WHERE e.cod_escola = $1
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))        
        AND m.ano = $2    
    ';

    // Transforma todos resultados em variáveis
    $d = '|';
    $return = '';
    $numeroRegistros = 23;

    foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $data_ini, $data_fim))) as $reg) {
      extract($reg);

      // validações transporte escolar
      $r80s11 = $r80s12 = 0;
      if ($transporte_escolar){
        $veiculo = false;
        for ($i=13; $i <= 23 ; $i++) { 
          if (${'r80s'.$i} == 1)
            $veiculo = true;
        }
        if ($veiculo)
          $r80s11 == 1;
        $r80s12 = $transporte_escolar;
      }else{
        for ($i=13; $i <= 23 ; $i++) { 
          ${'r80s'.$i} = NULL;          
        }
      }
      // fim validações transporte escolar

      for ($i=1; $i <= $numeroRegistros ; $i++)
        $return .= ${'r80s'.$i}.$d;
      $return .= "\n";
    }

    return $return;
  }  

  function cpfToCenso($cpf){
    $cpf = str_replace(array('.', '-'), '', int2CPF($cpf));
    return $cpf == '00000000000' ? NULL : $cpf;
  }    
}  
// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">

function marcarCheck(idValue) {
    // testar com formcadastro
    var contaForm = document.formcadastro.elements.length;
    var campo = document.formcadastro;
    var i;

    for (i=0; i<contaForm; i++) {
        if (campo.elements[i].id == idValue) {

            campo.elements[i].checked = campo.CheckTodos.checked;
        }
    }     
}
</script>
