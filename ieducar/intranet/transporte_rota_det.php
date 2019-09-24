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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesPontoTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesVeiculo.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';

require_once 'Portabilis/Date/Utils.php';
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Rotas');
    $this->processoAp = 21238;
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

    $this->titulo = 'Rota - Detalhe';


    $cod_rota_transporte_escolar = $_GET['cod_rota'];

    $tmp_obj = new clsModulesRotaTransporteEscolar($cod_rota_transporte_escolar);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('transporte_rota_lst.php');
    }

    $this->addDetalhe( array("Ano", $registro['ano']) );
    $this->addDetalhe( array("Código da rota", $cod_rota_transporte_escolar));
    $this->addDetalhe( array("Descrição", $registro['descricao']) );
    $this->addDetalhe( array("Destino", $registro['nome_destino']));
    $this->addDetalhe( array("Empresa", $registro['nome_empresa']));
    $this->addDetalhe( array("Tipo da rota", ($registro['tipo_rota'] == 'U' ? 'Urbana' : 'Rural' )));
    if (trim($registro['km_pav'])!='')
      $this->addDetalhe( array("Percurso pavimentado", $registro['km_pav'].' km'));
    if (trim($registro['km_npav'])!='')
      $this->addDetalhe( array("Percurso não pavimentado", $registro['km_npav'].' km'));

    $this->addDetalhe( array("Terceirizado", ($registro['tercerizado'] == 'S' ? 'Sim' : 'Não' )));

    // Itinerário

      $obj = new clsModulesItinerarioTransporteEscolar();
      $obj->setOrderby('seq ASC');
      $lst = $obj->lista(null, $cod_rota_transporte_escolar );

      if ($lst) {
        $tabela = '
          <table>
          <tr colspan=\'5\'><td><a style=\' text-decoration: underline;\' href=\'/intranet/transporte_itinerario_cad.php?cod_rota='.$cod_rota_transporte_escolar.'\'>Editar itinerário</a></td></tr>
            <tr align="center">
              <td bgcolor="#ccdce6"><b>Sequencial</b></td>
              <td bgcolor="#ccdce6"><b>Ponto</b></td>
              <td bgcolor="#ccdce6"><b>Hora</b></td>
              <td bgcolor="#ccdce6"><b>Tipo</b></td>
              <td bgcolor="#ccdce6"><b>Veículo</b></td>
            </tr>';

        $cont = 0;

        foreach ($lst as $valor) {
          if (($cont % 2) == 0) {
            $color = ' bgcolor="#f5f9fd" ';
          }
          else {
            $color = ' bgcolor="#FFFFFF" ';
          }

          $obj_veiculo = new clsModulesVeiculo($valor['ref_cod_veiculo']);
          $obj_veiculo = $obj_veiculo->detalhe();

          $motorista = new clsModulesMotorista($obj_veiculo['ref_cod_motorista']);
          $motorista = $motorista->detalhe();

          $valor_veiculo = $obj_veiculo['descricao']==''?'':$obj_veiculo['descricao'].' - Placa: '.$obj_veiculo['placa'] . ' - Motorista: ' . $motorista['nome_motorista'];

          $obj_ponto = new clsModulesPontoTransporteEscolar($valor['ref_cod_ponto_transporte_escolar']);
          $obj_ponto = $obj_ponto->detalhe();
          $valor_ponto = $obj_ponto['descricao'];

          $tabela .= sprintf('
            <tr>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
            </tr>',
            $color, $valor['seq'], $color, $valor_ponto, $color, $valor['hora'],$color, ($valor['tipo'] == 'V' ? 'Volta' : 'Ida'),$color, $valor_veiculo
          );

          $cont++;
        }

        $tabela .= '</table>';


    }
    if ($tabela) {
      $this->addDetalhe(array('Itinerário', $tabela));
    } else{
      $this->addDetalhe(array('Itinerário', '<a style=\' text-decoration: underline; font-size: 12px;\' href=\'/intranet/transporte_itinerario_cad.php?cod_rota='.$cod_rota_transporte_escolar.'\'>Editar itinerário</a>'));
    }

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21238, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Rota";
      $this->url_editar = "../module/TransporteEscolar/Rota?id={$cod_rota_transporte_escolar}";
    }

    $this->url_cancelar = "transporte_rota_lst.php";

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""                                  => "Detalhe da rota"
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
