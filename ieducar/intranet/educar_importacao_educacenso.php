<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

use iEducar\Modules\Educacenso\RunMigrations;

ini_set("max_execution_time", 0);
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
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesProfessorTurma.inc.php';

/**
 * @author    Caroline Salib Canto <caroline@portabilis.com.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Importação educacenso');
    $this->processoAp = 9998849;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $arquivo;

  function Inicializar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998849, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $this->breadcrumb('Importação educacenso', [
        url('intranet/educar_educacenso_index.php') => 'Educacenso',
    ]);

    $this->titulo = "Nova importação";

    return 'Editar';
  }

  function Gerar()
  {
      $resources = [
          null => 'Selecione',
          '2018' => '2018',
          '2019' => '2019',
      ];
      $options = [
          'label' => 'Ano',
          'resources' => $resources,
          'value' => $this->ano,
      ];
      $this->inputsHelper()->select('ano', $options);

    $this->campoArquivo('arquivo', 'Arquivo', $this->arquivo,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Somente arquivos com formato txt serão aceitos</span>');

    $this->nome_url_sucesso = "Importar";

      Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Importacao.js');
  }

  function Novo()
  {
    $this->Editar();
  }

  function Editar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998849, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
    if(!$this->ref_cod_instituicao){
      $this->ref_cod_instituicao = 1;
    }

    if (!$this->arquivo['tmp_name']){
      $this->mensagem = "Selecione um arquivo para a exportação.";
      return false;
    }

    $arquivo = file_get_contents($this->arquivo['tmp_name']);
    //$arquivo = iconv('Windows-1252', 'UTF-8', $arquivo);

    $registros = explode("\n", $arquivo);

    foreach ($registros as $registro) {
      @header_remove('Set-Cookie');
      $dadosRegistro = explode("|", $registro);
      $numeroRegistro = $dadosRegistro[0];

      //$time_start = microtime(true);

      switch ($numeroRegistro) {
        case '00':
          $this->importaRegistro00($dadosRegistro);
          break;
        case '10':
          $this->importaRegistro10($dadosRegistro);
          break;
        case '20':
          $this->importaRegistro20($dadosRegistro);
          break;
        case '30':
          $this->importaRegistro30($dadosRegistro);
          break;
        case '40':
          $this->importaRegistro40($dadosRegistro);
          break;
        case '50':
          $this->importaRegistro50($dadosRegistro);
          break;
        case '51':
          $this->importaRegistro51($dadosRegistro);
          break;
        case '60':
          $this->importaRegistro60($dadosRegistro);
          break;
        case '70':
          $this->importaRegistro70($dadosRegistro);
          break;
        case '80':
          $this->importaRegistro80($dadosRegistro);
          break;
      }
      //echo 'Tempo para importar registro '.$numeroRegistro.': ' . (microtime(true) - $time_start) . '<br/>';
    }

    $this->runMigrations();

    @header_remove('Set-Cookie');
    $this->mensagem = "Arquivo importado!";
    return true;
  }

  private function runMigrations()
  {
    $runMigrationsService = new RunMigrations();
    $runMigrationsService->run();
  }

  function importaRegistro00($dadosRegistro) {

    $inep = $dadosRegistro[1];
    $cpfGestor = idFederal2int($dadosRegistro[2]);
    $nomeGestor = utf8_encode($dadosRegistro[3]);
    $cargoGestor = $dadosRegistro[4];
    $emailGestor = $dadosRegistro[5];
    $situacao = $dadosRegistro[6];
    $dataInicioAnoLetivo = Portabilis_Date_Utils::brToPgSQL($dadosRegistro[7]);
    $dataFimAnoLetivo = Portabilis_Date_Utils::brToPgSQL($dadosRegistro[8]);
    $nomeEscola = utf8_encode($dadosRegistro[9]);
    $latitude = $dadosRegistro[10];
    $longitude = $dadosRegistro[11];
    $cep = $dadosRegistro[12]; // ep.cep
    $logradouro = $dadosRegistro[13]; // l.idtlog l.nome
    $enderecoNumero = $dadosRegistro[14]; // ep.numero
    $complemento = utf8_encode($dadosRegistro[15]); // ep.complemento
    $nomeBairro = $dadosRegistro[16]; // b.nome
    $ufIbge = $dadosRegistro[17]; // uf.cod_ibge
    $municipioIbge = $dadosRegistro[18]; // m.cod_ibge
    $distritoIbge = $dadosRegistro[19]; // d.cod_ibge
    $ddd = $dadosRegistro[20]; // fone_pessoa.ddd
    $telefone = $dadosRegistro[21]; // fone_pessoa.fone
    $telefonePublico = $dadosRegistro[22];
    $telefoneContato = $dadosRegistro[23]; // fone_pessoa.fone e tipo = 3
    $telefoneFAX = $dadosRegistro[24]; // fone_pessoa.fone e tipo = 4
    $email = $dadosRegistro[25];
    $codigoOrgaoRegional = ltrim($dadosRegistro[26], '0');
    $dependenciaAdministrativa = $dadosRegistro[27];
    $localizacao = $dadosRegistro[28]; // b.zona_localizacao
    $categoriaEscolaPrivada = $dadosRegistro[29];
    $convenioPoderPublico = $dadosRegistro[30];
    $mantenedorEmpresa = $dadosRegistro[31];
    $mantenedorSindicato = $dadosRegistro[32];
    $mantenedorOrganizacao = $dadosRegistro[33];
    $mantenedorInstituicao = $dadosRegistro[34];
    $mantenedorSistema = $dadosRegistro[35];
    $cnpjMantenedoraPrincipal = $dadosRegistro[36];
    $cnpj = idFederal2int($dadosRegistro[37]);
    $regulamentacao = $dadosRegistro[38];
    $unidadeVinculada = $dadosRegistro[39];

    $idpesGestor = $this->existePessoa($cpfGestor);
    $pessoa        = new clsPessoa_();
    $pessoa->idpes = $idpesGestor;
    $pessoa->nome  = $nomeGestor;
    $pessoa->email = addslashes($emailGestor);
    if($idpesGestor){
      $pessoa->idpes_rev = $this->pessoa_logada;
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();

    }else{
      $pessoa->tipo      = 'F';
      $pessoa->idpes_cad = $this->pessoa_logada;
      $idpesGestor      = $pessoa->cadastra();

      $fisica = new clsFisica();
      $fisica->idpes                = $idpesGestor;
      $fisica->cpf = $cpfGestor;
      $fisica->cadastra();
    }

    $codEscola = $this->existeEscola($inep);

    if(!$codEscola){
      $pessoa = new clsPessoa_(
        null, $nomeEscola, null, null,
        'J', null, null, $email
      );
      $idpesEscola = $pessoa->cadastra();
      if(!$cnpj){
        $cnpj = sprintf("%02d.%03d.%03d/%04d-%02d", rand(1, 99), rand(1, 999), rand(1, 999), rand(1, 9999), rand(1, 99));
        $cnpj = idFederal2int($cnpj);
      }

      $juridica = new clsJuridica(
        $idpesEscola,$cnpj, $nomeEscola,
        null, null, $this->pessoa_logada, null
      );
      $juridica->cadastra();

      $codEscolaRedeEnsino = $this->getOrCreateRedeDeEnsino();

      // FIXME #parameters
      $codEscola = $this->createEscola($localizacao, $codEscolaRedeEnsino, $idpesEscola, $nomeEscola, $idpesGestor, $cargoGestor, null);

      $this->createEscolaAnoLetivo($codEscola, $dataInicioAnoLetivo, $dataFimAnoLetivo);

      if(!$codEscola){
        return false;
      }

      $this->createEscolaEducacenso($codEscola, $inep);

      if($ddd && $telefone){
        $objTelefone = new clsPessoaTelefone( $idpesEscola, 1, str_replace( "-", "", $telefone ), $ddd );
        $objTelefone->cadastra();
      }

      if($ddd && $telefonePublico){
        $objTelefone = new clsPessoaTelefone( $idpesEscola, 2, str_replace( "-", "", $telefonePublico ), $ddd );
        $objTelefone->cadastra();
      }

      if($ddd && $telefoneContato){
        $objTelefone = new clsPessoaTelefone( $idpesEscola, 3, str_replace( "-", "", $telefoneContato ), $ddd );
        $objTelefone->cadastra();
      }

      if($ddd && $telefoneFAX){
        $objTelefone = new clsPessoaTelefone( $idpesEscola, 4, str_replace( "-", "", $telefoneFAX ), $ddd );
        $objTelefone->cadastra();
      }
    }

    $this->atualizaCamposEscolaRegistro00(
        $codEscola, $cargoGestor, $situacao,
        $latitude, $longitude, $codigoOrgaoRegional,
        $dependenciaAdministrativa, $regulamentacao,
        $categoriaEscolaPrivada, $convenioPoderPublico,
        $mantenedorEmpresa, $mantenedorSindicato,
        $mantenedorOrganizacao, $mantenedorInstituicao,
        $mantenedorSistema, $cnpjMantenedoraPrincipal
    );

    $escola = new clsPmieducarEscola($codEscola);
    $detEscola = $escola->detalhe();

    $idpesEscola = $detEscola['ref_idpes'];

    $this->cadastraEndereco($idpesEscola, $cep, $logradouro, $enderecoNumero, $complemento, $nomeBairro, $ufIbge, $municipioIbge, $distritoIbge, $localizacao);
  }

  function importaRegistro10($dadosRegistro){

    $inep = $dadosRegistro[2-1];

    $localFuncionamentoArray = array(
      '3' => $dadosRegistro[3-1],
      '4' => $dadosRegistro[4-1],
      '5' => $dadosRegistro[5-1],
      '6' => $dadosRegistro[6-1],
      '7' => $dadosRegistro[7-1],
      '8' => $dadosRegistro[8-1],
      '9' => $dadosRegistro[9-1],
      '10' => $dadosRegistro[10-1],
      '11' => $dadosRegistro[11-1],
    );
    $localFuncionamento = '0';
    foreach ($localFuncionamentoArray as $key => $value) {
      if($value == 1){
        $localFuncionamento = $key;
        break;
      }
    }

    $camposEscola = array(
      'possui_dependencias' => 0,
      'local_funcionamento' => (string) $localFuncionamento,
      'predio_compartilhado_outra_escola' => $dadosRegistro[13-1],
      'condicao' => $dadosRegistro[12-1],
      'codigo_inep_escola_compartilhada' => $dadosRegistro[14-1],
      'agua_consumida' => $dadosRegistro[20-1],
      'dependencia_sala_diretoria' => $dadosRegistro[39-1],
      'dependencia_sala_professores' => $dadosRegistro[40-1],
      'dependencia_sala_secretaria' => $dadosRegistro[41-1],
      'dependencia_laboratorio_informatica' => $dadosRegistro[42-1],
      'dependencia_laboratorio_ciencias' => $dadosRegistro[43-1],
      'dependencia_sala_aee' => $dadosRegistro[44-1],
      'dependencia_quadra_coberta' => $dadosRegistro[45-1],
      'dependencia_quadra_descoberta' => $dadosRegistro[46-1],
      'dependencia_cozinha' => $dadosRegistro[47-1],
      'dependencia_biblioteca' => $dadosRegistro[48-1],
      'dependencia_sala_leitura' => $dadosRegistro[49-1],
      'dependencia_parque_infantil' => $dadosRegistro[50-1],
      'dependencia_bercario' => $dadosRegistro[51-1],
      'dependencia_banheiro_fora' => $dadosRegistro[52-1],
      'dependencia_banheiro_dentro' => $dadosRegistro[53-1],
      'dependencia_banheiro_infantil' => $dadosRegistro[54-1],
      'dependencia_banheiro_deficiente' => $dadosRegistro[55-1],
      'dependencia_vias_deficiente' => $dadosRegistro[56-1],
      'dependencia_banheiro_chuveiro' => $dadosRegistro[57-1],
      'dependencia_refeitorio' => $dadosRegistro[58-1],
      'dependencia_dispensa' => $dadosRegistro[59-1],
      'dependencia_aumoxarifado' => $dadosRegistro[60-1],
      'dependencia_auditorio' => $dadosRegistro[61-1],
      'dependencia_patio_coberto' => $dadosRegistro[62-1],
      'dependencia_patio_descoberto' => $dadosRegistro[63-1],
      'dependencia_alojamento_aluno' => $dadosRegistro[64-1],
      'dependencia_alojamento_professor' => $dadosRegistro[65-1],
      'dependencia_area_verde' => $dadosRegistro[66-1],
      'dependencia_lavanderia' => $dadosRegistro[67-1],
      'dependencia_nenhuma_relacionada' => $dadosRegistro[68-1],
      'dependencia_numero_salas_existente' => $dadosRegistro[69-1],
      'dependencia_numero_salas_utilizadas' => $dadosRegistro[70-1],
      'televisoes' => $dadosRegistro[71-1],
      'videocassetes' => $dadosRegistro[72-1],
      'dvds' => $dadosRegistro[73-1],
      'antenas_parabolicas' => $dadosRegistro[74-1],
      'copiadoras' => $dadosRegistro[75-1],
      'retroprojetores' => $dadosRegistro[76-1],
      'impressoras' => $dadosRegistro[77-1],
      'aparelhos_de_som' => $dadosRegistro[78-1],
      'projetores_digitais' => $dadosRegistro[79-1],
      'faxs' => $dadosRegistro[80-1],
      'maquinas_fotograficas' => $dadosRegistro[81-1],
      'computadores' => $dadosRegistro[82-1],
      'impressoras_multifuncionais' => $dadosRegistro[83-1],
      'computadores_administrativo' => $dadosRegistro[84-1],
      'computadores_alunos' => $dadosRegistro[85-1],
      'acesso_internet' => $dadosRegistro[86-1],
      'total_funcionario' => $dadosRegistro[88-1],
      'atendimento_aee' => $dadosRegistro[90-1],
      'atividade_complementar' => $dadosRegistro[91-1],
      'fundamental_ciclo' => $dadosRegistro[96-1],
      'localizacao_diferenciada' => $dadosRegistro[97-1],
      'didatico_nao_utiliza' => $dadosRegistro[98-1],
      'didatico_quilombola' => $dadosRegistro[99-1],
      'didatico_indigena' => $dadosRegistro[100-1],
      'educacao_indigena' => $dadosRegistro[101-1],
      'lingua_ministrada' => $dadosRegistro[104-1],
      'espaco_brasil_aprendizado' => $dadosRegistro[105-1],
      'abre_final_semana' => $dadosRegistro[106-1],
      'proposta_pedagogica' => $dadosRegistro[107-1],
      'codigo_lingua_indigena' => $dadosRegistro[104-1],
      'espaco_brasil_aprendizado' => $dadosRegistro[105-1],
      'abre_final_semana' => $dadosRegistro[106-1],
      'proposta_pedagogica' => $dadosRegistro[107-1],
    );

    $camposEscola['abastecimento_agua'] = array();
    for ($i=1; $i <= 5; $i++) {
      if($dadosRegistro[20+$i-1]){
        $camposEscola['abastecimento_agua'][] = $i;
      }
    }
    $camposEscola['abastecimento_agua'] = implode(',', $camposEscola['abastecimento_agua']);

    $camposEscola['abastecimento_energia'] = array();
    for ($i=1; $i <= 4; $i++) {
      if($dadosRegistro[25+$i-1]){
        $camposEscola['abastecimento_energia'][] = $i;
      }
    }
    $camposEscola['abastecimento_energia'] = implode(',', $camposEscola['abastecimento_energia']);

    $camposEscola['esgoto_sanitario'] = array();
    for ($i=1; $i <= 3; $i++) {
      if($dadosRegistro[29+$i-1]){
        $camposEscola['esgoto_sanitario'][] = $i;
      }
    }
    $camposEscola['esgoto_sanitario'] = implode(',', $camposEscola['esgoto_sanitario']);

    $camposEscola['destinacao_lixo'] = array();
    for ($i=1; $i <= 6; $i++) {
      if($dadosRegistro[32+$i-1]){
        $camposEscola['destinacao_lixo'][] = $i;
      }
    }
    $camposEscola['destinacao_lixo'] = implode(',', $camposEscola['destinacao_lixo']);

    $codEscola = $this->existeEscola($inep);
    if($codEscola){
      $objEscola = new clsPmieducarEscola($codEscola);
      $fields = $objEscola->detalhe();

      foreach ($fields as $key => $value) {
        if(property_exists($objEscola, $key)){
          if ($this->isPostgresArray($value)) {
            $value = $this->cleanPostgresArray($value);
          }
          $objEscola->{$key} = $value;
        }
      }
      foreach ($camposEscola as $key => $value) {
        $objEscola->{$key} = $value;
      }
      $objEscola->edita();
    }
  }

  private function isPostgresArray($value)
  {
    if (substr($value, 0, 1) == '{' && substr($value, -1) == '}') {
      return true;
    }

    return false;
  }

  private function cleanPostgresArray($value)
  {
    return str_replace(['{','}'], '', $value);
  }

  function importaRegistro20($dadosRegistro){

    $inepEscola = $dadosRegistro[2-1];
    $inepTurma = $dadosRegistro[3-1];

    $nomeTurma = utf8_encode($dadosRegistro[5-1]);

    $horaInicial = sprintf("%02d:%02d:00", intval($dadosRegistro[7-1]), intval($dadosRegistro[8-1]));
    $horaFinal = sprintf("%02d:%02d:00", intval($dadosRegistro[9-1]), intval($dadosRegistro[10-1]));

    $diasSemana = array();

    for ($i=1; $i <= 7; $i++) {
      if($dadosRegistro[10+$i-1] == 1){
        $diasSemana[] = $i;
      }
    }
    $diasSemana = '{' . implode(',', $diasSemana) . '}';

    $disciplinas = array();
    $disciplinas[1] = $dadosRegistro[40-1];
    $disciplinas[2] = $dadosRegistro[41-1];
    $disciplinas[3] = $dadosRegistro[42-1];
    $disciplinas[4] = $dadosRegistro[43-1];
    $disciplinas[5] = $dadosRegistro[44-1];
    $disciplinas[6] = $dadosRegistro[45-1];
    $disciplinas[7] = $dadosRegistro[46-1];
    $disciplinas[8] = $dadosRegistro[47-1];
    $disciplinas[30] = $dadosRegistro[48-1];
    $disciplinas[9] = $dadosRegistro[49-1];
    $disciplinas[10] = $dadosRegistro[50-1];
    $disciplinas[11] = $dadosRegistro[51-1];
    $disciplinas[12] = $dadosRegistro[52-1];
    $disciplinas[13] = $dadosRegistro[53-1];
    $disciplinas[14] = $dadosRegistro[54-1];
    $disciplinas[28] = $dadosRegistro[55-1];
    $disciplinas[29] = $dadosRegistro[56-1];
    $disciplinas[16] = $dadosRegistro[57-1];
    $disciplinas[17] = $dadosRegistro[58-1];
    $disciplinas[20] = $dadosRegistro[59-1];
    $disciplinas[21] = $dadosRegistro[60-1];
    $disciplinas[23] = $dadosRegistro[61-1];
    $disciplinas[25] = $dadosRegistro[62-1];
    $disciplinas[26] = $dadosRegistro[63-1];
    $disciplinas[27] = $dadosRegistro[64-1];
    $disciplinas[99] = $dadosRegistro[65-1];

    $camposTurma = array(
      'tipo_atendimento' => $dadosRegistro[18-1],
      'turma_mais_educacao' => $dadosRegistro[19-1],
      'etapa_educacenso' => $dadosRegistro[38-1],
      'cod_curso_profissional' => $dadosRegistro[39-1],
      'tipo_mediacao_didatico_pedagogico' => $dadosRegistro[6-1]
    );

    $camposTurma['dias_semana'] = array();
    for ($i=1; $i <= 7; $i++) {
      if($dadosRegistro[10+$i-1]){
        $camposTurma['dias_semana'][] = $i;
      }
    }
    $camposTurma['dias_semana'] = '{'.implode(',', $camposTurma['dias_semana']).'}';

    $camposTurma['atividades_complementares'] = array();
    for ($i=1; $i <= 6; $i++) {
      if($dadosRegistro[19+$i-1]){
        $camposTurma['atividades_complementares'][] = $dadosRegistro[19+$i-1];
      }
    }
    $camposTurma['atividades_complementares'] = '{'.implode(',', $camposTurma['atividades_complementares']).'}';

    $camposTurma['atividades_aee'] = array();
    for ($i=1; $i <= 11; $i++) {
      if($dadosRegistro[25+$i-1]){
        $camposTurma['atividades_aee'][] = $i;
      }
    }
    $camposTurma['atividades_aee'] = '{'.implode(',', $camposTurma['atividades_aee']).'}';


    $modalidadeEnsinoCenso = $dadosRegistro[37-1];
    $etapaEnsinoCenso = $dadosRegistro[38-1];
    $tipoAtendimento = $dadosRegistro[18-1];
    $codEscola = $this->existeEscola($inepEscola);

    if($codEscola){
      $codTurma = null;
      if(!empty($inepTurma)){
        $codTurma = $this->existeTurma($inepTurma);
      }

      if(!$codTurma){

        $codTurmaTipo = $this->getOrCreateTurmaTipo();
        $codCurso = $this->getOrCreateCurso($etapaEnsinoCenso, $codEscola, $modalidadeEnsinoCenso, $tipoAtendimento);
        $codSerie = $this->getOrCreateSerie($etapaEnsinoCenso, $codEscola, $codCurso, $tipoAtendimento);

        $turma = new clsPmieducarTurma();
        $turma->ref_cod_instituicao = $this->ref_cod_instituicao;
        $turma->ref_usuario_cad = $this->pessoa_logada;
        $turma->ref_ref_cod_escola = $codEscola;
        $turma->ref_cod_curso = $codCurso;
        $turma->ref_ref_cod_serie = $codSerie;
        $turma->nm_turma = $nomeTurma;
        $turma->sgl_turma = '';
        $turma->max_aluno = 99;
        $turma->ativo = 1;
        $turma->multiseriada = 0;
        $turma->visivel = true;
        $turma->ref_cod_turma_tipo = $codTurmaTipo;
        $turma->hora_inicial = $horaInicial;
        $turma->hora_final = $horaFinal;
        $turma->ano = $this->ano;
        $turma->tipo_boletim = 1;
        $turma->dias_semana = $diasSemana;

        foreach ($camposTurma as $key => $value) {
          $turma->{$key} = $value;
        }
        $codTurma = $turma->cadastra();
        $turma->cod_turma = $codTurma;

        if(!empty($inepTurma)){
          $turma->updateInep($inepTurma);
        }

        foreach ($disciplinas as $disciplinaEducacenso => $usaDisciplina) {
          if($usaDisciplina >= 1){
            $this->vinculaDisciplinaTurma($codEscola, $codSerie, $codTurma, $disciplinaEducacenso);
          }
        }
      }
    }
  }

  function vinculaDisciplinaTurma($codEscola, $codSerie, $codTurma, $disciplinaEducacenso){
    $codDisciplina = $this->getOrCreateDisciplina($disciplinaEducacenso);
    $this->vinculaDisciplinaSerie($codDisciplina, $codSerie);
    $this->vinculaDisciplinaEscolaSerie($codDisciplina, $codEscola, $codSerie);
    $this->getOrCreateComponenteCurricularTurma($codEscola, $codSerie, $codTurma, $codDisciplina);
  }

  function vinculaDisciplinaEscolaSerie($codDisciplina, $codEscola, $codSerie){
    $obj = new clsPmieducarEscolaSerieDisciplina(
            $codSerie,
            $codEscola,
            $codDisciplina,
            1,
            null,
            null,
            null,
            [$this->ano]
          );

    if(!$obj->existe()){
      $obj->cadastra();
    }

  }

  function vinculaDisciplinaSerie($codDisciplina, $codSerie){
    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('componenteCurricular', 'anoEscolar');
    $where = array('componente_curricular_id'=>$codDisciplina, 'ano_escolar_id' => $codSerie);
    $componenteAno = $dataMapper->findAll(array(), $where);

    if(count($componenteAno) == 0){
      $data = array();

      $data['componenteCurricular'] = $codDisciplina;
      $data['anoEscolar'] = $codSerie;
      $data['anosLetivos'] = '{' . $this->ano . '}';
      $entity = $dataMapper->createNewEntityInstance();
      $entity->setOptions($data);

      $dataMapper->save($entity);
    }
  }

  function getOrCreateComponenteCurricularTurma($codEscola, $codSerie, $codTurma, $codDisciplina){
    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('componenteCurricular', 'turma');
    $where = array('componente_curricular_id'=>$codDisciplina, 'turma_id' => $codTurma);
    $turmas = $dataMapper->findAll(array());

    if(count($turmas) == 0){
      $data = array();

      $data['componenteCurricular'] = $codDisciplina;
      $data['turma'] = $codTurma;
      $data['anoEscolar'] = $codSerie;
      $data['escola'] = $codEscola;

      $entity = $dataMapper->createNewEntityInstance();
      $entity->setOptions($data);

      $dataMapper->save($entity);
    }
  }

  function getOrCreateAreaConhecimento(){
    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('areaConhecimento', 'area');
    $areas = $dataMapper->findAll(array());

    $codArea = null;
    if( count($areas)){
      $codArea = $areas[0]->id;
    }else{

      $sql = "INSERT INTO modules.area_conhecimento (instituicao_id, nome)
                VALUES ($1,$2) returning id ";
      $codArea = Portabilis_Utils_Database::selectField($sql, array($this->ref_cod_instituicao, "Migração"));
    }

    return $codArea;
  }

  function getOrCreateDisciplina($disciplinaEducacenso){

    $codArea = $this->getOrCreateAreaConhecimento();

    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('componenteCurricular', 'componente');
    $where = array('codigo_educacenso' => $disciplinaEducacenso);

    $disciplinas = $dataMapper->findAll(array(), $where);
    $codDisciplina = null;
    // Não existem componentes específicos para a turma
    if (count($disciplinas)) {
      $codDisciplina = $disciplinas[0]->id;
    }else{

      $codigoEducacenso = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
      $codigos = $codigoEducacenso->getData();

      $nome = $codigos[$disciplinaEducacenso] ? $codigos[$disciplinaEducacenso] : "Migração";
      $sigla = mb_substr($nome, 0, 3, 'UTF-8');
      $sql = "INSERT INTO modules.componente_curricular (instituicao_id, area_conhecimento_id, nome, codigo_educacenso, abreviatura, tipo_base)
                VALUES ('{$this->ref_cod_instituicao}','{$codArea}','{$nome}','{$disciplinaEducacenso}','{$sigla}',1) returning id ";

      $codDisciplina = Portabilis_Utils_Database::selectField($sql);

    }

    return $codDisciplina;
  }

  function getOrCreateTurmaTipo(){
    $codTurmaTipo= null;

    $turmaTipo = new clsPmieducarTurmaTipo();
    $turmaTipos = $turmaTipo->lista();

    if ($turmaTipos) {
      $codTurmaTipo = $turmaTipos[0]['cod_turma_tipo'];
    } else {
      $turmaTipo->ref_usuario_cad = $this->pessoa_logada;
      $turmaTipo->nm_tipo = "Regular";
      $turmaTipo->sgl_tipo = "Reg";
      $turmaTipo->ref_cod_instituicao = $this->ref_cod_instituicao;
      $codTurmaTipo = $turmaTipo->cadastra();
    }

    return $codTurmaTipo;
  }

  function getOrCreateSerie($etapaEnsinoCenso, $codEscola, $codCurso, $tipoAtendimento){
    $dadosSerie = $this->etapasCenso[$etapaEnsinoCenso];
    $codSerie = null;

    if ($this->isAtividadeComplementar($tipoAtendimento)) {
      $dadosSerie = $this->etapasCenso['atividade_complementar'];
    }

    if ($this->isAtendimentoEspecializado($tipoAtendimento)) {
      $dadosSerie = $this->etapasCenso['atendimento_educacional_especializado'];
    }

    $series = new clsPmieducarSerie();
    $series = $series->lista(null, null, null, $codCurso, null, $dadosSerie['etapa'], null, null, null, null, null, null, 1, $this->ref_cod_instituicao);
    if ($series) {
      $codSerie = $series[0]['cod_serie'];
    } else {
      $serie = new clsPmieducarSerie();
      $serie->ref_usuario_cad = $this->pessoa_logada;
      $serie->ref_cod_curso = $codCurso;
      $serie->nm_serie = $dadosSerie['serie'];
      $serie->etapa_curso = $dadosSerie['etapa'];
      $serie->concluinte = ($dadosSerie['etapa'] == $dadosSerie['etapas']) ? 1 : 0;
      $serie->carga_horaria = 800;
      $serie->dias_letivos = 200;
      $serie->ativo = 1;
      $serie->intervalo = 1;
      $codSerie = $serie->cadastra();
    }

    if ($codEscola) {
      $escolaSerie = new clsPmieducarEscolaSerie();
      $escolaSerie = $escolaSerie->lista($codEscola, $codSerie);
      if (!$escolaSerie) {
        $vinculo = new clsPmieducarEscolaSerie();
        $vinculo->ref_cod_escola = $codEscola;
        $vinculo->ref_cod_serie = $codSerie;
        $vinculo->ref_usuario_cad = $this->pessoa_logada;
        $vinculo->hora_inicial = "07:30:00";
        $vinculo->hora_final = "12:00:00";
        $vinculo->hora_inicio_intervalo = "09:50:00";
        $vinculo->hora_fim_intervalo = "10:20:00";
        $vinculo->anos_letivos = [$this->ano];
        $vinculo->cadastra();
      }
    }

    return $codSerie;
  }

  function getOrCreateCurso($etapaEnsinoCenso, $codEscola, $modalidade, $tipoAtendimento){
    $dadosCurso = $this->etapasCenso[$etapaEnsinoCenso];

    if ($this->isAtividadeComplementar($tipoAtendimento)) {
      $dadosCurso = $this->etapasCenso['atividade_complementar'];
    }

    if ($this->isAtendimentoEspecializado($tipoAtendimento)) {
      $dadosCurso = $this->etapasCenso['atendimento_educacional_especializado'];
    }

    $codCurso = $this->getCurso($dadosCurso['curso']);

    if (!$codCurso) {
      $codNivelEnsino = $this->getOrCreateNivelEnsino();
      $codTipoEnsino = $this->getOrCreateTipoEnsino();
      $curso = new clsPmieducarCurso();
      $curso->nm_curso = $dadosCurso['curso'];
      $curso->sgl_curso = mb_substr($dadosCurso['curso'], 0, 15, 'UTF-8');
      $curso->qtd_etapas = $dadosCurso['etapas'];
      $curso->carga_horaria = 800 * $dadosCurso['etapas'];
      $curso->ativo = 1;
      $curso->ref_cod_nivel_ensino = $codNivelEnsino;
      $curso->ref_cod_tipo_ensino = $codTipoEnsino;
      $curso->ref_cod_instituicao = $this->ref_cod_instituicao;
      $curso->ref_usuario_cad = $this->pessoa_logada;
      $curso->padrao_ano_escolar = 1;
      $curso->multi_seriado = 1;
      $curso->modalidade_curso = $modalidade;
      $codCurso = $curso->cadastra();
    }

    if ($codEscola) {
      $escolaCurso = new clsPmieducarEscolaCurso();
      $escolaCurso = $escolaCurso->lista($codEscola, $codCurso);
      if (!$escolaCurso) {
        $vinculo = new clsPmieducarEscolaCurso();
        $vinculo->ref_cod_curso = $codCurso;
        $vinculo->ref_cod_escola = $codEscola;
        $vinculo->ref_usuario_cad = $this->pessoa_logada;
        $vinculo->ativo = 1;
        $vinculo->anos_letivos = [$this->ano];
        $vinculo->cadastra();
      }
    }

    return $codCurso;
  }

  function getOrCreateTipoEnsino(){
    $codTipoEnsino = $this->getTipoEnsino();

    if(!$codTipoEnsino){
      $tipoEnsino = new clsPmieducarTipoEnsino();
      $tipoEnsino->nm_tipo = "Padrão";
      $tipoEnsino->ativo = 1;
      $tipoEnsino->ref_cod_instituicao = $this->ref_cod_instituicao;
      $tipoEnsino->ref_usuario_cad = $this->pessoa_logada;
      $codTipoEnsino = $tipoEnsino->cadastra();
    }

    return $codTipoEnsino;
  }

  function getTipoEnsino(){
    $sql = "SELECT cod_tipo_ensino
              FROM pmieducar.tipo_ensino
              WHERE ativo = 1
              AND ref_cod_instituicao = {$this->ref_cod_instituicao}
              LIMIT 1";
    return Portabilis_Utils_Database::selectField($sql);
  }

  function getOrCreateNivelEnsino($nivel = "Ano"){
    $codNivelEnsino = $this->getNivelEnsino($nivel);

    if(!$codNivelEnsino){
      $objNivelEnsino = new clsPmieducarNivelEnsino();
      $objNivelEnsino->nm_nivel = $nivel;
      $objNivelEnsino->ref_cod_instituicao = $this->ref_cod_instituicao;
      $objNivelEnsino->ativo = 1;
      $objNivelEnsino->ref_usuario_cad = $this->pessoa_logada;
      $codNivelEnsino = $objNivelEnsino->cadastra();
    }

    return $codNivelEnsino;
  }

  function getNivelEnsino($nivel){
    $sql = "SELECT cod_nivel_ensino
            FROM pmieducar.nivel_ensino
            WHERE ref_cod_instituicao = {$this->ref_cod_instituicao}
            AND nm_nivel ILIKE '{$nivel}'
            AND ativo = 1
            LIMIT 1
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function importaRegistro30($dadosRegistro){

    $inepEscola = $dadosRegistro[2-1];
    $inepServidor = $dadosRegistro[3-1];
    //$codServidor = $dadosRegistro[4-1];
    $nome = $dadosRegistro[5-1];
    $email = $dadosRegistro[6-1];
    $nis = $dadosRegistro[7-1];
    $dataNascimento =Portabilis_Date_Utils::brToPgSQL($dadosRegistro[8-1]);
    $sexo = $dadosRegistro[9-1] == "1" ? "M" : "F";
    $racaEducacenso = $dadosRegistro[10-1];
    $nomeMae = $dadosRegistro[12-1];
    $nomePai = $dadosRegistro[13-1];
    $tipoNacionalidade = $dadosRegistro[14-1];
    $paisNacionalidadeCenso = $dadosRegistro[15-1];
    $codIbgeMun = $dadosRegistro[17-1];

    $idmun = $codIbgeMun ? $this->getMunicipioByCodIbge($codIbgeMun) : null;

    if(!is_numeric($inepServidor)){
      return false;
    }
    $codServidor = $this->existeServidor($inepServidor);

    if(!$codServidor){
      $idpesPai = $nomePai ? $this->cadastraPessoaFisica($nomePai, null, "M") : null;
      $idpesMae = $nomeMae ? $this->cadastraPessoaFisica($nomeMae, null, "F") : null;
      $idpesServidor = $this->cadastraPessoaFisica($nome, $dataNascimento, $sexo, $idmun, $idpesMae, $idpesPai, $nis, $tipoNacionalidade, $paisNacionalidadeCenso);
      if(is_numeric($racaEducacenso)){
        $this->createOrUpdateRaca($idpesServidor, $racaEducacenso);
      }

      $codServidor = $this->createServidor($idpesServidor);

      if(!$codServidor){
        return false;
      }

      $this->createServidorFuncao($codServidor);
      $this->createServidorEducacenso($codServidor, $inepServidor);
    }
  }

  function getOrCreateFuncaoProfessor(){
    $objFuncao = new clsPmieducarFuncao();
    $funcoes = $objFuncao->lista(null, null, null, null, null, 1);
    $codFuncao = null;

    if(is_array($funcoes) && count($funcoes) > 0){
      $codFuncao = $funcoes[0]['cod_funcao'];
    }else{
      $objFuncao->ref_usuario_cad = $this->pessoa_logada;
      $objFuncao->nm_funcao = "Professor";
      $objFuncao->abreviatura = "Prof";
      $objFuncao->professor = 1;
      $objFuncao->ref_cod_instituicao = $this->ref_cod_instituicao;
      $codFuncao = $objFuncao->cadastra();
    }

    return $codFuncao;
  }

  function importaRegistro40($dadosRegistro){

    $inepEscola = $dadosRegistro[2-1];
    $inepServidor = $dadosRegistro[3-1];
    //$codServidor = $dadosRegistro[4-1];
    $cpf = idFederal2int($dadosRegistro[5-1]);
    $localizacao = $dadosRegistro[6-1];
    $cep = $dadosRegistro[7-1];
    $endereco = $dadosRegistro[8-1];
    $numero = $dadosRegistro[9-1];
    $complemento = utf8_encode($dadosRegistro[10-1]);
    $bairro = $dadosRegistro[11-1];
    $ufIbge = $dadosRegistro[12-1];
    $municipioIbge = $dadosRegistro[13-1];

    if(!is_numeric($inepServidor)){
      return false;
    }
    $codServidor = $this->existeServidor($inepServidor);

    if($codServidor){

      $objFisica = new clsFisica($codServidor);
      $objFisica->cpf = $cpf;
      $objFisica->idpes_rev = $this->pessoa_logada;
      $objFisica->edita();
      if(!empty($cep)){
        $this->cadastraEndereco($codServidor, $cep, $endereco, $numero, $complemento, $bairro, $ufIbge, $municipioIbge, null, $localizacao);
      }
    }
  }

  function importaRegistro50($dadosRegistro){

    $inepEscola = $dadosRegistro[2-1];
    $inepServidor = $dadosRegistro[3-1];
    //$codServidor = $dadosRegistro[4-1];

    $escolaridade = $dadosRegistro[5-1];

    $codigoCursoSuperior1 = $dadosRegistro[8-1];
    $instituicaoEnsinoSuperior1 = $dadosRegistro[11-1];
    $codigoCursoSuperior2 = $dadosRegistro[14-1];
    $instituicaoEnsinoSuperior2 = $dadosRegistro[17-1];
    $codigoCursoSuperior3 = $dadosRegistro[20-1];
    $instituicaoEnsinoSuperior3 = $dadosRegistro[23-1];

    $cursosServidor = array(
      'situacao_curso_superior_1' => $dadosRegistro[6-1],
      'formacao_complementacao_pedagogica_1' => $dadosRegistro[7-1],
      'ano_inicio_curso_superior_1' => $dadosRegistro[9-1],
      'ano_conclusao_curso_superior_1' => $dadosRegistro[10-1],
      'situacao_curso_superior_2' => $dadosRegistro[12-1],
      'formacao_complementacao_pedagogica_2' => $dadosRegistro[13-1],
      'ano_inicio_curso_superior_2' => $dadosRegistro[15-1],
      'ano_conclusao_curso_superior_2' => $dadosRegistro[16-1],
      'situacao_curso_superior_3' => $dadosRegistro[18-1],
      'formacao_complementacao_pedagogica_3' => $dadosRegistro[19-1],
      'ano_inicio_curso_superior_3' => $dadosRegistro[21-1],
      'ano_conclusao_curso_superior_3' => $dadosRegistro[22-1],
    );

    $cursosServidor['pos_graduacao'] = array();
    for ($i=1; $i <= 4; $i++) {
      if($dadosRegistro[23+$i-1]){
        $cursosServidor['pos_graduacao'][] = $i;
      }
    }
    $cursosServidor['pos_graduacao'] = '{'.implode(',', $cursosServidor['pos_graduacao']).'}';

    $cursosServidor['curso_formacao_continuada'] = array();
    for ($i=1; $i <= 16; $i++) {
      if($dadosRegistro[27+$i-1]){
        $cursosServidor['curso_formacao_continuada'][] = $i;
      }
    }
    $cursosServidor['curso_formacao_continuada'] = '{'.implode(',', $cursosServidor['curso_formacao_continuada']).'}';

    if(!is_numeric($inepServidor)){
      return false;
    }
    $codServidor = $this->existeServidor($inepServidor);

    if($codServidor){
      $servidor = new clsPmieducarServidor();
      $servidor->cod_servidor = $codServidor;
      $servidor->ref_cod_instituicao = $this->ref_cod_instituicao;

      if($escolaridade){
        $cursosServidor['ref_idesco'] = $this->getOrCreateEscolaridade($escolaridade);
      }

      if($codigoCursoSuperior1){
        $idCurso = $this->getIdCursoSuperiorEducacenso($codigoCursoSuperior1);
        if($idCurso){
          $cursosServidor['codigo_curso_superior_1'] = $idCurso;
        }
      }
      if($codigoCursoSuperior2){
        $idCurso = $this->getIdCursoSuperiorEducacenso($codigoCursoSuperior2);
        if($idCurso){
          $cursosServidor['codigo_curso_superior_2'] = $idCurso;
        }
      }
      if($codigoCursoSuperior3){
        $idCurso = $this->getIdCursoSuperiorEducacenso($codigoCursoSuperior3);
        if($idCurso){
          $cursosServidor['codigo_curso_superior_3'] = $idCurso;
        }
      }

      if($instituicaoEnsinoSuperior1){
        $idCurso = $this->getIdInstituicaoEducacenso($instituicaoEnsinoSuperior1);
        if($idCurso){
          $cursosServidor['instituicao_curso_superior_1'] = $idCurso;
        }
      }
      if($instituicaoEnsinoSuperior2){
        $idCurso = $this->getIdInstituicaoEducacenso($instituicaoEnsinoSuperior2);
        if($idCurso){
          $cursosServidor['instituicao_curso_superior_2'] = $idCurso;
        }
      }
      if($instituicaoEnsinoSuperior3){
        $idCurso = $this->getIdInstituicaoEducacenso($instituicaoEnsinoSuperior3);
        if($idCurso){
          $cursosServidor['instituicao_curso_superior_3'] = $idCurso;
        }
      }

      foreach ($cursosServidor as $key => $value) {
        $servidor->{$key} = $value;
      }
      $servidor->edita();
    }
  }

  function getOrCreateEscolaridade($escolaridade){
    $obj = new clsCadastroEscolaridade();
    $escolaridades = $obj->lista(null, null, $escolaridade);
    $codEscolaridade = null;
    if(is_array($escolaridades) && count($escolaridades) > 0){
      $codEscolaridade = $escolaridades[0]['idesco'];
    }else{
      $escolaridadeToString = array(1 => 'Fundamental incompleto',
                     2 => 'Fundamental completo',
                     3 => 'Ensino médio - Normal/Magistério',
                     4 => 'Ensino médio - Normal/Magistério Indígena',
                     5 => 'Ensino médio',
                     6 => 'Superior');
      $obj->escolaridade = $escolaridade;
      $obj->descricao = $escolaridadeToString[$escolaridade];
      $codEscolaridade = $obj->cadastra();
    }
    return $codEscolaridade;
  }


  function importaRegistro51($dadosRegistro){

    $inepEscola = $dadosRegistro[2-1];
    $inepServidor = $dadosRegistro[3-1];
    $inepTurma = $dadosRegistro[5-1];

    $funcao = $dadosRegistro[7-1];
    $tipoVinculo = $dadosRegistro[8-1];

    $disciplinas = array();
    for ($i=9-1; $i < 21-1; $i++) {
      if(!empty($dadosRegistro[$i])){
        $disciplinas[] = $dadosRegistro[$i];
      }
    }

    if(!is_numeric($inepServidor)){
      return false;
    }

    $codServidor = $this->existeServidor($inepServidor);
    $codTurma = $this->existeTurma($inepTurma);

    if(!$codServidor || !$codTurma){
      return false;
    }

    $obj = new clsModulesProfessorTurma(NULL, $this->ano, $this->ref_cod_instituicao, $codServidor, $codTurma, $funcao, $tipoVinculo);
    $id = $obj->existe2();
    if (!$id){
      $id = $obj->cadastra();
    }
    Portabilis_Utils_Database::fetchPreparedQuery('DELETE FROM modules.professor_turma_disciplina WHERE professor_turma_id = $1', array( 'params' => array($id)));
    foreach ($disciplinas as $disciplina) {
      $codDisciplina = $this->getOrCreateDisciplina($disciplina);

      Portabilis_Utils_Database::fetchPreparedQuery('INSERT INTO modules.professor_turma_disciplina VALUES ($1,$2)',array( 'params' =>  array($id, $codDisciplina) ));
    }

  }

  function importaRegistro60($dadosRegistro){
    $inepEscola = $dadosRegistro[2-1];
    $inepAluno = $dadosRegistro[3-1];
    $nomeCompleto = $dadosRegistro[5-1];
    $dataNascimento = Portabilis_Date_Utils::brToPgSQL($dadosRegistro[6-1]);
    $sexo = $dadosRegistro[7-1] == "1" ? "M" : "F";
    $corRacaEducacenso = $dadosRegistro[8-1];
    $nomeMae = $dadosRegistro[10-1];
    $nomePai = $dadosRegistro[11-1];
    $tipoNacionalidade = $dadosRegistro[12-1];
    $paisNacionalidadeCenso = $dadosRegistro[13-1];
    $municipioIbge = $dadosRegistro[15-1];

    $deficiencias = array();
    for ($i=17-1; $i < 29-1; $i++) {
      if($dadosRegistro[$i] == "1"){
        $deficiencias[] = $i-15;
      }
    }

    $recursosProva = array();
    for ($i=1; $i <= 9; $i++) {
      if($dadosRegistro[29+$i-1]){
        $recursosProva[] = $i;
      }
    }
    $recursosProva = '{'.implode(',', $recursosProva).'}';

    $codAluno = $this->existeAluno($inepAluno);

    if(!$codAluno){
      $idmun = $municipioIbge ? $this->getMunicipioByCodIbge($municipioIbge) : null;
      $idpesPai =  $nomePai ? $this->cadastraPessoaFisica($nomePai, null, "M") : null;
      $idpesMae = $nomeMae ? $this->cadastraPessoaFisica($nomeMae, null, "F") : null;
      $idpesAluno = $this->cadastraPessoaFisica($nomeCompleto, $dataNascimento, $sexo, $idmun, $idpesMae, $idpesPai, null, $tipoNacionalidade, $paisNacionalidadeCenso);
      if(is_numeric($corRacaEducacenso)){
        $this->createOrUpdateRaca($idpesAluno, $corRacaEducacenso);
      }
      $aluno = new clsPmieducarAluno(null, null, null, null, $this->pessoa_logada, $idpesAluno, null, null, 1);
      $aluno->recursos_prova_inep = $recursosProva;
      $codAluno = $aluno->cadastra();
      $this->createAlunoEducacenso($codAluno, $inepAluno);
      foreach ($deficiencias as $key => $deficienciaEducacenso) {
        $codDeficiencia = $this->getOrCreateDeficiencia($deficienciaEducacenso);
        $deficiencia = new clsCadastroFisicaDeficiencia($idpesAluno, $codDeficiencia);
        $deficiencia->cadastra();
      }
    }
  }

  function importaRegistro70($dadosRegistro){
    $inepEscola = $dadosRegistro[2-1];
    $inepAluno = $dadosRegistro[3-1];
    $identidade = $dadosRegistro[5-1];
    $orgaoEmissorRgCenso = $dadosRegistro[6-1];
    $ufIdentidadeCenso = $dadosRegistro[7-1];
    $dataExpedicaoRg = $dadosRegistro[8-1];
    $modeloCertidaoCivil = $dadosRegistro[9-1];
    $tipoCertidaoCivil = $dadosRegistro[10-1];
    $termoCertidaoCivil = $dadosRegistro[11-1];
    $folhaCertidaoCivil = $dadosRegistro[12-1];
    $livroCertidaoCivil = $dadosRegistro[13-1];
    $dataEmissaoCertidao = $dadosRegistro[14-1];
    $ufCartorio = $dadosRegistro[15-1];
    $municipioCartorio = $dadosRegistro[16-1];
    $codigoCartorio = $dadosRegistro[17-1];
    $numeroMatriculaCertidaoNova = $dadosRegistro[18-1];
    $cpf = idFederal2int($dadosRegistro[19-1]);
    $passaporte = $dadosRegistro[20-1];
    $nis = $dadosRegistro[21-1];

    $localizacao = $dadosRegistro[22-1];
    $cep = $dadosRegistro[23-1];
    $endereco = $dadosRegistro[24-1];
    $numero = $dadosRegistro[25-1];
    $complemento = utf8_encode($dadosRegistro[26-1]);
    $bairro = $dadosRegistro[27-1];
    $ufIbge = $dadosRegistro[28-1];
    $municipioIbge = $dadosRegistro[29-1];

    $codAluno = $this->existeAluno($inepAluno);

    if(!$codAluno){
      return false;
    }

    $aluno = new clsPmieducarAluno($codAluno);
    $detAluno = $aluno->detalhe();
    $idpesAluno = $detAluno['ref_idpes'];

    $fisica = new clsFisica($idpesAluno);
    $fisica->idpes_rev = $this->pessoa_logada;
    $fisica->cpf = $cpf;
    $fisica->nis_pis_pasep = $nis;
    $fisica->edita();

    $documento = new clsDocumento($idpesAluno);

    if(!empty($passaporte)){
      $documento->passaporte = $passaporte;
    }
    if(!empty($identidade)){
      $documento->rg = $identidade;
    }
    if(!empty($orgaoEmissorRgCenso)){
      $orgaoEmissorRg = $this->getOrgaoEmissorRgByCodCenso($orgaoEmissorRgCenso);
      $documento->idorg_exp_rg = $orgaoEmissorRg;
    }
    if(!empty($ufIdentidadeCenso)){
      $ufIdentidade = $this->getUfByCodIbge($ufIdentidadeCenso);
      $documento->sigla_uf_exp_rg = $ufIdentidade;
    }
    if(!empty($dataExpedicaoRg)){
      $documento->data_exp_rg = Portabilis_Date_Utils::brToPgSQL($dataExpedicaoRg);
    }
    if($modeloCertidaoCivil == 2 && !empty($numeroMatriculaCertidaoNova)){
      $documento->certidao_nascimento = $numeroMatriculaCertidaoNova;
    }elseif($modeloCertidaoCivil == 1){
      if($tipoCertidaoCivil == 1){
        $documento->tipo_cert_civil = "91";
      }else{
        $documento->tipo_cert_civil = "92";
      }

      $documento->num_termo = $termoCertidaoCivil;
      $documento->num_folha = $folhaCertidaoCivil;
      $documento->num_livro = $livroCertidaoCivil;

      $documento->data_emissao_cert_civil  = Portabilis_Date_Utils::brToPgSQL($dataEmissaoCertidao);
      if(!empty($ufCartorio)){
        $documento->sigla_uf_cert_civil      = $this->getUfByCodIbge($ufCartorio);
      }
      $documento->cartorio_cert_civil_inep = $this->getIdCartorioInep($codigoCartorio);
    }

    if(!$documento->existe()){
      $documento->idpes_cad = $this->pessoa_logada;
      $documento->cadastra();
    }else{
      $documento->idpes_rev = $this->pessoa_logada;
      $documento->edita();
    }

    $this->cadastraEndereco($idpesAluno, $cep, $endereco, $numero, $complemento, $bairro, $ufIbge, $municipioIbge, null, $localizacao);

  }

  function importaRegistro80($dadosRegistro){
    $inepEscola = $dadosRegistro[2-1];
    $inepAluno = $dadosRegistro[3-1];
    $inepTurma = $dadosRegistro[5-1];

    $recebeEscolarizacaoOutroEspaco = $dadosRegistro[10-1];
    $utilizaTransporte = $dadosRegistro[11-1];
    $poderPublicoTransporte = $dadosRegistro[12-1];

    $codAluno = $this->existeAluno($inepAluno);
    $codTurma = $this->existeTurma($inepTurma);

    if(!$codAluno || !$codTurma){
      return false;
    }

    if ($recebeEscolarizacaoOutroEspaco) {
        $obj = new clsPmieducarAluno($codAluno);

        switch ($recebeEscolarizacaoOutroEspaco) {
            case 1:
                $obj->recebe_escolarizacao_em_outro_espaco = 2;
                break;
            case 3:
                $obj->recebe_escolarizacao_em_outro_espaco = 1;
                break;
            default:
                $obj->recebe_escolarizacao_em_outro_espaco = 3;
                break;
        }

        $obj->edita();
    }

    $this->createOrUpdateAlunoTransporte($codAluno, $utilizaTransporte, $poderPublicoTransporte);

    $turma = new clsPmieducarTurma($codTurma);
    $detalheTurma = $turma->detalhe();


    $codMatricula = $this->existeMatricula($detalheTurma['ref_ref_cod_serie'], $codAluno, $this->ano);

    if(!$codMatricula){

      $obj = new clsPmieducarMatricula(NULL, NULL,
          $detalheTurma['ref_ref_cod_escola'], $detalheTurma['ref_ref_cod_serie'], NULL,
          $this->pessoa_logada, $codAluno, 1, NULL, NULL, 1, $this->ano,
          1, NULL, NULL, NULL, NULL, $detalheTurma['ref_cod_curso'],
          NULL, NULL, date('Y-m-d'));

      $codMatricula = $obj->cadastra();
    }

    if (! $this->existeEnturmacao($codTurma, $codMatricula)) {
      $enturmacao = new clsPmieducarMatriculaTurma($codMatricula,
                                                   $codTurma,
                                                  $this->pessoa_logada,
                                                   $this->pessoa_logada,
                                                   NULL,
                                                   NULL,
                                                   1);
      $enturmacao->data_enturmacao = date('Y-m-d');
      $enturmacao->cadastra();
    }

  }

  function createOrUpdateAlunoTransporte($codAluno, $utilizaTransporte, $poderPublicoTransporte){
    $transporte = 0;
    if($utilizaTransporte && $poderPublicoTransporte){
      $transporte = $poderPublicoTransporte;
    }

    $data = array(
      'aluno'       => $codAluno,
      'responsavel' => $transporte,
      'user'        => $this->pessoa_logada,
      'created_at'  => 'NOW()',
    );

    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('transporte', 'aluno');

    try {
      $entity = $dataMapper->find($codAluno);
    }
    catch(Exception $e) {
      $entity      = $dataMapper->createNewEntityInstance();
    }
    $entity->setOptions($data);

    $dataMapper->save($entity);
  }

  function removerFlagUltimaMatricula($alunoId){
    $matriculas = new clsPmieducarMatricula();
    $matriculas = $matriculas->lista(NULL, NULL, NULL, NULL, NULL, NULL, $alunoId,
                                     NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1);


    foreach ($matriculas as $matricula) {
      if (!$matricula['aprovado']==3){
        $matricula = new clsPmieducarMatricula($matricula['cod_matricula'], NULL, NULL, NULL,
                                               $this->pessoa_logada, NULL, $alunoId, NULL, NULL,
                                               NULL, 1, NULL, 0);
        $matricula->edita();
      }
    }

    return true;
  }

  function getIdCartorioInep($codigo){
    $sql = "SELECT id FROM cadastro.codigo_cartorio_inep WHERE id_cartorio = '{$codigo}' ";
    return Portabilis_Utils_Database::selectField($sql);
  }

  function getIdCursoSuperiorEducacenso($codigo){
    $sql = "SELECT id FROM modules.educacenso_curso_superior WHERE curso_id = '{$codigo}' ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getIdInstituicaoEducacenso($codigo){
    $sql = "SELECT id FROM modules.educacenso_ies WHERE ies_id = '{$codigo}' ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function createServidorFuncao($codServidor){
    $codFuncao = $this->getOrCreateFuncaoProfessor();
    $obj = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $codServidor, $codFuncao);
    $obj->cadastra();
  }

  function createServidorEducacenso($codServidor, $inepServidor){
    $sql = "INSERT INTO modules.educacenso_cod_docente (cod_servidor,cod_docente_inep, fonte, created_at)
                                                  VALUES ($1, $2,'I', 'NOW()')";
    Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($codServidor, $inepServidor)));
  }

  function createServidor($idpesServidor){
    $servidor = new clsPmieducarServidor();
    $servidor->cod_servidor = $idpesServidor;
    $servidor->ref_cod_instituicao = $this->ref_cod_instituicao;
    $servidor->carga_horaria = 0;

    return $servidor->cadastra();
  }

  function existeEnturmacao($codTurma, $codMatricula){
    $sql = "SELECT 1
              FROM pmieducar.matricula_turma
              WHERE ref_cod_turma = {$codTurma}
              AND ref_cod_matricula = {$codMatricula}
              AND ativo = 1
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existeMatricula($serie, $aluno, $ano){
    $sql = "SELECT cod_matricula
              FROM pmieducar.matricula
              WHERE ano = {$ano}
              AND ativo = 1
              AND ref_ref_cod_serie = {$serie}
              AND aprovado = 3
              AND ref_cod_aluno = {$aluno}
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existeAluno($inep){
    $sql = "SELECT cod_aluno
            FROM modules.educacenso_cod_aluno
            WHERE cod_aluno_inep = {$inep}
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existeServidor($inep){
    $sql = "SELECT cod_servidor
            FROM modules.educacenso_cod_docente
            WHERE cod_docente_inep = {$inep}
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getOrCreateRaca($racaEducacenso){
    $obj = new clsCadastroRaca();
    $racas = $obj->lista(null, null, null, null, null, null, null, null, $racaEducacenso);
    $codRaca = null;
    if ($racas) {
      $codRaca = $racas[0]['cod_raca'];
    }else{
      $educacensoToString = array(
        0 => 'Não declarada',
        1 => 'Branca',
        2 => 'Preta',
        3 => 'Parda',
        4 => 'Amarela',
        5 => 'Indígena',
      );
      $obj->idpes_cad = $this->pessoa_logada;
      $obj->nm_raca = $educacensoToString[$racaEducacenso];
      $obj->raca_educacenso = $racaEducacenso;
      $codRaca = $obj->cadastra();
    }

    return $codRaca;
  }

  function createOrUpdateRaca($idpes, $racaEducacenso){
    $codRaca = $this->getOrCreateRaca($racaEducacenso);

    $raca = new clsCadastroFisicaRaca($idpes, $codRaca);

    if ($raca->existe()){
      $raca->edita();
    }else{
      $raca->cadastra();
    }
  }

  function cadastraPessoaFisica($nome, $dataNascimento = null, $sexo = null, $idmun = null, $idpesMae = null, $idpesPai = null, $nis = null, $tipoNacionalidade = null, $paisNacionalidadeCenso = null){
    $idpes = null;
    if(!empty($nome)){
      $pessoa = new clsPessoa_(
        null, $nome, null, null,
        'F', null, null, $email
      );
      $idpes = $pessoa->cadastra();

      $fisica = new clsFisica(
        $idpes,$dataNascimento, $sexo
      );
      if($idmun){
        $fisica->idmun_nascimento = $idmun;
      }
      if($idpesMae){
        $fisica->idpes_mae = $idpesMae;
      }
      if($idpesPai){
        $fisica->idpes_pai = $idpesPai;
      }
      if($nis){
        $fisica->nis_pis_pasep = $nis;
      }
      if($tipoNacionalidade){
        $fisica->nacionalidade = $tipoNacionalidade;
      }
      if($paisNacionalidadeCenso){
        $codPais = $this->getPaisByCodIbge($paisNacionalidadeCenso);
        if($codPais){
          $fisica->idpais_estrangeiro = $codPais;
        }
      }
      $fisica->idpes_cad = $this->pessoa_logada;
      $fisica->cadastra();
    }
    return $idpes;
  }

  function cadastraEndereco($idpes, $cep, $logradouro, $enderecoNumero, $complemento, $nomeBairro, $ufIbge, $municipioIbge, $distritoIbge, $localizacao){
    $enderecoNumero = (int) $enderecoNumero;
    // TODO (Notificar quando endereço não for criado?)

    if($this->checkEnderecoPessoa($idpes)){
      return false;
    }

    $idmun = $municipioIbge ? $this->getMunicipioByCodIbge($municipioIbge) : null;

    if(!$idmun){
      return false;
    }

    $iddis = $distritoIbge ? $this->getDistritoByCodIbge($distritoIbge) : null;

    if(!$iddis){
      $iddis = $this->getDistritoByMunicipio($idmun);
    }

    if(!$iddis){
      return false;
    }

    $idbai = $this->getOrCreateBairro($idmun, $iddis, $nomeBairro, $localizacao);

    $idlog = $this->getOrCreateLogradouro($logradouro, $idmun);

    if(!$idlog || !$idbai){
      return false;
    }

    $obj = new clsCepLogradouro($cep, $idlog);
    if(!$obj->existe()){
      $obj->cadastra();
    }
    $obj = new clsCepLogradouroBairro($idlog, $cep, $idbai);
    if(!$obj->existe()){
      $obj->cadastra();
    }

    $objEndereco = new clsPessoaEndereco($idpes, $cep, $idlog, $idbai, $enderecoNumero, $complemento);
    $objEndereco->idpes_cad = $this->pessoa_logada;
    $objEndereco->cadastra();
  }

  function getOrCreateLogradouro($logradouro, $idmun){
    $logradouro = utf8_encode($logradouro);
    $idlog = $this->getLogradouro($logradouro, $idmun);

    if(!$idlog){
      $split = explode(' ', $logradouro, 2);
      $parteLogradouro = isset($split[1]) ? $split[1] : $logradouro;

      // TODO: Verificar forma melhor de verificar o tipo do logradouro na string
      if($split[0] == "RUA"){
        $logradouro = $parteLogradouro;
      }
      $objLogradouro = new clsLogradouro();
      $objLogradouro->idtlog = "RUA";
      $objLogradouro->nome = $logradouro;
      $objLogradouro->idmun = $idmun;
      $objLogradouro->ident_oficial = "N";
      $idlog = $objLogradouro->cadastra();
    }

    return $idlog;
  }

  function getLogradouro($logradouro, $idmun){
    $split = explode(' ', $logradouro, 2);
    $parteLogradouro = isset($split[1]) ? $split[1] : $logradouro;

    $sql = "SELECT idlog
            from public.logradouro
            WHERE (
              nome ILIKE '{$logradouro}'
              OR nome ILIKE '{$parteLogradouro}'
            ) AND idmun = {$idmun}
            limit 1
    ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getOrCreateDeficiencia($deficienciaEducacenso){
    $codDeficiencia = $this->getDeficiencia($deficienciaEducacenso);

    $nomesDeficiencias = array (
                 1 => "Cegueira",
                 2 => "Baixa visão",
                 3 => "Surdez",
                 4 => "Deficiência auditiva",
                 5 => "Surdocegueira",
                 6 => "Deficiência física",
                 7 => "Deficiência Intelectual",
                 9 => "Autismo Infantil",
                10 => "Síndrome de Asperger",
                11 => "Síndrome de Rett",
                12 => "Transtorno desintegrativo da infância",
                13 => "Altas habilidades/Superdotação",);

    if(!$codDeficiencia){
      $deficiencia = new clsCadastroDeficiencia(null, $nomesDeficiencias[$deficienciaEducacenso], $deficienciaEducacenso);
      $codDeficiencia = $deficiencia->cadastra();
    }

    return $codDeficiencia;
  }

  function getDeficiencia($deficienciaEducacenso){
    $sql = "SELECT cod_deficiencia
              FROM cadastro.deficiencia
              WHERE deficiencia_educacenso = {$deficienciaEducacenso}

    ";
    return Portabilis_Utils_Database::selectField($sql);
  }

  function getCurso($nomeCurso){
    $sql = "SELECT cod_curso
              FROM pmieducar.curso
              WHERE translate(upper(nm_curso),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') = translate(upper('{$nomeCurso}'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')
              AND ref_cod_instituicao = {$this->ref_cod_instituicao}

    ";
    return Portabilis_Utils_Database::selectField($sql);
  }

  function getOrCreateBairro($idmun, $iddis, $nomeBairro, $localizacao){
    $idbai = $this->getBairro($idmun, $iddis, $nomeBairro);
    $nomeBairro = utf8_decode($nomeBairro);
    if(!$idbai){
      $bairro = new clsBairro();
      $bairro->idmun = $idmun;
      $bairro->iddis = $iddis;
      $bairro->nome = $nomeBairro;
      $bairro->zona_localizacao = $localizacao;
      $idbai = $bairro->cadastra();
    }

    return $idbai;
  }

  function getBairro($idmun, $iddis, $nomeBairro){
    $nomeBairro = utf8_encode($nomeBairro);
    $sql = "SELECT idbai
              FROM public.bairro
              WHERE idmun = {$idmun}
              AND iddis = {$iddis}
              AND nome ILIKE '{$nomeBairro}%'
    ";
    return Portabilis_Utils_Database::selectField($sql);
  }

  function getDistritoByMunicipio($idmun){
    $sql = "SELECT iddis
              from public.distrito
              where idmun = {$idmun}
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getDistritoByCodIbge($distritoIbge){
    $sql = "SELECT iddis
              from public.distrito
              where cod_ibge = '{$distritoIbge}'
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getMunicipioByCodIbge($municipioIbge){
    $sql = "SELECT idmun
              from public.municipio
              where cod_ibge = '{$municipioIbge}'
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getUfByCodIbge($ufIbge){
    $sql = "SELECT sigla_uf
              from public.uf
              where cod_ibge = '{$ufIbge}'
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getPaisByCodIbge($paisIbge){
    $sql = "SELECT idpais
              from public.pais
              where cod_ibge = '{$paisIbge}'
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function getOrgaoEmissorRgByCodCenso($orgaoEmissorRgCenso){
    $sql = "SELECT idorg_rg
              from cadastro.orgao_emissor_rg
              where codigo_educacenso = '{$orgaoEmissorRgCenso}'
              limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function checkEnderecoPessoa($idpes){
    $sql = "SELECT idpes from cadastro.endereco_pessoa where idpes = {$idpes} limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function atualizaCamposEscolaRegistro00(
    $codEscola, $cargoGestor, $situacao, $latitude,
    $longitude, $codigoOrgaoRegional, $dependenciaAdministrativa,
    $regulamentacao, $categoriaEscolaPrivada, $convenioPoderPublico,
    $mantenedorEmpresa, $mantenedorSindicato, $mantenedorOrganizacao,
    $mantenedorInstituicao, $mantenedorSistema, $cnpjMantenedoraPrincipal
    ){

    $escola = new clsPmieducarEscola($codEscola);

    $fields = $escola->detalhe();

    foreach ($fields as $key => $value){
      if(property_exists($escola, $key)){
         if ($this->isPostgresArray($value)) {
           $value = $this->cleanPostgresArray($value);
         }
        $escola->{$key} = $value;
      }
    }

    if($cargoGestor){
      $escola->cargo_gestor = $cargoGestor;
    }
    if($situacao){
      $escola->situacao_funcionamento = $situacao;
    }

    if(!empty($latitude)){
      $escola->latitude = $latitude;
    }
    if(!empty($longitude)){
      $escola->longitude = $longitude;
    }
    if($codigoOrgaoRegional){
      $escola->orgao_regional = $codigoOrgaoRegional;
    }
    if($dependenciaAdministrativa){
      $escola->dependencia_administrativa = $dependenciaAdministrativa;
    }
    if($regulamentacao){
      $escola->regulamentacao = $regulamentacao;
    }
    if($categoriaEscolaPrivada){
      $escola->categoria_escola_privada = $categoriaEscolaPrivada;
    }
    if($convenioPoderPublico){
      $escola->conveniada_com_poder_publico = $convenioPoderPublico;
    }
    $mantenedora_escola_privada = array();
    if($mantenedorEmpresa){
      $mantenedora_escola_privada[] = 1;
    }
    if($mantenedorSindicato){
      $mantenedora_escola_privada[] = 2;
    }
    if($mantenedorOrganizacao){
      $mantenedora_escola_privada[] = 3;
    }
    if($mantenedorInstituicao){
      $mantenedora_escola_privada[] = 4;
    }
    if($mantenedorSistema){
      $mantenedora_escola_privada[] = 5;
    }
    if(count($mantenedora_escola_privada)){
      $escola->mantenedora_escola_privada = implode(',', $mantenedora_escola_privada);
    }

    if($cnpjMantenedoraPrincipal){
      $escola->cnpj_mantenedora_principal = idFederal2int($cnpjMantenedoraPrincipal);
    }

    $escola->edita();
  }

  function createAlunoEducacenso($codAluno, $inep){
    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('educacenso', 'aluno');

    $data = array(
      'aluno'      => $codAluno,
      'alunoInep'  => $inep,
      'fonte'      => 'importador',
      'nomeInep'   => '-',
      'created_at' => 'NOW()',
    );

    $entity = $dataMapper->createNewEntityInstance();
    $entity->setOptions($data);

    $dataMapper->save($entity);
  }

  function createEscolaEducacenso($codEscola, $inep){
    $dataMapper = (new Portabilis_DataMapper_Utils)->getDataMapperFor('educacenso', 'escola');

    $data = array(
      'escola'      => $codEscola,
      'escolaInep'  => $inep,
      'fonte'      => 'importador',
      'nomeInep'   => '-',
      'created_at' => 'NOW()',
    );

    $entity = $dataMapper->createNewEntityInstance();
    $entity->setOptions($data);

    $dataMapper->save($entity);
  }

  function createEscolaAnoLetivo($codEscola, $dataInicioAnoLetivo, $dataFimAnoLetivo){
    $obj = new clsPmieducarEscolaAnoLetivo($codEscola, $this->ano, $this->pessoa_logada, null, 1, date('Y-m-d'), null, 1);
    $obj->cadastra();
    $codModulo = $this->getOrCreateModulo();
    $obj = new clsPmieducarAnoLetivoModulo($this->ano, $codEscola, 1, $codModulo, $dataInicioAnoLetivo, $dataFimAnoLetivo, 200);
    $obj->cadastra();
  }

  function getOrCreateModulo(){
    $obj = new clsPmieducarModulo();
    $modulos = $obj->lista();
    $codModulo = null;

    if(is_array($modulos) && count($modulos) > 0){
      $codModulo = $modulos[0]['cod_modulo'];
    }else{
      $obj->ref_usuario_cad = $this->pessoa_logada;
      $obj->nm_tipo = "Módulo Importação";
      $obj->num_meses = 1;
      $obj->num_semanas = 1;
      $obj->ref_cod_instituicao = $this->ref_cod_instituicao;
      $codModulo = $obj->cadastra();
    }

    return $codModulo;
  }

  function createEscola($localizacao, $codEscolaRedeEnsino, $idpesEscola, $nomeEscola, $idpesGestor, $cargoGestor, $emailGestor){
    $escola = new clsPmieducarEscola();

    $escola->ref_usuario_cad = $this->pessoa_logada;
    $escola->ref_cod_instituicao = $this->ref_cod_instituicao;
    $escola->zona_localizacao = $localizacao;
    $escola->ref_cod_escola_rede_ensino = $codEscolaRedeEnsino;
    $escola->ref_idpes = $idpesEscola;
    $escola->ref_idpes_gestor = $idpesGestor;
    $escola->cargo_gestor = $cargoGestor;
    $escola->email_gestor = $emailGestor;
    $escola->sigla = mb_substr($nomeEscola, 0, 5, 'UTF-8');
    $escola->ativo = 1;

    return $escola->cadastra();
  }

  function getOrCreateRedeDeEnsino(){
    $codEscolaRedeEnsino = $this->getRedeDeEnsino();

    if(!$codEscolaRedeEnsino){
      $rede_ensino = new clsPmieducarEscolaRedeEnsino();
      $rede_ensino->ref_usuario_cad = $this->pessoa_logada;
      $rede_ensino->nm_rede = "Importação Educacenso";
      $rede_ensino->ativo = 1;
      $rede_ensino->ref_cod_instituicao = $this->ref_cod_instituicao;

      $codEscolaRedeEnsino = $rede_ensino->cadastra();
    }
    return $codEscolaRedeEnsino;
  }

  function getRedeDeEnsino() {
    $sql = "SELECT cod_escola_rede_ensino FROM pmieducar.escola_rede_ensino LIMIT 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existePessoa($cpf) {
    $sql = "SELECT idpes
              FROM cadastro.fisica
             WHERE cpf = '{$cpf}' ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existeTurma($inep) {
    $sql = "SELECT cod_turma
              FROM modules.educacenso_cod_turma
             WHERE cod_turma_inep = {$inep}";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function existeEscola($inep) {
    $sql = "SELECT cod_escola
              FROM modules.educacenso_cod_escola
             WHERE cod_escola_inep = {$inep}";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function isAtividadeComplementar($tipoAtendimento) {
    return $tipoAtendimento == 4;
  }

  function isAtendimentoEspecializado($tipoAtendimento) {
    return $tipoAtendimento == 5;
  }

  private $etapasCenso = array(
      'atividade_complementar' => array(
        'curso' => "Atividade complementar",
        'serie' => "Atividade complementar",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Outros'
      ),
      'atendimento_educacional_especializado' => array(
        'curso' => "Atendimento educacional especializado (AEE)",
        'serie' => "Atendimento educacional especializado (AEE)",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Outros'
      ),
      1 => array(
        'curso' => "Educação Infantil",
        'serie' => "Creche (0 a 3 anos)",
        'etapa' => 1,
        'etapas' => 3,
        'nivel' => 'Infantil'
      ),
      2 => array(
        'curso' => "Educação Infantil",
        'serie' => "Pré-escola (4 e 5 anos)",
        'etapa' => 2,
        'etapas' => 3,
        'nivel' => 'Infantil'
      ),
      3 => array(
        'curso' => "Educação Infantil",
        'serie' => "Unificada (0 a 5 anos)",
        'etapa' => 3,
        'etapas' => 3,
        'nivel' => 'Infantil'
      ),
      4 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "1ª Série",
        'etapa' => 1,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      5 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "2ª Série",
        'etapa' => 2,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      6 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "3ª Série",
        'etapa' => 3,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      7 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "4ª Série",
        'etapa' => 4,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      8 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "5ª Série",
        'etapa' => 5,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      9 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "6ª Série",
        'etapa' => 6,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      10 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "7ª Série",
        'etapa' => 7,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      11 => array(
        'curso' => "Ensino Fundamental de 8 anos",
        'serie' => "8ª Série",
        'etapa' => 8,
        'etapas' => 8,
        'nivel' => 'Fundamental'
      ),
      12 => array(
        'curso' => "Ensino Fundamental de 8 anos - Multi",
        'serie' => "Multi",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      13 => array(
        'curso' => "Ensino Fundamental de 8 anos - Correção de Fluxo",
        'serie' => "Correção de Fluxo",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      14 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "1º Ano",
        'etapa' => 1,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      15 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "2º Ano",
        'etapa' => 2,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      16 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "3º Ano",
        'etapa' => 3,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      17 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "4º Ano",
        'etapa' => 4,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      18 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "5º Ano",
        'etapa' => 5,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      19 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "6º Ano",
        'etapa' => 6,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      20 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "7º Ano",
        'etapa' => 7,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      21 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "8º Ano",
        'etapa' => 8,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      22 => array(
        'curso' => "Ensino Fundamental de 9 anos - Multi",
        'serie' => "Multi",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      41 => array(
        'curso' => "Ensino Fundamental de 9 anos",
        'serie' => "9º Ano",
        'etapa' => 9,
        'etapas' => 9,
        'nivel' => 'Fundamental'
      ),
      23 => array(
        'curso' => "Ensino Fundamental de 9 anos - Correção de Fluxo",
        'serie' => "Correção de Fluxo",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      24 => array(
        'curso' => "Ensino Fundamental de 8 e 9 anos",
        'serie' => "Multi 8 e 9 anos",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      56 => array(
        'curso' => "Educação Infantil e Ensino Fundamental (8 e 9 anos)",
        'serie' => "Multietapa",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Fundamental'
      ),
      25 => array(
        'curso' => "Ensino Médio",
        'serie' => "1ª Série",
        'etapa' => 1,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      26 => array(
        'curso' => "Ensino Médio",
        'serie' => "2ª Série",
        'etapa' => 2,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      27 => array(
        'curso' => "Ensino Médio",
        'serie' => "3ª Série",
        'etapa' => 3,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      28 => array(
        'curso' => "Ensino Médio",
        'serie' => "4ª Série",
        'etapa' => 4,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      29 => array(
        'curso' => "Ensino Médio Não-seriado",
        'serie' => "Não Seriada",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      30 => array(
        'curso' => "Ensino Médio Integrado",
        'serie' => "Integrado 1ª Série",
        'etapa' => 1,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      31 => array(
        'curso' => "Ensino Médio Integrado",
        'serie' => "Integrado 2ª Série",
        'etapa' => 2,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      32 => array(
        'curso' => "Ensino Médio Integrado",
        'serie' => "Integrado 3ª Série",
        'etapa' => 3,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      33 => array(
        'curso' => "Ensino Médio Integrado",
        'serie' => "Integrado 4ª Série",
        'etapa' => 4,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      34 => array('curso' => "Ensino Médio Integrado Não-Seriado",
        'serie' => "Integrado Não Seriada",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      74 => array('curso' => "Ensino Médio Integrado Não-Seriado",
        'serie' => "EJA",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      35 => array(
        'curso' => "Ensino Médio - Magistério",
        'serie' => "Normal/Magistério 1ª Série",
        'etapa' => 1,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      36 => array(
        'curso' => "Ensino Médio - Magistério",
        'serie' => "Normal/Magistério 2ª Série",
        'etapa' => 2,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      37 => array(
        'curso' => "Ensino Médio - Magistério",
        'serie' => "Normal/Magistério 3ª Série",
        'etapa' => 3,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      38 => array(
        'curso' => "Ensino Médio - Magistério",
        'serie' => "Normal/Magistério 4ª Série",
        'etapa' => 4,
        'etapas' => 4,
        'nivel' => 'Médio'
      ),
      39 => array(
        'curso' => "Educação Profissional (Concomitante)",
        'serie' => "Não-seriado",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      40 => array(
        'curso' => "Educação Profissional (Subseqüente)",
        'serie' => "Não-seriado",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      64 => array(
        'curso' => "Educação Profissional (Subseqüente)",
        'serie' => "Curso técnico misto",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'Médio'
      ),
      69 => array(
        'curso' => "EJA - Ensino fundamental",
        'serie' => "Anos iniciais",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'EJA'
      ),
      70 => array(
        'curso' => "EJA - Ensino fundamental",
        'serie' => "Anos finais",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'EJA'
      ),
      72 => array(
        'curso' => "EJA - Ensino fundamental",
        'serie' => "Anos iniciais e anos finais",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'EJA'
      ),
      65 => array(
        'curso' => "EJA - Ensino fundamental",
        'serie' => "Projovem Urbano",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'EJA'
      ),
      71 => array(
        'curso' => "EJA - Ensino médio",
        'serie' => "Ensino médio",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'EJA'
      ),
      67 => array(
        'curso' => "EJA - Ensino médio",
        'serie' => "Ensino médio",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'FIC'
      ),
      73 => array(
        'curso' => "EJA - Ensino médio",
        'serie' => "Ensino médio",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'FIC'
      ),
      68 => array(
        'curso' => "EJA - Ensino médio",
        'serie' => "Ensino médio",
        'etapa' => 1,
        'etapas' => 1,
        'nivel' => 'FIC'
      )
    );
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
