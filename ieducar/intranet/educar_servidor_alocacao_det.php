<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
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
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'Educacenso/Model/DocenteDataMapper.php';

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
class clsIndexBase extends clsBase {
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor alocação');
    $this->processoAp = 635;
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
class indice extends clsDetalhe
{
  var $titulo;

  /**
   * Atributos de dados
   */
  var $cod_servidor_alocacao = null;
  var $ref_cod_servidor = null;
  var $ref_cod_instituicao = null;
  var $ref_cod_servidor_funcao = null;
  var $ref_cod_funcionario_vinculo = null;
  var $ano = null;
  var $data_admissao = null;
  var $data_saida = null;
  /**
   * Implementação do método Gerar()
   */
  function Gerar()
  {
    $this->titulo = 'Servidor alocação - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->cod_servidor_alocacao = $_GET['cod_servidor_alocacao'];

    $tmp_obj = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, $this->ref_cod_instituicao);

    $registro = $tmp_obj->detalhe();

    if (!$registro) {
        $this->simpleRedirect('educar_servidor_lst.php');
    }

    $this->ref_cod_servidor            = $registro['ref_cod_servidor'];
    $this->ref_cod_instituicao         = $registro['ref_ref_cod_instituicao'];
    $this->ref_cod_servidor_funcao     = $registro['ref_cod_servidor_funcao'];
    $this->data_admissao               = $registro['data_admissao'];
    $this->data_saida                  = $registro['data_saida'];
    $this->ref_cod_funcionario_vinculo = $registro['ref_cod_funcionario_vinculo'];
    $this->ano                         = $registro['ano'];

    //Nome do servidor
    $fisica = new clsPessoaFisica($this->ref_cod_servidor);
    $fisica = $fisica->detalhe();

    $this->addDetalhe(array("Servidor", "{$fisica["nome"]}"));

    //Escola
    $escola = new clsPmieducarEscola($registro['ref_cod_escola']);
    $escola = $escola->detalhe();

    $this->addDetalhe(array("Escola", "{$escola["nome"]}"));

    //Ano
    $this->addDetalhe(array("Ano", "{$registro['ano']}"));

    //Periodo
    $periodo = array(
      1  => 'Matutino',
      2  => 'Vespertino',
      3  => 'Noturno'
    );

    $this->addDetalhe(array("Periodo", "{$periodo[$registro['periodo']]}"));

    //Carga horária
    $this->addDetalhe(array("Carga horária", "{$registro['carga_horaria']}"));

    //Função
    if ($this->ref_cod_servidor_funcao) {
      $funcaoServidor = new clsPmieducarServidorFuncao(null, null, null, null, $this->ref_cod_servidor_funcao);
      $funcaoServidor = $funcaoServidor->detalhe();

      $funcao = new clsPmieducarFuncao($funcaoServidor['ref_cod_funcao']);
      $funcao = $funcao->detalhe();

      $this->addDetalhe(array("Função", "{$funcao['nm_funcao']}"));
    }

    //Vinculo
    if ($this->ref_cod_funcionario_vinculo) {
      $funcionarioVinculo = new clsPortalFuncionario();
      $funcionarioVinculo = $funcionarioVinculo->getNomeVinculo($registro['ref_cod_funcionario_vinculo']);

      $this->addDetalhe(array("Vinculo", "{$funcionarioVinculo}"));
    }

    if (!empty($this->data_admissao)) {
      $this->addDetalhe(array("Data de admissão", Portabilis_Date_Utils::pgSQLToBr($this->data_admissao)));
    }

    if (!empty($this->data_saida)) {
      $this->addDetalhe(array("Data de saída", Portabilis_Date_Utils::pgSQLToBr($this->data_saida)));
    }

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {

      $this->url_novo   = "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
      $this->url_editar = "educar_servidor_alocacao_cad.php?cod_servidor_alocacao={$this->cod_servidor_alocacao}";
    }

    $this->url_cancelar = "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
    $this->largura = '100%';

    $this->breadcrumb('Detalhe da alocação', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
  }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
