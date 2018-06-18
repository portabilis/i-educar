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
 *
 * @category  i-Educar
 *
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 *
 * @package   Ied_Public
 *
 * @since     ?
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicDistrito.inc.php';
require_once('include/pmieducar/geral.inc.php');
require_once('include/modules/clsModulesAuditoriaGeral.inc.php');
require_once 'App/Model/Pais.php';
require_once 'App/Model/NivelAcesso.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
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
        $this->SetTitulo($this->_instituicao . ' Distrito');
        $this->processoAp = 759;
        $this->addEstilo('localizacaoSistema');
    }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
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
class indice extends clsCadastro
{
    /**
     * Referência a usuário da sessão.
     *
     * @var int
     */
    public $pessoa_logada;

    public $idmun;
    public $geom;
    public $iddis;
    public $nome;
    public $cod_ibge;
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

    public function Inicializar()
    {
        $retorno = 'Novo';
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->iddis = $_GET['iddis'];

        if (is_numeric($this->iddis)) {
            $obj_distrito = new clsPublicDistrito();
            $lst_distrito = $obj_distrito->lista(
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
          null,
          null,
          null,
          null,
          null,
          $this->iddis
      );

            if ($lst_distrito) {
                $registro = $lst_distrito[0];
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ?
      'public_distrito_det.php?iddis=' . $registro['iddis'] :
      'public_distrito_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_enderecamento_index.php'    => 'Endereçamento',
         ''        => "{$nomeMenu} distrito"
    ]);
        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('iddis', $this->iddis);

        // foreign keys
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
            echo '<!--\nErro\nClasse clsPais nao encontrada\n-->';
            $opcoes = ['' => 'Erro na geracao'];
        }
        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];
        if (class_exists('clsUf')) {
            if ($this->idpais) {
                $objTemp = new clsUf();

                $lista = $objTemp->lista(false, false, $this->idpais, false, false, 'nome ASC');

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes[$registro['sigla_uf']] = $registro['nome'];
                    }
                }
            }
        } else {
            echo '<!--\nErro\nClasse clsUf nao encontrada\n-->';
            $opcoes = ['' => 'Erro na geracao'];
        }

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf);

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
            echo '<!--\nErro\nClasse clsMunicipio nao encontrada\n-->';
            $opcoes = ['' => 'Erro na geracao'];
        }

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);

        $this->campoTexto('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7, null, null, null, 'Somente números');
    }

    public function Novo()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicDistrito(
        $this->idmun,
        null,
        null,
        $this->nome,
        null,
      null,
        'U',
        $this->pessoa_logada,
        null,
        'I',
        null,
        9,
      $this->cod_ibge
    );

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $enderecamento = new clsPublicDistrito();
            $enderecamento->iddis = $cadastrou;
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Distrito', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: public_distrito_lst.php');
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
        echo "<!--\nErro ao cadastrar clsPublicBairro\nvalores obrigatorios\nis_numeric( $this->idmun ) && is_string( $this->nome ) && is_string( $this->origem_gravacao ) && is_string( $this->operacao ) && is_numeric( $this->idsis_cad )\n-->";

        return false;
    }

    public function Editar()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $enderecamentoDetalhe = new clsPublicDistrito(null, null, $this->iddis);
        $enderecamentoDetalhe->cadastrou = $this->iddis;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicDistrito(
        $this->idmun,
        null,
        $this->iddis,
        $this->nome,
      $this->pessoa_logada,
        null,
        'U',
        null,
        null,
        'I',
        null,
        9,
      $this->cod_ibge
    );

        $editou = $obj->edita();
        if ($editou) {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Distrito', $this->pessoa_logada, $this->iddis);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            header('Location: public_distrito_lst.php');
            die();
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
        echo "<!--\nErro ao editar clsPublicDistrito\nvalores obrigatorios\nif( is_numeric( $this->iddis ) )\n-->";

        return false;
    }

    public function Excluir()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicDistrito(null, null, $this->iddis, null, $this->pessoa_logada);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            header('Location: public_distrito_lst.php');
            die();
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
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
<script type='text/javascript'>
document.getElementById('idpais').onchange = function()
{
  var campoPais = document.getElementById('idpais').value;

  var campoUf= document.getElementById('sigla_uf');
  campoUf.length = 1;
  campoUf.disabled = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax( getUf );
  xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
}

function getUf(xml_uf)
{
  var campoUf = document.getElementById('sigla_uf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data,
        DOM_array[i].getAttribute('sigla_uf'), false, false);
    }
  }
  else {
    campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
}

document.getElementById('sigla_uf').onchange = function()
{
  var campoUf = document.getElementById('sigla_uf').value;

  var campoMunicipio= document.getElementById('idmun');
  campoMunicipio.length = 1;
  campoMunicipio.disabled = true;
  campoMunicipio.options[0].text = 'Carregando município...';

  var xml_municipio = new ajax(getMunicipio);
  xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
}

function getMunicipio(xml_municipio)
{
  var campoMunicipio = document.getElementById('idmun');
  var DOM_array = xml_municipio.getElementsByTagName('municipio');

  if(DOM_array.length) {
    campoMunicipio.length = 1;
    campoMunicipio.options[0].text = 'Selecione um município';
    campoMunicipio.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoMunicipio.options[campoMunicipio.options.length] = new Option(DOM_array[i].firstChild.data,
        DOM_array[i].getAttribute('idmun'), false, false);
    }
  }
  else {
    campoMunicipio.options[0].text = 'O estado não possui nenhum município';
  }
}
</script>