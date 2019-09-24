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
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Usuários de transporte');
    $this->processoAp = 21240;
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

    $this->titulo = 'Usuário de transporte - Detalhe';


    $cod_pt = $_GET['cod_pt'];

    $tmp_obj = new clsModulesPessoaTransporte($cod_pt);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('transporte_empresa_lst.php');
    }

    $this->addDetalhe( array("Código", $cod_pt));
    $this->addDetalhe( array("Pessoa", $registro['nome_pessoa']) );
    $this->addDetalhe( array("Rota", $registro['nome_rota']) );
    $this->addDetalhe( array("Destino", (trim($registro['nome_destino'])=='' ? $registro['nome_destino2'] : $registro['nome_destino'])) );
    $this->addDetalhe( array("Ponto de embarque", $registro['nome_ponto'] ));
    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']) );

    $turnos = [
      1 => 'Matutino',
      2 => 'Vespertino',
      3 => 'Noturno',
      4 => 'Integral',
      5 => 'Matutino e vespertino',
      6 => 'Matutino e noturno',
      7 => 'Vespertino e noturno'
    ];
    $nm_turno = $turnos[$registro['turno']] ?? '';
    $this->addDetalhe( array("Turno", $nm_turno) );
    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21240, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Pessoatransporte";
      $this->url_editar = "../module/TransporteEscolar/Pessoatransporte?id={$cod_pt}";
    }

    $this->url_cancelar = "transporte_pessoa_lst.php";

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""                                  => "Detalhe do usu&aacute;rio de transporte"
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
