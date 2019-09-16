<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Município");
        $this->processoAp = 755;
    }
}

class indice extends clsListagem
{
    public $__limite;
    public $__offset;
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

    public function Gerar()
    {
        $this->__titulo = 'Município - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Estado'
        ]);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $objTemp = new clsUf();
            $lista = $objTemp->lista(false, false, $this->idpais, false, false, 'nome ASC');

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
                }
            }
        }

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf, '', false, '', '', false, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $obj_municipio = new clsPublicMunicipio();
        $obj_municipio->setOrderby('nome ASC');
        $obj_municipio->setLimite($this->__limite, $this->__offset);

        $lista = $obj_municipio->lista(
            $this->nome,
            $this->sigla_uf
        );

        $total = $obj_municipio->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_sigla_uf = new clsUf($registro['sigla_uf']);
                $det_sigla_uf = $obj_sigla_uf->detalhe();
                $registro['sigla_uf'] = $det_sigla_uf['nome'];

                $this->addLinhas([
                    "<a href=\"public_municipio_det.php?idmun={$registro['idmun']}\">{$registro['nome']}</a>",
                    "<a href=\"public_municipio_det.php?idmun={$registro['idmun']}\">{$registro['sigla_uf']}</a>"
                ]);
            }
        }

        $this->addPaginador2('public_municipio_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(755, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_municipio_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de municípios', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script>
  document.getElementById('idpais').onchange = function () {
    var campoPais = document.getElementById('idpais').value;

    var campoUf = document.getElementById('sigla_uf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia("public_uf_xml.php?pais=" + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('sigla_uf');
    var DOM_array = xml_uf.getElementsByTagName("estado");

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("sigla_uf"), false, false);
      }
    } else
      campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
</script>
