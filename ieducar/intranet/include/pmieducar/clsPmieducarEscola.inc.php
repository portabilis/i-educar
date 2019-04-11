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

use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';
require_once 'App/Model/NivelTipoUsuario.php';

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
  var $zona_localizacao;
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
  var $email_gestor;
  var $local_funcionamento;
  var $condicao;
  var $codigo_inep_escola_compartilhada;
  var $codigo_inep_escola_compartilhada2;
  var $codigo_inep_escola_compartilhada3;
  var $codigo_inep_escola_compartilhada4;
  var $codigo_inep_escola_compartilhada5;
  var $codigo_inep_escola_compartilhada6;
  var $decreto_criacao;
  var $area_terreno_total;
  var $area_disponivel;
  var $area_construida;
  var $num_pavimentos;
  var $tipo_piso;
  var $medidor_energia;
  var $abastecimento_agua;
  var $abastecimento_energia;
  var $esgoto_sanitario;
  var $destinacao_lixo;
  var $agua_consumida;
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
  var $dependencia_aumoxarifado;
  var $dependencia_auditorio;
  var $dependencia_patio_coberto;
  var $dependencia_patio_descoberto;
  var $dependencia_alojamento_aluno;
  var $dependencia_alojamento_professor;
  var $dependencia_area_verde;
  var $dependencia_lavanderia;
  var $dependencia_nenhuma_relacionada;
  var $dependencia_numero_salas_existente;
  var $dependencia_numero_salas_utilizadas;
  var $total_funcionario;
  var $atendimento_aee;
  var $atividade_complementar;
  var $fundamental_ciclo;
  var $localizacao_diferenciada;
  var $materiais_didaticos_especificos;
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
  var $impressoras_multifuncionais;
  var $acesso_internet;
  var $ato_criacao;
  var $ato_autorizativo;
  var $ref_idpes_secretario_escolar;
  var $utiliza_regra_diferenciada;
  var $categoria_escola_privada;
  var $conveniada_com_poder_publico;
  var $mantenedora_escola_privada;
  var $cnpj_mantenedora_principal;
  var $orgao_vinculado_escola;
  var $unidade_vinculada_outra_instituicao;
  var $inep_escola_sede;
  var $codigo_ies;
  var $codUsuario;
  var $esfera_administrativa;

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
  function __construct($cod_escola = NULL,
                              $ref_usuario_cad = NULL,
                              $ref_usuario_exc = NULL,
                              $ref_cod_instituicao = NULL,
                              $zona_localizacao = NULL,
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

    $this->_campos_lista = $this->_todos_campos = 'e.cod_escola, e.ref_usuario_cad, e.ref_usuario_exc, e.ref_cod_instituicao, e.zona_localizacao, e.ref_cod_escola_rede_ensino, e.ref_idpes, e.sigla, e.data_cadastro,
          e.data_exclusao, e.ativo, e.bloquear_lancamento_diario_anos_letivos_encerrados, e.situacao_funcionamento, e.dependencia_administrativa, e.latitude, e.longitude, e.regulamentacao, e.acesso, e.cargo_gestor, e.ref_idpes_gestor, e.area_terreno_total,
          e.condicao, e.area_construida, e.area_disponivel, e.num_pavimentos, e.decreto_criacao, e.tipo_piso, e.medidor_energia, e.agua_consumida, e.abastecimento_agua, e.abastecimento_energia, e.esgoto_sanitario, e.destinacao_lixo,
          e.dependencia_sala_diretoria, e.dependencia_sala_professores, e.dependencia_sala_secretaria, e.dependencia_laboratorio_informatica, e.dependencia_laboratorio_ciencias, e.dependencia_sala_aee,
          e.dependencia_quadra_coberta, e.dependencia_quadra_descoberta, e.dependencia_cozinha, e.dependencia_biblioteca, e.dependencia_sala_leitura, e.dependencia_parque_infantil, e.dependencia_bercario, e.dependencia_banheiro_fora,
          e.dependencia_banheiro_dentro, e.dependencia_banheiro_infantil, e.dependencia_banheiro_deficiente, e.dependencia_banheiro_chuveiro, e.dependencia_vias_deficiente, e.dependencia_refeitorio, e.dependencia_dispensa, e.dependencia_aumoxarifado, e.dependencia_auditorio,
          e.dependencia_patio_coberto, e.dependencia_patio_descoberto, e.dependencia_alojamento_aluno, e.dependencia_alojamento_professor, e.dependencia_area_verde, e.dependencia_lavanderia,
          e.dependencia_nenhuma_relacionada, e.dependencia_numero_salas_existente, dependencia_numero_salas_utilizadas,
          e.total_funcionario, e.atendimento_aee, e.fundamental_ciclo, e.localizacao_diferenciada, e.materiais_didaticos_especificos, e.educacao_indigena, e.lingua_ministrada, e.espaco_brasil_aprendizado,
          e.abre_final_semana, e.codigo_lingua_indigena, e.atividade_complementar, e.proposta_pedagogica, e.local_funcionamento, e.codigo_inep_escola_compartilhada, e.codigo_inep_escola_compartilhada2, e.codigo_inep_escola_compartilhada3, e.codigo_inep_escola_compartilhada4, 
          e.codigo_inep_escola_compartilhada5, e.codigo_inep_escola_compartilhada6, e.televisoes, e.videocassetes, e.dvds, e.antenas_parabolicas, e.copiadoras, e.retroprojetores, e.impressoras, e.aparelhos_de_som, 
          e.projetores_digitais, e.faxs, e.maquinas_fotograficas, e.computadores, e.computadores_administrativo, e.computadores_alunos, e.impressoras_multifuncionais, e.acesso_internet, e.ato_criacao, 
          e.ato_autorizativo, e.ref_idpes_secretario_escolar, e.utiliza_regra_diferenciada, e.categoria_escola_privada, e.conveniada_com_poder_publico, e.mantenedora_escola_privada, e.cnpj_mantenedora_principal, 
          e.email_gestor, e.orgao_vinculado_escola, e.esfera_administrativa, e.unidade_vinculada_outra_instituicao, e.inep_escola_sede, e.codigo_ies
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

    if (is_numeric($zona_localizacao)) {
        $this->zona_localizacao = $zona_localizacao;
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
      is_numeric($this->zona_localizacao) &&
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

      if (is_numeric($this->zona_localizacao)) {
        $campos .= "{$gruda}zona_localizacao";
        $valores .= "{$gruda}{$this->zona_localizacao}";
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

      if (is_string($this->orgao_vinculado_escola)) {
        $campos .= "{$gruda}orgao_vinculado_escola";
        $valores .= "{$gruda}'{{". $this->orgao_vinculado_escola . "}}'";
        $gruda = ", ";
      }

      if (is_numeric($this->unidade_vinculada_outra_instituicao)) {
        $campos .= "{$gruda}unidade_vinculada_outra_instituicao";
        $valores .= "{$gruda}{$this->unidade_vinculada_outra_instituicao}";
        $gruda = ", ";
      }

      if (is_numeric($this->inep_escola_sede)) {
        $campos .= "{$gruda}inep_escola_sede";
        $valores .= "{$gruda}{$this->inep_escola_sede}";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_ies)) {
        $campos .= "{$gruda}codigo_ies";
        $valores .= "{$gruda}{$this->codigo_ies}";
        $gruda = ", ";
      }

      if ($this->latitude) {
        $campos .= "{$gruda}latitude";
        $valores .= "{$gruda}'{$this->latitude}'";
        $gruda = ", ";
      }

      if ($this->longitude) {
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

      if (is_string($this->email_gestor)) {
        $campos .= "{$gruda}email_gestor";
        $valores .= "{$gruda}'{$this->email_gestor}'";
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

      if (is_numeric($this->codigo_inep_escola_compartilhada2)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada2";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada2}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada3)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada3";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada3}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada4)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada4";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada4}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada5)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada5";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada5}'";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada6)) {
        $campos .= "{$gruda}codigo_inep_escola_compartilhada6";
        $valores .= "{$gruda}'{$this->codigo_inep_escola_compartilhada6}'";
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

      if (is_string($this->abastecimento_agua)) {
        $campos .= "{$gruda}abastecimento_agua";
        $valores .= "{$gruda}'{{$this->abastecimento_agua}}'";
        $gruda = ", ";
      }

      if (is_string($this->abastecimento_energia)) {
        $campos .= "{$gruda}abastecimento_energia";
        $valores .= "{$gruda}'{{$this->abastecimento_energia}}'";
        $gruda = ", ";
      }

      if (is_string($this->esgoto_sanitario)) {
        $campos .= "{$gruda}esgoto_sanitario";
        $valores .= "{$gruda}'{{$this->esgoto_sanitario}}'";
        $gruda = ", ";
      }

      if (is_string($this->destinacao_lixo)) {
        $campos .= "{$gruda}destinacao_lixo";
        $valores .= "{$gruda}'{{$this->destinacao_lixo}}'";
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

      if (is_numeric($this->materiais_didaticos_especificos)) {
        $campos .= "{$gruda}materiais_didaticos_especificos";
        $valores .= "{$gruda}'{$this->materiais_didaticos_especificos}'";
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

      if (is_numeric($this->impressoras_multifuncionais)) {
        $campos .= "{$gruda}impressoras_multifuncionais";
        $valores .= "{$gruda}'{$this->impressoras_multifuncionais}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso_internet)) {
        $campos .= "{$gruda}acesso_internet";
        $valores .= "{$gruda}'{$this->acesso_internet}'";
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

      if (is_numeric($this->categoria_escola_privada)) {
        $campos .= "{$gruda}categoria_escola_privada";
        $valores .= "{$gruda}'{$this->categoria_escola_privada}'";
        $gruda = ", ";
      }

      if (is_numeric($this->conveniada_com_poder_publico)) {
        $campos .= "{$gruda}conveniada_com_poder_publico";
        $valores .= "{$gruda}'{$this->conveniada_com_poder_publico}'";
        $gruda = ", ";
      }

      if (is_string($this->mantenedora_escola_privada)) {
        $campos .= "{$gruda}mantenedora_escola_privada";
        $valores .= "{$gruda}'{". $this->mantenedora_escola_privada . "}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cnpj_mantenedora_principal)) {
        $campos .= "{$gruda}cnpj_mantenedora_principal";
        $valores .= "{$gruda}'{$this->cnpj_mantenedora_principal}'";
        $gruda = ", ";
      }

      if (is_numeric($this->esfera_administrativa)) {
        $campos .= "{$gruda}esfera_administrativa";
        $valores .= "{$gruda}'{$this->esfera_administrativa}'";
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
      echo "<br><br>is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_instituicao) && is_numeric($this->zona_localizacao) && is_numeric($this->ref_cod_escola_rede_ensino) && is_string($this->sigla )";
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
      $set = '';
      $gruda = '';

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

      if (is_numeric($this->zona_localizacao)) {
        $set .= "{$gruda}zona_localizacao = '{$this->zona_localizacao}'";
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

      if (is_string($this->orgao_vinculado_escola)) {
        $set .= "{$gruda}orgao_vinculado_escola = '{{$this->orgao_vinculado_escola}}'";
        $gruda = ", ";
      } else {
        $set .= "{$gruda}orgao_vinculado_escola = null";
        $gruda = ", ";
      }

      if (is_numeric($this->unidade_vinculada_outra_instituicao)) {
        $set .= "{$gruda}unidade_vinculada_outra_instituicao = {$this->unidade_vinculada_outra_instituicao}";
        $gruda = ", ";
      } else {
        $set .= "{$gruda}unidade_vinculada_outra_instituicao = null";
        $gruda = ", ";
      }

      if (is_numeric($this->inep_escola_sede)) {
        $set .= "{$gruda}inep_escola_sede = {$this->inep_escola_sede}";
        $gruda = ", ";
      } else {
        $set .= "{$gruda}inep_escola_sede = null";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_ies)) {
        $set .= "{$gruda}codigo_ies = {$this->codigo_ies}";
        $gruda = ", ";
      } else {
        $set .= "{$gruda}codigo_ies = null";
        $gruda = ", ";
      }

      if (is_numeric($this->latitude)) {
        $set .= "{$gruda}latitude = '{$this->latitude}'";
        $gruda = ", ";
      }elseif (is_null($this->latitude) || $this->latitude == '') {
        $set .= "{$gruda}latitude = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->longitude)) {
        $set .= "{$gruda}longitude = '{$this->longitude}'";
        $gruda = ", ";
      }elseif (is_null($this->longitude) || $this->longitude == '') {
        $set .= "{$gruda}longitude = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->regulamentacao)) {
        $set .= "{$gruda}regulamentacao = '{$this->regulamentacao}'";
        $gruda = ", ";
      } else {
        $set .= "{$gruda}regulamentacao = null";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso)) {
        $set .= "{$gruda}acesso = '{$this->acesso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_idpes_gestor)) {
        $set .= "{$gruda}ref_idpes_gestor = '{$this->ref_idpes_gestor}'";
        $gruda = ", ";
      }elseif (is_null($this->ref_idpes_gestor) || $this->ref_idpes_gestor == '') {
        $set .= "{$gruda}ref_idpes_gestor = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->cargo_gestor)) {
        $set .= "{$gruda}cargo_gestor = '{$this->cargo_gestor}'";
        $gruda = ", ";
      }

      if (is_string($this->email_gestor)) {
        $set .= "{$gruda}email_gestor = '{$this->email_gestor}'";
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

      if (is_numeric($this->codigo_inep_escola_compartilhada2)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada2 = '{$this->codigo_inep_escola_compartilhada2}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada2 = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada3)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada3 = '{$this->codigo_inep_escola_compartilhada3}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada3 = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada4)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada4 = '{$this->codigo_inep_escola_compartilhada4}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada4 = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada5)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada5 = '{$this->codigo_inep_escola_compartilhada5}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada5 = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->codigo_inep_escola_compartilhada6)) {
        $set .= "{$gruda}codigo_inep_escola_compartilhada6 = '{$this->codigo_inep_escola_compartilhada6}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}codigo_inep_escola_compartilhada6 = NULL ";
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

      if (is_string($this->abastecimento_agua)) {
        $set .= "{$gruda}abastecimento_agua = '{{$this->abastecimento_agua}}'";
        $gruda = ", ";
      }

      if (is_string($this->abastecimento_energia)) {
        $set .= "{$gruda}abastecimento_energia = '{{$this->abastecimento_energia}}'";
        $gruda = ", ";
      }

      if (is_string($this->esgoto_sanitario)) {
        $set .= "{$gruda}esgoto_sanitario = '{{$this->esgoto_sanitario}}'";
        $gruda = ", ";
      }

      if (is_string($this->destinacao_lixo)) {
        $set .= "{$gruda}destinacao_lixo = '{{$this->destinacao_lixo}}'";
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
        $set .= "{$gruda}dependencia_alojamento_aluno = '{$this->dependencia_alojamento_aluno}'";
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
      }else {
        $set .= "{$gruda}localizacao_diferenciada = null";
        $gruda = ", ";
      }

      if (is_numeric($this->materiais_didaticos_especificos)) {
        $set .= "{$gruda}materiais_didaticos_especificos = '{$this->materiais_didaticos_especificos}'";
        $gruda = ", ";
      }

      if (is_numeric($this->educacao_indigena)) {
        $set .= "{$gruda}educacao_indigena = '{$this->educacao_indigena}'";
        $gruda = ", ";
      }

      if (is_numeric($this->lingua_ministrada)) {
        $set .= "{$gruda}lingua_ministrada = '{$this->lingua_ministrada}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}lingua_ministrada = NULL";
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
      }else{
        $set .= "{$gruda}codigo_lingua_indigena = NULL";
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

      if (is_numeric($this->impressoras_multifuncionais)) {
        $set .= "{$gruda}impressoras_multifuncionais = '{$this->impressoras_multifuncionais}'";
        $gruda = ", ";
      }

      if (is_numeric($this->acesso_internet)) {
        $set .= "{$gruda}acesso_internet = '{$this->acesso_internet}'";
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
      }elseif(is_null($this->ref_idpes_secretario_escolar) || $this->ref_idpes_secretario_escolar == ''){
        $set .= "{$gruda}ref_idpes_secretario_escolar = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->categoria_escola_privada)) {
        $set .= "{$gruda}categoria_escola_privada = '{$this->categoria_escola_privada}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}categoria_escola_privada = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->conveniada_com_poder_publico)) {
        $set .= "{$gruda}conveniada_com_poder_publico = '{$this->conveniada_com_poder_publico}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}conveniada_com_poder_publico = NULL ";
        $gruda = ", ";
      }

      if (is_string($this->mantenedora_escola_privada) && $this->mantenedora_escola_privada != "{}") {
        $set .= "{$gruda}mantenedora_escola_privada = '{". $this->mantenedora_escola_privada . "}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}mantenedora_escola_privada = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->cnpj_mantenedora_principal)) {
        $set .= "{$gruda}cnpj_mantenedora_principal = '{$this->cnpj_mantenedora_principal}'";
        $gruda = ", ";
      }else{
        $set .= "{$gruda}cnpj_mantenedora_principal = NULL ";
        $gruda = ", ";
      }

      if (is_numeric($this->esfera_administrativa)) {
        $gruda = ", ";
        $set .= "{$gruda}esfera_administrativa = '{$this->esfera_administrativa}'";
      } elseif (is_null($this->esfera_administrativa) || $this->esfera_administrativa == '') {
        $gruda = ", ";
        $set .= "{$gruda}esfera_administrativa = NULL ";
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
    $zona_localizacao = NULL, $int_ref_cod_escola_rede_ensino = NULL,
    $int_ref_idpes = NULL, $str_sigla = NULL, $date_data_cadastro = NULL,
    $date_data_exclusao = NULL, $int_ativo = NULL, $str_nome = NULL,
    $escola_sem_avaliacao = NULL, $cod_usuario = NULL)
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
    }elseif ($this->codUsuario) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
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

    if (is_numeric($zona_localizacao)) {
      $filtros .= "{$whereAnd} zona_localizacao = {$zona_localizacao}";
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

    if (isset($date_data_cadastro_ini) && is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    //todo Remover variável inexistente
    if (isset($date_data_cadastro_fim) && is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    //todo Remover variável inexistente
    if (isset($date_data_exclusao_ini) && is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    //todo Remover variável inexistente
    if (isset($date_data_exclusao_fim) && is_string($date_data_exclusao_fim)) {
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
      $filtros .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
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

    if (is_numeric($cod_usuario)) {
      $permissao = new clsPermissoes();
      $nivel = $permissao->nivel_acesso(Session::get('id_pessoa'));

      if ($nivel == App_Model_NivelTipoUsuario::ESCOLA ||
          $nivel == App_Model_NivelTipoUsuario::BIBLIOTECA) {
        $filtros .= "{$whereAnd} EXISTS (SELECT *
                                           FROM pmieducar.escola_usuario
                                          WHERE escola_usuario.ref_cod_escola = cod_escola
                                            AND ref_cod_usuario = '{$cod_usuario}')";
        $whereAnd = " AND ";
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

  function lista_escola(){
    $db = new clsBanco();
    $resultado = array();
    $db->Consulta("SELECT COALESCE((SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
                                      FROM cadastro.pessoa ps, cadastro.juridica
                                     WHERE escola.ref_idpes = juridica.idpes
                                       AND juridica.idpes = ps.idpes
                                       AND ps.idpes = escola.ref_idpes),
                                   (SELECT nm_escola
                                      FROM pmieducar.escola_complemento
                                    WHERE ref_cod_escola = escola.cod_escola)) as nome, escola.cod_escola
                     FROM pmieducar.escola
                    WHERE ativo = 1
                    ORDER BY nome
                 ");

      while ($db->ProximoRegistro()){
        $tupla = $db->Tupla();
        $resultado[] = $tupla;
      }
      if (count($resultado)){
       return $resultado;
      }
  }

  function possuiTurmasDoEnsinoFundamentalEmCiclos() {
    $anoAtual = date('Y');
    $sql = "SELECT EXISTS (SELECT 1
                             FROM pmieducar.turma
                            WHERE ref_ref_cod_escola = {$this->cod_escola}
                              AND etapa_educacenso IN (4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,41,56)
                              AND ano = {$anoAtual})";
    $db = new clsBanco();
    return $db->CampoUnico($sql);
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
