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
 *
 * @category  i-Educar
 *
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 *
 * @package   Ied_Public
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicDistrito.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Public
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Bairro');
        $this->processoAp = 756;
        $this->addEstilo('localizacaoSistema');
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Public
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
    public $pessoa_logada;
    public $__titulo;
    public $__limite;
    public $__offset;

    public $idmun;
    public $geom;
    public $idbai;
    public $nome;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idsis_rev;
    public $idsis_cad;

    public $idpais;
    public $sigla_uf;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->__titulo = 'Bairro - Listagem';

        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addBanner(
        'imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg',
        'Intranet'
    );

        $this->addCabecalhos([
      'Nome',
      'Zona Localização',
      'Município',
      'Estado',
      'Pais'
    ]);

        // Filtros de Foreign Keys
        $opcoes = ['' => 'Selecione'];

        if (class_exists('clsPais')) {
            $objTemp = new clsPais();
            $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['idpais']] = $registro['nome'];
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPais nao encontrada\n-->";
            $opcoes = ['' => 'Erro na geração'];
        }

        $this->campoLista(
        'idpais',
        'Pais',
        $opcoes,
        $this->idpais,
        '',
        false,
        '',
      '',
        false,
        false
    );

        $opcoes = ['' => 'Selecione'];

        if (class_exists('clsUf')) {
            if ($this->idpais) {
                $objTemp = new clsUf();
                $lista = $objTemp->lista(
            false,
            false,
            $this->idpais,
            false,
            false,
          'nome ASC'
        );

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes[$registro['sigla_uf']] = $registro['nome'];
                    }
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
            $opcoes = ['' => 'Erro na geração'];
        }

        $this->campoLista(
        'sigla_uf',
        'Estado',
        $opcoes,
        $this->sigla_uf,
        '',
        false,
      '',
        '',
        false,
        false
    );

        $opcoes = ['' => 'Selecione'];

        if (class_exists('clsMunicipio')) {
            if ($this->sigla_uf) {
                $objTemp = new clsMunicipio();
                $lista = $objTemp->lista(
            false,
            $this->sigla_uf,
            false,
            false,
            false,
          false,
            false,
            false,
            false,
            false,
            false,
            'nome ASC'
        );

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes[$registro['idmun']] = $registro['nome'];
                    }
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsMunicipio nao encontrada\n-->";
            $opcoes = ['' => 'Erro na geração'];
        }

        $this->campoLista(
        'idmun',
        'Município',
        $opcoes,
        $this->idmun,
        '',
        false,
      '',
        '',
        false,
        false
    );

        if (class_exists('clsPublicDistrito')) {
            if ($this->idmun) {
                $objTemp = new clsPublicDistrito();
                $objTemp->setOrderby(' nome ASC ');

                $lista = $objTemp->lista($this->idmun);

                if (is_array($lista) && count($lista)) {
                    $opcoesTemp = ['' => 'Selecione'];
                    foreach ($lista as $registro) {
                        $opcoesTemp[$registro['iddis']] = $registro['nome'];
                    }
                } else {
                    $opcoesTemp = ['' => 'Não existem distritos para este município.'];
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPublicDistrito nao encontrada\n-->";
            $opcoesTemp = ['' => 'Erro na geração'];
        }

        $this->campoLista(
        'iddis',
        'Distrito',
        $opcoesTemp,
        $this->iddis,
        '',
        false,
      '',
        '',
        false,
        false
    );

        // Outros filtros
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET['pagina_' . $this->nome]) ?
      ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;

        $obj_bairro = new clsPublicBairro();
        $obj_bairro->setOrderby('nome ASC');
        $obj_bairro->setLimite($this->__limite, $this->__offset);

        $lista = $obj_bairro->lista(
      $this->idmun,
      null,
      $this->nome,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      $this->idpais,
      $this->sigla_uf,
      null,
      null,
      $this->iddis
    );

        $total = $obj_bairro->_total;

        // Zona Localização.
        $zona = App_Model_ZonaLocalizacao::getInstance();

        // UrlHelper.
        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $options = ['query' => ['idbai' => null]];

        // Monta a lista.
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $zl = $zona->getValue($registro['zona_localizacao']);
                $options['query']['idbai'] = $registro['idbai'];

                $this->addLinhas([
          $url->l($registro['nome'], 'public_bairro_det.php', $options),
          $url->l($zl, 'public_bairro_det.php', $options),
          $url->l($registro['nm_municipio'], 'public_bairro_det.php', $options),
          $url->l($registro['nm_estado'], 'public_bairro_det.php', $options),
          $url->l($registro['nm_pais'], 'public_bairro_det.php', $options)
        ]);
            }
        }

        $this->addPaginador2('public_bairro_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(756, $this->pessoa_logada, 7, null, true)) {
            $this->acao      = 'go("public_bairro_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_enderecamento_index.php'    => 'Endereçamento',
         ''                                  => 'Listagem de bairros'
    ]);
        $this->enviaLocalizacao($localizacao->montar());
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('idpais').onchange = function()
{
  var campoPais = document.getElementById('idpais').value;

  var campoUf= document.getElementById('sigla_uf');
  campoUf.length = 1;
  campoUf.disabled        = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax(getUf);
  xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
}

function getUf(xml_uf)
{
  var campoUf   = document.getElementById('sigla_uf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length          = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoUf.options[campoUf.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('sigla_uf'),
        false, false
      );
    }
  }
  else {
    campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
}

document.getElementById('sigla_uf').onchange = function()
{
  var campoUf = document.getElementById('sigla_uf').value;

  var campoMunicipio      = document.getElementById('idmun');
  campoMunicipio.length   = 1;
  campoMunicipio.disabled = true;

  campoMunicipio.options[0].text = 'Carregando município...';

  var xml_municipio = new ajax(getMunicipio);
  xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
}

function getMunicipio(xml_municipio)
{
  var campoMunicipio = document.getElementById('idmun');
  var DOM_array      = xml_municipio.getElementsByTagName( "municipio" );

  if (DOM_array.length) {
    campoMunicipio.length          = 1;
    campoMunicipio.options[0].text = 'Selecione um município';
    campoMunicipio.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoMunicipio.options[campoMunicipio.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('idmun'),
        false, false
      );
    }
  }
  else {
    campoMunicipio.options[0].text = 'O estado não possui nenhum município';
  }
}

document.getElementById('idmun').onchange = function()
{
  var campoMunicipio = document.getElementById('idmun').value;

  var campoDistrito      = document.getElementById('iddis');
  campoDistrito.length   = 1;
  campoDistrito.disabled = true;

  campoDistrito.options[0].text = 'Carregando distritos...';

  var xml_distrito = new ajax(getDistrito);
  xml_distrito.envia('public_distrito_xml.php?idmun=' + campoMunicipio);
}

function getDistrito(xml_distrito)
{
  var campoDistrito = document.getElementById('iddis');
  var DOM_array      = xml_distrito.getElementsByTagName( "distrito" );
  console.log(DOM_array);

  if (DOM_array.length) {
    campoDistrito.length          = 1;
    campoDistrito.options[0].text = 'Selecione um distrito';
    campoDistrito.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoDistrito.options[campoDistrito.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('iddis'),
        false, false
      );
    }
  }
  else {
    campoDistrito.options[0].text = 'O município não possui nenhum distrito';
  }
}
</script>