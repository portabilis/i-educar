<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
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
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $arquivo;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998849, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "Início",
         "educar_educacenso_index.php" => "Educacenso",
         "" => "Importação educacenso"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return 'Editar';
  }

  function Gerar()
  {
    $this->campoArquivo('arquivo', 'Arquivo', $this->arquivo);
    $this->nome_url_sucesso = "Importar";
  }

  function Novo()
  {
    $this->Editar();
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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

    $registros = explode("\n", $arquivo);

    foreach ($registros as $registro) {
      $dadosRegistro = explode("|", $registro);
      $numeroRegistro = $dadosRegistro[0];

      switch ($numeroRegistro) {
        case '00':
          $this->ImportaRegistro00($dadosRegistro);
          break;
      }
    }
    return true;
  }

  function ImportaRegistro00($dadosRegistro) {
    $this->mensagem = "Importação do registro 00";

    $inep = $dadosRegistro[1];
    $cpfGestor = (int) $dadosRegistro[2];
    $nomeGestor = $dadosRegistro[3];
    $cargoGestor = $dadosRegistro[4];
    $emailGestor = $dadosRegistro[5];
    $situacao = $dadosRegistro[6];
    $dataInicioAnoLetivo = $dadosRegistro[7];
    $dataFimAnoLetivo = $dadosRegistro[8];
    $nomeEscola = $dadosRegistro[9];
    $latitude = $dadosRegistro[10];
    $longitude = $dadosRegistro[11];
    $cep = $dadosRegistro[12]; // ep.cep
    $logradouro = $dadosRegistro[13]; // l.idtlog l.nome
    $enderecoNumero = $dadosRegistro[14]; // ep.numero
    $complemento = $dadosRegistro[15]; // ep.complemento
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
    $cnpj = $dadosRegistro[37];
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

      $cnpj = sprintf("%02d.%03d.%03d/%04d-%02d", rand(1, 99), rand(1, 999), rand(1, 999), rand(1, 9999), rand(1, 99));
      $juridica = new clsJuridica(
        $idpesEscola,idFederal2int($cnpj), $nomeEscola,
        null, null, $this->usuario_cad, null
      );
      $juridica->cadastra();

      $codEscolaRedeEnsino = $this->getOrCreateRedeDeEnsino();

      $codEscolaLocalizacao = $this->getOrCreateLocalizacaoEscola($localizacao);

      $codEscola = $this->createEscola($codEscolaLocalizacao, $codEscolaRedeEnsino, $idpesEscola, $nomeEscola);

      if(!$codEscola){
        return false;
      }

      $this->createEscolaEducacenso($codEscola, $inep);
    }

    $this->atualizaCamposEscolaRegistro00($codEscola, $cargoGestor, $situacao, $latitude, $longitude, $codigoOrgaoRegional, $dependenciaAdministrativa, $regulamentacao);

    $this->cadastraEnderecoEscola($codEscola, $cep, $logradouro, $enderecoNumero, $complemento, $nomeBairro, $ufIbge, $municipioIbge, $distritoIbge, $localizacao);


    if ($this->existeEscola($inep)) {
      $escola->edita();
    } else {
      $escola->cadastra();
    }
  }

  function cadastraEnderecoEscola($codEscola, $cep, $logradouro, $enderecoNumero, $complemento, $nomeBairro, $ufIbge, $municipioIbge, $distritoIbge, $localizacao){
    $escola = new clsPmieducarEscola($codEscola);
    $detEscola = $escola->detalhe();
    $idpesEscola = $detEscola->idpes;

    if($this->checkEnderecoPessoa($idpesEscola)){
      return false;
    }

    $idmun = $this->getMunicipioByCodIbge($municipioIbge);

    if(!$idmun){
      return false;
    }

    $iddis = $this->getDistritoByCodIbge($distritoIbge);

    if(!$iddis){
      return false;
    }

    $idbai = $this->getOrCreateBairro($idmun, $iddis, $nomeBairro);

    $idlog = $this->getOrCreateLogradouro($logradouro, $idmun);

    if(!$idlog || !$idbai){
      return false;
    }

    $objEndereco = new clsPessoaEndereco($idpesEscola, $cep, $idlog, $idbai, $enderecoNumero, $complemento);
    $objEndereco->cadastra();
  }

  function getOrCreateLogradouro($logradouro, $idmun){
    $idlog = $this->getLogradouro($logradouro, $idmun);

    if(!$idlog){
      $split = explode(' ', $logradouro, 2);
      $parteLogradouro = isset($split[1]) ? $split[1] : $logradouro;

      if($split[0] == "RUA"){
        $logradouro = $parteLogradouro;
      }
      $objLogradouro = new clsLogradouro();
      $objLogradouro->idtlog = "RUA";
      $objLogradouro->nome = $logradouro;
      $objLogradouro->idmun = $idmun;
      $objLogradouro->ident_oficial = " ";
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

  function getOrCreateBairro($idmun, $iddis, $nomeBairro){
    $idbai = $this->getBairro($idmun, $iddis, $nomeBairro);

    if(!$idbai){
      $bairro = new clsBairro();
      $bairro->idmun = $idmun;
      $bairro->iddis = $iddis;
      $bairro->nome = $nomeBairro;
      $idbai = $bairro->cadastra();
    }

    return $idbai;
  }

  function getBairro($idmun, $iddis, $nomeBairro){
    $sql = "SELECT idbai
              FROM public.bairro
              WHERE idmun = {$idmun}
              AND iddis = {$iddis}
              AND nome ILIKE '{$nomeBairro}%'
    ";
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

  function checkEnderecoPessoa($idpes){
    $sql = "SELECT idpes from cadastro.endereco_pessoa where idpes = {$idpes} limit 1 ";

    return Portabilis_Utils_Database::selectField($sql);
  }

  function atualizaCamposEscolaRegistro00(
    $codEscola, $cargoGestor, $situacao, $latitude,
    $longitude, $codigoOrgaoRegional, $dependenciaAdministrativa,
    $regulamentacao
    ){

    $escola = new clsPmieducarEscola($codEscola);

    if($cargoGestor){
      $escola->cargo_gestor = $cargoGestor;
    }
    if($situacao){
      $escola->situacao_funcionamento = $situacao;
    }
    if($latitude){
      $escola->latitude = $latitude;
    }
    if($longitude){
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

    $escola->edita();
  }

  function createEscolaEducacenso($codEscola, $inep){
    $dataMapper = Portabilis_DataMapper_Utils::getDataMapperFor('educacenso', 'escola');

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

  function createEscola($codEscolaLocalizacao, $codEscolaRedeEnsino, $idpesEscola, $nomeEscola){
    $escola = new clsPmieducarEscola();

    $escola->ref_usuario_cad = $this->pessoa_logada;
    $escola->ref_cod_instituicao = $this->ref_cod_instituicao;
    $escola->ref_cod_escola_localizacao = $codEscolaLocalizacao;
    $escola->ref_cod_escola_rede_ensino = $codEscolaRedeEnsino;
    $escola->ref_idpes = $idpesEscola;
    $escola->sigla = substr($nomeEscola, 0, 5);
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

  function getOrCreateLocalizacaoEscola($localizacao){
    $localizacaoString = $localizacao == "1" ? "Urbana" : "Rural";

    $codEscolaLocalizacao = $this->getLocalizacaoEscola($localizacaoString);
    if (!$codEscolaLocalizacao) {
      $escolaLocalizacao = new clsPmieducarEscolaLocalizacao();
      $escolaLocalizacao->ref_usuario_cad = $this->pessoa_logada;
      $escolaLocalizacao->nm_localizacao = $localizacaoString;
      $escolaLocalizacao->ativo = 1;
      $escolaLocalizacao->ref_cod_instituicao = $this->ref_cod_instituicao;

      $codEscolaLocalizacao = $escolaLocalizacao->cadastra();
    }
    return $codEscolaLocalizacao;
  }

  function getLocalizacaoEscola($nm_localizacao){
    $sql = "SELECT cod_escola_localizacao
              FROM pmieducar.escola_localizacao
              WHERE nm_localizacao ILIKE '{$nm_localizacao}'
              LIMIT 1 ";

    return Portabilis_Utils_Database::selectField($sql);
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

  function existeEscola($inep) {
    $sql = "SELECT cod_escola
              FROM modules.educacenso_cod_escola
             WHERE cod_escola_inep = {$inep}";

    return Portabilis_Utils_Database::selectField($sql);
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
