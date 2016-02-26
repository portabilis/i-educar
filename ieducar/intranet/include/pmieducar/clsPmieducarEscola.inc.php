<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarEscola class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarEscola
{
  var $cod_escola;
  var $ref_usuario_cad;
  var $ref_usuario_exc;
  var $ref_cod_instituicao;
  var $ref_cod_escola_localizacao;
  var $ref_cod_escola_rede_ensino;
  var $ref_idpes;
  var $sigla;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $situacao_funcionamento;
  var $dependencia_administrativa;
  var $latitude;
  var $longitude;
  var $regulamentacao;
  var $acesso;
  var $ref_idpes_gestor;
  var $cargo_gestor;
  var $local_funcionamento;
  var $condicao;
  var $codigo_inep_escola_compartilhada;
  var $decreto_criacao;
  var $area_terreno_total;
  var $area_disponivel;
  var $area_construida;
  var $num_pavimentos;
  var $tipo_piso;
  var $medidor_energia;
  var $agua_consumida;
  var $agua_rede_publica;
  var $agua_poco_artesiano;
  var $agua_cacimba_cisterna_poco;
  var $agua_fonte_rio;
  var $agua_inexistente;
  var $energia_rede_publica;
  var $energia_gerador;
  var $energia_outros;
  var $energia_inexistente;
  var $esgoto_rede_publica;
  var $esgoto_fossa;
  var $esgoto_inexistente;
  var $lixo_coleta_periodica;
  var $lixo_queima;
  var $lixo_joga_outra_area;
  var $lixo_recicla;
  var $lixo_enterra;
  var $lixo_outros;
  var $dependencia_sala_diretoria;
  var $dependencia_sala_professores;
  var $dependencia_sala_secretaria;
  var $dependencia_laboratorio_informatica;
  var $dependencia_laboratorio_ciencias;
  var $dependencia_sala_aee;
  var $dependencia_quadra_coberta;
  var $dependencia_quadra_descoberta;
  var $dependencia_cozinha;
  var $dependencia_biblioteca;
  var $dependencia_sala_leitura;
  var $dependencia_parque_infantil;
  var $dependencia_bercario;
  var $dependencia_banheiro_fora;
  var $dependencia_banheiro_dentro;
  var $dependencia_banheiro_infantil;
  var $dependencia_banheiro_deficiente;
  var $dependencia_banheiro_chuveiro;
  var $dependencia_vias_deficiente;
  var $dependencia_refeitorio;
  var $dependencia_dispensa;
  var $dependencia_aumoxarifadorgao_regionalo;
  var $dependencia_auditorio;
  var $dependencia_patio_coberto;
  var $dependencia_patio_descoberto;
  var $dependencia_alojamento_aluno;
  var $dependencia_alojamento_professor;
  var $dependencia_area_verde;
  var $dependencia_lavanderia;
  var $dependencia_unidade_climatizada;
  var $dependencia_quantidade_ambiente_climatizado;
  var $dependencia_nenhuma_relacionada;
  var $dependencia_numero_salas_existente;
  var $dependencia_numero_salas_utilizadas;
  var $porte_quadra_descoberta;
  var $porte_quadra_coberta;
  var $tipo_cobertura_patio;
  var $total_funcionario;
  var $atendimento_aee;
  var $atividade_complementar;
  var $fundamental_ciclo;
  var $localizacao_diferenciada;
  var $didatico_nao_utiliza;
  var $didatico_quilombola;
  var $didatico_indigena;
  var $educacao_indigena;
  var $lingua_ministrada;
  var $espaco_brasil_aprendizado;
  var $abre_final_semana;
  var $codigo_lingua_indigena;
  var $proposta_pedagogica;
  var $televisoes;
  var $videocassetes;
  var $dvds;
  var $antenas_parabolicas;
  var $copiadoras;
  var $retroprojetores;
  var $impressoras;
  var $aparelhos_de_som;
  var $projetores_digitais;
  var $faxs;
  var $maquinas_fotograficas;
  var $computadores;
  var $computadores_administrativo;
  var $computadores_alunos;
  var $acesso_internet;
  var $banda_larga;
  var $ato_criacao;
  var $ato_autorizativo;
  var $ref_idpes_secretario_escolar;
  var $utiliza_regra_diferenciada;
  var $orgao_regional;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function clsPmieducarEscola($cod_escola = NULL,
                              $ref_usuario_cad = NULL,
                              $ref_usuario_exc = NULL,
                              $ref_cod_instituicao = NULL,
                              $ref_cod_escola_localizacao = NULL,
                              $ref_cod_escola_rede_ensino = NULL,
                              $ref_idpes = NULL,
                              $sigla = NULL,
                              $data_cadastro = NULL,
                              $data_exclusao = NULL,
                              $ativo = NULL,
                              $bloquear_lancamento_diario_anos_letivos_encerrados = NULL,
                              $utiliza_regra_diferenciada = FALSE) {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'escola';

    $this->_campos_lista = $this->_todos_campos = 'e.cod_escola, e.ref_usuario_cad, e.ref_usuario_exc, e.ref_cod_instituicao, e.ref_cod_escola_localizacao, e.ref_cod_escola_rede_ensino, e.ref_idpes, e.sigla, e.data_cadastro,
          e.data_exclusao, e.ativo, e.bloquear_lancamento_diario_anos_letivos_encerrados, e.situacao_funcionamento, e.dependencia_administrativa, e.latitude, e.longitude, e.regulamentacao, e.acesso, e.cargo_gestor, e.ref_idpes_gestor, e.area_terreno_total,
          e.condicao, e.area_construida, e.area_disponivel, e.num_pavimentos, e.decreto_criacao, e.tipo_piso, e.medidor_energia, e.agua_consumida, e.agua_rede_publica, e.agua_poco_artesiano, e.agua_cacimba_cisterna_poco, e.agua_fonte_rio,
          e.agua_inexistente, e.energia_rede_publica, e.energia_outros, e.energia_gerador, e.energia_inexistente, e.esgoto_rede_publica, e.esgoto_fossa, e.esgoto_inexistente, e.lixo_coleta_periodica, e.lixo_queima, e.lixo_joga_outra_area,
          e.lixo_recicla, e.lixo_enterra, e.lixo_outros, e.dependencia_sala_diretoria, e.dependencia_sala_professores, e.dependencia_sala_secretaria, e.dependencia_laboratorio_informatica, e.dependencia_laboratorio_ciencias, e.dependencia_sala_aee,
          e.dependencia_quadra_coberta, e.dependencia_quadra_descoberta, e.dependencia_cozinha, e.dependencia_biblioteca, e.dependencia_sala_leitura, e.dependencia_parque_infantil, e.dependencia_bercario, e.dependencia_banheiro_fora,
          e.dependencia_banheiro_dentro, e.dependencia_banheiro_infantil, e.dependencia_banheiro_deficiente, e.dependencia_banheiro_chuveiro, e.dependencia_vias_deficiente, e.dependencia_refeitorio, e.dependencia_dispensa, e.dependencia_aumoxarifado, e.dependencia_auditorio,
          e.dependencia_patio_coberto, e.dependencia_patio_descoberto, e.dependencia_alojamento_aluno, e.dependencia_alojamento_professor, e.dependencia_area_verde, e.dependencia_lavanderia, e.dependencia_unidade_climatizada,
          e.dependencia_quantidade_ambiente_climatizado, e.dependencia_nenhuma_relacionada, e.dependencia_numero_salas_existente, dependencia_numero_salas_utilizadas, e.porte_quadra_descoberta, e.porte_quadra_coberta, e.tipo_cobertura_patio,
          e.total_funcionario, e.atendimento_aee, e.fundamental_ciclo, e.localizacao_diferenciada, e.didatico_nao_utiliza, e.didatico_quilombola, e.didatico_indigena, e.educacao_indigena, e.lingua_ministrada, e.espaco_brasil_aprendizado,
          e.abre_final_semana, e.codigo_lingua_indigena, e.atividade_complementar, e.proposta_pedagogica, e.local_funcionamento, e.codigo_inep_escola_compartilhada, e.televisoes, e.videocassetes, e.dvds, e.antenas_parabolicas, e.copiadoras,
          e.retroprojetores, e.impressoras, e.aparelhos_de_som, e.projetores_digitais, e.faxs, e.maquinas_fotograficas, e.computadores, e.computadores_administrativo, e.computadores_alunos, e.acesso_internet, e.banda_larga, e.ato_criacao, e.ato_autorizativo, e.ref_idpes_secretario_escolar, e.utiliza_regra_diferenciada, e.orgao_regional
          ';

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_cod_instituicao)) {
      if (class_exists("clsPmieducarInstituicao")) {
        $tmp_obj = new clsPmieducarInstituicao($ref_cod_instituicao);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'")) {
          $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
      }
    }

    if (is_numeric($ref_cod_escola_localizacao)) {
      if (class_exists("clsPmieducarEscolaLocalizacao")) {
        $tmp_obj = new clsPmieducarEscolaLocalizacao($ref_cod_escola_localizacao);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola_localizacao WHERE cod_escola_localizacao = '{$ref_cod_escola_localizacao}'")) {
          $this->ref_cod_escola_localizacao = $ref_cod_escola_localizacao;
        }
      }
    }

    if (is_numeric($ref_cod_escola_rede_ensino)) {
      if (class_exists("clsPmieducarEscolaRedeEnsino")) {
        $tmp_obj = new clsPmieducarEscolaRedeEnsino($ref_cod_escola_rede_ensino);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe())
          {
            $this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola_rede_ensino WHERE cod_escola_rede_ensino = '{$ref_cod_escola_rede_ensino}'")) {
          $this->ref_cod_escola_rede_ensino = $ref_cod_escola_rede_ensino;
        }
      }
    }

    if (is_numeric($ref_idpes)) {
      if (class_exists("clsCadastroJuridica")) {
        $tmp_obj = new clsCadastroJuridica($ref_idpes);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_idpes = $ref_idpes;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_idpes = $ref_idpes;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM cadastro.juridica WHERE idpes = '{$ref_idpes}'")) {
          $this->ref_idpes = $ref_idpes;
        }
      }
    }

    if (is_numeric($cod_escola)) {
      $this->cod_escola = $cod_escola;
    }

    if (is_string($sigla)) {
      $this->sigla = $sigla;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    $this->bloquear_lancamento_diario_anos_letivos_encerrados = $bloquear_lancamento_diario_anos_letivos_encerrados;
    $this->utiliza_regra_diferenciada = $utiliza_regra_diferenciada;
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_instituicao) &&
      is_numeric($this->ref_cod_escola_localizacao) &&
      is_numeric($this->ref_cod_escola_rede_ensino) && is_string($this->sigla)
    ) {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $campos .= "{$gruda}ref_usuario_exc";
        $valores .= "{$gruda}'{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao)) {
        $campos .= "{$gruda}ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_escola_localizacao)) {
        $campos .= "{$gruda}ref_cod_escola_localizacao";
        $valores .= "{$gruda}'{$this->ref_cod_escola_localizacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_escola_rede_ensino)) {
        $campos .= "{$gruda}ref_cod_escola_rede_ensino";
        $valores .= "{$gruda}'{$this->ref_cod_escola_rede_ensino}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_idpes)) {
        $campos .= "{$gruda}ref_idpes";
        $valores .= "{$gruda}'{$this->ref_idpes}'";
        $gruda = ", ";
      }

      if (is_string($this->sigla)) {
        $campos .= "{$gruda}sigla";
        $valores .= "{$gruda}'{$this->sigla}'";
        $gruda = ", ";
      }

      if (is_numeric($this->bloquear_lancamento_diario_anos_letivos_encerrados)) {
        $campos .= "{$gruda}bloquear_lancamento_diario_anos_letivos_encerrados";
        $valores .= "{$gruda}'{$this->bloquear_lancamento_diario_anos_letivos_encerrados}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}utiliza_regra_diferenciada";

      if ($this->utiliza_regra_diferenciada)
        $valores .= "{$gruda}'t'";
      else
        $valores .= "{$gruda}'f'";

      $gruda = ", ";

      if (is_numeric($this->situacao_funcionamento)) {
        $campos .= "{$gruda}situacao_funcionamento";
        $valores .= "{$gruda}'{$this->situacao_funcionamento}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_administrativa)) {
        $campos .= "{$gruda}dependencia_administrativa";
        $valores .= "{$gruda}'{$this->dependencia_administrativa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->latitude)) {
        $campos .= "{$gruda}latitude";
        $valores .= "{$gruda}'{$this->latitude}'";
        $gruda = ", ";
      }

      if (is_numeric($this->longitude)) {
        $campos .= "{$gruda}longitude";
        $valores .= "{$gruda}'{$this->longitude}'";
        $gruda = ", ";
      }

      if (is_numeric($this->regulamentacao)) {
        $campos .= "{$gruda}regulamentacao";
        $valores .= "{$gruda}'{$this->regulamentacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso)) {
        $campos .= "{$gruda}acesso";
        $valores .= "{$gruda}'{$this->acesso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_idpes_gestor)) {
        $campos .= "{$gruda}ref_idpes_gestor";
        $valores .= "{$gruda}'{$this->ref_idpes_gestor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cargo_gestor)) {
        $campos .= "{$gruda}cargo_gestor";
        $valores .= "{$gruda}'{$this->cargo_gestor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->local_funcionamento)) {
        $campos .= "{$gruda}local_funcionamento";
        $valores .= "{$gruda}'{$this->local_funcionamento}'";
        $gruda = ", ";
      }

      if (is_numeric($this->condicao)) {
        $campos .= "{$gruda}condicao";
        $valores .= "{$gruda}'{$this->condicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->num_pavimentos)) {
        $campos .= "{$gruda}num_pavimentos";
        $valores .= "{$gruda}'{$this->num_pavimentos}'";
        $gruda = ", ";
      }

      if (is_string($this->decreto_criacao)) {
        $campos .= "{$gruda}decreto_criacao";
        $valores .= "{$gruda}'{$this->decreto_criacao}'";
        $gruda = ", ";
      }

      if (is_string($this->area_terreno_total)) {
        $campos .= "{$gruda}area_terreno_total";
        $valores .= "{$gruda}'{$this->area_terreno_total}'";
        $gruda = ", ";
      }

      if (is_string($this->area_disponivel)) {
        $campos .= "{$gruda}area_disponivel";
        $valores .= "{$gruda}'{$this->area_disponivel}'";
        $gruda = ", ";
      }

      if (is_string($this->area_construida)) {
        $campos .= "{$gruda}area_construida";
        $valores .= "{$gruda}'{$this->area_construida}'";
        $gruda = ", ";
      }

      if (is_numeric($this->tipo_piso)) {
        $campos .= "{$gruda}tipo_piso";
        $valores .= "{$gruda}'{$this->tipo_piso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->medidor_energia)) {
        $campos .= "{$gruda}medidor_energia";
        $valores .= "{$gruda}'{$this->medidor_energia}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_consumida)) {
        $campos .= "{$gruda}agua_consumida";
        $valores .= "{$gruda}'{$this->agua_consumida}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_rede_publica)) {
        $campos .= "{$gruda}agua_rede_publica";
        $valores .= "{$gruda}'{$this->agua_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_poco_artesiano)) {
        $campos .= "{$gruda}agua_poco_artesiano";
        $valores .= "{$gruda}'{$this->agua_poco_artesiano}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_cacimba_cisterna_poco)) {
        $campos .= "{$gruda}agua_cacimba_cisterna_poco";
        $valores .= "{$gruda}'{$this->agua_cacimba_cisterna_poco}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_fonte_rio)) {
        $campos .= "{$gruda}agua_fonte_rio";
        $valores .= "{$gruda}'{$this->agua_fonte_rio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_inexistente)) {
        $campos .= "{$gruda}agua_inexistente";
        $valores .= "{$gruda}'{$this->agua_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_rede_publica)) {
        $campos .= "{$gruda}energia_rede_publica";
        $valores .= "{$gruda}'{$this->energia_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_gerador)) {
        $campos .= "{$gruda}energia_gerador";
        $valores .= "{$gruda}'{$this->energia_gerador}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_outros)) {
        $campos .= "{$gruda}energia_outros";
        $valores .= "{$gruda}'{$this->energia_outros}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_inexistente)) {
        $campos .= "{$gruda}energia_inexistente";
        $valores .= "{$gruda}'{$this->energia_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_rede_publica)) {
        $campos .= "{$gruda}esgoto_rede_publica";
        $valores .= "{$gruda}'{$this->esgoto_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_fossa)) {
        $campos .= "{$gruda}esgoto_fossa";
        $valores .= "{$gruda}'{$this->esgoto_fossa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_inexistente)) {
        $campos .= "{$gruda}esgoto_inexistente";
        $valores .= "{$gruda}'{$this->esgoto_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_coleta_periodica)) {
        $campos .= "{$gruda}lixo_coleta_periodica";
        $valores .= "{$gruda}'{$this->lixo_coleta_periodica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_queima)) {
        $campos .= "{$gruda}lixo_queima";
        $valores .= "{$gruda}'{$this->lixo_queima}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_joga_outra_area)) {
        $campos .= "{$gruda}lixo_joga_outra_area";
        $valores .= "{$gruda}'{$this->lixo_joga_outra_area}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_recicla)) {
        $campos .= "{$gruda}lixo_recicla";
        $valores .= "{$gruda}'{$this->lixo_recicla}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_enterra)) {
        $campos .= "{$gruda}lixo_enterra";
        $valores .= "{$gruda}'{$this->lixo_enterra}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_outros)) {
        $campos .= "{$gruda}lixo_outros";
        $valores .= "{$gruda}'{$this->lixo_outros}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_diretoria)) {
        $campos .= "{$gruda}dependencia_sala_diretoria";
        $valores .= "{$gruda}'{$this->dependencia_sala_diretoria}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_professores)) {
        $campos .= "{$gruda}dependencia_sala_professores";
        $valores .= "{$gruda}'{$this->dependencia_sala_professores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_secretaria)) {
        $campos .= "{$gruda}dependencia_sala_secretaria";
        $valores .= "{$gruda}'{$this->dependencia_sala_secretaria}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_laboratorio_informatica)) {
        $campos .= "{$gruda}dependencia_laboratorio_informatica";
        $valores .= "{$gruda}'{$this->dependencia_laboratorio_informatica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_laboratorio_ciencias)) {
        $campos .= "{$gruda}dependencia_laboratorio_ciencias";
        $valores .= "{$gruda}'{$this->dependencia_laboratorio_ciencias}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_aee)) {
        $campos .= "{$gruda}dependencia_sala_aee";
        $valores .= "{$gruda}'{$this->dependencia_sala_aee}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quadra_coberta)) {
        $campos .= "{$gruda}dependencia_quadra_coberta";
        $valores .= "{$gruda}'{$this->dependencia_quadra_coberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quadra_descoberta)) {
        $campos .= "{$gruda}dependencia_quadra_descoberta";
        $valores .= "{$gruda}'{$this->dependencia_quadra_descoberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_cozinha)) {
        $campos .= "{$gruda}dependencia_cozinha";
        $valores .= "{$gruda}'{$this->dependencia_cozinha}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_biblioteca)) {
        $campos .= "{$gruda}dependencia_biblioteca";
        $valores .= "{$gruda}'{$this->dependencia_biblioteca}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_leitura)) {
        $campos .= "{$gruda}dependencia_sala_leitura";
        $valores .= "{$gruda}'{$this->dependencia_sala_leitura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_parque_infantil)) {
        $campos .= "{$gruda}dependencia_parque_infantil";
        $valores .= "{$gruda}'{$this->dependencia_parque_infantil}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_bercario)) {
        $campos .= "{$gruda}dependencia_bercario";
        $valores .= "{$gruda}'{$this->dependencia_bercario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_fora)) {
        $campos .= "{$gruda}dependencia_banheiro_fora";
        $valores .= "{$gruda}'{$this->dependencia_banheiro_fora}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_dentro)) {
        $campos .= "{$gruda}dependencia_banheiro_dentro";
        $valores .= "{$gruda}'{$this->dependencia_banheiro_dentro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_infantil)) {
        $campos .= "{$gruda}dependencia_banheiro_infantil";
        $valores .= "{$gruda}'{$this->dependencia_banheiro_infantil}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_deficiente)) {
        $campos .= "{$gruda}dependencia_banheiro_deficiente";
        $valores .= "{$gruda}'{$this->dependencia_banheiro_deficiente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_chuveiro)) {
        $campos .= "{$gruda}dependencia_banheiro_chuveiro";
        $valores .= "{$gruda}'{$this->dependencia_banheiro_chuveiro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_vias_deficiente)) {
        $campos .= "{$gruda}dependencia_vias_deficiente";
        $valores .= "{$gruda}'{$this->dependencia_vias_deficiente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_refeitorio)) {
        $campos .= "{$gruda}dependencia_refeitorio";
        $valores .= "{$gruda}'{$this->dependencia_refeitorio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_dispensa)) {
        $campos .= "{$gruda}dependencia_dispensa";
        $valores .= "{$gruda}'{$this->dependencia_dispensa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_aumoxarifado)) {
        $campos .= "{$gruda}dependencia_aumoxarifado";
        $valores .= "{$gruda}'{$this->dependencia_aumoxarifado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_auditorio)) {
        $campos .= "{$gruda}dependencia_auditorio";
        $valores .= "{$gruda}'{$this->dependencia_auditorio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_patio_coberto)) {
        $campos .= "{$gruda}dependencia_patio_coberto";
        $valores .= "{$gruda}'{$this->dependencia_patio_coberto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_patio_descoberto)) {
        $campos .= "{$gruda}dependencia_patio_descoberto";
        $valores .= "{$gruda}'{$this->dependencia_patio_descoberto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_alojamento_aluno)) {
        $campos .= "{$gruda}dependencia_alojamento_aluno";
        $valores .= "{$gruda}'{$this->dependencia_alojamento_aluno}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_alojamento_professor)) {
        $campos .= "{$gruda}dependencia_alojamento_professor";
        $valores .= "{$gruda}'{$this->dependencia_alojamento_professor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_area_verde)) {
        $campos .= "{$gruda}dependencia_area_verde";
        $valores .= "{$gruda}'{$this->dependencia_area_verde}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_lavanderia)) {
        $campos .= "{$gruda}dependencia_lavanderia";
        $valores .= "{$gruda}'{$this->dependencia_lavanderia}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_unidade_climatizada)) {
        $campos .= "{$gruda}dependencia_unidade_climatizada";
        $valores .= "{$gruda}'{$this->dependencia_unidade_climatizada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quantidade_ambiente_climatizado)) {
        $campos .= "{$gruda}dependencia_quantidade_ambiente_climatizado";
        $valores .= "{$gruda}'{$this->dependencia_quantidade_ambiente_climatizado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_nenhuma_relacionada)) {
        $campos .= "{$gruda}dependencia_nenhuma_relacionada";
        $valores .= "{$gruda}'{$this->dependencia_nenhuma_relacionada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_numero_salas_existente)) {
        $campos .= "{$gruda}dependencia_numero_salas_existente";
        $valores .= "{$gruda}'{$this->dependencia_numero_salas_existente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_numero_salas_utilizadas)) {
        $campos .= "{$gruda}dependencia_numero_salas_utilizadas";
        $valores .= "{$gruda}'{$this->dependencia_numero_salas_utilizadas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->porte_quadra_descoberta)) {
        $campos .= "{$gruda}porte_quadra_descoberta";
        $valores .= "{$gruda}'{$this->porte_quadra_descoberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->porte_quadra_coberta)) {
        $campos .= "{$gruda}porte_quadra_coberta";
        $valores .= "{$gruda}'{$this->porte_quadra_coberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->tipo_cobertura_patio)) {
        $campos .= "{$gruda}tipo_cobertura_patio";
        $valores .= "{$gruda}'{$this->tipo_cobertura_patio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->total_funcionario)) {
        $campos .= "{$gruda}total_funcionario";
        $valores .= "{$gruda}'{$this->total_funcionario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->atendimento_aee)) {
        $campos .= "{$gruda}atendimento_aee";
        $valores .= "{$gruda}'{$this->atendimento_aee}'";
        $gruda = ", ";
      }

      if (is_numeric($this->atividade_complementar)) {
        $campos .= "{$gruda}atividade_complementar";
        $valores .= "{$gruda}'{$this->atividade_complementar}'";
        $gruda = ", ";
      }

      if (is_numeric($this->fundamental_ciclo)) {
        $campos .= "{$gruda}fundamental_ciclo";
        $valores .= "{$gruda}'{$this->fundamental_ciclo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->localizacao_diferenciada)) {
        $campos .= "{$gruda}localizacao_diferenciada";
        $valores .= "{$gruda}'{$this->localizacao_diferenciada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_nao_utiliza)) {
        $campos .= "{$gruda}didatico_nao_utiliza";
        $valores .= "{$gruda}'{$this->didatico_nao_utiliza}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_quilombola)) {
        $campos .= "{$gruda}didatico_quilombola";
        $valores .= "{$gruda}'{$this->didatico_quilombola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_indigena)) {
        $campos .= "{$gruda}didatico_indigena";
        $valores .= "{$gruda}'{$this->didatico_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->educacao_indigena)) {
        $campos .= "{$gruda}educacao_indigena";
        $valores .= "{$gruda}'{$this->educacao_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lingua_ministrada)) {
        $campos .= "{$gruda}lingua_ministrada";
        $valores .= "{$gruda}'{$this->lingua_ministrada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->espaco_brasil_aprendizado)) {
        $campos .= "{$gruda}espaco_brasil_aprendizado";
        $valores .= "{$gruda}'{$this->espaco_brasil_aprendizado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->abre_final_semana)) {
        $campos .= "{$gruda}abre_final_semana";
        $valores .= "{$gruda}'{$this->abre_final_semana}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_lingua_indigena)) {
        $campos .= "{$gruda}codigo_lingua_indigena";
        $valores .= "{$gruda}'{$this->codigo_lingua_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->proposta_pedagogica)) {
        $campos .= "{$gruda}proposta_pedagogica";
        $valores .= "{$gruda}'{$this->proposta_pedagogica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->televisoes)) {
        $campos .= "{$gruda}televisoes";
        $valores .= "{$gruda}'{$this->televisoes}'";
        $gruda = ", ";
      }

      if (is_numeric($this->videocassetes)) {
        $campos .= "{$gruda}videocassetes";
        $valores .= "{$gruda}'{$this->videocassetes}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dvds)) {
        $campos .= "{$gruda}dvds";
        $valores .= "{$gruda}'{$this->dvds}'";
        $gruda = ", ";
      }

      if (is_numeric($this->antenas_parabolicas)) {
        $campos .= "{$gruda}antenas_parabolicas";
        $valores .= "{$gruda}'{$this->antenas_parabolicas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->copiadoras)) {
        $campos .= "{$gruda}copiadoras";
        $valores .= "{$gruda}'{$this->copiadoras}'";
        $gruda = ", ";
      }

      if (is_numeric($this->retroprojetores)) {
        $campos .= "{$gruda}retroprojetores";
        $valores .= "{$gruda}'{$this->retroprojetores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->impressoras)) {
        $campos .= "{$gruda}impressoras";
        $valores .= "{$gruda}'{$this->impressoras}'";
        $gruda = ", ";
      }

      if (is_numeric($this->aparelhos_de_som)) {
        $campos .= "{$gruda}aparelhos_de_som";
        $valores .= "{$gruda}'{$this->aparelhos_de_som}'";
        $gruda = ", ";
      }

      if (is_numeric($this->projetores_digitais)) {
        $campos .= "{$gruda}projetores_digitais";
        $valores .= "{$gruda}'{$this->projetores_digitais}'";
        $gruda = ", ";
      }

      if (is_numeric($this->faxs)) {
        $campos .= "{$gruda}faxs";
        $valores .= "{$gruda}'{$this->faxs}'";
        $gruda = ", ";
      }

      if (is_numeric($this->maquinas_fotograficas)) {
        $campos .= "{$gruda}maquinas_fotograficas";
        $valores .= "{$gruda}'{$this->maquinas_fotograficas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores)) {
        $campos .= "{$gruda}computadores";
        $valores .= "{$gruda}'{$this->computadores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores_administrativo)) {
        $campos .= "{$gruda}computadores_administrativo";
        $valores .= "{$gruda}'{$this->computadores_administrativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores_alunos)) {
        $campos .= "{$gruda}computadores_alunos";
        $valores .= "{$gruda}'{$this->computadores_alunos}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso_internet)) {
        $campos .= "{$gruda}acesso_internet";
        $valores .= "{$gruda}'{$this->acesso_internet}'";
        $gruda = ", ";
      }

      if (is_numeric($this->banda_larga)) {
        $campos .= "{$gruda}banda_larga";
        $valores .= "{$gruda}'{$this->banda_larga}'";
        $gruda = ", ";
      }

      if (is_numeric($this->orgao_regional)) {
        $campos .= "{$gruda}orgao_regional";
        $valores .= "{$gruda}'{$this->orgao_regional}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_criacao)) {
        $campos .= "{$gruda}ato_criacao";
        $valores .= "{$gruda}'{$this->ato_criacao}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_autorizativo)) {
        $campos .= "{$gruda}ato_autorizativo";
        $valores .= "{$gruda}'{$this->ato_autorizativo}'";
        $gruda = ", ";
      }

      if(is_numeric($this->ref_idpes_secretario_escolar)){
        $campos .= "{$gruda}ref_idpes_secretario_escolar";
        $valores .= "{$gruda}'{$this->ref_idpes_secretario_escolar}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      $recordId = $db->InsertId("{$this->_tabela}_cod_escola_seq");

      return $recordId;
    }
    else {
      echo "<br><br>is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_cod_escola_localizacao) && is_numeric($this->ref_cod_escola_rede_ensino) && is_string($this->sigla )";
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_escola)) {
      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao)) {
        $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_escola_localizacao)) {
        $set .= "{$gruda}ref_cod_escola_localizacao = '{$this->ref_cod_escola_localizacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_escola_rede_ensino)) {
        $set .= "{$gruda}ref_cod_escola_rede_ensino = '{$this->ref_cod_escola_rede_ensino}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_idpes)) {
        $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
        $gruda = ", ";
      }

      if (is_string($this->sigla)) {
        $set .= "{$gruda}sigla = '{$this->sigla}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->bloquear_lancamento_diario_anos_letivos_encerrados)) {
        $set .= "{$gruda}bloquear_lancamento_diario_anos_letivos_encerrados = '{$this->bloquear_lancamento_diario_anos_letivos_encerrados}'";
        $gruda = ", ";
      }

      if ($this->utiliza_regra_diferenciada)
        $set .= "{$gruda}utiliza_regra_diferenciada = 't'";
      else
        $set .= "{$gruda}utiliza_regra_diferenciada = 'f' ";

      $gruda = ", ";

      if (is_numeric($this->situacao_funcionamento)) {
        $set .= "{$gruda}situacao_funcionamento = '{$this->situacao_funcionamento}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_administrativa)) {
        $set .= "{$gruda}dependencia_administrativa = '{$this->dependencia_administrativa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->latitude)) {
        $set .= "{$gruda}latitude = '{$this->latitude}'";
        $gruda = ", ";
      }

      if (is_numeric($this->longitude)) {
        $set .= "{$gruda}longitude = '{$this->longitude}'";
        $gruda = ", ";
      }

      if (is_numeric($this->regulamentacao)) {
        $set .= "{$gruda}regulamentacao = '{$this->regulamentacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso)) {
        $set .= "{$gruda}acesso = '{$this->acesso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_idpes_gestor)) {
        $set .= "{$gruda}ref_idpes_gestor = '{$this->ref_idpes_gestor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cargo_gestor)) {
        $set .= "{$gruda}cargo_gestor = '{$this->cargo_gestor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->num_pavimentos)) {
        $set .= "{$gruda}num_pavimentos = '{$this->num_pavimentos}'";
        $gruda = ", ";
      }

      if (is_numeric($this->local_funcionamento)) {
        $set .= "{$gruda}local_funcionamento = '{$this->local_funcionamento}'";
        $gruda = ", ";
      }

      if (is_numeric($this->condicao)) {
        $set .= "{$gruda}condicao = '{$this->condicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada = '{$this->codigo_inep_escola_compartilhada}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada = NULL ";
        $gruda = ", ";
      }

      if (is_string($this->area_terreno_total)) {
        $set .= "{$gruda}area_terreno_total = '{$this->area_terreno_total}'";
        $gruda = ", ";
      }

      if (is_string($this->area_construida)) {
        $set .= "{$gruda}area_construida = '{$this->area_construida}'";
        $gruda = ", ";
      }

      if (is_string($this->area_disponivel)) {
        $set .= "{$gruda}area_disponivel = '{$this->area_disponivel}'";
        $gruda = ", ";
      }

      if (is_string($this->decreto_criacao)) {
        $set .= "{$gruda}decreto_criacao = '{$this->decreto_criacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->tipo_piso)) {
        $set .= "{$gruda}tipo_piso = '{$this->tipo_piso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->medidor_energia)) {
        $set .= "{$gruda}medidor_energia = '{$this->medidor_energia}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_consumida)) {
        $set .= "{$gruda}agua_consumida = '{$this->agua_consumida}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_rede_publica)) {
        $set .= "{$gruda}agua_rede_publica = '{$this->agua_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_poco_artesiano)) {
        $set .= "{$gruda}agua_poco_artesiano = '{$this->agua_poco_artesiano}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_cacimba_cisterna_poco)) {
        $set .= "{$gruda}agua_cacimba_cisterna_poco = '{$this->agua_cacimba_cisterna_poco}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_fonte_rio)) {
        $set .= "{$gruda}agua_fonte_rio = '{$this->agua_fonte_rio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->agua_inexistente)) {
        $set .= "{$gruda}agua_inexistente = '{$this->agua_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_rede_publica)) {
        $set .= "{$gruda}energia_rede_publica = '{$this->energia_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_gerador)) {
        $set .= "{$gruda}energia_gerador = '{$this->energia_gerador}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_inexistente)) {
        $set .= "{$gruda}energia_inexistente = '{$this->energia_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->energia_outros)) {
        $set .= "{$gruda}energia_outros = '{$this->energia_outros}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_rede_publica)) {
        $set .= "{$gruda}esgoto_rede_publica = '{$this->esgoto_rede_publica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_fossa)) {
        $set .= "{$gruda}esgoto_fossa = '{$this->esgoto_fossa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esgoto_inexistente)) {
        $set .= "{$gruda}esgoto_inexistente = '{$this->esgoto_inexistente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_coleta_periodica)) {
        $set .= "{$gruda}lixo_coleta_periodica = '{$this->lixo_coleta_periodica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_queima)) {
        $set .= "{$gruda}lixo_queima = '{$this->lixo_queima}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_joga_outra_area)) {
        $set .= "{$gruda}lixo_joga_outra_area = '{$this->lixo_joga_outra_area}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_recicla)) {
        $set .= "{$gruda}lixo_recicla = '{$this->lixo_recicla}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_enterra)) {
        $set .= "{$gruda}lixo_enterra = '{$this->lixo_enterra}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lixo_outros)) {
        $set .= "{$gruda}lixo_outros = '{$this->lixo_outros}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_diretoria)) {
        $set .= "{$gruda}dependencia_sala_diretoria = '{$this->dependencia_sala_diretoria}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_professores)) {
        $set .= "{$gruda}dependencia_sala_professores = '{$this->dependencia_sala_professores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_secretaria)) {
        $set .= "{$gruda}dependencia_sala_secretaria = '{$this->dependencia_sala_secretaria}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_laboratorio_informatica)) {
        $set .= "{$gruda}dependencia_laboratorio_informatica = '{$this->dependencia_laboratorio_informatica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_laboratorio_ciencias)) {
        $set .= "{$gruda}dependencia_laboratorio_ciencias = '{$this->dependencia_laboratorio_ciencias}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_aee)) {
        $set .= "{$gruda}dependencia_sala_aee = '{$this->dependencia_sala_aee}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quadra_coberta)) {
        $set .= "{$gruda}dependencia_quadra_coberta = '{$this->dependencia_quadra_coberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quadra_descoberta)) {
        $set .= "{$gruda}dependencia_quadra_descoberta = '{$this->dependencia_quadra_descoberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_cozinha)) {
        $set .= "{$gruda}dependencia_cozinha = '{$this->dependencia_cozinha}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_biblioteca)) {
        $set .= "{$gruda}dependencia_biblioteca = '{$this->dependencia_biblioteca}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_sala_leitura)) {
        $set .= "{$gruda}dependencia_sala_leitura = '{$this->dependencia_sala_leitura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_parque_infantil)) {
        $set .= "{$gruda}dependencia_parque_infantil = '{$this->dependencia_parque_infantil}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_bercario)) {
        $set .= "{$gruda}dependencia_bercario = '{$this->dependencia_bercario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_fora)) {
        $set .= "{$gruda}dependencia_banheiro_fora = '{$this->dependencia_banheiro_fora}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_dentro)) {
        $set .= "{$gruda}dependencia_banheiro_dentro = '{$this->dependencia_banheiro_dentro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_infantil)) {
        $set .= "{$gruda}dependencia_banheiro_infantil = '{$this->dependencia_banheiro_infantil}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_deficiente)) {
        $set .= "{$gruda}dependencia_banheiro_deficiente = '{$this->dependencia_banheiro_deficiente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_banheiro_chuveiro)) {
        $set .= "{$gruda}dependencia_banheiro_chuveiro = '{$this->dependencia_banheiro_chuveiro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_vias_deficiente)) {
        $set .= "{$gruda}dependencia_vias_deficiente = '{$this->dependencia_vias_deficiente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_refeitorio)) {
        $set .= "{$gruda}dependencia_refeitorio = '{$this->dependencia_refeitorio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_dispensa)) {
        $set .= "{$gruda}dependencia_dispensa = '{$this->dependencia_dispensa}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_aumoxarifado)) {
        $set .= "{$gruda}dependencia_aumoxarifado = '{$this->dependencia_aumoxarifado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_auditorio)) {
        $set .= "{$gruda}dependencia_auditorio = '{$this->dependencia_auditorio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_patio_coberto)) {
        $set .= "{$gruda}dependencia_patio_coberto = '{$this->dependencia_patio_coberto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_patio_descoberto)) {
        $set .= "{$gruda}dependencia_patio_descoberto = '{$this->dependencia_patio_descoberto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_alojamento_aluno)) {
        $set .= "{$gruda}dependencia_alojamento_aluno = '{$this->dependencia_patio_coberto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_alojamento_professor)) {
        $set .= "{$gruda}dependencia_alojamento_professor = '{$this->dependencia_alojamento_professor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_area_verde)) {
        $set .= "{$gruda}dependencia_area_verde = '{$this->dependencia_area_verde}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_lavanderia)) {
        $set .= "{$gruda}dependencia_lavanderia = '{$this->dependencia_lavanderia}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_unidade_climatizada)) {
        $set .= "{$gruda}dependencia_unidade_climatizada = '{$this->dependencia_unidade_climatizada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_quantidade_ambiente_climatizado)) {
        $set .= "{$gruda}dependencia_quantidade_ambiente_climatizado = '{$this->dependencia_quantidade_ambiente_climatizado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_nenhuma_relacionada)) {
        $set .= "{$gruda}dependencia_nenhuma_relacionada = '{$this->dependencia_nenhuma_relacionada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_numero_salas_existente)) {
        $set .= "{$gruda}dependencia_numero_salas_existente = '{$this->dependencia_numero_salas_existente}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dependencia_numero_salas_utilizadas)) {
        $set .= "{$gruda}dependencia_numero_salas_utilizadas = '{$this->dependencia_numero_salas_utilizadas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->porte_quadra_descoberta)) {
        $set .= "{$gruda}porte_quadra_descoberta = '{$this->porte_quadra_descoberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->porte_quadra_coberta)) {
        $set .= "{$gruda}porte_quadra_coberta = '{$this->porte_quadra_coberta}'";
        $gruda = ", ";
      }

      if (is_numeric($this->tipo_cobertura_patio)) {
        $set .= "{$gruda}tipo_cobertura_patio = '{$this->tipo_cobertura_patio}'";
        $gruda = ", ";
      }

      if (is_numeric($this->total_funcionario)) {
        $set .= "{$gruda}total_funcionario = '{$this->total_funcionario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->atendimento_aee)) {
        $set .= "{$gruda}atendimento_aee = '{$this->atendimento_aee}'";
        $gruda = ", ";
      }

      if (is_numeric($this->atividade_complementar)) {
        $set .= "{$gruda}atividade_complementar = '{$this->atividade_complementar}'";
        $gruda = ", ";
      }

      if (is_numeric($this->fundamental_ciclo)) {
        $set .= "{$gruda}fundamental_ciclo = '{$this->fundamental_ciclo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->localizacao_diferenciada)) {
        $set .= "{$gruda}localizacao_diferenciada = '{$this->localizacao_diferenciada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_nao_utiliza)) {
        $set .= "{$gruda}didatico_nao_utiliza = '{$this->didatico_nao_utiliza}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_quilombola)) {
        $set .= "{$gruda}didatico_quilombola = '{$this->didatico_quilombola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->didatico_indigena)) {
        $set .= "{$gruda}didatico_indigena = '{$this->didatico_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->educacao_indigena)) {
        $set .= "{$gruda}educacao_indigena = '{$this->educacao_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lingua_ministrada)) {
        $set .= "{$gruda}lingua_ministrada = '{$this->lingua_ministrada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->espaco_brasil_aprendizado)) {
        $set .= "{$gruda}espaco_brasil_aprendizado = '{$this->espaco_brasil_aprendizado}'";
        $gruda = ", ";
      }

      if (is_numeric($this->abre_final_semana)) {
        $set .= "{$gruda}abre_final_semana = '{$this->abre_final_semana}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_lingua_indigena)) {
        $set .= "{$gruda}codigo_lingua_indigena = '{$this->codigo_lingua_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->proposta_pedagogica)) {
        $set .= "{$gruda}proposta_pedagogica = '{$this->proposta_pedagogica}'";
        $gruda = ", ";
      }

      if (is_numeric($this->televisoes)) {
        $set .= "{$gruda}televisoes = '{$this->televisoes}'";
        $gruda = ", ";
      }

      if (is_numeric($this->videocassetes)) {
        $set .= "{$gruda}videocassetes = '{$this->videocassetes}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dvds)) {
        $set .= "{$gruda}dvds = '{$this->dvds}'";
        $gruda = ", ";
      }

      if (is_numeric($this->antenas_parabolicas)) {
        $set .= "{$gruda}antenas_parabolicas = '{$this->antenas_parabolicas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->copiadoras)) {
        $set .= "{$gruda}copiadoras = '{$this->copiadoras}'";
        $gruda = ", ";
      }

      if (is_numeric($this->retroprojetores)) {
        $set .= "{$gruda}retroprojetores = '{$this->retroprojetores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->impressoras)) {
        $set .= "{$gruda}impressoras = '{$this->impressoras}'";
        $gruda = ", ";
      }

      if (is_numeric($this->aparelhos_de_som)) {
        $set .= "{$gruda}aparelhos_de_som = '{$this->aparelhos_de_som}'";
        $gruda = ", ";
      }

      if (is_numeric($this->projetores_digitais)) {
        $set .= "{$gruda}projetores_digitais = '{$this->projetores_digitais}'";
        $gruda = ", ";
      }

      if (is_numeric($this->faxs)) {
        $set .= "{$gruda}faxs = '{$this->faxs}'";
        $gruda = ", ";
      }

      if (is_numeric($this->maquinas_fotograficas)) {
        $set .= "{$gruda}maquinas_fotograficas = '{$this->maquinas_fotograficas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores)) {
        $set .= "{$gruda}computadores = '{$this->computadores}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores_administrativo)) {
        $set .= "{$gruda}computadores_administrativo = '{$this->computadores_administrativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->computadores_alunos)) {
        $set .= "{$gruda}computadores_alunos = '{$this->computadores_alunos}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso_internet)) {
        $set .= "{$gruda}acesso_internet = '{$this->acesso_internet}'";
        $gruda = ", ";
      }

      if (is_numeric($this->banda_larga)) {
        $set .= "{$gruda}banda_larga = '{$this->banda_larga}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_criacao)) {
        $set .= "{$gruda}ato_criacao = '{$this->ato_criacao}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_autorizativo)) {
        $set .= "{$gruda}ato_autorizativo = '{$this->ato_autorizativo}'";
        $gruda = ", ";
      }

      if(is_numeric($this->ref_idpes_secretario_escolar)){
        $set .= "{$gruda}ref_idpes_secretario_escolar = '{$this->ref_idpes_secretario_escolar}'";
        $gruda = ", ";
      }

      if (is_numeric($this->orgao_regional)) {
        $set .= "{$gruda}orgao_regional = '{$this->orgao_regional}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}orgao_regional = NULL ";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_escola = '{$this->cod_escola}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  public function lista($int_cod_escola = NULL, $int_ref_usuario_cad = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_cod_instituicao = NULL,
    $int_ref_cod_escola_localizacao = NULL, $int_ref_cod_escola_rede_ensino = NULL,
    $int_ref_idpes = NULL, $str_sigla = NULL, $date_data_cadastro = NULL,
    $date_data_exclusao = NULL, $int_ativo = NULL, $str_nome = NULL,
    $escola_sem_avaliacao = NULL)
  {

    $sql = "
      SELECT * FROM
      (
        SELECT j.fantasia AS nome, {$this->_campos_lista}, 1 AS tipo_cadastro
          FROM {$this->_tabela} e, cadastro.juridica j
          WHERE e.ref_idpes = j.idpes
        UNION
        SELECT c.nm_escola AS nome, {$this->_campos_lista}, 2 AS tipo_cadastro
          FROM {$this->_tabela} e, pmieducar.escola_complemento c
          WHERE e.cod_escola = c.ref_cod_escola
      ) AS sub";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_cod_escola)) {
      $filtros .= "{$whereAnd} cod_escola = '{$int_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola_localizacao)) {
      $filtros .= "{$whereAnd} ref_cod_escola_localizacao = '{$int_ref_cod_escola_localizacao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola_rede_ensino)) {
      $filtros .= "{$whereAnd} ref_cod_escola_rede_ensino = '{$int_ref_cod_escola_rede_ensino}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_idpes)) {
      $filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_sigla)) {
      $filtros .= "{$whereAnd} sigla LIKE '%{$str_sigla}%'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ativo)) {
      $filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
      $whereAnd = " AND ";
    }else{
      $filtros .= "{$whereAnd} ativo = 1";
      $whereAnd = " AND ";
    }

    if (is_string( $str_nome)) {
      $filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
      $whereAnd = " AND ";
    }

    if (is_bool($escola_sem_avaliacao)) {
      if (dbBool($escola_sem_avaliacao)) {
        $filtros .= "{$whereAnd} NOT EXISTS (SELECT 1 FROM pmieducar.escola_curso ec, pmieducar.curso c WHERE
                        ec.ref_cod_escola = cod_escola
                        AND ec.ref_cod_curso = c.cod_curso
                        AND ec.ativo = 1 AND c.ativo = 1)";
      }
      else {
        $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.escola_curso ec, pmieducar.curso c WHERE
                        ec.ref_cod_escola = cod_escola
                        AND ec.ref_cod_curso = c.cod_curso
                        AND ec.ativo = 1 AND c.ativo = 1)";
      }
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();
    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $db->Consulta("
        SELECT COUNT(0) FROM
        (
          SELECT j.fantasia AS nome, {$this->_campos_lista}, 1 AS tipo_cadastro
          FROM {$this->_tabela} e, cadastro.juridica j
          WHERE e.ref_idpes = j.idpes
        UNION
          SELECT c.nm_escola AS nome, {$this->_campos_lista}, 2 AS tipo_cadastro
          FROM {$this->_tabela} e, pmieducar.escola_complemento c
          WHERE e.cod_escola = c.ref_cod_escola
        ) AS sub
        {$filtros}
    ");

    $db->ProximoRegistro();
    list($this->_total) = $db->Tupla();
    $db->Consulta($sql);

    if($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
        $this->_total = count( $tupla);
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_escola)) {
      $db = new clsBanco();
      $db->Consulta( "
        SELECT * FROM
        (
          SELECT c.nm_escola AS nome, {$this->_todos_campos}, 2 AS tipo_cadastro
          FROM {$this->_tabela} e, pmieducar.escola_complemento c
          WHERE e.cod_escola = c.ref_cod_escola

        UNION

          SELECT j.fantasia AS nome, {$this->_todos_campos}, 1 AS tipo_cadastro
          FROM {$this->_tabela} e, cadastro.juridica j
          WHERE e.ref_idpes = j.idpes


        ) AS sub WHERE cod_escola = '{$this->cod_escola}'"
      );
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_escola)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_escola = '{$this->cod_escola}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_escola)) {
      $this->ativo = 0;
      return $this->edita();
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}
