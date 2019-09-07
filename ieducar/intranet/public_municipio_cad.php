<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'App/Model/Pais.php';
require_once 'App/Model/NivelAcesso.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Município');
        $this->processoAp = 755;
    }
}

class indice extends clsCadastro
{
    public $idmun;
    public $nome;
    public $sigla_uf;
    public $cod_ibge;
    public $geom;
    public $tipo;
    public $idpes_rev;
    public $idpes_cad;
    public $data_rev;
    public $data_cad;
    public $origem_gravacao;
    public $operacao;
    public $idpais;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->idmun = $_GET['idmun'];

        if (is_numeric($this->idmun)) {
            $obj = new clsPublicMunicipio($this->idmun);
            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_uf = new clsUf($this->sigla_uf);
                $det_uf = $obj_uf->detalhe();
                $this->idpais = $det_uf['idpais']->idpais;

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_municipio_det.php?idmun=' . $registro['idmun']
            : 'public_municipio_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} município", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idmun', $this->idmun);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idpais']] = $registro['nome'];
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $objTemp = new clsUf();
            $lista = $objTemp->lista(false, false, $this->idpais, false, false, 'nome ASC');

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['sigla_uf']] = $registro['nome'];
                }
            }
        }

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, true);
        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7);
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicMunicipio(
            null,
            $this->nome,
            $this->sigla_uf,
            null,
            null,
            null,
            $this->cod_ibge,
            null,
            'M',
            null,
            null,
            $this->pessoa_logada,
            null,
            null,
            'U',
            'I',
            null,
            9
        );

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $enderecamento = new clsPublicMunicipio($cadastrou);
            $enderecamento->cadastrou = $cadastrou;
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Municipio', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('public_municipio_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $enderecamentoDetalhe = new clsPublicMunicipio($this->idmun);
        $enderecamentoDetalhe->cadastrou = $this->idmun;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicMunicipio(
            $this->idmun,
            $this->nome,
            $this->sigla_uf,
            null,
            null,
            null,
            $this->cod_ibge,
            null,
            'M',
            null,
            $this->pessoa_logada,
            null,
            null,
            null,
            'U',
            'I',
            null,
            9
        );

        $editou = $obj->edita();

        if ($editou) {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Municipio', $this->pessoa_logada, $this->idmun);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('public_municipio_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicMunicipio(
            $this->idmun,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->pessoa_logada
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_municipio_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script type="text/javascript">
  document.getElementById('idpais').onchange = function () {
    var campoPais = document.getElementById('idpais').value;

    var campoUf = document.getElementById('sigla_uf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('sigla_uf');
    var DOM_array = xml_uf.getElementsByTagName('estado');

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(
          DOM_array[i].firstChild.data, DOM_array[i].getAttribute('sigla_uf'),
          false, false);
      }
    } else {
      campoUf.options[0].text = 'O pais não possui nenhum estado';
    }
  }
</script>
