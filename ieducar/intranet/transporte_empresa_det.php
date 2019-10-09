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
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesEmpresaTransporteEscolar.inc.php';

require_once 'Portabilis/View/Helper/Application.php';


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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Empresas');
    $this->processoAp = 21235;
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

  function Gerar()
  {
    // Verificação de permissão para cadastro.
    $this->obj_permissao = new clsPermissoes();

    $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

    $this->titulo = 'Empresa transporte escolar - Detalhe';


    $cod_empresa_transporte_escolar = $_GET['cod_empresa'];

    $tmp_obj = new clsModulesEmpresaTransporteEscolar($cod_empresa_transporte_escolar);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('transporte_empresa_lst.php');
    }

    $objPessoaJuridica = new clsPessoaJuridica();
    list ($id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $ins_est) = $objPessoaJuridica->queryRapida($registro['ref_idpes'], "cnpj","logradouro","cep","bairro","cidade", "ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax", "email","insc_estadual");

    $this->addDetalhe( array("Código da empresa", $cod_empresa_transporte_escolar));
    $this->addDetalhe( array("Nome fantasia", $registro['nome_empresa']) );
    $this->addDetalhe( array("Nome do responsável", $registro['nome_responsavel']) );
    $this->addDetalhe( array("CNPJ", int2CNPJ($id_federal)) );
    $this->addDetalhe( array("Endere&ccedil;o", $endereco) );
    $this->addDetalhe( array("CEP", $cep) );
    $this->addDetalhe( array("Bairro", $nm_bairro) );
    $this->addDetalhe( array("Cidade", $cidade) );
    if (trim($telefone_1)!='')
      $this->addDetalhe( array("Telefone 1", "({$ddd_telefone_1}) {$telefone_1}") );
    if (trim($telefone_2)!='')
      $this->addDetalhe( array("Telefone 2", "({$ddd_telefone_2}) {$telefone_2}") );
    if (trim($telefone_mov)!='')
      $this->addDetalhe( array("Celular", "({$ddd_telefone_mov}) {$telefone_mov}") );
    if (trim($telefone_fax)!='')
      $this->addDetalhe( array("Fax", "({$ddd_telefone_fax}) {$telefone_fax}") );

    $this->addDetalhe( array("E-mail", $email) );

    if( ! $ins_est ) $ins_est = "isento";
      $this->addDetalhe( array("Inscri&ccedil;&atilde;o estadual", $ins_est) );
    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']));
    $this->url_cancelar = "transporte_empresa_lst.php";

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21235, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Empresa";
      $this->url_editar = "../module/TransporteEscolar/Empresa?id={$cod_empresa_transporte_escolar}";
    }

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""                                  => "Detalhe da empresa de transporte"
    ));
    $this->enviaLocalizacao($localizacao->montar());
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
