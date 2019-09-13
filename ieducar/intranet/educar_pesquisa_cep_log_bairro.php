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

use Illuminate\Support\Facades\Session;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Listagem de Ruas');
    $this->processoAp         = 0;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

/**
 * miolo1 class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class miolo1 extends clsListagem
{
  var $funcao_js = 'cv_libera_campos(\'cep_\', \'ref_sigla_uf_\', \'cidade\', \'nm_bairro\', \'ref_idtlog\', \'nm_logradouro\', \'isEnderecoExterno\', \'zona_localizacao\')';

  function Gerar()
  {
    Session::put([
        'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
        'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
        'campo3' => $_GET['campo3'] ?? Session::get('campo3'),
        'campo4' => $_GET['campo4'] ?? Session::get('campo4'),
        'campo5' => $_GET['campo5'] ?? Session::get('campo5'),
        'campo6' => $_GET['campo6'] ?? Session::get('campo6'),
        'campo7' => $_GET['campo7'] ?? Session::get('campo7'),
        'campo8' => $_GET['campo8'] ?? Session::get('campo8'),
        'campo9' => $_GET['campo9'] ?? Session::get('campo9'),
        'campo10' => $_GET['campo10'] ?? Session::get('campo10'),
        'campo11' => $_GET['campo11'] ?? Session::get('campo11'),
        'campo12' => $_GET['campo12'] ?? Session::get('campo12'),
        'campo13' => $_GET['campo13'] ?? Session::get('campo13'),
        'campo14' => $_GET['campo14'] ?? Session::get('campo14'),
    ]);
    Session::save();
    Session::start();

    $this->nome = 'form1';

    $this->funcao_js = sprintf(
      'cv_libera_campos(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
      Session::get('campo10'), Session::get('campo11'), Session::get('campo7'),
      Session::get('campo1'), Session::get('campo12'), Session::get('campo4'),
      Session::get('campo9'), Session::get('campo14')
    );

    $this->titulo = 'Endereço';

    // Paginador
    $limite = 7;
    $iniciolimit = $_GET['pagina_' . $this->nome] ?
      ($_GET['pagina_' . $this->nome] * $limite - $limite) : 0;

    // Filtros
    $this->campoTexto('nm_bairro', 'Bairro', $_GET['nm_bairro'], 40, 255);
    $this->campoCep('nr_cep', 'CEP', $_GET['nr_cep']);
    $this->campoTexto('nm_logradouro', 'Logradouro', $_GET['nm_logradouro'], 40, 255);
    $this->campoTexto('cidade', 'Cidade', $_GET['cidade'], 40, 60);

    // uf

    $defaultProvince = isset($_GET['ref_sigla_uf']) ? $_GET['ref_sigla_uf'] : config('legacy.app.locale.province');

    $options = array(
      'required' => false,
      'label'    => 'Estado',
      'value'    => $defaultProvince
    );

    $helperOptions = array(
      'attrName' => 'ref_sigla_uf'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    $this->addCabecalhos(array('Bairro', 'CEP', 'Logradouro', 'UF', 'Cidade'));


    // consulta dados

    $pre_select = '
      SELECT
        c.idlog, c.cep, c.idbai, u.sigla_uf, m.nome, t.idtlog, m.idmun, b.zona_localizacao ';

    $select = '
      FROM
        urbano.cep_logradouro_bairro c, public.bairro b, public.logradouro l,
        public.municipio m, public.uf u, urbano.tipo_logradouro t
      WHERE
        c.idlog = l.idlog AND
        c.idbai = b.idbai AND
        l.idmun = b.idmun AND
        l.idmun = m.idmun AND
        l.idtlog = t.idtlog AND
        m.sigla_uf = u.sigla_uf';

    $params = array();

    if (isset($_GET['nr_cep']))
      $params['c.cep'] = idFederal2int($_GET['nr_cep']);

    if (isset($_GET['nm_bairro']))
      $params['b.nome'] = $_GET['nm_bairro'];

    if (isset($_GET['nm_logradouro']))
      $params['l.nome'] = $_GET['nm_logradouro'];

    if (isset($_GET['ref_sigla_uf']))
      $params['u.sigla_uf'] = $_GET['ref_sigla_uf'];

    if (isset($_GET['cidade']))
      $params['m.nome'] = $_GET['cidade'];

    $paramCount = 1;

    foreach ($params as $name => $value) {
      $select .= " AND $name::varchar ILIKE '%'||\$$paramCount||'%'";
      $paramCount++;
    }

    $total  = Portabilis_Utils_Database::selectField(' SELECT COUNT(0) '.$select, array('params' => array_values($params)));

    $select .= sprintf(' LIMIT %s OFFSET %s', $limite, $iniciolimit);

    $result = Portabilis_Utils_Database::fetchPreparedQuery($pre_select.$select, array('params' => array_values($params)));


    foreach ($result as $record) {
      list($idlog, $cep, $idbai, $uf, $cidade, $tipoLogradouroId, $id_mun, $zona) = $record;

      $cidade     = addslashes($cidade);

      $logradouro = new clsLogradouro($idlog);
      $logradouro = $logradouro->detalhe();
      $logradouro = addslashes($logradouro['nome']);

      $bairro     = new clsBairro($idbai);
      $bairro     = $bairro->detalhe();
      $bairro     = addslashes($bairro['nome']);

      $cep2  = int2CEP($cep);
      $s_end = '0';

      $url = sprintf(
        '<a href="javascript:void(0);" onclick="cv_set_campo(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\');">%%s</a>',
        Session::get('campo1'), $bairro, Session::get('campo2'),
        $idbai, Session::get('campo3'), $cep,
        Session::get('campo4'), $logradouro,
        Session::get('campo5'), $idlog,
        Session::get('campo6'), $uf, Session::get('campo7'), $cidade,
        Session::get('campo8'), $tipoLogradouroId, Session::get('campo9'), $s_end,
        Session::get('campo10'), $cep2, Session::get('campo11'), $uf,
        Session::get('campo12'), Session::get('campo13'), $id_mun,
        Session::get('campo14'), $zona
      );

      $this->addLinhas(array(
        sprintf($url, $bairro),
        sprintf($url, $cep2),
        sprintf($url, $logradouro),
        sprintf($url, $uf),
        sprintf($url, $cidade)
      ));
    }

    $this->largura = '100%';
    $this->addPaginador2('educar_pesquisa_cep_log_bairro.php', $total, $_GET,
      $this->nome, $limite);

/*
    if ($_GET['param']) {
      $this->rodape = '
        <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
          <tr width="100%">
            <td>
              <div align="center">[ <a href="javascript:void(0);" onclick="liberaCamposOuvidoria()">Cadastrar Novo Endereço</a> ]</div>
            </td>
          </tr>
        </table>';
    }
    else {
      $this->rodape = sprintf('
        <table border="0" cellspacing="0" cellpadding="0" width="100%%" align="center">
          <tr width="100%%">
            <td>
              <div align="center">[ <a href="javascript:void(0);" onclick="%s">Cadastrar Novo Endereço</a> ]</div>
            </td>
          </tr>
        </table>',
        $this->funcao_js
      );
    }*/
  }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new miolo1();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
