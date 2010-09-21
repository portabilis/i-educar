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
 * @package   iEd_Include
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'Core/Controller/Page/Abstract.php';

/**
 * clsCampos class.
 *
 * Implementa toda a lógica necessária para criar elementos de formulário e os
 * códigos HTML e Javascript necessários para o funcionamento no i-Educar.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsCampos extends Core_Controller_Page_Abstract
{
  var $campos               = array();
  var $num_espaco           = 1;
  var $__nome               = "formcadastro";
  var $__adicionando        = FALSE;
  var $__adicionador_inline = FALSE;
  var $__blocos             = 0;
  var $__bloco_bt_add       = FALSE;
  var $campoFoco            = FALSE;

  var $__adicionando_tabela = FALSE;
  var $__id_tabela          = 1;
  var $__campos_tabela      = array();
  var $__cabecalho_tabela   = array();
  var $__nm_tabela;
  var $__titulo_tabela;
  var $__largura_tabela;
  var $__valores_tabela        = array(array());
  var $__valores_listas_tabela = array(array());

  var $__adicionando_tab = FALSE;
  var $__id_tab;
  var $__nm_tab;
  var $__tabs       = array();
  var $__campos_tab = array();
  var $__id_tabbed  = 1;
  var $__cabecalho_tab     = array();
  var $__desabilitado_tab  = array();
  var $__segue_fluxo       = FALSE;
  var $__sequencia_fluxo   = FALSE;
  var $__sequencia_default = TRUE;
  var $__acao_enviar_abas  = 'acao()';

  function campoTabInicio($nome ,$largura = '', $segue_fluxo = FALSE, $array_sequencia = NULL)
  {
    $this->__id_tab      = 1;
    $this->__nm_tab      = $nome;
    $this->__segue_fluxo = $segue_fluxo;

    if (is_array($array_sequencia)) {
      $this->__sequencia_fluxo   = serialize($array_sequencia);
      $this->__sequencia_default = FALSE;
    }

    $this->campos['tabbed_add_' . $this->__id_tabbed] = $this->__id_tabbed;
  }

  function campoTabFim()
  {
    $this->campos['fim_tab']          = 1;
    $this->campos['cabecalho_tab']    = $this->__cabecalho_tab;
    $this->campos['desabilitado_tab'] = $this->__desabilitado_tab;
    $this->__cabecalho_tab = array();
    $this->__id_tabbed++;
  }

  function campoAdicionaTab($nome_tab, $desabilitado_tab = FALSE)
  {
    if($this->__sequencia_default) {
      $this->__sequencia_fluxo   = unserialize($this->__sequencia_fluxo);
      $this->__sequencia_fluxo[] = $this->__id_tab;
      $this->__sequencia_fluxo   = serialize($this->__sequencia_fluxo);
    }

    $this->campos['tab_name_' . $this->__id_tab] = array(
      'nome' => $nome_tab,
      'id'   => $this->__id_tab
    );

    $this->__cabecalho_tab[]    = $nome_tab;
    $this->__desabilitado_tab[] = $desabilitado_tab == TRUE ? 1 : 0;
    $this->__id_tab++;
  }

  function campoTabelaInicio($nome , $titulo = '', $arr_campos = array(),
    $arr_valores = array(array()), $largura = '', $array_valores_lista = array(array()))
  {
    $this->__adicionando_tabela = TRUE;

    unset($this->__campos_tabela);

    $this->__campos_tabela         = array();
    $this->__cabecalho_tabela      = $arr_campos;
    $this->__nm_tabela             = $nome;
    $this->__valores_tabela        = $arr_valores;
    $this->__titulo_tabela         = $titulo;
    $this->__largura_tabela        = $largura;
    $this->__valores_listas_tabela = $array_valores_lista;
  }

  function campoTabelaFim()
  {
    if (count($this->__campos_tabela) && is_array($this->__campos_tabela))
    {
      $this->campos['tab_add_' . $this->__id_tabela][]                = $this->__campos_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['cabecalho']     = $this->__cabecalho_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['nome']          = $this->__nm_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['valores']       = $this->__valores_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['titulo']        = $this->__titulo_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['largura']       = $this->__largura_tabela;
      $this->campos['tab_add_' . $this->__id_tabela]['valores_lista'] = $this->__valores_listas_tabela;
    }

    unset($this->__cabecalho_tabela);
    $this->__cabecalho_tabela   = array();
    $this->__adicionando_tabela = FALSE;
    $this->__id_tabela++;
  }

  function blocoAdicionadorBegin($precadastrados_inline = TRUE)
  {
    $this->campos['bloco_beg_' . $this->__blocos] = array('bloco_adicionador', 'begin');

    $this->campos['teste1'] = array('rotulo', 'teste1', 'teste1', 'teste1');
    $this->campos['teste2'] = array('rotulo', 'teste2', 'teste2', 'teste2');
    $this->campos['teste3'] = array('rotulo', 'teste3', 'teste3', 'teste3');
    $this->campos['teste4'] = array('rotulo', 'teste4', 'teste4', 'teste4');
    $this->campos['teste5'] = array('rotulo', 'teste5', 'teste5', 'teste5');

    $this->__adicionador_inline = $precadastrados_inline;
  }

  function blocoAdicionadorEnd()
  {
    $this->campos['bloco_end_' . $this->__blocos] = array('bloco_adicionador', 'end');
    $this->__blocos++;
  }

  function campoAnexarLista($nome, $campo, $valor, $default,
    $valores_ja_incluidos = FALSE, $caminho = '', $acao = '', $duplo = FALSE,
    $descricao = '', $descricao2 = '')
  {
    $this->campos[$nome] = array('anexarListaPesquisa', $campo, '', $valor,
      $default, $acao, $descricao, $caminho, $descricao2, $valores_ja_incluidos);
  }

  function campoAddCampos($nome, $arr_campos = array(), $arr_valores = array())
  {
    $this->campos[$nome] = array('addCampos', $nome, FALSE, $arr_campos, $arr_valores);
  }

  function campoBoolLista($nome, $campo, $default, $val_true = 'Sim',
    $val_false = 'Não', $val_undefined = NULL)
  {
    $valor = array();
    $valor['f'] = $val_false;
    $valor['t'] = $val_true;

    if (! is_null($val_undefined)) {
      $valor[''] = $val_undefined;
    }
    $this->campoLista( $nome, $campo, $valor, $default );
  }

  function campoAddForm($nome, $arr_campos = array())
  {
    $this->campos['linha_' . $nome] = array('addForm', $nome, $arr_campos);
  }

  function campoAdicionar($nome, $campo, $valor, $default, $acao = '' ,
    $duplo = FALSE, $descricao = '', $link, $url_janela, $largura_j,
    $altura_j, $obrigatorio = FALSE, $redirecionamento = '')
  {
    $this->campos[$nome] = array('adicionar', $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor, $default, $acao, $descricao, $link, $url_janela,
      $largura_j, $altura_j, $redirecionamento);
  }

  function campoArquivo($nome, $campo, $valor, $tamanho,
    $descricao = '', $tr_invisivel = FALSE)
  {
    $this->campos[$nome] = array('arquivo', $campo, '', $valor, $tamanho,
      $descricao, 'tr_invisivel' => $tr_invisivel);

    $this->form_enctype  = " enctype='multipart/form-data'";
  }

  function campoCep($nome, $campo, $valor, $obrigatorio = FALSE,
    $hifen = '-', $descricao = FALSE, $disable = FALSE)
  {
    $arr_componente = array(
      'cep',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/([0-9]{5})$hifen([0-9]{3})/" : "*(/([0-9]{5})$hifen([0-9]{3})/)",
      $valor,
      10,
      (8 + @strlen($hifen)),
      'nnnnn-nnn',
      $descricao,
      ($disable) ? 'disabled' : ''
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoCheck($nome, $campo, $valor, $desc = '', $duplo = FALSE,
    $script = FALSE, $disable = FALSE)
  {
    $arr_componente = array(
      $duplo ? 'checkDuplo' : 'check',
      $this->__adicionando_tabela ? $nome : $campo,
      FALSE,
      $valor,
      $desc,
      $script,
      ($disable) ? 'disabled' : ''
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoCnpj($nome, $campo, $valor, $obrigatorio = FALSE)
  {
    $arr_componente = array(
      "cnpj",
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : "*(/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/)",
      $valor,
      20,
      18,
      'nn.nnn.nnn/nnnn-nn'
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoCnpjPesq($nome, $campo, $valor, $arquivo_pesquisa,
    $parametros_serializados = FALSE, $obrigatorio = FALSE)
  {
    $this->campos[$nome] = array(
      'cnpj_pesq',
      $campo,
      $obrigatorio ? "/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : "*(/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/)",
      $valor,
      20,
      18,
      'nn.nnn.nnn/nnnn-nn',
      $arquivo_pesquisa,
      $parametros_serializados
    );
  }

  function campoCor($nome, $campo, $valor, $obrigatorio = FALSE, $descricao = '')
  {
    $this->campos[$nome] = array(
      'cor',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      'cor',
      'cor',
      $descricao
    );
  }

  function campoCpf($nome, $campo, $valor, $obrigatorio = FALSE, $descricao = FALSE, $disabled = FALSE)
  {
    $arr_componente = array(
      'cpf',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/" : '',
      $valor,
      16,
      14,
      'nnn.nnn.nnn-nn',
      $descricao,
      $disabled
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoIdFederal($nome, $campo, $valor, $obrigatorio = FALSE,
    $invisivel = FALSE, $descricao = FALSE)
  {
    $arr_componente = array(
      'idFederal',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ?"/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/+/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : '',
      $valor,
      20,
      18,
      'nnn.nnn.nnn-nn ou nn.nnn.nnn/nnnn-nn',
      $invisivel ? 'disabled' : '', $descricao
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoData($nome, $campo, $valor, $obrigatorio = FALSE, $descricao = '',
    $duplo = FALSE, $acao = '', $disabled = FALSE)
  {
    $arr_componente = array(
      $duplo ? 'dataDupla' : 'data',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/(((0[1-9]|[12][0-9])\/(02))|((0[1-9]|[12][0-9]|(30))\/(0[4689]|(11)))|((0[1-9]|[12][0-9]|3[01])\/(0[13578]|(10)|(12))))\/[1-2][0-9]{3}/" : "*/(((0[1-9]|[12][0-9])\/(02))|((0[1-9]|[12][0-9]|(30))\/(0[4689]|(11)))|((0[1-9]|[12][0-9]|3[01])\/(0[13578]|(10)|(12))))\/[1-2][0-9]{3}/",
      $valor,
      9,
      10,
      'dd/mm/aaaa',
      $descricao,
      $acao,
      $disabled
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoDetalhe($nome, $campo, $valor, $default, $acao = '', $duplo = FALSE,
    $descricao = '', $link, $url_janela, $largura_j, $altura_j, $obrigatorio = FALSE)
  {
    $this->campos[$nome] = array(
      'detalhe',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      $default,
      $acao,
      $descricao,
      $link,
      $url_janela,
      $largura_j,
      $altura_j
    );
  }

  function campoExterno($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $caminho, $nome_caminho, $obrigatorio = FALSE,  $duplo = FALSE, $descricao = '')
  {
    $this->campos[$nome] = array(
      'textoExterno',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $caminho,
      $nome_caminho
    );
  }

  function campoProcurarAdicionar($nome, $campo, $valor, $tamanhovisivel,
    $tamanhomaximo, $caminho, $nome_caminho, $caminho_dest, $nome_caminho_dest,
    $obrigatorio = FALSE, $duplo = FALSE, $descricao = '')
  {
    $this->campos[$nome] = array(
      'adicionarProcurar',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $caminho,
      $nome_caminho,
      $caminho_dest,
      $nome_caminho_dest
    );
  }

  function campoFone($nome, $campo, $valor, $obrigatorio = FALSE)
  {
    $this->campos[$nome] = array(
      'fone',
      $campo,
      $obrigatorio ? "/^\([0-9]{2}\)[0-9]{3}[0-9]?\-[0-9]{4}$/" : "*(/^\([0-9]{2}\)[0-9]{3}[0-9]?\-[0-9]{4}$/)",
      $valor,
      20,
      13,
      '(nn)nnnn-nnnn ou (nn)nnn-nnnn'
    );
  }

  function campoHora($nome, $campo, $valor, $obrigatorio = FALSE, $descricao = '', $acao = '')
  {
    $arr_componente = array(
      'hora',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/[0-9]{2}:[0-9]{2}/" : "*(/[0-9]{2}:[0-9]{2}/)",
      $valor,
      6,
      5,
      'hh:mm',
      $descricao,
      $acao
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoLista($nome, $campo, $valor, $default, $acao = '', $duplo = FALSE,
    $descricao = '', $complemento = '', $desabilitado = FALSE, $obrigatorio = TRUE,
    $multiple = FALSE)
  {
    $filtro = '';

    if ($obrigatorio) {
      $filtro = "/[^ ]/";
    }

    $arr_componente = array(
      $duplo ? 'listaDupla' : 'lista',
      ($this->__adicionando_tabela === TRUE ? $nome : $campo),
      $filtro,
      $valor,
      $default,
      $acao,
      $descricao,
      $complemento,
      $desabilitado ? "disabled='disabled'" : '',
      $multiple
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoListaDin($nome, $campo, $valor, $default, $acao = '',
    $arquivo, $campo_destino, $_GET_campos = array(), $complemento = '',
    $desabilitado = FALSE, $obrigatorio = TRUE, $function_extra = '', $duplo = FALSE)
  {
    $filtro = ($obrigatorio) ? "/[^ ]/": '';

    $this->campos[$nome] = array(
      'listaDin',
      $campo,
      $filtro,
      $valor,
      $default,
      $acao,
      '',
      $campo_destino,
      $complemento,
      $desabilitado ? 'disabled' : '',
      $arquivo,
      $nome,
      $_GET_campos,
      $function_extra,
      $duplo
    );
  }

  function campoListaAtivarPeso($nome, $campo, $valor, $default = '0',
    $ativado = FALSE, $colunas = '10', $tamanho = '10', $default1 = '')
  {
    $this->campos[$nome] = array(
      'listaativarpeso',
      $campo,
      "/[^ ]/",
      $valor,
      $default,
      $ativado,
      '',
      $colunas,
      $tamanho,
      $default1
    );
  }

  function campoListaAtivarPesoSelect($nome, $campo, $valor, $default = '0',
    $ativado = FALSE, $valor2)
  {
    $this->campos[$nome] = array(
      'listaativarpesoselect',
      $campo,
      "/[^ ]/",
      $valor,
      $default,
      $ativado,
      $valor2
    );
  }

  function campoListaPesq($nome, $campo, $valor, $default, $caminho = '', $acao = '',
    $duplo = FALSE, $descricao = '', $descricao2 = '', $flag = NULL, $pag_cadastro = NULL,
    $disabled = '', $div = FALSE, $serializedcampos = FALSE, $obrigatorio = FALSE)
  {
    $this->campos[$nome] = array(
      'listapesquisa',
      $campo,
      $obrigatorio ? "/[^ ]/": '',
      $valor,
      $default,
      $acao,
      $descricao,
      $caminho,
      $descricao2,
      $flag,
      $pag_cadastro,
      $disabled,
      $div,
      $serializedcampos,
      $obrigatorio
    );
  }

  function campoMemo($nome, $campo, $valor, $colunas, $linhas, $obrigatorio = FALSE,
    $descricao = '', $conta = '', $duplo = FALSE, $script = FALSE, $evento = 'onclick', $disabled = FALSE)
  {
    $this->campos[$nome] = array(
      'memo',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      $colunas,
      $linhas,
      $descricao,
      $conta,
      $duplo,
      $evento,
      $script,
      $disabled
    );
  }

  function campoNumero($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $descricao = '', $descricao2 = '', $script = FALSE,
    $evento = FALSE, $duplo = FALSE, $disabled = FALSE)
  {
    $arr_componente = array(
      $duplo ? 'textoDuplo' : 'texto',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/^[0-9]+$/" : "*(/^[0-9]+$/)",
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao ? $descricao : 'somente números',
      $descricao2,
      $script,
      $evento,
      $disabled
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoMonetario($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $descricao = '', $script = '', $evento = 'onChange',
    $disabled = FALSE, $show_sub = TRUE, $descricao2 = '', $duplo = FALSE)
  {
    $arr_componente = array(
      $duplo ? 'monetarioDuplo' : 'monetario',
      $this->__adicionando_tabela ? $nome : $campo,
      $obrigatorio ? "/^[0-9.,]+$/" : "*(/^[0-9.,]+$/)",
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $script,
      $evento,
      $disabled,
      $show_sub,
      $descricao2
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoOculto($nome, $valor)
  {
    $arr_componente = array(
      'oculto',
      $nome,
      '',
      $valor
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela['oculto'][] = $arr_componente;
    }
  }

  function campoRadio($nome, $campo, $valor, $default, $acao = '', $descricao = '')
  {
    $this->campos[$nome] = array(
      'radio',
      $campo,
      '',
      $valor,
      $default,
      $acao,
      $descricao
    );
  }

  function campoRotulo($nome, $campo, $valor, $duplo = FALSE, $descricao = '', $separador = ':')
  {
    $arr_componente = array(
      $duplo ? 'rotuloDuplo' : 'rotulo',
      $this->__adicionando_tabela ? $nome : $campo,
      '',
      $valor,
      6 => $descricao,
      'separador' => $separador
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoSenha($nome, $campo, $valor, $obrigatorio = FALSE, $descricao = '')
  {
    $this->campos[$nome] = array(
      'senha',
      $campo,
      $obrigatorio ? "/[^ ]/" : '',
      $valor,
      10,
      100,
      $descricao
    );
  }

  function campoTexto($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $expressao = FALSE, $duplo = FALSE, $descricao = '',
    $descricao2 = '', $script = '', $evento = 'onKeyUp', $disabled = FALSE)
  {
    $arr_componente = array(
      $duplo ? 'textoDuplo' : 'texto',
      $this->__adicionando_tabela ? $nome : $campo,
      $expressao ? $expressao : ($obrigatorio ? "/[^ ]/" : ''),
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $descricao2,
      $script,
      $evento,
      $disabled
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoEmail($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $expressao = FALSE, $duplo = FALSE, $descricao = '',
    $descricao2 = '', $script = '')
  {
    $this->campos[$nome] = array(
      $duplo ? 'emailDuplo' : 'email',
      $campo,
      $expressao ? $expressao : ($obrigatorio ? "/^[a-z_\-\.0-9]+$/" : ''),
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $descricao2,
      $script
    );
  }

  function campoSuggest($nome, $campo, $valor, $obrigatorio = FALSE, $expressao = FALSE,
    $duplo = FALSE, $descricao = '', $arquivo = 'xml_pessoas.php')
  {
    $this->campos[$nome] = array(
      'suggest',
      $campo,
      $expressao ? $expressao : ($obrigatorio ? "/[^ ]/" : ''),
      $valor,
      $descricao,
      $descricao2
    );
  }

  function campoTextoPesquisa($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $caminho = '', $expressao = FALSE, $duplo = FALSE,
    $descricao = '', $descricao2 = '', $serializedcampos = NULL, $disabled = FALSE,
    $script = '', $evento = 'onChange')
  {
    $arr_componente = array(
      $duplo ? 'textoPesquisaDuplo' : 'textoPesquisa',
      $this->__adicionando_tabela ? $nome : $campo,
      $expressao ? $expressao : ($obrigatorio ? "/[^ ]/" : ''),
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $descricao2,
      $caminho,
      $serializedcampos,
      $disabled,
      $script,
      $evento
    );

    if (! $this->__adicionando_tabela) {
      $this->campos[$nome] = $arr_componente;
    }
    else {
      $this->__campos_tabela[] = $arr_componente;
    }
  }

  function campoTextoInv($nome, $campo, $valor, $tamanhovisivel, $tamanhomaximo,
    $obrigatorio = FALSE, $expressao = FALSE, $duplo = FALSE, $descricao = '',
    $descricao2 = '', $script = '', $evento = 'onKeyUp', $name = '')
  {
    $name = $name ? $name : $nome;
    $this->campos[$nome] = array(
      $duplo ? 'textoDuploInv' : 'textoInv',
      $campo,
      $expressao ? $expressao : ($obrigatorio ? "/[^ ]/" : ''),
      $valor,
      $tamanhovisivel,
      $tamanhomaximo,
      $descricao,
      $descricao2,
      $script,
      $evento,
      $name
    );
  }

  function campoTheEditor ($nome, $campo, $valor, $largura, $altura)
  {
    $this->campos[$nome] = array(
      'theEditor',
      $campo,
      $valor,
      $largura,
      $altura
    );
  }

  function campoQuebra()
  {
    $this->campos['espaco' . $this->num_espaco] = array('espaco', '', '', '', '', '', '', '');
    $this->num_espaco++;
  }

  function campoQuebra2($cor = '#000000', $altura = 2)
  {
    $this->campos['linha_preta' . $this->num_espaco] = array(
      'linha_preta',
      'cor' => $cor,
      'altura' => $altura,
      '',
      '',
      '',
      '',
      ''
    );

    $this->num_espaco++;
  }

  function setFocus($campo)
  {
    $this->campoFoco = $campo;
  }

  function MakeCampos($array_campos = NULL, $adicionador_indice = NULL, $todos_inline = FALSE,
    $todos_disabled = FALSE, $junta_linhas = FALSE, $start_md = NULL)
  {
    $retorno = '';
    $gerou_script = FALSE;

    if (! $arr_campos) {
      $arr_campos = $this->campos;
    }

    reset($arr_campos);
    $campo_anterior = '';
    $md = TRUE;

    if (! is_null($start_md) && is_bool($start_md)) {
      $md = $start_md;
    }

    $foiDuplo = $junta_linhas;

    // Marca quantos valores foram passados para o prenchimento das repetições
    $adicionador_total_valores = 5;

    $javascript .= "
  function tabela(name, counter)
  {
    // Seta variavel para não reordenar id dos campos
    this.setReordena = function(reordenar)
    {
      this.reordenar = reordenar;
    }

    // Construtor
    this.constructor = function() {

      // Contém uma referência ao objeto
      var This = this;

      this.id = counter;

      this.afterAddRow    = function(){};
      this.afterRemoveRow = function(){};

      this.isIE      = (navigator.appName.indexOf('Microsoft') != -1) ? 1 : 0;
      this.nome      = name;
      this.campos    = new Array();
      this.reordenar = true;

      this.getId = function() { return this.id; }

      var numColumns = document.getElementById(This.nome).rows[1].childNodes.length;

      var row = document.getElementById(This.nome).insertRow(document.getElementById(This.nome).rows.length-1);

      for (var ct = 0; ct < numColumns - 1; ct++) {
        var campo = document.getElementById(This.nome).rows[2].childNodes[ct].cloneNode(true);

        if (This.isIE) {
          if(campo.childNodes[0].type == 'text')
            campo.childNodes[0].value  = '';
          else if(campo.childNodes[0].type == 'select-one')
            campo.childNodes[0].value  = '';
          else if(campo.childNodes[0].type == 'checkbox')
            campo.childNodes[0].checked = false;
        }
        else {
          if (campo.childNodes[1].type == 'text')
            campo.childNodes[1].value  = '';
          else if(campo.childNodes[1].type == 'select-one')
            campo.childNodes[1].value  = '';
          else if(campo.childNodes[1].type == 'checkbox')
            campo.childNodes[1].checked = false;
        }

        This.campos.push(campo);
      }

      var campo         = document.getElementById(This.nome).rows[2].childNodes[ct].cloneNode(true);
      var campos_oculto = campo.getElementsByTagName('INPUT');

      for (var co = 0; co < campos_oculto.length; co++) {
        campos_oculto[co].id = /[a-zA-Z_-]*/.exec(campos_oculto[co].id) + '[' + This.id +']';
        campos_oculto[co].value = '';
      }

      This.campos.push(campo);
    }

    // Call the constructor
    this.constructor();

    this.classe = (this.id % 2 == 0 )? 'formlttd' : 'formmdtd';
    this.addRow = function()
    {
      var This = this;

      This.classe    = (This.id % 2 == 0 )? 'formmdtd' : 'formlttd';
      var numColumns = This.campos.length

      var row = document.getElementById(This.nome).insertRow(document.getElementById(This.nome).rows.length-2);

      row.setAttribute(\"id\", \"tr_\"+This.nome+\"[\"+This.id+\"]\");
      row.setAttribute(\"name\", \"tr_\"+This.nome+\"[]\");

      row.className = This.classe;

      for (var ct = 0; ct < numColumns - 1; ct++) {
          var campo       = This.campos[ct].cloneNode(true);
          campo.className = This.classe;
          campo.setAttribute(\"id\", /[a-zA-Z_-]*/.exec(campo.id)+\"[\"+This.id+\"]\");

          if (This.isIE) {
            campo.childNodes[0].id = /[a-zA-Z_-]*/.exec(campo.childNodes[0].id) + '[' + This.id +']';
            campo.childNodes[0].name = /[a-zA-Z_-]*/.exec(campo.childNodes[0].name) + '[' + This.id +']';

            if (campo.childNodes[0].type == 'select-one')
              campo.childNodes[0].selectedIndex = '';
          }
          else {
            campo.childNodes[1].id = /[a-zA-Z_-]*/.exec(campo.childNodes[1].id) + '[' + This.id +']';
            campo.childNodes[1].name = /[a-zA-Z_-]*/.exec(campo.childNodes[1].name) + '[' + This.id +']';

            if (campo.childNodes[1].type == 'select-one')
              campo.childNodes[1].selectedIndex = '';
          }

        row.appendChild(campo);
      }

      This.classe = (This.classe == 'formmdtd') ? 'formlttd' : 'formmdtd';

      var campo = this.campos[This.campos.length-1].cloneNode(true);

      row.appendChild(campo);

      var campos_oculto = campo.getElementsByTagName('INPUT');

      for (var co = 0; co < campos_oculto.length; co++) {
        campos_oculto[co].id = /[a-zA-Z_-]*/.exec(campos_oculto[co].id) + '[' + This.id +']';
        campos_oculto[co].name = /[a-zA-Z_-]*/.exec(campos_oculto[co].name) + '[' + This.id +']';
      }

      var link_deletar = campo.getElementsByTagName('A');

      link_deletar[0].id = /[a-zA-Z_-]*/.exec(link_deletar[0].id) + '[' + This.id +']';

      This.id++;
      this.afterAddRow();
    }

    this.removeRow = function(row)
    {
      var This = this;

      var tab = row;
      var tr;
      while (tab.nodeName != 'TABLE') {
        if(tab.nodeName == 'TR')
          tr = tab;

        tab = tab.parentNode;
      }

      var trs = tab.getElementsByTagName('TR');

      var linha = 0;

      for (var ct = 2; ct < trs.length - 1; ct++) {
        if (trs[ct] == tr) {
          tab.deleteRow(ct);
        }
      }

      trs = document.getElementsByName('tr_'+This.nome + '[]');
      var classe = 'formmdtd';

      for (var ct = 0; ct < trs.length; ct++) {
        if (trs[ct] && trs[ct].id != 'adicionar_linha') {
          trs[ct].className = classe;

          for (var c = 0; c < trs[ct].cells.length; c++)
            trs[ct].cells[c].className = classe;

          classe = (classe == 'formmdtd')? 'formlttd' : 'formmdtd';
        }
      }

      if (This.reordenar) {
        This.setId(tab);
        This.id--;
      }

      if (This.id == 0)
        This.addRow();

      this.afterRemoveRow();
    }

    this.setId = function setId(tab)
    {
      var trs = tab.getElementsByTagName('TR');
      var cod_ini = 0;

      for (var ct = 2; ct < trs.length; ct++) {
        var nome_tr = /[a-zA-Z-_]*/.exec(trs[ct].id);

        if (!nome_tr)
           continue;

         trs[ct].setAttribute( \"id\",nome_tr + '[' + cod_ini + ']');

        for (var c = 0; c < trs[ct].cells.length; c++) {
          var nome_td = /[a-zA-Z-_]*/.exec(trs[ct].cells[c].id);
          trs[ct].cells[c].setAttribute( \"id\",nome_td + '[' + cod_ini + ']');

          var campos = trs[ct].cells[c].childNodes;

          for (var inp = 0 ; inp < campos.length; inp++) {
            if (!campos[inp].id)
              continue;

            var nome_inp = /[a-zA-Z-_]*/.exec(campos[inp].id);

            campos[inp].setAttribute(\"id\",nome_inp + '[' + cod_ini + ']');
            campos[inp].setAttribute(\"name\",nome_inp + '[' + cod_ini + ']');
          }
        }

        cod_ini++;
      }
    }
  }
  ";

    $retorno .= "<script>$javascript</script>";
    $classe   = $md ? 'formlttd' : 'formmdtd';
    $md = $md ? FALSE : TRUE;
    $index = 0;

    while (list($nome, $componente) = each($arr_campos)) {
      $nome_add = $nome;
      $campo_tabela = FALSE;

      // Cria nova tab
      if (ereg("^(tabbed_add_[0-9]+)", $nome) === 1) {
        $nomes_tab = urlencode(serialize($arr_campos['cabecalho_tab']));
        unset($arr_campos['cabecalho_tab']);

        // $arr_campos
        $desabilitado_tab = urlencode(serialize($arr_campos['desabilitado_tab']));
        unset($arr_campos['desabilitado_tab']);

        $retorno .=  "<tr id='tr_tab'>
                <td valign='top' colspan='2' width='100%' align='center'>";

        $rand = rand();

        $retorno .= "<!-- INICIO TABELA 1 --><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"tabela_principal\" align=center width='100%'>
                <tr>
                    <td valign=top height=30>
                    <link href=\"styles/styles_tab.css?$rand\" rel=\"stylesheet\" type=\"text/css\" />
                      <script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"scripts/dtabs.js?$rand\"></script>
                      <script type=\"text/javascript\" src=\"scripts/tabs_cab.php?nomes_tab={$nomes_tab}&desabilitado_tab=$desabilitado_tab\"></script>
                    </td>
                </tr>";
        $retorno .= '<tr>
                  <td valign=top height=auto class="pageTabb">&nbsp;
                  <!-- INICIO TABELA 2 -->
                     <table cellpadding="0" cellspacing="0" border="0" height=auto>
                       <tr>
                           <td valign=top width=1><img src="imagens/img/blank.gif" width=1 height=1></td>
                           <td valign=top width=100% class="tabPage" height="auto">';

        $existe_tab_iniciada = TRUE;
        continue;
      }

      if (ereg("^(tab_name_[0-9]+)", $nome) === 1) {
        if ($existe_tab_aberta) {
          if ($this->__segue_fluxo) {
            $colspan = 2;
            $sequencia = unserialize($this->__sequencia_fluxo);

            if (isset($sequencia[$index - 1])) {
              $prox_id = array_flip($sequencia);
              $prox_id = $prox_id[$index] * 2;

              $retorno .= "<tr><td align=left><img onclick='LTb0(\"0\", \"$prox_id\")' style='cursor:pointer' src='imagens/bot_anterior.gif' border='0' /></td>";
              $colspan = 1;
            }
            if ($sequencia[$index] != 0) {
              $prox_id = $sequencia[$index++]*2;
              $retorno .= "<td colspan=$colspan align=right><img onclick='validaTab(".($componente['id']-1).");LTb0(\"0\", \"$prox_id\")' style='cursor:pointer' src='imagens/bot_proximo.gif' border='0' /></td></tr>";
            }
            else {
              $retorno .= "<td colspan=$colspan align=right><img onclick='{$this->__acao_enviar_abas}' style='cursor:pointer' src='imagens/bot_salvar.gif' border='0' /></td></tr>";
            }
          }

          $retorno .= "<!-- FIM TABELA 3 --></table></div>";
        }

        $existe_tab_aberta = TRUE;
        $retorno .= "<div id=\"content{$componente['id']}\" style=\"visibility: hidden;\" class=\"tabPage\">";
        $retorno .= "<!-- INICIO TABELA 3 --><table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=100%  align=center>";

        continue;
      }

      if ($nome === 'fim_tab') {
        if ($existe_tab_aberta) {
          if ($this->__segue_fluxo) {
            $colspan = 2;
            $sequencia = unserialize($this->__sequencia_fluxo);

            if (isset($sequencia[$index - 1])) {
              $prox_id = array_flip($sequencia);
              $prox_id = $prox_id[$index] * 2;

              $retorno .= "<tr><td align=left><img onclick='LTb0(\"0\", \"$prox_id\")' style='cursor:pointer' src='imagens/bot_anterior.gif' border='0' /></td>";
              $colspan = 1;
            }

            if (! $this->__sequencia_default) {
              if($sequencia[$index] != 0) {
                $prox_id  = $sequencia[$index] * 2;
                $retorno .= "<td colspan=$colspan align=right><img onclick='validaTab(".($componente['id']-1).");LTb0(\"0\", \"$prox_id\")' style='cursor:pointer' src='imagens/bot_proximo.gif' border='0' /></td></tr>";
              }
              else {
                $retorno .= "<td colspan=$colspan align=right><img onclick='{$this->__acao_enviar_abas}' style='cursor:pointer' src='imagens/bot_salvar.gif' border='0' /></td></tr>";
              }
            }
            else {
              $retorno .= "<td colspan=$colspan align=right><img onclick='{$this->__acao_enviar_abas}' style='cursor:pointer' src='imagens/bot_salvar.gif' border='0' /></td></tr>";
            }
          }

          $retorno .= "<!-- FIM TABELA 3 --></table></div>";
        }

        $retorno .= "        </td>
                           <td valign=top width=1><img src='imagens/img/blank.gif' width=1 height=1></td>
                       </tr>
                     </table><!-- FIM TABELA 2 -->
                  </td>
              </tr>
            </table><!-- FIM TABELA 1 -->";
        $retorno .= '<br></td></tr>';

        continue;
      }

      if (ereg("^(tab_add_[0-9]+)",$nome) === 1) {
        $campo_tabela = TRUE;
        $javascript   = '';

        $cabecalho   = $componente['cabecalho'];
        $nome_tabela = $componente['nome'];
        $valores     = $componente['valores'];
        $titulo      = $componente['titulo'];
        $largura     = $componente['largura'] ? " width=\"{$componente['largura']}\" " : "";
        $valores_lista_tabela = $componente['valores_lista'];
        $componente  = array_shift($componente);

        $campos_oculto = $componente['oculto'];
        unset($componente['oculto']);

        $classe = $md ? 'formlttd' : 'formmdtd';
        $md     = $md ? FALSE : TRUE;

        $retorno .= "<tr id='tr_$nome_tabela' class='$classe'><td valign='top' align='center' colspan='2'>";
        $retorno .= "\n<table cellspacing='0' $largura id='$nome_tabela' cellpadding='2' style='border-width:1px 1px 1px 1px;border-color:#000000;border-style:solid;margin:10px 0px 10px 0px;' >";

        $total_campos = count($cabecalho);
        $span = $total_campos + 1;

        if ($titulo) {
          $retorno.= "<tr align='center' id='tr_{$nome_tabela}_tit' style='font-weight:bold'  class='formdktd'><td colspan='$span'>$titulo</td></tr>";
        }
        else {
          $retorno.= "<tr align='center' id='tr_{$nome_tabela}_tit' style='font-weight:bold;display:none;visibility:hidden;' ><td colspan='$span'>&nbsp;</td></tr>";
        }

        $retorno.= "<tr align='center' style='font-weight:bold' id='tr_{$nome_tabela}_cab'>";

        foreach ($cabecalho as $key => $cab) {
          $expressao_regular = $componente[$key][2];

          if ($expressao_regular && substr( $expressao_regular, 0, 1 ) != "*") {
            $obrigatorio = "<span class=\"campo_obrigatorio\">*</span>";
          }
          else {
            $obrigatorio = '';
          }

          $retorno.= "<td class='formmdtd' id='td_$cab' align='center'><span class='form'>$cab</span>{$obrigatorio}</td>";
        }

        $retorno.= "<td class='formmdtd' id='td_acao' align='center'><span class='form'>A&ccedil;&atilde;o</span></td>";
        $retorno.= "</tr>";

        $click = "$nome_add.removeRow(this);";

        $img = "<img src=\"imagens/banco_imagens/excluirrr.gif\" border=\"0\" alt=\"excluir\" />";
        $md2 = FALSE;

        if (! count($valores)) {
          $valores[0] = '';
        }

        foreach ($valores as $key2 => $valor) {
          $classe2 = $md2 ? 'formlttd' : 'formmdtd';
          $md2     = $md2 ? FALSE : TRUE;

          $retorno .= "<tr id='tr_{$nome_tabela}[$key2]' name='tr_{$nome_tabela}[]'  $style class='$classe2'>";
          $array_valores_lista = $valores_lista_tabela[$key2];

          foreach ($componente as $key => $campo_) {
            $nome = $campo_[1];

            if ($campo_[10]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            if ($campo_[9] && $campo_[8]) {
              $evento = " {$campo_[9]}=\"{$campo_[8]}\"";
            }
            else {
              $evento = '';
            }

            $expressao_regular = $campo_[2];

            if ($expressao_regular && substr( $expressao_regular, 0, 1 ) != "*") {
              $class = 'obrigatorio';
            }
            else {
              $class = 'geral';
            }

            $center   = (strtolower($campo_[0]) == 'rotulo' || strtolower($campo_[0]) == 'check' || $largura) ?
              'align="center"' : '';

            $retorno .= "<td class='$classe2' $center id='td_{$nome}[{$key2}]' valign='top'>\n";

            switch (strtolower($campo_[0])) {
              case 'texto':
                $retorno .= $this->getCampoTexto("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$campo_[4],$campo_[5],$evento,$campo_[10],"",$class,$campo_[7]);
                break;

              case 'monetario':
                $retorno .= $this->getCampoMonetario("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$campo_[4],$campo_[5],$campo_[9],$campo_[6],$campo_[11],$class,$campo_[8],$campo_[7]);
                break;

              case 'hora':
                $retorno .= $this->getCampoHora("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],"","");
                break;

              case 'lista':
                $lista = NULL;

                if (is_array($array_valores_lista)) {
                  $lista = array_shift($array_valores_lista);
                }

                $lista = (sizeof($lista)) ?
                  $lista : $campo_[3];

                $retorno .= $this->getCampoLista("{$nome}[{$key2}]","{$nome}[$key2]",$campo_[5],$lista,$valor[$key],$campo_[7],$campo_[8],$class,$campo_[9]);
                break;

              case 'rotulo':
                $retorno .= $this->getCampoRotulo($campo_[3]?$campo_[3] : $valor[$key]);
                break;

              case 'check' :
                $retorno .= $this->getCampoCheck("{$nome}[{$key2}]","{$nome}[{$key2}]",$campo_[3]?$campo_[3] : $valor[$key],$campo_[4],$campo_[5],$campo_[6]);
                break;

              case 'cnpj':
                $retorno .= $this->getCampoCNPJ("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5]);
                break;

              case 'cpf':
                $retorno .= $this->getCampoCPF("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],$campo_[8]);
                break;

              case 'idfederal':
                $retorno .= $this->getCampoIdFederal("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],$campo_[8]);
                break;

              case 'data':
                $retorno .= $this->getCampoData("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],$campo_[9]);
                break;

              case 'textopesquisa':
                $retorno .= $this->getCampoTextoPesquisa("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],$campo_[10],$campo_[8],$campo_[9],$campo_[7], $campo_[12], $campo_[11]);
                break;

              case 'cep':
                $retorno .= $this->getCampoCep("{$nome}[{$key2}]","{$nome}[{$key2}]",$valor[$key],$class,$campo_[4],$campo_[5],$campo_[8],$campo_[7]);
                break;
            }

            $retorno .= '</td>';
            $evento = '';
          }

          $retorno_oculto = '';

          if (is_array($campos_oculto)) {
            foreach ($campos_oculto as $key_oculto => $campo_oculto) {
              $key_oculto = $key_oculto + sizeof($cabecalho);

              $campo_oculto[3] = $campo_oculto[3] ?
                $campo_oculto[3] : $valor[$key_oculto];

              $retorno_oculto .= $this->getCampoOculto("{$campo_oculto[1]}[{$key2}]",$campo_oculto[3],"{$campo_oculto[1]}[{$key2}]");
            }
          }

          $retorno .= "<td align='center'><a href='javascript:void(0)' onclick='$click' id='link_remove[$key2]' style='outline: none;'>$img</a>$retorno_oculto</td>";
          $img      = "<img src=\"imagens/banco_imagens/excluirrr.gif\" border=\"0\" alt=\"excluir\" />";
          $id       = count($valores);

          $javascript .= "
                  var $nome_add = new tabela('{$nome_tabela}','{$id}');\n";
        } // endforeach.

        $retorno .= '</tr>';

        $click    = "$nome_add.addRow();";
        $img      = "<img src=\"imagens/nvp_bot_novo.png\" border=\"0\" alt=\"incluir\" />";
        $retorno .= "<tr id='adicionar_linha'>";
        $tt       = $total_campos + 1;
        $retorno .= "<td colspan='$tt' align='left' style='border-width:1px 0px 0px 0px;border-style:dotted;border-color:#000000;'><a href='javascript:void(0)' id='btn_add_$nome_add' onclick='$click' style='outline: none;'>$img</a></td>";
        $retorno .= "</tr>";

        $retorno .= "</table>";
        $retorno .= "</td></tr>";

        $retorno .= "<script  type='text/javascript'>$javascript</script>";
        continue;
      }

      if ($this->__adicionando && FALSE) {
        // Adiciona o campo no bloco de repetição
        $adicionador_campos_repeticao[$nome] = $componente;
        $adicionador_complemento_campo       = "[{$adicionador_indice}]";
        $campo_valor                         = $componente[3][$adicionador_indice];
      }
      else {
        $adicionador_complemento_campo = '';
        $campo_valor = $componente[3];
      }

      $nome .= $adicionador_complemento_campo;
      $expressao_regular = $componente[2];

      if ($expressao_regular && substr($expressao_regular, 0, 1) != "*") {
        $class = 'obrigatorio';
        $obrigatorio = "<span class=\"campo_obrigatorio\">*</span>";
      }
      else {
        $class       = 'geral';
        $obrigatorio = '';
      }

      // Separador: insere uma linha preta
      if ($componente[0] == 'linha_preta') {
        $retorno .=  "<tr><td  style='padding:0px;background-color:{$componente["cor"]};' colspan='2' height='{$componente["altura"]}'></td></tr>";
        continue;
      }
      elseif ($componente[0] == 'espaco') {
        $retorno .=  "<tr><td colspan='2'><hr></td></tr>";
        continue;
      }
      elseif ($componente[0] == 'bloco_adicionador') {
        if ($componente[1] == 'begin') {
          // Flag de que estamos no meio de um bloco de adiçã
          $this->__adicionando = TRUE;

          // Se for a primeira vez que cai aqui define o índice como zero,
          // nas vezes subsequentes (chamadas por recursividade) o índice será
          // passado na chamada da função
          if (is_null($adicionador_indice)) {
            $adicionador_indice = 0;
          }

          // Cria um array para armazenar os campos a serem repetidos
          $adicionador_campos_repeticao = array();

          // Adiciona o campo begin no ínicio do bloco de repetição
          $adicionador_campos_repeticao[$nome] = $componente;
        }
        else {
          // Desmarca a flag de bloco de adiçã
          $this->__adicionando = FALSE;

          // Adiciona o campo end no fim do bloco de repetição
          $adicionador_campos_repeticao[$nome] = $componente;

          // Ainda não repetiu o bloco por tempo suficiente
          $adicionador_indice++;
          if ($adicionador_indice < $adicionador_total_valores) {
            $retorno .= $this->MakeCampos($adicionador_campos_repeticao,
              $adicionador_indice, FALSE, TRUE, TRUE, ! $md);
          }
        }
      }
      elseif ($componente[0] != 'oculto') {
        $tipo = $componente[0];

        if (! ($componente['separador'])) {
          $campo = $componente[1];
        }
        else {
          $campo = $componente[1] . "{$componente['separador']}";
        }

        $validador = $componente[2];

        if (($campo == $campo_anterior) && ($campo != '-:')) {
          $campo = '';
        }
        else {
          $campo_anterior = $campo;
          $tipo_anterior = $tipo;

          if (! $foiDuplo) {
            $md = ! $md;
          }
        }

        $classe = $md ? 'formmdtd' : 'formlttd';

        if ($campo_tabela && FALSE) {
          if($componente[10] && ($componente[0] == 'textoDuploInv'
            || $componente[0]  =="textoInv" ))
          {
            $name = " name='tr_{$componente[10]}'  ";
          }
          else {
            $name = '';
          }

          $retorno .=  "<tr id='tr_$nome' {$name} $style><td class='$classe' valign='top'><span class='form'>$campo</span>{$obrigatorio}{$explicacao}</td><td class='$classe' valign='top'><span class='form'>\n";
        }
        elseif (! $foiDuplo) {
          if ($campo == '-:') {
            if (empty($componente[3])) {
              $componente[3] = renderpixel();
            }

            $explicacao = ($componente[6]) ?
              "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : "";

            $retorno .=  "<tr><td colspan='2' class='$classe'><span class='form'><b>$componente[3]</b></span>{$explicacao}</td></tr>\n";
          }
          else {
            if ($componente[10] && ($componente[0] == "textoDuploInv"
              || $componente[0]  =="textoInv" ))
            {
              $name = " name='tr_{$componente[10]}'  ";
            }
            else {
              $name = '';
            }

            $style = ($componente['tr_invisivel']) ?
              "style='visibility:collapse'" : '';

            $explicacao = ($componente[6]) ?
              "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : '';

            $retorno .= "<tr id='tr_$nome' {$name} $style><td class='$classe' valign='top'><span class='form'>$campo</span>{$obrigatorio}{$explicacao}</td><td class='$classe' valign='top'><span class='form'>\n";
          }
        }
        elseif ($tipo) {
          if ($componente[10] == TRUE) {
            $explicacao = ($componente[6]) ?
              "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : '';

            $retorno .= "<span class='form'>$campo</span>{$explicacao}\n";
          }
          else {
            $retorno .= "<span class='form'>$campo</span>\n";
          }

          $foiDuplo = FALSE;
        }

        switch ($tipo) {
          case 'anexarListaPesquisa':
            $inicio     = 0;
            $javascript = '';
            $inclusos   = array();

            $retorno .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"anexador_{$nome}\">";

            if (is_array($componente[9])) {
              // Existem itens já incluídos, monta a lista com eles (com o botão remover ao lado)
              foreach ($componente[9] as $ja_incluido) {
                list($cod, $label) = $ja_incluido;

                $retorno .= "<tr><td><select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}[]' id='{$nome}_{$inicio}'>";

                $opcoes = $componente[3];

                if ($componente[7]) {
                  // É um adicionador, então vamos inserir os já incluídos na lista padrão
                  $opcoes[$cod] = $label;
                }

                reset($opcoes);

                while (list($chave, $texto) = each($opcoes)) {
                  $retorno .= "<option id=\"{$nome}_{$inicio}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

                  if ($chave == $cod) {
                    $retorno .=  ' selected';
                  }

                  $retorno .= ">$texto</option>";
                }

                $retorno .= '</select>';

                if ($componente[7]) {
                  $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" onclick=\"pesquisa_valores_popless('{$componente[7]}', '{$nome}_{$inicio}')\">";
                }

                $retorno   .= " {$componente[8]}</td><td id=\"td_adicionador_{$nome}_{$inicio}\" style=\"padding-left:30px;\"><img name=\"adicionador_{$nome}_{$inicio}\" id=\"adicionador_{$nome}_{$inicio}\" onclick=\"remove_{$nome}('{$inicio}');\" src=\"imagens/banco_imagens/excluirrr.gif\" title=\"Excluir\" border=0></td></tr>";
                $inclusos[] = $inicio;

                $inicio++;
              }
            }

            $inclusos[]  = $inicio;
            $javascript .= "\nitens_{$nome} = [" . implode( ",", $inclusos ) . "];";

            $retorno    .= "<tr><td><select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}[]' id='{$nome}_{$inicio}'>";
            $javascript .= "
  function remove_{$nome}(id)
  {
    posicao = 0;
    tabela = document.getElementById( 'anexador_{$nome}' );

    for (i = 0; i < id; i++) {
      if (itens_{$nome}[i] >= id) {
        break;
      }

      if (itens_{$nome}[i] != null) {
        posicao++;
      }
    }

    itens_{$nome}[id] = null;
    tabela.deleteRow( posicao );
  }

  function duplica_{$nome}()
  {
    id = itens_{$nome}.length;
    id = itens_{$nome}.length;

    tabela = document.getElementById( 'anexador_{$nome}' );

    linha = tabela.insertRow( tabela.rows.length );

    col1 = linha.insertCell( 0 );
    col2 = linha.insertCell( 1 );
    col2.style.paddingLeft = '30px';

    sel = document.createElement('select');
    novoIndice = 0;
            ";

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option id=\"{$nome}_{$inicio}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              $javascript .= "
    sel.options[novoIndice] = new Option( '{$texto}' );
    sel.options[novoIndice].value = '" . urlencode($chave) . "';
              ";

              if ($chave == $componente[4]) {
                $retorno    .= ' selected';
                $javascript .= '
    sel.options[novoIndice].selected = true;
                ';
              }

              $retorno    .= ">$texto</option>";
              $javascript .= "
    novoIndice++;
              ";
            }

            $retorno    .= '</select>';
            $javascript .= "
    sel.className = '{$class}';
    col1.appendChild(sel);

    img         = document.createElement('img');
    img.src     = 'imagens/banco_imagens/entrada2.gif';
    img.title   = 'Incluir';
    img.id      = 'adicionador_{$nome}_' + id;
    img.name    = 'adicionador_{$nome}_' + id;
    img.onclick = function() { duplica_{$nome}(); };

    col2.appendChild(img);

    id_antecessor = id - 1;

    img_ant     = document.getElementById('adicionador_{$nome}_' + id_antecessor);
    img_ant.src = 'imagens/banco_imagens/excluirrr.gif';

    document.getElementById('adicionador_{$nome}_' + id_antecessor).onclick = function() { onclick=remove_{$nome}(this.name.substr(this.name.lastIndexOf( '_' ) + 1)); };

    itens_{$nome}[id] = id;
  }
            ";

            if ($componente[7]) {
              $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" onclick=\"pesquisa_valores_popless('{$componente[7]}', '{$nome}_{$inicio}')\">";
            }

            $retorno .= " {$componente[8]}</td><td id=\"td_adicionador_{$nome}_{$inicio}\" style=\"padding-left:30px;\"><img name=\"adicionador_{$nome}_{$inicio}\" id=\"adicionador_{$nome}_{$inicio}\" onclick=\"duplica_{$nome}();\" src=\"imagens/banco_imagens/entrada2.gif\" title=\"Incluir\" border=0></td></tr></table><script type=\"text/javascript\">$javascript</script>";
            break;

          case 'rotuloDuplo':
            $foiDuplo = TRUE;
          case 'rotulo':
            if ($campo != '-:') {
              $retorno .=  $componente[3];
            }
            break;

          case 'cep':
            $retorno .= "<input onKeyPress=\"formataCEP(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" $componente[8]>$componente[7]\n";
            break;

          case 'fone':
            $retorno .= "<input class='{$class}' type='text' name=\"ddd_{$nome}\" id=\"ddd_{$nome}\" value=\"{$componente[3]}\" size=\"3\" maxlength=\"2\"> <input onKeyPress=\"formataFone(this,event);\" class='{$class}' type='text' name=\"fone_{$nome}\" id=\"fone_{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">\n";
            break;

          case 'data':
            $retorno .=  "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[8]}> $componente[7]\n";
            break;

          case 'dataDupla':
            $retorno .=  "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\"> $componente[7]";
            break;

          case 'hora':
            $componente[3] = strlen( $componente[3] ) < 6 ? $componente[3]: substr($componente[3],0,5);
            $retorno      .= "<input onKeyPress=\"formataHora(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[8]}>{$componente[7]}";
            break;

          case 'cor':
            $cor = empty($componente[3]) ? "ffffff" : $componente[3];
            $retorno .=  "<input type='text' name='{$nome}' value='$componente[3]' size='6' maxlength='6' onBlur='setColor();' class='{$class}' /> *
          <a href='#' class='link' onClick=\"newwin=window.open('./cores.html', 'cores', 'width=320, height=260, scollbars=false');\">Alterar cor</a>
          <span id=\"{$nome}1\" style=\" background:#$cor;\"><a href=\"#\" onClick=\"newwin=window.open('./cores.html', 'cores', 'width=320, height=260, scollbars=false');\"><img src=\"./imagens/shim.gif\" border=\"1\" width=\"40\" style=\"border-color:#000000;\" height=\"18\"></a></span>";
            break;

          case 'mapa':
            $cor = empty($componente[3]) ? "ffffff" : $componente[3];
            $retorno .=  "<input disabled type='text' name='{$nome}' value='$componente[3]' size='6' maxlength='6' onBlur='setColor();' class='{$class}' /> *
          <a href='#' class='link' onClick=\"newwin=window.open('./escolhe_coordenada.php', 'escolhe_coordenada', 'width=400, height=430, scollbars=false');\">Seleciona Coordenada</a>";
            break;

          case 'adicionar':
            $retorno .=  "<select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}' id='{$nome}' >";

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  " selected";
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .= "</select>";
            $retorno .= "<a href='#' class='imagem' onClick=\"receptor=document.formcadastro.{$nome};newwin=window.open('".$componente[8]."', '{$nome}_link', 'width=".$componente[9].", height=".$componente[10].", scollbars=false');\"> <img src='imagens/nvp_bot_novo.png' border=0 alt='".$componente[7]."'></a>";
            $retorno .= "
            <script type=\"text/javascript\">
            var receptor;
              document.formcadastro.{$nome}.muda = function(valor, texto)
              {
                novoIndice = this.options.length;
                this.options[novoIndice] = new Option(texto);
                opcao = this.options[novoIndice];
                opcao.value    = valor;
                opcao.selected = true;
                {$componente[11]}
              }
            </script>
            ";

            break;

          case 'detalhe':
            $retorno .= "<select onchange=\"if(this.value > 0) CarregaDetalhe( '{$nome}_div','".$componente[8]."'+this.value); $componente[5]\"  class='{$class}' name='{$nome}' id='{$nome}' >";
            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  " selected";
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .=  "</select>";

            if ($componente[4]) {
              $carrega ="CarregaDetalhe('{$nome}_div','".$componente[8].$componente[4]."');";
            }

            $onClick  = "AbreFecha('{$nome}_div', '{$nome}_img');";
            $retorno .= "<a href='#' class='imagem' onClick=\"$carrega $onClick\"> <img src='imagens/log-info.gif' border=0 alt='".$componente[7]."' id='{$nome}_img'></a>";
            $retorno .= "<div style='overflow: hidden;height: 1px;position: relative;width:100%;' name='{$nome}_div' id='{$nome}_div'></div>";

            $classe = ($classe == 'formmdtd') ?
              'formlttd' : 'formmdtd';

            break;

          case 'cpf':
            $retorno .= "<input onKeyPress=\"formataCPF(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">$componente[7]";
            break;

          case 'idFederal':
            $retorno .= "<input onkeyPress=\"formataIdFederal(this,event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[7]}> {$componente[8]}";
            break;

          case 'cnpj':
            $retorno .= "<input onKeyPress=\"formataCNPJ(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">";
            break;

          case 'cnpj_pesq':
            $retorno .= "<input onKeyPress=\"formataCNPJ(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">";
            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" onclick=\"pesquisa_valores_popless('{$componente[7]}?campos={$componente[8]}', '{$nome}')\"> {$componente[9]}";
            break;

          case 'check':
            $onClick = '';
            if ($componente[5]) {
              $onClick = "onclick=\"{$componente[5]}\"";
            }

            $retorno .= "<input value=\"{$componente[3]}\" type='checkbox' name=\"{$nome}\" id=\"{$nome}\" {$onClick}";

            if ($componente[3]) {
              $retorno .= ' checked';
            }

            $retorno .= " {$componente[6]}> {$componente[4]}";
            break;

          case 'checkDuplo':
            $retorno .= "<input type='checkbox' name=\"{$nome}\"";

            if ($componente[3]) {
              $retorno .= ' checked';
            }

            $retorno .= "> {$componente[4]} ";
            $foiDuplo = TRUE;
            break;

          case 'latitude':
            $retorno .= "<input onBlur=\"colocaMenos(this);\" class='{$class}' type='text' name=\"{$nome}_graus\" value=\"{$componente[3]}\" size=\"3\" maxlength=\"3\"><sup style='vertical-align:top;'>o</sup><input class='{$class}' type='text' name=\"{$nome}_min\" value=\"{$componente[7]}\" size=\"2\" maxlength=\"2\"><sup style='vertical-align:top;'>'</sup><input class='{$class}' type='text' name=\"{$nome}_seg\" value=\"{$componente[8]}\" size=\"2\" maxlength=\"2\"><sup style='vertical-align:top;'>\"</sup>";
            break;

          case 'longitude':
            $retorno .= "<input onBlur=\"colocaMenos(this);\" class='{$class}' type='text' name=\"{$nome}_graus\" value=\"{$componente[3]}\" size=\"3\" maxlength=\"3\"><sup style='vertical-align:top;'>o</sup><input class='{$class}' type='text' name=\"{$nome}_min\" value=\"{$componente[7]}\" size=\"2\" maxlength=\"2\"><sup style='vertical-align:top;'>'</sup><input class='{$class}' type='text' name=\"{$nome}_seg\" value=\"{$componente[8]}\" size=\"2\" maxlength=\"2\"><sup style='vertical-align:top;'>\"</sup>";
            break;

          case 'texto':
            if ($componente[10]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            if ($componente[9] && $componente[8]) {
              $evento = " {$componente[9]}=\"{$componente[8]}\"";
            }
            else {
              $evento = '';
            }

            $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$evento} {$disabled}> {$componente[7]}";
            break;

          case 'monetario':
            if ($componente[9]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            if (! $componente[6]) {
              $componente[6] = $componente[11];
            }

            $retorno .= "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $componente[8] = \"{$componente[7]}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$disabled}> {$componente[6]}";
            break;

          case 'monetarioDuplo':
            if ($componente[9]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            if (! $componente[6]) {
              $componente[6] = $componente[11];
            }

            $retorno .= "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $componente[8] = \"{$componente[7]}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$disabled}> {$componente[6]}";
            $foiDuplo = TRUE;
            break;

          case 'email':
            $retorno .=  "<input class='{$class}' style='text-align: left;' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\"> $componente[7]";
            break;

          case 'suggest':
            $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" onfocus=\"DOM_focusIn();\" onblur=\"DOM_focusOut();\" class=\"DOM_suggest\" style=\"width:250px;\">";
            $retorno .= "<script type=\"text/javascript\">";
            $retorno .= "DOM_atual = new Object();";
            $retorno .= "DOM_atual.objectId = \"{$nome}\";";
            $retorno .= "DOM_atual.arquivo = \"{$componente[7]}\";";
            $retorno .= "document.getElementById(\"{$nome}\").onkeyup=function(e){ DOM_atual.objectId=\"{$nome}\"; DOM_navegaUpDown(e); };";
            $retorno .= "</script>";
            break;

          case 'textoPesquisa':
            if ($componente[10]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[12]}='{$componente[11]}' {$disabled}> ";

            if ($componente[9]) {
              // tem serialized campos
              $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[8]}?campos={$componente[9]}', '{$nome}')\"> {$componente[7]}";
            }
            else {
              $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[8]}', '{$nome}')\"> {$componente[7]}";
            }

            break;

          case 'textoInv':
            $retorno .=  "<input class='{$class}' type='text' name=\"{$componente[10]}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true {$componente[9]}=\"{$componente[8]}\">&nbsp;$componente[7]";
            break;

          case 'textoDuploInv':
            $retorno .= "<input class='{$class}' type='text' name=\"{$componente[10]}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true>";
            $foiDuplo = TRUE;
            break;

          case 'textoExterno':
            $retorno .=  "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true> <a href='#' onclick=\"javascript:$componente[7]\">$componente[8]</a>";
            break;

          case 'adicionarProcurar':
            $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true> <a href='#' onclick=\"javascript:$componente[7]\">$componente[8]</a>&nbsp;&nbsp;<a  href='#' onclick=\"javascript:$componente[9]\">$componente[10]</a>";
            break;

          case 'senha':
            $retorno .=  "<input class='{$class}' type='password' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">";
            break;

          case 'textoDuplo':
            if ($componente[10]) {
              $disabled = 'disabled';
            }
            else {
              $disabled = '';
            }

            $retorno .=  "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\" {$disabled}>";
            $foiDuplo = TRUE;
            break;

          case 'memo':
            if ($componente[11]) {
              $retorno .=  "<textarea class='{$class}' name=\"{$nome}\" id=\"{$nome}\" cols=\"{$componente[4]}\" rows=\"{$componente[5]}\" style='wrap:virtual' {$evento} disabled ";
            }
            else {
              $retorno .=  "<textarea class='{$class}' name=\"{$nome}\" id=\"{$nome}\" cols=\"{$componente[4]}\" rows=\"{$componente[5]}\" style='wrap:virtual' {$evento} ";
            }

            if ($componente[9] && $componente[10]) {
              $evento = "{$componente[9]}=\"{$componente[10]}\"";
            }
            else {
              $evento = "";
            }

            if ($componente[7] > 0) {
              $retorno .=  "ONKEYDOWN='this.form.{$nome}.value=this.form.{$nome}.value.length >= {$componente[7]} ? this.form.{$nome}.value.substring(0,{$componente[7]}-1) : this.form.{$nome}.value;'";
            }

            $retorno .= ">{$campo_valor}</textarea>\n";
            $foiDuplo = $componente[8];
            break;

          case 'lista':
            if (is_numeric($componente[9])) {
              $multiple = " multiple='multiple' SIZE='{$componente[9]}' ";
            }
            else {
              $multiple = '';
            }

            $retorno .= "<select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}' id='{$nome}' {$componente[8]} {$multiple}>";
            $opt_open = FALSE;

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              if (substr($texto, 0, 9) == 'optgroup:') {
                $opt_open = TRUE;
                $retorno .= "<optgroup label=\"" . substr( $texto, 9 ) . "\">";
              }
              elseif (substr($texto, 0, 8) == 'optgroup') {
                // optgroup
                if ($opt_open) {
                  $opt_open = FALSE;
                  $retorno .= '</optgroup>';
                }
              }
              else {
                // option normal
                $retorno .= "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";
                if (is_array($componente[4])) {
                  if ($chave == $componente[4][$adicionador_indice]) {
                    $retorno .= " selected";
                  }
                }
                elseif ($chave == $componente[4]) {
                  $retorno .= " selected";
                }

                $retorno .= ">$texto</option>";
              }
            }

            if ($opt_open) {
              $retorno .= '</optgroup>';
            }

            $retorno .= "</select> {$componente[7]}";
            break;

          case 'AddForm':
            $arr_campos["linha_{$nome}"] = array('addForm', $nome, $arr_campos);

            $span  = '';
            $input = '';
            $cont  = 0;

            if ($arr_campos) {
              foreach ($arr_campos as $campo => $atributo) {
                $span .= "var span$cont = document.createElement( \"span{$cont}\" );";
                $cont++;
              }
            }

            $retorno = "
              function adiciona (obj)
              {
                tabela = document.getElementById( 'table_fotos' );
                linha = tabela.insertRow( tabela.rows.length );

                var td = document.createElement( \"td\" );
                var input = document.createElement( \"input\" );
                var botao = document.createElement( \"input\" );

                botao.setAttribute( \"type\", \"button\");
                botao.setAttribute( \"id\", \"foto_[]\" );
                botao.setAttribute( \"name\", \"foto_[]\" );
                botao.setAttribute( \"onclick\", \"adiciona(this);\" );
                botao.setAttribute( \"value\", \"Adicionar[+]\"  );

                input.setAttribute( \"type\", \"text\" );
                input.setAttribute( \"id\", \"foto[]\" );
                input.setAttribute( \"name\", \"foto[]\"  );
                input.setAttribute( \"maxlength\", \"50\"  );
                input.setAttribute( \"size\", \"50\"  );

                obj.setAttribute( \"onclick\", \"this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);\"  );
                obj.setAttribute( \"value\", \"Remover[-]\"  );

                linha.appendChild(td);
                td.appendChild(input);
                td.appendChild(botao);
              }
            ";

          case 'addCampos':
            $nome = $componente[1];
            $nome = str_replace(' ', '_', $nome);

            $arr_campos  = $componente[3];
            $arr_valores = $componente[4];
            $arr_tipos   = array();

            $input        = '';
            $strCampos    = '';
            $setAtributos = '';
            $addCampo     = '';
            $valores      = '';
            $br           = '';
            $ValEditar    = '';

            $quebra = '';
            $inicio = "<div id=\"div{$nome}0\">";

            $cont = 0;

            if ($arr_campos) {
              foreach ($arr_campos as $campo => $atributo) {
                $atributo['quebra'] = ! isset($atributo['quebra']) ?
                  TRUE : $atributo['quebra'];

                $arr_tipos[$campo] = $atributo['type'];

                if (! empty($atributo['campo'])) {
                  $atributo['campo'] = "<br>{$atributo['campo']}:<br>";
                }
                else {
                  $atributo['campo'] = '';
                }

                $disabled = ! empty($atributo['disabled']) ?
                  ' disabled = true ' : '';

                $inicio .= "$quebra<span >{$atributo['campo']}</span>
                <input id='{$atributo['id']}0' name='{$atributo['name']}[]' ";

                $input .= "var input_$campo = document.createElement( \"input\" );\n";
                $input .= "var span_$campo = document.createElement( \"span\" );\n";
                $input .= "span_$campo.setAttribute( \"id\", \"span_$campo\"+cont{$nome} );\n";

                $id = $atributo['id'];

                if ($atributo) {
                  foreach ($atributo as $formId => $valor) {
                    if ($formId == 'id') {
                      $setAtributos .= "input_{$campo}.setAttribute( \"$formId\", \"{$valor}\"+cont{$nome} );\n";
                    }
                    elseif ($formId == 'name') {
                      $valores .= "document.getElementById( \"{$id}\"+cont{$nome} ).name = \"{$valor}[]\"; \n ";
                    }
                    elseif ($formId != 'quebra' || $formId == 'campo') {
                      $valor = $formId == "disabled" ? "TRUE" : $valor;
                      $setAtributos .= "input_{$campo}.setAttribute( '$formId', '$valor' );\n";
                      $inicio .= "$formId = '$valor'" ;
                    }
                  }
                }

                $inicio   .= "{$disabled} >";
                $quebra    = $atributo['quebra'] == TRUE || !isset($atributo['quebra']) ?
                  '<br>' : '';

                $addCampo .= "\n div_{$nome}.appendChild(span_{$campo});  \n document.getElementById(\"span_$campo\"+cont{$nome}).innerHTML = '$quebra{$atributo['campo']}  ';\n div_{$nome}.appendChild(input_{$campo});\n";

                $atributo['campo'] = '';
              }
            }

            if ($arr_valores) {
              if (array_key_exists('options', $arr_valores)) {
                $opcoes = array_pop($arr_campos);
                print_r($opcoes);
              }

              foreach ($arr_valores as $val) {
                $ValEditar .= "<div>";

                if ($val) {
                  $chave = key($arr_valores);

                  $valor_chave = reset($val);
                  $disabled    = substr($valor_chave, -2);
                  $valor_chave = substr($valor_chave, 0, -2);

                  $disabled = $disabled == '_1' ?
                    'disabled' : '';

                  foreach ($arr_tipos as $id => $type) {
                    if ($type == 'file') {
                      $ValEditar .= "{$valor_chave}<br>";
                    }
                    elseif ($type != 'button') {
                      if ($type == 'hidden') {
                        $valor_chave = (!$valor_chave) ?
                          $chave : $valor_chave;

                        $disabled = '';
                      }
                      elseif(strstr($id, 'data') != FALSE) {
                        $keypress = " onKeyPress=\"formataData(this, event);\" maxlength=\"10\"";
                      }
                      else {
                        $keypress = '';
                      }

                      $ValEditar .= "<input type='{$type}' $keypress class='geral' value='{$valor_chave}' name='edit_{$id}[{$chave}]' $disabled><br>";
                    }

                    $valor_chave = next($val);
                  }

                  $chave = next($arr_valores);
                  $chave = key($arr_valores);
                }
                $ValEditar .= "<img vspace='4' src='imagens/banco_imagens/excluir.gif' onclick='this.parentNode.parentNode.removeChild(this.parentNode);'><hr></div>";
              }
            }

            $inicio .= "<img hspace='5' src='imagens/banco_imagens/incluir.gif' onclick='adiciona_{$nome}();'><br><br>";
            $retorno .= "
              <script type=\"text/javascript\">
                  var cont{$nome} = 1;

                  function adiciona_{$nome}()
                  {
                    principal = document.getElementById('{$nome}');

                    var div_{$nome} = document.createElement(\"div\");\n;
                    var div_linha = document.createElement(\"hr\");\n;

                    $input
                    principal.appendChild(div_{$nome});
                    div_{$nome}.appendChild(div_linha);
                    add_new = '<img hspace=\'5\' src=\'imagens/banco_imagens/incluir.gif\' onclick=\'adiciona_{$nome}();\'>';
                    del = '<img hspace=\'5\' src=\'imagens/banco_imagens/excluir.gif\' onclick=\'this.parentNode.parentNode.removeChild(this.parentNode);\'><br><br>';

                    div_{$nome}.setAttribute(\"id\", \"div{$nome}\"+cont{$nome});\n
                    $setAtributos

                    $addCampo

                    document.getElementById('div{$nome}'+cont{$nome}).innerHTML += add_new;
                    document.getElementById('div{$nome}'+cont{$nome}).innerHTML += del;

                    $valores

                    cont{$nome}++;
                  }
              </script>

            <div =\"valores{$nome}\">
              $ValEditar
            </div>

            <div id=\"{$nome}\">
              $inicio
            </div>
            ";
            break;

          case 'listaDin':
            $url = 'var campos = [];';

            if ($componente[12]) {
              foreach ($componente[12] as $key => $campo) {
                $url .= "campos[$key] = '$campo';";
              }
            }

            $rand   = rand(1, 10000);
            $script = "
            <script type=\"text/javascript\">
            function get_{$componente[11]}()
            {
              $url
              url = '?';
              junta = '';
              for (i = 0; i < campos.length; i++) {
                url += junta+campos[i]+'='+document.getElementById(campos[i]).value;
                junta = '&';
              }

              // define qual a funcao que devera ser executada quando o xml for carregado
              DOM_execute_when_xmlhttpChange = function() { get_{$componente[11]}Done(); };
              strURL = \"{$componente[10]}\"+url+'&rand='+\"{$url}\";
              DOM_loadXMLDoc(strURL);
            }

            function get_{$componente[11]}Done()
            {
              var valores = [];
              var campos  = [];
              ObjSelect   = document.getElementById(\"{$componente[7]}\");
              ObjSelect.options.length =0;
              ObjSelect.options[ObjSelect.options.length] = new Option ('Selecione', '0', true, true);

              for (i = 0; i < DOM_itensArray.length; i = i + 2) {
                objXML = DOM_itensArray[i].firstChild;
                valores[i] = objXML.data;

                objXML = DOM_itensArray[i+1].firstChild;
                campos[i] = objXML.data;

                ObjSelect.options[ObjSelect.options.length] = new Option (campos[i], valores[i],  true, true);
              }

              ObjSelect.value = 0;
              {$componente[13]}
            }
            </script>
            ";

            $retorno .=  "$script<select onchange=\"{$componente[5]} get_{$componente[11]}();\"  class='{$class}' name='{$nome}' id='{$nome}' {$componente[8]}>";

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  " selected";
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .=  "</select>";

            if ($componente[14]) {
              $foiDuplo = TRUE;
            }

            break;

          case "listaativarpeso" :
            $retorno .= "<input type='checkbox' ";
            $retorno .= ($componente[5]) ? "checked" : "";
            $retorno .= " name='{$nome_chk}' onclick='ativaCampo(\"{$nome}\")'>&nbsp;&nbsp;<select ";
            $retorno .= ($componente[5]) ? "" : "disabled";
            $retorno .= " class='{$class}' name='{$nome}_lst' id='{$nome}_lst'>";

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  " selected";
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .= "</select> &nbsp;&nbsp; <input ";

            $retorno .= ($componente[5]) ?
              '' : 'disabled';

            $retorno .= " name='{$nome}_val' id='{$nome}_val' type='text' size='{$componente[7]}' maxlength='{$componente[8]}' value={$componente[9]}>";
            break;

          case "listaativarpesoselect" :
            $retorno .= "<input type='checkbox' ";
            $retorno .= ($componente[5]) ? "checked" : "";
            $retorno .= " name='{$nome_chk}' onclick='ativaCampo(\"{$nome}\")'>&nbsp;&nbsp;<select ";
            $retorno .= ($componente[5]) ? "" : "disabled";
            $retorno .= " class='{$class}' name='{$nome}_lst' id='{$nome}_lst'>";

            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  " selected";
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .=  "</select> &nbsp;&nbsp;";
            $retorno .= "<select ";

            $retorno .= ($componente[5]) ?
              '' : 'disabled';

            $retorno .= " class='{$class}' name='{$nome}_val' id='{$nome}_val'>";
            reset($componente[6]);

            while (list( $chave, $texto) = each($componente[6])) {
              $retorno .= "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              if ($chave == $componente[9]) {
                $retorno .=  ' selected';
              }

              $retorno .=  ">$texto</option>";
            }
            $retorno .=  "</select>";
            break;

          case "listapesquisa" :
            $class = ($componente[14]) ?
              'obrigatorio' : 'geral';

            $retorno .= "<select onchange=\"{$componente[5]}\" class='{$class}' name='{$nome}' id='{$nome}' {$componente[11]}>";
            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .=  "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .=  ' selected';
              }

              $retorno .=  ">$texto</option>";
            }

            $retorno .= '</select> ';

            if ($componente[13]) {
              // Tem serialized campos
              $retorno .=  "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[7]}?campos={$componente[13]}', '{$nome}')\"> {$componente[8]}";
            }
            else {
              if ($componente[12]) {
                // Vai abrir em um div
                $retorno .=  "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[7]}', '{$nome}')\"> {$componente[8]}";
              }
              else {
                // Abre num pop-up
                $retorno .=  "<img id='lupa' src=\"imagens/lupa.png\" alt=\"Pesquisa\" name='{$nome}_lupa' id='{$nome}_lupa' border=\"0\" onclick=\"pesquisa_valores_f('{$componente[7]}', '{$nome}', '{$componente[9]}', '{$componente[10]}')\"> {$componente[8]}";
              }
            }
            break;

          case 'listaDupla':
            $retorno .=  "<select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}' id='{$nome}' {$componente[8]}>";
            reset($componente[3]);

            while (list($chave, $texto) = each($componente[3])) {
              $retorno .= "<option value=\"".urlencode($chave)."\"";

              if ($chave == $componente[4]) {
                $retorno .= " selected";
              }

              $retorno .= ">$texto</option>";
            }

            $retorno .= "</select>";
            $foiDuplo = TRUE;
            break;

          case 'arquivo':
            $retorno .= "<input class='{$class}' name=\"{$nome}\" id=\"{$nome}\" type='file' size=\"{$componente[4]}\" value=\"{$componente[3]}\">";

            if (! empty($componente[5])) {
              $retorno.= "&nbsp;$componente[5]";
            }

            break;

          case 'email':
            $retorno .= "<a href='www.google.com.br' class='linkBory'>Enviar Por Email</a>";
            break;

          case 'emailDuplo':
            $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\">";
            $foiDuplo = TRUE;
            break;

          case 'radio':
            $primeiro = TRUE;

            reset($componente[3]);

            $retorno .= "<span onclick=\"{$componente[5]}\" >";

            while (list($chave, $texto) = each($componente[3])) {
              if ($primeiro) {
                $primeiro = FALSE;
                $id = "id=\"{$nome}\"";
              }
              else {
                $id = '';
                $retorno .= '<br>';
              }

              $retorno .= "<input type='radio' class='{$class}' name=\"{$nome}\" {$id} value=\"".urlencode($chave)."\"";
              if ($chave == $componente[4]) {
                $retorno .=  " checked";
              }

              $retorno .=  ">$texto";
            }

            $retorno .=  "</span>";
            break;

          case 'theEditor':
            $theEditorCampo  = $nome;
            $theEditorForm   = $this->__nome;
            $theEditorValue  = $componente[2];
            $theEditorHeight = $componente[3];
            $theEditorWidth  = $componente[4];
            require_once 'include/clsEditor.inc.php';
            break;
        } // endswitch

        if ($this->erros[$nome]) {
          $retorno .=  "<br><font color=red>".$this->erros[$nome]."</font>";
        }

        if (! $foiDuplo) {
          $retorno .= "</span></td></tr>";
        }
      }

      if ($todos_inline) {
        $foiDuplo = TRUE;
      }
    }

    return $retorno;
  }

  function MakeFormat()
  {
    $ret = " \n{$onload} \n
    function CarregaDetalhe(id_div, endereco)
    {
      var elemento_div = document.getElementById(id_div);
      if (endereco != '') {
        xmlhttp.open(\"GET\", endereco, true);
        xmlhttp.onreadystatechange = function()
        {
          if (xmlhttp.readyState==4) {
            elemento_div.innerHTML = xmlhttp.responseText;
          }
        }

        xmlhttp.send(null);
      }
    }

    function AbreFecha(id_div, id_img)
    {
      var elemento_div = document.getElementById(id_div);
      var elemento_img = document.getElementById(id_img);

      if (!aberto) {
        elemento_div.style.overflow = 'visible';
        if (goodIE) {
          elemento_div.style.height = '0px';
          elemento_img.src =  '".$config['urlImagens']."excluir_1.gif';
          elemento_img.alt =  'Fechar';
        }
        else {
          elemento_div.style.height = '100%';
          elemento_img.src =  '".$config['urlImagens']."excluir_1.gif';
          elemento_img.alt =  'Fechar';
        }
      }
      else {
        elemento_img.src =  '".$config['urlImagens']."log-info.gif';
        elemento_div.style.overflow = 'hidden';
        elemento_div.style.height = '1px';
        elemento_img.alt =  'Visualizar detalhes';
      }

      aberto = !aberto;
    }

    function AdicionaItem(chave, item, nome_pai, submete)
    {
      var x = document.getElementById(nome_pai);

      opt = document.createElement('OPTION');
      opt.value = chave;
      opt.selected = true;
      opt.appendChild(document.createTextNode(item));

      x.appendChild(opt);
      if (submete) {
    ";

    if ($this->executa_submete) {
      $ret .=  "
        document.".$this->__nome.".".$this->executa_submete;
    }

    $ret .= "
        document.$this->__nome.submit();
      }
    }

    function go(url)
    {
      document.location = url;
    }

    function excluir()
    {
      document.$this->__nome.reset();

      if (confirm('Excluir registro?')) {
        document.$this->__nome.tipoacao.value = 'Excluir';
        document.$this->__nome.submit();
      }
    }

    function ExcluirImg()
    {
      document.$this->__nome.reset();
      if (confirm('Excluir imagem?')) {
        document.$this->__nome.tipoacao.value = 'ExcluirImg';
        document.$this->__nome.submit();
      }
    }
    ";

    return $ret;
  }

  function getCampoTexto($nome, $id = '', $valor = '', $tamanhovisivel = '',
    $tamanhomaximo = '', $evento = '', $disabled = '', $descricao = '',
    $class = '', $descricao = '')
  {
    $id = $id ? $id : $nome;

    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    $tamanhomaximo = $tamanhomaximo ?
      "maxlength=\"{$tamanhomaximo}\"" : '';

    $tamanhovisivel = $tamanhovisivel ?
      "size=\"{$tamanhovisivel}\"" : '';

    $class = $class ?
      "class=\"{$class}\"" : '';

    return "<input {$class} type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" {$tamanhovisivel} {$tamanhomaximo} {$evento} {$disabled}> {$descricao}";
  }

  function getCampoLista($nome, $id = '', $acao = '', $valor, $default,
    $complemento = '', $desabilitado = FALSE, $class, $multiple = FALSE)
  {
    $id = $id ? $id : $nome;

    if (is_numeric($multiple)) {
      $multiple = " multiple='multiple' SIZE='$multiple' ";
    }
    else {
      $multiple = '';
    }

    $retorno  = "<select onchange=\"{$acao}\" class='{$class}' name='{$nome}' id='{$id}' {$desabilitado} $multiple>";
    $opt_open = FALSE;

    reset($valor);

    while (list($chave, $texto) = each($valor)) {
      if (substr($texto, 0, 9) == 'optgroup:') {
        // optgroup
        if ($opt_open) {
          $retorno .= "</optgroup>";
        }

        $retorno .= "<optgroup label=\"" . substr( $texto, 9 ) . "\">";
      }
      else {
        // option normal
        $retorno .= "<option id=\"{$nome}_".urlencode($chave)."\" value=\"".urlencode($chave)."\"";

        if (is_array($default)) {
          if ($chave == $default[$adicionador_indice]) {
            $retorno .= " selected";
          }
        }
        elseif ($chave == $default) {
          $retorno .= " selected";
        }

        $retorno .= ">$texto</option>";
      }
    }

    if ($opt_open) {
      $retorno .= '</optgroup>';
    }

    $retorno .=  "</select> {$complemento}";
    return $retorno;
  }

  function getCampoMonetario($nome, $id = '', $valor = '', $tamanhovisivel,
    $tamanhomaximo, $disabled = FALSE, $descricao = '', $descricao2 = '',
    $class, $evento = 'onChange', $script = '')
  {
    $id = $id ? $id : $nome;

    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    if (! $descricao) {
      $descricao = $descricao2;
    }

    return "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $evento = \"{$script}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}> {$descricao}";
  }

  function getCampoHora($nome, $id = '', $valor = '', $class, $tamanhovisivel,
    $tamanhomaximo, $acao = '', $descricao = '')
  {
    $id = $id ? $id : $nome;

    $valor = strlen($valor) < 6 ? $valor : substr($valor, 0, 5);

    return "<input onKeyPress=\"formataHora(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" $acao>{$descricao}";
  }

  function getCampoRotulo($valor)
  {
    return "<span class=\"form\"> $valor</span>";
  }

  function getCampoCheck($nome, $id = '', $valor, $desc = '', $script = FALSE, $disable = FALSE)
  {
    $id = $id ? $id : $nome;

    $onClick = '';

    if ($script) {
      $onClick = "onclick=\"{$script}\"";
    }

    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    $retorno = "<input type='checkbox' name=\"{$nome}\" id=\"{$id}\" {$onClick}";

    if ($valor) {
      $retorno .= ' checked';
    }

    $retorno .= " {$disabled}> {$desc}";

    return $retorno;
  }

  function getCampoCNPJ($nome, $id = '', $valor, $class, $tamanhovisivel, $tamanhomaximo)
  {
    $id = $id ? $id : $nome;

    return  "<input onKeyPress=\"formataCNPJ(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\">";
  }

  function getCampoCPF($nome, $id = '', $valor, $class, $tamanhovisivel, $tamanhomaximo, $disabled = FALSE)
  {
    $id = $id ? $id : $nome;

    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    return "<input onKeyPress=\"formataCPF(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" $disabled>";
  }

  function getCampoIdFederal($nome, $id = '', $valor, $class, $tamanhovisivel,
    $tamanhomaximo, $disabled = FALSE)
  {
    $id = $id ? $id : $nome;

    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    return "<input onkeyPress=\"formataIdFederal(this,event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}>";
  }

  function getCampoOculto($nome, $valor, $id = '')
  {
    $id = $id ? $id : $nome;

    if ($valor) {
      $valor = urlencode($valor);
    }

    return "<input name='$nome' id='$id' type='hidden' value='{$valor}'>\n";
  }

  function getCampoData($nome, $id = '', $valor, $class, $tamanhovisivel, $tamanhomaximo, $disabled = FALSE)
  {
    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    $id = $id ? $id : $nome;

    return  "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}> \n";
  }

  function getCampoCep($nome, $id = '', $valor, $class, $tamanhovisivel, $tamanhomaximo,
    $disabled = FALSE, $descricao = '')
  {
    if ($disabled) {
      $disabled = "disabled='disabled'";
    }
    else {
      $disabled = '';
    }

    $id = $id ? $id : $nome;

    return $retorno .= "<input onKeyPress=\"formataCEP(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}>$descricao\n";
  }

  /**
   * TODO: Remover método. No único caso possível de ser invocado, não o é
   *   (através de intranet/funcionario_cad.php). É necessário estudar
   *   o método clsCampos::MakeCampos() para entender o caso possível em que
   *   este método seria invocado.
   *
   * @see scripts/padrao.js::pesquisa_valores_f()
   * @see clsCampos::MakeCampos()
   */
  function getCampoTextoPesquisa($nome, $id = '', $valor, $class, $tamanhovisivel,
    $tamanhomaximo, $disabled = FALSE, $caminho, $campos_serializados = NULL,
    $descricao = NULL, $script = NULL, $evento = NULL)
  {
    if ($disabled) {
      $disabled = 'disabled';
    }
    else {
      $disabled = '';
    }

    $id = $id ? $id : $nome;

    $retorno =  "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$evento}='{$script}' {$disabled}> ";

    if ($campos_serializados) {
      // Tem serialized campos
      $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$id}_lupa' onclick=\"pesquisa_valores_popless('{$caminho}?campos={$campos_serializados}', '{$nome}')\">$descricao";
    }
    else {
      $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" onclick=\"pesquisa_valores_f('{$caminho}', '{$nome}')\"> $descricao";
    }

    return $retorno;
  }
}

class clsCampo
{
}