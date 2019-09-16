<?php

$desvio_diretorio = '';

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/Geral.inc.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Pesquisa por CEP!");
        $this->processoAp = 0;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsListagem
{
    public function Gerar()
    {
        $this->addCabecalhos(['CEP', 'Logradouro', 'Bairro', 'Cidade']);
        $this->campoCep('cep', 'CEP', '');
        $this->campoTexto('logradouro', 'Logradouro', '', 30, 255);
        $this->campoTexto('cidade', 'Cidade', '', 30, 255);

        if ($_GET['busca'] == 'S') {
            $cep = @$_GET['cep'];
            $logradouro = @$_GET['logradouro'];
            $cidade = @$_GET['cidade'];
            $cep = idFederal2int($cep);
        }

        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

        if ($cidade && $logradouro) {
            $obj_mun = new clsMunicipio();
            $lista = $obj_mun->lista($cidade);

            $obj_logradouro = new clsLogradouro();
            $lista_logradouros = $obj_logradouro->lista(false, $logradouro, $lista[0]['idmun'], false, false);

            if ($lista_logradouros) {
                foreach ($lista_logradouros as $logradouro) {
                    $objCepLogBairro = new clsCepLogradouroBairro();
                    $listaCepLogBairro = $objCepLogBairro->lista($logradouro['idlog'], false, '', 'idlog', $iniciolimit, $limite);

                    if ($listaCepLogBairro) {
                        foreach ($listaCepLogBairro as $id => $juncao) {
                            $det_cepLog = $juncao['idlog']->detalhe();
                            $det_log = $det_cepLog['idlog']->detalhe();
                            $det_TLog = $det_log['idtlog']->detalhe();
                            $det_bai = $juncao['idbai']->detalhe();
                            $det_mun = $det_bai['idmun']->detalhe();
                            $det_uf = $det_mun['sigla_uf']->detalhe();
                            $cep_formatado = int2CEP($det_cepLog['cep']);
                            $funcao = "enviar( '{$det_cepLog['cep']}', '{$det_bai['idbai']}', '{$det_log['idlog']}', '{$det_mun['nome']}', '{$det_bai['nome']}', '{$det_log['nome']}', '{$det_uf['sigla_uf']}', '{$det_TLog['idtlog']}' )";
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cep_formatado}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_log['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_bai['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_mun['nome']}</a>"]);
                            $total = $juncao['total'];
                        }
                    }
                }
            }
        }

        if ($cep || (!$cep && !$logradouro && !$cidade)) {
            $obj_cepLogBai = new clsCepLogradouroBairro();
            $lst_cepLogBai = $obj_cepLogBai->lista(false, $cep, false, 'idlog', $iniciolimit, $limite);

            if ($lst_cepLogBai) {
                foreach ($lst_cepLogBai as $juncao) {
                    $det_bai = $juncao['idbai']->detalhe();
                    $det_mun = $det_bai['idmun']->detalhe();
                    $det_uf = $det_mun['sigla_uf']->detalhe();

                    $cepLogradouro = $juncao['idlog'];
                    $_logradouro = $cepLogradouro->detalhe();

                    if (!is_null($_logradouro['idlog'])) {
                        $_logradouro['idlog']->detalhe();
                    }

                    $_logradouro = $_logradouro['idlog'];
                    $cepFormatado = int2CEP($cepLogradouro->cep);

                    $funcao = "enviar( '{$cepLogradouro->cep}', '{$det_bai['idbai']}', '{$cepLogradouro->idlog}', '{$det_mun['nome']}', '{$det_bai['nome']}', '{$_logradouro->nome}', '{$det_uf['sigla_uf']}', '{$_logradouro->idtlog}' )";

                    $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cepFormatado}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$_logradouro->nome}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_bai['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_mun['nome']}</a>"]);
                    $total = $juncao['total'];
                }
            } else {
                $this->addLinhas(['Não existe nenhum resultado a ser apresentado.']);
            }
        } elseif ($logradouro) {
            $obj_logradouro = new clsLogradouro();
            $lista_logradouros = $obj_logradouro->lista(false, $logradouro, false, false, false);

            if ($lista_logradouros) {
                foreach ($lista_logradouros as $logradouro) {
                    $objCepLogBairro = new clsCepLogradouroBairro();
                    $listaCepLogBairro = $objCepLogBairro->lista($logradouro['idlog'], false, '', 'idlog', $iniciolimit, $limite);

                    if ($listaCepLogBairro) {
                        foreach ($listaCepLogBairro as $id => $juncao) {
                            $det_cepLog = $juncao['idlog']->detalhe();
                            $det_log = $det_cepLog['idlog']->detalhe();
                            $det_TLog = $det_log['idtlog']->detalhe();
                            $det_bai = $juncao['idbai']->detalhe();
                            $det_mun = $det_bai['idmun']->detalhe();
                            $det_uf = $det_mun['sigla_uf']->detalhe();
                            $cep_formatado = int2CEP($det_cepLog['cep']);
                            $funcao = "enviar( '{$det_cepLog['cep']}', '{$det_bai['idbai']}', '{$det_log['idlog']}', '{$det_mun['nome']}', '{$det_bai['nome']}', '{$det_log['nome']}', '{$det_uf['sigla_uf']}', '{$det_TLog['idtlog']}' )";
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cep_formatado}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_log['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_bai['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_mun['nome']}</a>"]);
                            $total = $juncao['total'];
                        }
                    }
                }
            }
        } elseif ($cidade) {
            $cidade = strtoupper(limpa_acentos($cidade));
            $obj_mun = new clsMunicipio();
            $lista = $obj_mun->lista($cidade);

            if ($lista) {
                foreach ($lista as $cidade) {
                    $det_uf = $cidade['sigla_uf']->detalhe();
                    $objBairro = new clsBairro();
                    $listaBairro = $objBairro->lista($cidade['idmun'], false);

                    if ($listaBairro) {
                        foreach ($listaBairro as $bairro) {
                            $objCepLogBairro = new clsCepLogradouroBairro();
                            $listaCepLogBairro = $objCepLogBairro->lista(false, false, $bairro['idbai'], 'idlog', $iniciolimit, $limite);

                            if ($listaCepLogBairro) {
                                foreach ($listaCepLogBairro as $id => $juncao) {
                                    $det_cepLog = $juncao['idlog']->detalhe();
                                    $det_log = $det_cepLog['idlog']->detalhe();
                                    $det_TLog = $det_log['idtlog']->detalhe();
                                    $cep_formatado = int2CEP($det_cepLog['cep']);

                                    if ($logradouro) {
                                        if (substr_count(strtolower($det_log['nome']), strtolower($logradouro)) > 0) {
                                            $funcao = "enviar( '{$det_cepLog['cep']}', '{$bairro['idbai']}', '{$det_log['idlog']}', '{$cidade['nome']}', '{$bairro['nome']}', '{$det_log['nome']}', '{$det_uf['sigla_uf']}', '{$det_TLog['idtlog']}' )";
                                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cep_formatado}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_log['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$bairro['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cidade['nome']}</a>"]);
                                            $total = $juncao['total'];
                                        }
                                    } else {
                                        $funcao = "enviar( '{$det_cepLog['cep']}', '{$bairro['idbai']}', '{$det_log['idlog']}', '{$cidade['nome']}', '{$bairro['nome']}', '{$det_log['nome']}', '{$det_uf['sigla_uf']}', '{$det_TLog['idtlog']}' )";
                                        $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cep_formatado}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$det_log['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$bairro['nome']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$cidade['nome']}</a>"]);
                                        $total = $juncao['total'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->addPaginador2('pesquisa_cep_lst.php', $total, $_GET, $this->nome, $limite);

        $funcao_js = 'pesquisa_libera_campos( \'cep_\', \'sigla_uf\', \'cidade\', \'bairro\', \'idtlog\', \'logradouro\', \'idbai\', \'idlog\' )';
        $this->rodape = "
                        <table border='0' cellspacing='0' cellpadding='0' width=\"100%\" align=\"center\">
                        <tr width='100%'>
                        <td>
                        <div align='center'>[ <a href='javascript:void(0);' onclick=\"{$funcao_js}\">Cadastrar Novo Endereço</a> ]</div>
                        </td>
                        </tr>
                        </table>";

        $this->largura = '100%';
    }
}

$pagina = new clsIndex();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script type="text/javascript">

  function pesquisa_libera_campos (campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8) {
    if (window.opener) {
      window.opener.document.getElementById(campo1).disabled = false;
      window.opener.document.getElementById(campo1).value = "";
      window.opener.document.getElementById(campo2).disabled = false;
      window.opener.document.getElementById(campo3).disabled = false;
      window.opener.document.getElementById(campo3).value = "";
      window.opener.document.getElementById(campo4).disabled = false;
      window.opener.document.getElementById(campo4).value = "";
      window.opener.document.getElementById(campo5).disabled = false;
      window.opener.document.getElementById(campo5).value = "";
      window.opener.document.getElementById(campo6).disabled = false;
      window.opener.document.getElementById(campo6).value = "";
      window.opener.document.getElementById(campo7).value = "";
      window.opener.document.getElementById(campo8).value = "";
      window.close();
    } else if (window.document) {
      parent.document.getElementById(campo1).disabled = false;
      parent.document.getElementById(campo1).value = "";
      parent.document.getElementById(campo2).disabled = false;
      parent.document.getElementById(campo3).disabled = false;
      parent.document.getElementById(campo3).value = "";
      parent.document.getElementById(campo4).disabled = false;
      parent.document.getElementById(campo4).value = "";
      parent.document.getElementById(campo5).disabled = false;
      parent.document.getElementById(campo5).value = "";
      parent.document.getElementById(campo6).disabled = false;
      parent.document.getElementById(campo6).value = "";
      parent.document.getElementById(campo7).value = "";
      parent.document.getElementById(campo8).value = "";
      parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
    }
  }

  try {
    window.onload = setTimeout("document.forms[0].elements[1].focus()", 1000);
  } catch (e) {

  }

  function enviar (cep, idbai, idlog, cidade, bairro, logradouro, estado, idtlog) {
    if (window.opener) {
      if (cep) {
        window.opener.document.getElementById('cep_').value = cep.substr(0, 5) + '-' + cep.substr(5);
        window.opener.document.getElementById('cep_').disabled = true;
        window.opener.document.getElementById('cidade').value = cidade;
        window.opener.document.getElementById('cidade').disabled = true;
        window.opener.document.getElementById('bairro').value = bairro;
        window.opener.document.getElementById('bairro').disabled = true;
        window.opener.document.getElementById('logradouro').value = logradouro;
        window.opener.document.getElementById('logradouro').disabled = true;
        window.opener.document.getElementById('numero').disabled = false;
        if (window.opener.document.getElementById('letra')) {
          window.opener.document.getElementById('letra').disabled = false;
        }
        if (window.opener.document.getElementById('complemento')) {
          window.opener.document.getElementById('complemento').disabled = false;
        }
        window.opener.document.getElementById('cep').value = cep.substr(0, 5) + '-' + cep.substr(5);
        window.opener.document.getElementById('cidade').value = cidade;
        window.opener.document.getElementById('bairro').value = bairro;
        window.opener.document.getElementById('logradouro').value = logradouro;
        window.opener.document.getElementById('sigla_uf').value = estado;
        window.opener.document.getElementById('idlog').value = idlog;
        window.opener.document.getElementById('idbai').value = idbai;
        opt_estado = 'sigla_uf_' + estado;
        selecionado = window.opener.document.getElementById(opt_estado);
        window.opener.document.getElementById('sigla_uf').disabled = true;
        opt_idtlog = 'idtlog_' + idtlog;
        window.opener.document.getElementById(opt_idtlog).selected = true;
        window.opener.document.getElementById('idtlog').disabled = true;
      } else {
        window.opener.document.getElementById('cep').value = '';
        window.opener.document.getElementById('idlog').value = '';
        window.opener.document.getElementById('idbai').value = '';
        window.opener.document.getElementById('cep_').disabled = false;
        window.opener.document.getElementById('cep_').value = '';
        window.opener.document.getElementById('cidade').disabled = false;
        window.opener.document.getElementById('cidade').value = '';
        window.opener.document.getElementById('bairro').disabled = false;
        window.opener.document.getElementById('bairro').value = '';
        window.opener.document.getElementById('sigla_uf').disabled = false;
        window.opener.document.getElementById('sigla_uf').value = '';
        window.opener.document.getElementById('logradouro').disabled = false
        window.opener.document.getElementById('logradouro').value = ''
        window.opener.document.getElementById('numero').disabled = false;
        window.opener.document.getElementById('numero').value = '';
        if (window.opener.document.getElementById('letra')) {
          window.opener.document.getElementById('letra').disabled = false;
          window.opener.document.getElementById('letra').value = '';
        }
        if (window.opener.document.getElementById('complemento')) {
          window.opener.document.getElementById('complemento').disabled = false;
          window.opener.document.getElementById('complemento').value = '';
        }
        window.opener.document.getElementById('idtlog').disabled = false;
        window.opener.document.getElementById('idtlog').value = '';
      }
      window.close();
    } else if (window.document) {
      if (cep) {
        parent.document.getElementById('cep_').value = cep.substr(0, 5) + '-' + cep.substr(5);
        parent.document.getElementById('cep_').disabled = true;
        parent.document.getElementById('cidade').value = cidade;
        parent.document.getElementById('cidade').disabled = true;
        parent.document.getElementById('bairro').value = bairro;
        parent.document.getElementById('bairro').disabled = true;
        parent.document.getElementById('logradouro').value = logradouro;
        parent.document.getElementById('logradouro').disabled = true;
        parent.document.getElementById('numero').disabled = false;
        if (parent.document.getElementById('letra')) {
          parent.document.getElementById('letra').disabled = false;
        }
        if (parent.document.getElementById('complemento')) {
          parent.document.getElementById('complemento').disabled = false;
        }
        parent.document.getElementById('cep').value = cep.substr(0, 5) + '-' + cep.substr(5);
        parent.document.getElementById('cidade').value = cidade;
        parent.document.getElementById('bairro').value = bairro;
        parent.document.getElementById('logradouro').value = logradouro;
        parent.document.getElementById('sigla_uf').value = estado;
        parent.document.getElementById('idlog').value = idlog;
        parent.document.getElementById('idbai').value = idbai;
        opt_estado = 'sigla_uf_' + estado;
        selecionado = parent.document.getElementById(opt_estado);
        parent.document.getElementById('sigla_uf').disabled = true;
        opt_idtlog = 'idtlog_' + idtlog;
        parent.document.getElementById(opt_idtlog).selected = true;
        parent.document.getElementById('idtlog').disabled = true;
      } else {
        parent.document.getElementById('cep').value = '';
        parent.document.getElementById('idlog').value = '';
        parent.document.getElementById('idbai').value = '';
        parent.document.getElementById('cep_').disabled = false;
        parent.document.getElementById('cep_').value = '';
        parent.document.getElementById('cidade').disabled = false;
        parent.document.getElementById('cidade').value = '';
        parent.document.getElementById('bairro').disabled = false;
        parent.document.getElementById('bairro').value = '';
        parent.document.getElementById('sigla_uf').disabled = false;
        parent.document.getElementById('sigla_uf').value = '';
        parent.document.getElementById('logradouro').disabled = false
        parent.document.getElementById('logradouro').value = ''
        parent.document.getElementById('numero').disabled = false;
        parent.document.getElementById('numero').value = '';
        if (parent.document.getElementById('letra')) {
          parent.document.getElementById('letra').disabled = false;
          parent.document.getElementById('letra').value = '';
        }
        if (parent.document.getElementById('complemento')) {
          parent.document.getElementById('complemento').disabled = false;
          parent.document.getElementById('complemento').value = '';
        }
        parent.document.getElementById('idtlog').disabled = false;
        parent.document.getElementById('idtlog').value = '';
      }
      window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
    }
  }
</script>