function setFiltro()
{
  alert('filtro');
}

// Função específica para ouvidoria
function setaCamposOuvidoria(valor1, valor2, valor3, valor4, valor5, valor6,
  valor7, valor8, valor9, valor10, valor11, valor12)
{
  // Campo oculto flag atualiza
  parent.document.getElementById('atualiza').value  = 'false';
  parent.document.getElementById('nendereco').value = 'false';

  // Campo oculto cep
  obj1 = parent.document.getElementById('cep');
  obj1.value = valor1;

  // Campo visível cep
  obj2 = parent.document.getElementById('cep_');
  obj2.value    = valor2;
  obj2.disabled = true;

  // Campo oculto sigla_uf
  obj3 = parent.document.getElementById('sigla_uf');
  obj3.value = valor3;

   // Campo visível sigla_uf
  obj4 = parent.document.getElementById('sigla_uf_');
  obj4.value    = valor4;
  obj4.disabled = true;

  // Campo oculto cidade
  obj5 = parent.document.getElementById('cidade');
  obj5.value = valor5;

   // Campo visível cidade
  obj6 = parent.document.getElementById('cidade_');
  obj6.value    = valor6;
  obj6.disabled = true;

  // Campo oculto nmCidade
  obj14 = parent.document.getElementById('nmCidade');
  obj14.value = valor6;

  // Campo oculto Bairro
  obj7 = parent.document.getElementById('idbai');
  obj7.value = valor7;

   // Campo visível Bairro
  obj8 = parent.document.getElementById('bairro_');
  obj8.value    = valor8;
  obj8.disabled = true;

  obj13 = parent.document.getElementById('bairro');
  obj13.value = valor8;

  // Campo oculto idtlog ("tipo logradouro")
  obj9 = parent.document.getElementById('idtlog');
  obj9.value = valor9;

  // Campo visível idtlog_ ("tipo logradouro")
  obj10 = parent.document.getElementById('idtlog_');
  obj10.value    = valor10;
  obj10.disabled = true;

  // Campo oculto logradouro
  obj11 = parent.document.getElementById('idlog');
  obj11.value = valor11;

  // Campo visível logradouro
  obj12 = parent.document.getElementById('logradouro_');
  obj12.value    = valor12;
  obj12.disabled = true;

  obj14 = parent.document.getElementById('logradouro');
  obj14.value = valor12;

  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}

function liberaCamposOuvidoria()
{
  parent.document.getElementById('atualiza').value  = 'false';
  parent.document.getElementById('nendereco').value = 'true';

  // Campo oculto cep
  obj1 = parent.document.getElementById('cep');
  obj1.value = null;

   // Campo visível cep
  obj2 = parent.document.getElementById('cep_');
  obj2.value    = null;
  obj2.disabled = false;

  // Campo oculto sigla_uf
  obj3 = parent.document.getElementById('sigla_uf');
  obj3.value = null;

   // Campo visível sigla_uf
  obj4 = parent.document.getElementById('sigla_uf_');
  obj4.value    = null;
  obj4.disabled = false;

  // Campo oculto cidade
  obj5 = parent.document.getElementById('cidade');
  obj5.value = null;

  // Campo visível cidade
  obj6 = parent.document.getElementById('cidade_');
  obj6.value    = null;
  obj6.disabled = false;

  // Campo oculto bairro
  obj7 = parent.document.getElementById('idbai');
  obj7.value = null;

   // Campo visível bairro
  obj8 = parent.document.getElementById('bairro_');
  obj8.value    = null;
  obj8.disabled = false;

  obj13 = parent.document.getElementById('bairro');
  obj13.value = null;

  // Campo oculto idtlog ("tipo logradouro")
  obj9 = parent.document.getElementById('idtlog');
  obj9.value = null;

  // Campo visível itlog_ ("tipo logradouro")
  obj10 = parent.document.getElementById('idtlog_');
  obj10.value    = null;
  obj10.disabled = false;

  // Campo oculto logradouro
  obj11 = parent.document.getElementById('idlog');
  obj11.value = null;

  // Campo visível logradouro_
  obj12 = parent.document.getElementById('logradouro_');
  obj12.value    = null;
  obj12.disabled = false;

  obj14 = parent.document.getElementById('logradouro');
  obj14.value = null;

  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}
</script>
