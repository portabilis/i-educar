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
    $this->mensagem .= "Importação do registro 00";

    $inep = $dadosRegistro[2];
    $cpfGestor = $dadosRegistro[3];
    $nomeGestor = $dadosRegistro[4];
    $cargoGestor = $dadosRegistro[5];
    $emailGestor = $dadosRegistro[6];
    $situacao = $dadosRegistro[7];
    $dataInicioAnoLetivo = $dadosRegistro[8];
    $dataFimAnoLetivo = $dadosRegistro[9];
    $nomeEscola = $dadosRegistro[10];
    $latitude = $dadosRegistro[11];
    $longitude = $dadosRegistro[12];
    $cep = $dadosRegistro[13];
    $endereco = $dadosRegistro[14];
    $enderecoNumero = $dadosRegistro[15];
    $complemento = $dadosRegistro[16];
    $bairro = $dadosRegistro[17];
    $uf = $dadosRegistro[18];
    $municipio = $dadosRegistro[19];
    $distrito = $dadosRegistro[20];
    $ddd = $dadosRegistro[21];
    $telefone = $dadosRegistro[22];
    $telefonePublico = $dadosRegistro[23];
    $telefoneContato = $dadosRegistro[24];
    $telefoneFAX = $dadosRegistro[25];
    $email = $dadosRegistro[26];
    $codigoOrgaoRegional = $dadosRegistro[27];
    $dependenciaAdministrativa = $dadosRegistro[28];
    $localizacao = $dadosRegistro[29];
    $categoriaEscolaPrivada = $dadosRegistro[30];
    $convenioPoderPublico = $dadosRegistro[31];
    $mantenedorEmpresa = $dadosRegistro[32];
    $mantenedorSindicato = $dadosRegistro[33];
    $mantenedorOrganizacao = $dadosRegistro[34];
    $mantenedorInstituicao = $dadosRegistro[35];
    $mantenedorSistema = $dadosRegistro[36];
    $cnpjMantenedoraPrincipal = $dadosRegistro[37];
    $cnpj = $dadosRegistro[38];
    $regulamentacao = $dadosRegistro[39];
    $unidadeVinculada = $dadosRegistro[40];
    $dadosRegistro[41];

    echo "<pre>";print_r($cnpjMantenedoraPrincipal);die;

    $escola = new clsPmieducarEscola();

    if ($this->existeEscola($inep)) {
      $escola->edita();
    } else {
      $escola->cadastra();
    }
  }

  function existeEscola($inep) {
    $sql = "SELECT cod_escola
              FROM modules.educacenso_cod_escola
             WHERE cod_escola_inep = {$inep}";

    $db = new clsBanco();
    $db->Consulta($sql);
    $db->ProximoRegistro();
    return $db->Tupla();
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
