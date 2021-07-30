<?php

class clsCampos extends Core_Controller_Page_Abstract
{
    public $campos = [];

    public $num_espaco = 1;

    public $__nome = 'formcadastro';

    public $__adicionando_tabela = false;

    public $__id_tabela = 1;

    public $__campos_tabela = [];

    public $__cabecalho_tabela = [];

    public $__nm_tabela;

    public $__titulo_tabela;

    public $__largura_tabela;

    public $__valores_tabela = [[]];

    public $__valores_listas_tabela = [[]];

    public $ref_cod_escola;

    public $ref_cod_instituicao;

    public function campoTabelaInicio(
        $nome,
        $titulo = '',
        $arr_campos = [],
        $arr_valores = [[]],
        $largura = '',
        $array_valores_lista = [[]]
    ) {
        $this->__adicionando_tabela = true;

        unset($this->__campos_tabela);

        $this->__campos_tabela = [];
        $this->__cabecalho_tabela = $arr_campos;
        $this->__nm_tabela = $nome;
        $this->__valores_tabela = $arr_valores;
        $this->__titulo_tabela = $titulo;
        $this->__largura_tabela = $largura;
        $this->__valores_listas_tabela = $array_valores_lista;
    }

    public function campoTabelaFim()
    {
        if (count($this->__campos_tabela) && is_array($this->__campos_tabela)) {
            $this->campos['tab_add_' . $this->__id_tabela][] = $this->__campos_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['cabecalho'] = $this->__cabecalho_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['nome'] = $this->__nm_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['valores'] = $this->__valores_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['titulo'] = $this->__titulo_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['largura'] = $this->__largura_tabela;
            $this->campos['tab_add_' . $this->__id_tabela]['valores_lista'] = $this->__valores_listas_tabela;
        }

        unset($this->__cabecalho_tabela);

        $this->__cabecalho_tabela = [];
        $this->__adicionando_tabela = false;
        $this->__id_tabela++;
    }

    public function campoBoolLista(
        $nome,
        $campo,
        $default,
        $val_true = 'Sim',
        $val_false = 'Não',
        $val_undefined = null
    ) {
        $valor = [];
        $valor['f'] = $val_false;
        $valor['t'] = $val_true;

        if (!is_null($val_undefined)) {
            $valor[''] = $val_undefined;
        }

        $this->campoLista($nome, $campo, $valor, $default);
    }

    public function campoArquivo(
        $nome,
        $campo,
        $valor,
        $tamanho,
        $descricao = '',
        $tr_invisivel = false
    ) {
        $this->campos[$nome] = [
            'arquivo',
            $campo,
            '',
            $valor,
            $tamanho,
            $descricao,
            'tr_invisivel' => $tr_invisivel
        ];

        $this->form_enctype = ' enctype=\'multipart/form-data\'';
    }

    public function campoCep(
        $nome,
        $campo,
        $valor,
        $obrigatorio = false,
        $hifen = '-',
        $descricao = false,
        $disable = false
    ) {
        $arr_componente = [
            'cep',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/([0-9]{5})$hifen([0-9]{3})/" : "*(/([0-9]{5})$hifen([0-9]{3})/)",
            $valor,
            10,
            (8 + @strlen($hifen)),
            'nnnnn-nnn',
            $descricao,
            ($disable) ? 'disabled' : ''
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoCheckMultiplo($nome, $label, $opcoes, $selecionados = [])
    {
        $opcoesCampo = [
            'checkMultiplo',
            $label,
            $filtro = '',
            $opcoes,
            $selecionados
        ];

        $this->campos[$nome] = $opcoesCampo;
    }

    public function campoCheck(
        $nome,
        $campo,
        $valor,
        $desc = '',
        $duplo = false,
        $script = false,
        $disable = false,
        $dica = null
    ) {
        $arr_componente = [
            $duplo ? 'checkDuplo' : 'check',
            $this->__adicionando_tabela ? $nome : $campo,
            false,
            $valor,
            $desc,
            $script,
            $dica,
            ($disable) ? 'disabled' : ''
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoCnpj($nome, $campo, $valor, $obrigatorio = false)
    {
        $arr_componente = [
            'cnpj',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : '',
            $valor,
            20,
            18,
            'nn.nnn.nnn/nnnn-nn'
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoCnpjPesq(
        $nome,
        $campo,
        $valor,
        $arquivo_pesquisa,
        $parametros_serializados = false,
        $obrigatorio = false
    ) {
        $this->campos[$nome] = [
            'cnpj_pesq',
            $campo,
            $obrigatorio ? "/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : "*(/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/)",
            $valor,
            20,
            18,
            'nn.nnn.nnn/nnnn-nn',
            $arquivo_pesquisa,
            $parametros_serializados
        ];
    }

    public function campoCpf($nome, $campo, $valor, $obrigatorio = false, $descricao = false, $disabled = false, $onChange = '')
    {
        $arr_componente = [
            'cpf',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/" : '',
            $valor,
            16,
            14,
            'nnn.nnn.nnn-nn',
            $descricao,
            $disabled,
            $onChange
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoRA($nome, $campo, $valor, $obrigatorio = false, $descricao = false, $disabled = false)// RA = Registro do Aluno, aluno_estado_id
    {
        $arr_componente = [
            'cpf',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{1}/" : '',
            $valor,
            13,
            13,
            'nnn.nnn.nnn-n ou nnn.nnn.nnn',
            $descricao,
            $disabled
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoIdFederal(
        $nome,
        $campo,
        $valor,
        $obrigatorio = false,
        $invisivel = false,
        $descricao = false
    ) {
        $arr_componente = [
            'idFederal',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/+/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/" : '',
            $valor,
            20,
            18,
            'nnn.nnn.nnn-nn ou nn.nnn.nnn/nnnn-nn',
            $invisivel ? 'disabled' : '', $descricao
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function addHtml($html)
    {
        $this->campos['html'] = $html;
    }

    public function campoData(
        $nome,
        $campo,
        $valor,
        $obrigatorio = false,
        $descricao = '',
        $duplo = false,
        $acao = '',
        $disabled = false,
        $teste = null,
        $dica = 'dd/mm/aaaa'
    ) {
        $arr_componente = [
            $duplo ? 'dataDupla' : 'data',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? "/^(((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))$/" : "*/^(((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))$/",
            $valor,
            9,
            10,
            $dica,
            $descricao,
            $acao,
            $disabled,
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoDataDiaMes(
        $nome,
        $campo,
        $valor,
        $obrigatorio = false,
        $descricao = '',
        $duplo = false,
        $acao = '',
        $disabled = false,
        $teste = null,
        $dica = 'dd/mm'
    ) {
        $arr_componente = [
            $duplo ? 'dataDupla' : 'data',
            $this->__adicionando_tabela ? $nome : $campo,
            false,
            $valor,
            9,
            10,
            $dica,
            $descricao,
            $acao,
            $disabled,
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoHora($nome, $campo, $valor, $obrigatorio = false, $descricao = '', $acao = '', $limitaHora = true, $desabilitado = false, $maxLength = 5)
    {
        $arr_componente = [
            'hora',
            $this->__adicionando_tabela ? $nome : $campo,
            $limitaHora ? ($obrigatorio ? '/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])(:[0-5][0-9])?$/' : '*(/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])(:[0-5][0-9])?$/)') : ($obrigatorio ? '/[0-9]{2}:[0-9]{2}/' : '*(/[0-9]{2}:[0-9]{2}/)'),
            $valor,
            6,
            $maxLength,
            'hh:mm',
            $descricao,
            $acao,
            $desabilitado ? 'disabled="disabled"' : ''
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoHoraServidor($nome, $campo, $valor, $obrigatorio = false, $descricao = '', $acao = '', $limitaHora = true)
    {
        $arr_componente = [
            'hora',
            $this->__adicionando_tabela ? $nome : $campo,
            $limitaHora ? ($obrigatorio ? '/^([0-9]?[0-9]|9[0-9]):([0-5][0-9])(:[0-5][0-9])?$/' : '*(/^([0-9]?[0-9]|9[0-9]):([0-5][0-9])(:[0-5][0-9])?$/)') : ($obrigatorio ? '/[0-9]{2}:[0-9]{2}/' : '*(/[0-9]{9}:[0-9]{2}/)'),
            $valor,
            6,
            5,
            'hh:mm',
            $descricao,
            $acao
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoLista(
        $nome,
        $campo,
        $valor,
        $default = null,
        $acao = '',
        $duplo = false,
        $descricao = '',
        $complemento = '',
        $desabilitado = false,
        $obrigatorio = true,
        $multiple = false
    ) {
        $filtro = '';

        if ($obrigatorio) {
            $filtro = '/[^ ]/';
        }

        $arr_componente = [
            $duplo ? 'listaDupla' : 'lista',
            ($this->__adicionando_tabela === true ? $nome : $campo),
            $filtro,
            $valor,
            $default,
            $acao,
            $descricao,
            $complemento,
            $desabilitado ? 'disabled=\'disabled\'' : '',
            $multiple
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    /**
     * Configurar campo do tipo ListaPesquisa
     *
     * @param $options array
     */
    public function setOptionsListaPesquisa($nome, array $options)
    {
        $this->campos[$nome] = ['listapesquisa'];

        foreach ($options as $key => $option) {
            $this->campos[$nome][] = $option;
        }
    }

    /**
     * [Obsoleta] Ver função setOptionsListaPesquisa
     *
     * TODO: converter todos que utilizam essa fução para a
     * função setOptionsListaPesquisa
     */
    public function campoListaPesq(
        $nome,
        $campo,
        $valor,
        $default,
        $caminho = '',
        $acao = '',
        $duplo = false,
        $descricao = '',
        $descricao2 = '',
        $flag = null,
        $pag_cadastro = null,
        $disabled = '',
        $div = false,
        $serializedcampos = false,
        $obrigatorio = false
    ) {
        $this->campos[$nome] = [
            'listapesquisa',
            $campo,
            $obrigatorio ? '/[^ ]/' : '',
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
        ];
    }

    public function campoMemo(
        $nome,
        $campo,
        $valor,
        $colunas,
        $linhas,
        $obrigatorio = false,
        $descricao = '',
        $conta = '',
        $duplo = false,
        $script = false,
        $evento = 'onclick',
        $disabled = false
    ) {
        $this->campos[$nome] = [
            'memo',
            $campo,
            $obrigatorio ? '/[^ ]/' : '',
            $valor,
            $colunas,
            $linhas,
            $descricao,
            $conta,
            $duplo,
            $evento,
            $script,
            $disabled
        ];
    }

    public function campoNumero(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel = null,
        $tamanhomaximo = null,
        $obrigatorio = false,
        $descricao = '',
        $descricao2 = '',
        $script = false,
        $evento = false,
        $duplo = false,
        $disabled = false
    ) {
        $arr_componente = [
            $duplo ? 'textoDuplo' : 'texto',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? '/^-?\\d*\\.{0,1}\\d+$/' : '*(/^-?\\d*\\.{0,1}\\d+$/)',
            $valor,
            $tamanhovisivel,
            $tamanhomaximo,
            $descricao ? $descricao : 'somente números',
            $descricao2,
            $script,
            $evento,
            $disabled
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoMonetario(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel,
        $tamanhomaximo,
        $obrigatorio = false,
        $descricao = '',
        $script = '',
        $evento = 'onChange',
        $disabled = false,
        $show_sub = true,
        $descricao2 = '',
        $duplo = false
    ) {
        $arr_componente = [
            $duplo ? 'monetarioDuplo' : 'monetario',
            $this->__adicionando_tabela ? $nome : $campo,
            $obrigatorio ? '/^[0-9.,]+$/' : '*(/^[0-9.,]+$/)',
            $valor,
            $tamanhovisivel,
            $tamanhomaximo,
            $descricao,
            $script,
            $evento,
            $disabled,
            $show_sub,
            $descricao2
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoOculto($nome, $valor)
    {
        $arr_componente = [
            'oculto',
            $nome,
            '',
            $valor
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela['oculto'][] = $arr_componente;
        }
    }

    public function campoRadio($nome, $campo, $valor, $default, $acao = '', $descricao = '')
    {
        $this->campos[$nome] = [
            'radio',
            $campo,
            '',
            $valor,
            $default,
            $acao,
            $descricao
        ];
    }

    public function campoRotulo($nome, $campo, $valor = '', $duplo = false, $descricao = '', $separador = ':')
    {
        $arr_componente = [
            $duplo ? 'rotuloDuplo' : 'rotulo',
            $this->__adicionando_tabela ? $nome : $campo,
            '',
            $valor,
            6 => $descricao,
            'separador' => $campo == '' ? '' : $separador
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoSenha($nome, $campo, $valor = '', $obrigatorio = false, $descricao = '')
    {
        $this->campos[$nome] = [
            'senha',
            $campo,
            $obrigatorio ? '/[^ ]/' : '',
            $valor,
            10,
            100,
            $descricao
        ];
    }

    public function campoTexto(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel = null,
        $tamanhomaximo = null,
        $obrigatorio = false,
        $expressao = false,
        $duplo = false,
        $descricao = '',
        $descricao2 = '',
        $script = '',
        $evento = 'onKeyUp',
        $disabled = false
    ) {
        $arr_componente = [
            $duplo ? 'textoDuplo' : 'texto',
            $this->__adicionando_tabela ? $nome : $campo,
            $expressao ? $expressao : ($obrigatorio ? '/[^ ]/' : ''),
            $valor,
            $tamanhovisivel,
            $tamanhomaximo,
            $descricao,
            $descricao2,
            $script,
            $evento,
            $disabled
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoEmail(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel,
        $tamanhomaximo,
        $obrigatorio = false,
        $expressao = false,
        $duplo = false,
        $descricao = '',
        $descricao2 = '',
        $script = ''
    ) {
        $this->campos[$nome] = [
            $duplo ? 'emailDuplo' : 'email',
            $campo,
            $expressao ? $expressao : ($obrigatorio ? "/^[a-z_\-\.0-9]+$/" : ''),
            $valor,
            $tamanhovisivel,
            $tamanhomaximo,
            $descricao,
            $descricao2,
            $script
        ];
    }

    public function campoTextoPesquisa(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel,
        $tamanhomaximo,
        $obrigatorio = false,
        $caminho = '',
        $expressao = false,
        $duplo = false,
        $descricao = '',
        $descricao2 = '',
        $serializedcampos = null,
        $disabled = false,
        $script = '',
        $evento = 'onChange'
    ) {
        $arr_componente = [
            $duplo ? 'textoPesquisaDuplo' : 'textoPesquisa',
            $this->__adicionando_tabela ? $nome : $campo,
            $expressao ? $expressao : ($obrigatorio ? '/[^ ]/' : ''),
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
        ];

        if (!$this->__adicionando_tabela) {
            $this->campos[$nome] = $arr_componente;
        } else {
            $this->__campos_tabela[] = $arr_componente;
        }
    }

    public function campoTextoInv(
        $nome,
        $campo,
        $valor,
        $tamanhovisivel,
        $tamanhomaximo,
        $obrigatorio = false,
        $expressao = false,
        $duplo = false,
        $descricao = '',
        $descricao2 = '',
        $script = '',
        $evento = 'onKeyUp',
        $name = ''
    ) {
        $name = $name ? $name : $nome;
        $this->campos[$nome] = [
            $duplo ? 'textoDuploInv' : 'textoInv',
            $campo,
            $expressao ? $expressao : ($obrigatorio ? '/[^ ]/' : ''),
            $valor,
            $tamanhovisivel,
            $tamanhomaximo,
            $descricao,
            $descricao2,
            $script,
            $evento,
            $name
        ];
    }

    public function campoQuebra()
    {
        $this->campos['espaco' . $this->num_espaco] = ['espaco', '', '', '', '', '', '', ''];
        $this->num_espaco++;
    }

    public function campoQuebra2($cor = '#47728f', $altura = 2)
    {
        $this->campos['linha_preta' . $this->num_espaco] = [
            'linha_preta',
            'cor' => $cor,
            'altura' => $altura,
            '',
            '',
            '',
            '',
            ''
        ];

        $this->num_espaco++;
    }

    public function MakeCampos(
        $array_campos = null,
        $adicionador_indice = null,
        $todos_inline = false,
        $todos_disabled = false,
        $junta_linhas = false,
        $start_md = null
    ) {
        $retorno = '';
        $style = '';

        if (!$array_campos) {
            $arr_campos = $this->campos;
        }

        reset($arr_campos);
        $campo_anterior = '';
        $md = true;

        if (!is_null($start_md) && is_bool($start_md)) {
            $md = $start_md;
        }

        $foiDuplo = $junta_linhas;

        // Marca quantos valores foram passados para o prenchimento das repetições
        $adicionador_total_valores = 5;

        $javascript = '
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

      this.isIE      = (navigator.appName.indexOf(\'Microsoft\') != -1) ? 1 : 0;
      this.nome      = name;
      this.campos    = new Array();
      this.reordenar = true;

      this.getId = function() { return this.id; }

      var numColumns = document.getElementById(This.nome).rows[1].childNodes.length;

      var row = document.getElementById(This.nome).insertRow(document.getElementById(This.nome).rows.length-1);

      for (var ct = 0; ct < numColumns - 1; ct++) {
        var campo = document.getElementById(This.nome).rows[2].childNodes[ct].cloneNode(true);

        if (This.isIE) {
          if(campo.childNodes[0].type == \'text\')
            campo.childNodes[0].value  = \'\';
          else if(campo.childNodes[0].type == \'select-one\')
            campo.childNodes[0].value  = \'\';
          else if(campo.childNodes[0].type == \'checkbox\')
            campo.childNodes[0].checked = false;
        }
        else {
          if (campo.childNodes[1].type == \'text\')
            campo.childNodes[1].value  = \'\';
          else if(campo.childNodes[1].type == \'select-one\')
            campo.childNodes[1].value  = \'\';
          else if(campo.childNodes[1].type == \'checkbox\')
            campo.childNodes[1].checked = false;
        }

        This.campos.push(campo);
      }

      var campo         = document.getElementById(This.nome).rows[2].childNodes[ct].cloneNode(true);
      var campos_oculto = campo.getElementsByTagName(\'INPUT\');

      for (var co = 0; co < campos_oculto.length; co++) {
        campos_oculto[co].id = /[a-zA-Z_-]*/.exec(campos_oculto[co].id) + \'[\' + This.id +\']\';
        campos_oculto[co].value = \'\';
      }

      This.campos.push(campo);
    }

    // Call the constructor
    this.constructor();

    this.classe = (this.id % 2 == 0 )? \'formlttd\' : \'formmdtd\';
    this.addRow = function()
    {
      var This = this;

      This.classe    = (This.id % 2 == 0 )? \'formmdtd tr_\' + This.nome : \'formlttd tr_\' + This.nome;
      var numColumns = This.campos.length

      var row = document.getElementById(This.nome).insertRow(document.getElementById(This.nome).rows.length-2);

      row.setAttribute("id", "tr_"+This.nome+"["+This.id+"]");
      row.setAttribute("name", "tr_"+This.nome+"[]");

      row.className = This.classe;

      for (var ct = 0; ct < numColumns - 1; ct++) {
          var campo       = This.campos[ct].cloneNode(true);
          campo.className = This.classe;
          campo.setAttribute("id", /[a-zA-Z_-]*/.exec(campo.id)+"["+This.id+"]");

          if (This.isIE) {
            campo.childNodes[0].id = /[a-zA-Z_-]*/.exec(campo.childNodes[0].id) + \'[\' + This.id +\']\';
            campo.childNodes[0].name = /[a-zA-Z_-]*/.exec(campo.childNodes[0].name) + \'[\' + This.id +\']\';

            if (campo.childNodes[0].type == \'select-one\')
              campo.childNodes[0].selectedIndex = \'\';
          }
          else {
            campo.childNodes[1].id = /[a-zA-Z_-]*/.exec(campo.childNodes[1].id) + \'[\' + This.id +\']\';
            campo.childNodes[1].name = /[a-zA-Z_-]*/.exec(campo.childNodes[1].name) + \'[\' + This.id +\']\';

            if (campo.childNodes[1].type == \'select-one\')
              campo.childNodes[1].selectedIndex = \'\';
          }

        row.appendChild(campo);
      }

      This.classe = (This.classe == \'formmdtd\') ? \'formlttd\' : \'formmdtd\';

      var campo = this.campos[This.campos.length-1].cloneNode(true);

      row.appendChild(campo);

      var campos_oculto = campo.getElementsByTagName(\'INPUT\');

      for (var co = 0; co < campos_oculto.length; co++) {
        campos_oculto[co].id = /[a-zA-Z_-]*/.exec(campos_oculto[co].id) + \'[\' + This.id +\']\';
        campos_oculto[co].name = /[a-zA-Z_-]*/.exec(campos_oculto[co].name) + \'[\' + This.id +\']\';
      }

      var link_deletar = campo.getElementsByTagName(\'A\');

      link_deletar[0].id = /[a-zA-Z_-]*/.exec(link_deletar[0].id) + \'[\' + This.id +\']\';

      This.id++;
      this.afterAddRow();
    }

    this.removeRow = function(row)
    {
      var This = this;

      var tab = row;
      var tr;
      while (tab.nodeName != \'TABLE\') {
        if(tab.nodeName == \'TR\')
          tr = tab;

        tab = tab.parentNode;
      }

      var trs = tab.getElementsByTagName(\'TR\');

      var linha = 0;

      for (var ct = 2; ct < trs.length - 1; ct++) {
        if (trs[ct] == tr) {
          tab.deleteRow(ct);
        }
      }

      trs = document.getElementsByName(\'tr_\'+This.nome + \'[]\');
      var classe = \'formmdtd tr_\' + This.nome;

      for (var ct = 0; ct < trs.length; ct++) {
        if (trs[ct] && trs[ct].id != \'adicionar_linha\') {
          trs[ct].className = classe;

          for (var c = 0; c < trs[ct].cells.length; c++)
            trs[ct].cells[c].className = classe;

          classe = (classe == \'formmdtd\')? \'formlttd tr_\' + This.nome : \'formmdtd tr_\' + This.nome;
        }
      }

      if (This.reordenar) {
        This.setId(tab);
        This.id--;
      }

      // if (This.id == 0)
      //   This.addRow();   // Foi comentado para permitir excluir todas as alocações do servidor publico.

      this.afterRemoveRow();
    }

    this.setId = function setId(tab)
    {
      var trs = tab.getElementsByTagName(\'TR\');
      var cod_ini = 0;

      for (var ct = 2; ct < trs.length; ct++) {
        var nome_tr = /[a-zA-Z-_]*/.exec(trs[ct].id);

        if (!nome_tr)
           continue;

         trs[ct].setAttribute( "id",nome_tr + \'[\' + cod_ini + \']\');

        for (var c = 0; c < trs[ct].cells.length; c++) {
          var nome_td = /[a-zA-Z-_]*/.exec(trs[ct].cells[c].id);
          trs[ct].cells[c].setAttribute( "id",nome_td + \'[\' + cod_ini + \']\');

          var campos = trs[ct].cells[c].childNodes;

          for (var inp = 0 ; inp < campos.length; inp++) {
            if (!campos[inp].id)
              continue;

            var nome_inp = /[a-zA-Z-_]*/.exec(campos[inp].id);

            campos[inp].setAttribute("id",nome_inp + \'[\' + cod_ini + \']\');
            campos[inp].setAttribute("name",nome_inp + \'[\' + cod_ini + \']\');
          }
        }

        cod_ini++;
      }
    }
  }
  ';

        $retorno .= "<script>$javascript</script>";
        $classe = $md ? 'formlttd' : 'formmdtd';
        $md = $md ? false : true;
        $index = 0;

        foreach ($arr_campos as $nome => $componente) {
            $nome_add = $nome;
            $campo_tabela = false;

            if (preg_match('/^(tab_add_[0-9]+)/', $nome) === 1) {
                $campo_tabela = true;
                $javascript = '';

                $cabecalho = $componente['cabecalho'];
                $nome_tabela = $componente['nome'];
                $valores = $componente['valores'];
                $titulo = $componente['titulo'];
                $largura = $componente['largura'] ? " width=\"{$componente['largura']}\" " : '';
                $valores_lista_tabela = $componente['valores_lista'];
                $componente = array_shift($componente);

                $campos_oculto = $componente['oculto'];
                unset($componente['oculto']);

                $classe = $md ? 'formlttd' : 'formmdtd';
                $md = $md ? false : true;

                $retorno .= "<tr id='tr_$nome_tabela' class='$classe'><td valign='top' align='center' colspan='2'>";
                $retorno .= "\n<table cellspacing='0' $largura id='$nome_tabela' class='tabela-adicao' cellpadding='2' style='margin:10px 0px 10px 0px;' >";

                $total_campos = count($cabecalho);
                $span = $total_campos + 1;

                if ($titulo) {
                    $retorno .= "<tr align='center' id='tr_{$nome_tabela}_tit' style='font-weight:bold'  class='formdktd'><td colspan='$span'>$titulo</td></tr>";
                } else {
                    $retorno .= "<tr align='center' id='tr_{$nome_tabela}_tit' style='font-weight:bold;display:none;visibility:hidden;' ><td colspan='$span'>&nbsp;</td></tr>";
                }

                $retorno .= "<tr align='center' style='font-weight:bold' id='tr_{$nome_tabela}_cab'>";

                foreach ($cabecalho as $key => $cab) {
                    $expressao_regular = $componente[$key][2];

                    if ($expressao_regular && substr($expressao_regular, 0, 1) != '*') {
                        $obrigatorio = '<span class="campo_obrigatorio">*</span>';
                    } else {
                        $obrigatorio = '';
                    }
                    $cabId = str_replace(' ', '_', strtolower($cab));

                    $retorno .= "<td class='formmdtd' id='td_$cabId' align='center'><span class='form'>$cab</span>{$obrigatorio}</td>";
                }

                $retorno .= '<td class=\'formmdtd\' id=\'td_acao\' align=\'center\'><span class=\'form\'>A&ccedil;&atilde;o</span></td>';
                $retorno .= '</tr>';

                $click = "$nome_add.removeRow(this);";

                $img = '<img src="/intranet/imagens/banco_imagens/excluirrr.png" border="0" alt="excluir" />';
                $md2 = false;

                if (!count($valores)) {
                    $valores[0] = '';
                }

                foreach ($valores as $key2 => $valor) {
                    $classe2 = $md2 ? 'formlttd dd' : 'formmdtd dd';
                    $md2 = $md2 ? false : true;

                    $retorno .= "<tr id='tr_{$nome_tabela}[$key2]' name='tr_{$nome_tabela}[]'  $style class='$classe2 tr_{$nome_tabela}'>";
                    $array_valores_lista = $valores_lista_tabela[$key2];

                    foreach ($componente as $key => $campo_) {
                        $nome = $campo_[1];

                        if ($campo_[10]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        if ($campo_[9] && $campo_[8]) {
                            $evento = " {$campo_[9]}=\"{$campo_[8]}\"";
                        } else {
                            $evento = '';
                        }

                        $expressao_regular = $campo_[2];

                        if ($expressao_regular && substr($expressao_regular, 0, 1) != '*') {
                            $class = 'obrigatorio';
                        } else {
                            $class = 'geral';
                        }

                        $center = (strtolower($campo_[0]) == 'rotulo' || strtolower($campo_[0]) == 'check' || $largura) ?
                            'align="center"' : '';

                        $retorno .= "<td class='$classe2 {$nome}' $center id='td_{$nome}[{$key2}]' valign='top'>\n";

                        switch (strtolower($campo_[0])) {
                            case 'html':
                                $retorno .= $componente;
                                // no break
                            case 'texto':
                                $retorno .= $this->getCampoTexto("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $campo_[4], $campo_[5], $evento, $campo_[10], '', $class, $campo_[7]);
                                break;

                            case 'monetario':
                                $retorno .= $this->getCampoMonetario("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $campo_[4], $campo_[5], $campo_[9], $campo_[6], $campo_[11], $class, $campo_[8], $campo_[7]);
                                break;

                            case 'hora':
                                $retorno .= $this->getCampoHora("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], '', '');
                                break;

                            case 'lista':
                                $lista = null;

                                if (is_array($array_valores_lista)) {
                                    $lista = array_shift($array_valores_lista);
                                }

                                $lista = (sizeof($lista)) ?
                                    $lista : $campo_[3];

                                $retorno .= $this->getCampoLista("{$nome}[{$key2}]", "{$nome}[$key2]", $campo_[5], $lista, $valor[$key], $campo_[7], $campo_[8], $class, $campo_[9]);
                                break;

                            case 'rotulo':
                                $retorno .= $this->getCampoRotulo($campo_[3] ? $campo_[3] : $valor[$key]);
                                break;

                            case 'check':
                                $retorno .= $this->getCampoCheck("{$nome}[{$key2}]", "{$nome}[{$key2}]", $campo_[3] ? $campo_[3] : $valor[$key], $campo_[4], $campo_[5], $campo_[6]);
                                break;

                            case 'cnpj':
                                $retorno .= $this->getCampoCNPJ("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5]);
                                break;

                            case 'cpf':
                                $retorno .= $this->getCampoCPF("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], $campo_[8], $campo_[9]);
                                break;

                            case 'idfederal':
                                $retorno .= $this->getCampoIdFederal("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], $campo_[8]);
                                break;

                            case 'data':
                                $retorno .= $this->getCampoData("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], $campo_[9]);
                                break;

                            case 'textopesquisa':
                                $retorno .= $this->getCampoTextoPesquisa("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], $campo_[10], $campo_[8], $campo_[9], $campo_[7], $campo_[12], $campo_[11]);
                                break;

                            case 'cep':
                                $retorno .= $this->getCampoCep("{$nome}[{$key2}]", "{$nome}[{$key2}]", $valor[$key], $class, $campo_[4], $campo_[5], $campo_[8], $campo_[7]);
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

                            $retorno_oculto .= $this->getCampoOculto("{$campo_oculto[1]}[{$key2}]", $campo_oculto[3], "{$campo_oculto[1]}[{$key2}]");
                        }
                    }

                    $retorno .= "<td align='center'><a href='javascript:void(0)' onclick='$click' id='link_remove[$key2]' style='outline: none;'>$img</a>$retorno_oculto</td>";
                    $img = '<img src="/intranet/imagens/banco_imagens/excluirrr.png" border="0" alt="excluir" />';
                    $id = count($valores);

                    $javascript .= "
                  var $nome_add = new tabela('{$nome_tabela}','{$id}');\n";
                } // endforeach.

                $retorno .= '</tr>';

                $click = "$nome_add.addRow();";
                $img = '<img src="/intranet/imagens/nvp_bot_novo.png" border="0" alt="incluir" style="float:left; margin:5px;" />';
                $retorno .= '<tr id=\'adicionar_linha\' style="background-color:#f5f9fd;">';
                $tt = $total_campos + 1;
                $retorno .= "<td colspan='$tt' align='left' style='padding-top: 17px !important;'><a style=\"color: #47728f; text-decoration:none;\" href='javascript:void(0)' id='btn_add_$nome_add' onclick='$click' style='outline: none;'>$img <p style=\"padding:9px; margin:0;\">ADICIONAR NOVO<p></a></td>";
                $retorno .= '</tr>';

                $retorno .= '</table>';
                $retorno .= '</td></tr>';

                $retorno .= "<script  type='text/javascript'>$javascript</script>";
                continue;
            }

            $adicionador_complemento_campo = '';
            $campo_valor = $componente[3] ?? null;

            $nome .= $adicionador_complemento_campo;
            $expressao_regular = $componente[2];

            if ($expressao_regular && substr($expressao_regular, 0, 1) != '*') {
                $class = 'obrigatorio';
                $obrigatorio = '<span class="campo_obrigatorio">*</span>';
            } else {
                $class = 'geral';
                $obrigatorio = '';
            }
            if ($nome == 'html') {
                $retorno .= $componente;
            } elseif // Separador: insere uma linha preta
            ($componente[0] == 'linha_preta') {
                $retorno .= "<tr><td  style='padding:0px;background-color:{$componente['cor']};' colspan='2' height='{$componente['altura']}'></td></tr>";
                continue;
            } elseif ($componente[0] == 'espaco') {
                $retorno .= '<tr><td colspan=\'2\'><hr></td></tr>';
                continue;
            } elseif ($componente[0] != 'oculto') {
                $tipo = $componente[0];

                if (!isset($componente['separador'])) {
                    $campo = $componente[1];
                } else {
                    $campo = $componente[1] . "{$componente['separador']}";
                }

                if (($campo == $campo_anterior) && ($campo != '-:')) {
                    $campo = '';
                } else {
                    $campo_anterior = $campo;

                    if (!$foiDuplo) {
                        $md = !$md;
                    }
                }

                $classe = $md ? 'formmdtd' : 'formlttd';

                if ($campo_tabela && false) {
                    if ($componente[10] && ($componente[0] == 'textoDuploInv'
                            || $componente[0] == 'textoInv')) {
                        $name = " name='tr_{$componente[10]}'  ";
                    } else {
                        $name = '';
                    }

                    $retorno .= "<tr id='tr_$nome' {$name} $style><td class='$classe' valign='top'><span class='form'>$campo</span>{$obrigatorio}</td><td class='$classe' valign='top'><span class='form'>\n";
                } elseif (!$foiDuplo) {
                    if ($campo == '-:') {
                        if (empty($componente[3])) {
                            $componente[3] = renderpixel();
                        }

                        $explicacao = ($componente[6]) ?
                            "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : '';

                        $retorno .= "<tr><td colspan='2' class='$classe'><span class='form'><b>$componente[3]</b></span>{$explicacao}</td></tr>\n";
                    } else {
                        if (!empty($componente[10]) && !empty($componente[0])
                            && ($componente[0] == 'textoDuploInv' || $componente[0] == 'textoInv')) {
                            $name = " name='tr_{$componente[10]}'  ";
                        } else {
                            $name = '';
                        }

                        $style = (isset($componente['tr_invisivel']) && $componente['tr_invisivel']) ?
                            'style=\'visibility:collapse\'' : '';

                        $explicacao = !empty($componente[6]) ?
                            "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : '';

                        $retorno .= "<tr id='tr_$nome' {$name} $style><td class='$classe' valign='top'><span class='form'>$campo</span>{$obrigatorio}{$explicacao}</td><td class='$classe' valign='top'><span class='form'>\n";
                    }
                } elseif ($tipo) {
                    if (!empty($componente[10]) && $componente[10] == true) {
                        $explicacao = !empty($componente[6]) ?
                            "<br><sub style='vertical-align:top;'>{$componente[6]}</sub>" : '';

                        $retorno .= "<span class='form'>$campo</span>{$explicacao}\n";
                    } else {
                        $retorno .= "<span class='form'>$campo</span>\n";
                    }

                    $foiDuplo = false;
                }

                switch ($tipo) {
                    case 'html':
                        $retorno .= $componente;
                        break;

                    case 'rotuloDuplo':
                        $foiDuplo = true;
                        // no break
                    case 'rotulo':
                        if ($campo != '-:') {
                            $retorno .= $componente[3];
                        }
                        break;

                    case 'cep':
                        $retorno .= "<input onKeyPress=\"formataCEP(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" $componente[8]>$componente[7]\n";
                        break;

                    case 'data':
                        $retorno .= "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[8]}> $componente[7]\n";
                        break;

                    case 'dataDupla':
                        $retorno .= "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\"> $componente[7]";
                        break;

                    case 'hora':
                        $componente[3] = (strlen($componente[3]) < 6  || $componente[5] != 5) ? $componente[3] : substr($componente[3], 0, 5);
                        $segundos = ($componente[5] != 5) ? 'true' : 'false';
                        $retorno .= "<input onKeyPress=\"formataHora(this, event, {$segundos});\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[8]} {$componente[9]}>{$componente[7]}";
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

                        $retorno .= " {$componente[7]}> {$componente[4]}";

                        break;

                    case 'checkDuplo':
                        $retorno .= "<input type='checkbox' name=\"{$nome}\"";

                        if ($componente[3]) {
                            $retorno .= ' checked';
                        }

                        $retorno .= "> {$componente[4]} ";
                        $foiDuplo = true;
                        break;

                    case 'texto':
                        if ($componente[10]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        if ($componente[9] && $componente[8]) {
                            $evento = " {$componente[9]}=\"{$componente[8]}\"";
                        } else {
                            $evento = '';
                        }

                        $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$evento} {$disabled}> {$componente[7]}";
                        break;

                    case 'monetario':
                        if ($componente[9]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        $retorno .= "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $componente[8] = \"{$componente[7]}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$disabled}> {$componente[11]}";
                        break;

                    case 'monetarioDuplo':
                        if ($componente[9]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        if (!$componente[6]) {
                            $componente[6] = $componente[11];
                        }

                        $retorno .= "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $componente[8] = \"{$componente[7]}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$disabled}> {$componente[6]}";
                        $foiDuplo = true;
                        break;

                    case 'email':
                        $retorno .= "<input class='{$class}' style='text-align: left;' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\"> $componente[7]";
                        break;

                    case 'textoPesquisa':
                        if ($componente[10]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" {$componente[12]}='{$componente[11]}' {$disabled}> ";

                        if ($componente[9]) {
                            // tem serialized campos
                            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[8]}?campos={$componente[9]}', '{$nome}')\"> {$componente[7]}";
                        } else {
                            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[8]}', '{$nome}')\"> {$componente[7]}";
                        }

                        break;

                    case 'textoInv':
                        $retorno .= "<input class='{$class}' type='text' name=\"{$componente[10]}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true {$componente[9]}=\"{$componente[8]}\">&nbsp;$componente[7]";
                        break;

                    case 'textoDuploInv':
                        $retorno .= "<input class='{$class}' type='text' name=\"{$componente[10]}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" disabled=true>";
                        $foiDuplo = true;
                        break;

                    case 'senha':
                        $retorno .= "<input class='{$class}' type='password' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\">";
                        break;

                    case 'textoDuplo':
                        if ($componente[10]) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }

                        $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$campo_valor}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\" {$disabled}>";
                        $foiDuplo = true;
                        break;

                    case 'memo':
                        if ($componente[11]) {
                            $retorno .= "<textarea class='{$class}' name=\"{$nome}\" id=\"{$nome}\" cols=\"{$componente[4]}\" rows=\"{$componente[5]}\" style='wrap:virtual' {$evento} disabled ";
                        } else {
                            $retorno .= "<textarea class='{$class}' name=\"{$nome}\" id=\"{$nome}\" cols=\"{$componente[4]}\" rows=\"{$componente[5]}\" style='wrap:virtual' {$evento} ";
                        }

                        if ($componente[9] && $componente[10]) {
                            $evento = "{$componente[9]}=\"{$componente[10]}\"";
                        } else {
                            $evento = '';
                        }

                        if ($componente[7] > 0) {
                            $retorno .= "ONKEYDOWN='this.form.{$nome}.value=this.form.{$nome}.value.length >= {$componente[7]} ? this.form.{$nome}.value.substring(0,{$componente[7]}-1) : this.form.{$nome}.value;'";
                        }

                        $retorno .= ">{$campo_valor}</textarea>\n";
                        $foiDuplo = $componente[8];
                        break;

                    case 'lista':
                        if (is_numeric($componente[9])) {
                            $multiple = " multiple='multiple' SIZE='{$componente[9]}' ";
                        } else {
                            $multiple = '';
                        }

                        $retorno .= "<select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}' id='{$nome}' {$componente[8]} {$multiple}>";
                        $opt_open = false;

                        reset($componente[3]);

                        foreach ($componente[3] as $chave => $texto) {
                            if (substr($texto, 0, 9) == 'optgroup:') {
                                $opt_open = true;
                                $retorno .= '<optgroup label="' . substr($texto, 9) . '">';
                            } elseif (substr($texto, 0, 8) == 'optgroup') {
                                // optgroup
                                if ($opt_open) {
                                    $opt_open = false;
                                    $retorno .= '</optgroup>';
                                }
                            } else {
                                // option normal
                                $retorno .= "<option id=\"{$nome}_" . urlencode($chave) . '" value="' . urlencode($chave) . '"';
                                $defaultValue = is_array($componente[4]) ? $componente[4][$adicionador_indice] : $componente[4];

                                if (!is_null($defaultValue) && $defaultValue !== '' && $chave == $defaultValue) {
                                    $retorno .= ' selected';
                                }

                                $retorno .= ">$texto</option>";
                            }
                        }

                        if ($opt_open) {
                            $retorno .= '</optgroup>';
                        }

                        $retorno .= "</select> {$componente[7]}";
                        break;

                    case 'listapesquisa':
                        $class = ($componente[14]) ?
                            'obrigatorio' : 'geral';

                        $retorno .= "<select onchange=\"{$componente[5]}\" class='{$class}' name='{$nome}' id='{$nome}' {$componente[11]}>";
                        reset($componente[3]);

                        while (list($chave, $texto) = each($componente[3])) {
                            $retorno .= "<option id=\"{$nome}_" . urlencode($chave) . '" value="' . urlencode($chave) . '"';

                            if ($chave == $componente[4]) {
                                $retorno .= ' selected';
                            }

                            $retorno .= ">$texto</option>";
                        }

                        $retorno .= '</select> ';

                        if ($componente[13]) {
                            // Tem serialized campos
                            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[7]}?campos={$componente[13]}', '{$nome}')\"> {$componente[8]}";
                        } else {
                            if ($componente[12]) {
                                // Vai abrir em um div
                                $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$nome}_lupa' onclick=\"pesquisa_valores_popless('{$componente[7]}', '{$nome}')\"> {$componente[8]}";
                            } else {
                                // Abre num pop-up
                                $retorno .= "<img id='lupa' src=\"imagens/lupa.png\" alt=\"Pesquisa\" name='{$nome}_lupa' id='{$nome}_lupa' border=\"0\" onclick=\"pesquisa_valores_f('{$componente[7]}', '{$nome}', '{$componente[9]}', '{$componente[10]}')\"> {$componente[8]}";
                            }
                        }
                        break;

                    case 'listaDupla':
                        $retorno .= "<select onchange=\"{$componente[5]}\"  class='{$class}' name='{$nome}' id='{$nome}' {$componente[8]}>";
                        reset($componente[3]);

                        while (list($chave, $texto) = each($componente[3])) {
                            $retorno .= '<option value="' . urlencode($chave) . '"';

                            if ($chave == $componente[4]) {
                                $retorno .= ' selected';
                            }

                            $retorno .= ">$texto</option>";
                        }

                        $retorno .= '</select>';
                        $foiDuplo = true;
                        break;

                    case 'arquivo':
                        $retorno .= "<input class='inputfile inputfile-buttom' name=\"{$nome}\" id=\"{$nome}\" type='file' size=\"{$componente[4]}\" value=\"{$componente[3]}\">
            <label id=\"{$nome}\" for=\"{$nome}\"><span></span> <strong>Escolha um arquivo</strong></label>";

                        if (!empty($componente[5])) {
                            $retorno .= "&nbsp;$componente[5]";
                        }

                        break;

                    case 'email':
                        $retorno .= '<a href=\'www.google.com.br\' class=\'linkBory\'>Enviar Por Email</a>';
                        break;

                    case 'emailDuplo':
                        $retorno .= "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$componente[3]}\" size=\"{$componente[4]}\" maxlength=\"{$componente[5]}\" onKeyUp=\"{$componente[8]}\">";
                        $foiDuplo = true;
                        break;

                    case 'radio':
                        $primeiro = true;

                        reset($componente[3]);

                        $retorno .= "<span onclick=\"{$componente[5]}\" >";

                        while (list($chave, $texto) = each($componente[3])) {
                            if ($primeiro) {
                                $primeiro = false;
                                $id = "id=\"{$nome}\"";
                            } else {
                                $id = '';
                                $retorno .= '<br>';
                            }

                            $retorno .= "<input type='radio' class='{$class}' name=\"{$nome}\" {$id} value=\"" . urlencode($chave) . '"';
                            if ($chave == $componente[4]) {
                                $retorno .= ' checked';
                            }

                            $retorno .= ">$texto";
                        }

                        $retorno .= '</span>';
                        break;

                    case 'checkMultiplo':
                        $tmpRetorno = [];

                        foreach ($componente[3] as $k => $v) {
                            $checked = in_array($k, $componente[4]) ? ' checked="checked"' : '';
                            $name = $nome . '[]';
                            $tmpInput = sprintf('<label><input name="%s" type="checkbox" value="%s"%s> %s</label>', $name, $k, $checked, $v);
                            $tmpRetorno[] = $tmpInput;
                        }

                        $retorno .= join('<br>', $tmpRetorno);
                        break;
                } // endswitch

                if (isset($this->erros[$nome])) {
                    $retorno .= '<br><font color=red>' . $this->erros[$nome] . '</font>';
                }

                if (!$foiDuplo) {
                    $retorno .= '</span></td></tr>';
                }
            }

            if ($todos_inline) {
                $foiDuplo = true;
            }
        }

        return $retorno;
    }

    public function MakeFormat()
    {
        $ret = '
    function AdicionaItem(chave, item, nome_pai, submete)
    {
      var x = document.getElementById(nome_pai);

      opt = document.createElement(\'OPTION\');
      opt.value = chave;
      opt.selected = true;
      opt.appendChild(document.createTextNode(item));

      x.appendChild(opt);
      if (submete) {
    ';

        if (isset($this->executa_submete)) {
            $ret .= '
        document.' . $this->__nome . '.' . $this->executa_submete;
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

    public function getCampoTexto(
        $nome,
        $id = '',
        $valor = '',
        $tamanhovisivel = '',
        $tamanhomaximo = '',
        $evento = '',
        $disabled = '',
        $__descricao = '',
        $class = '',
        $descricao = ''
    ) {
        $id = $id ? $id : $nome;

        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
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

    public function getCampoLista(
        $nome,
        $id,
        $acao,
        $valor,
        $default,
        $complemento,
        $desabilitado,
        $class,
        $multiple = false
    ) {
        $id = $id ? $id : $nome;

        if (is_numeric($multiple)) {
            $multiple = " multiple='multiple' SIZE='$multiple' ";
        } else {
            $multiple = '';
        }

        $retorno = "<select onchange=\"{$acao}\" class='{$class}' name='{$nome}' id='{$id}' {$desabilitado} $multiple>";
        $opt_open = false;

        reset($valor);

        $adicionador_indice = null;

        while (list($chave, $texto) = each($valor)) {
            if (substr($texto, 0, 9) == 'optgroup:') {
                // optgroup
                if ($opt_open) {
                    $retorno .= '</optgroup>';
                }

                $retorno .= '<optgroup label="' . substr($texto, 9) . '">';
            } else {
                // option normal
                $retorno .= "<option id=\"{$nome}_" . urlencode($chave) . '" value="' . urlencode($chave) . '"';
                $defaultValue = is_array($default) ? $default[$adicionador_indice] : $default;

                if (!is_null($defaultValue) && $defaultValue !== '' && $chave == $defaultValue) {
                    $retorno .= ' selected';
                }

                $retorno .= ">$texto</option>";
            }
        }

        if ($opt_open) {
            $retorno .= '</optgroup>';
        }

        $retorno .= "</select> {$complemento}";

        return $retorno;
    }

    public function getCampoMonetario(
        $nome,
        $id,
        $valor,
        $tamanhovisivel,
        $tamanhomaximo,
        $disabled,
        $descricao,
        $descricao2,
        $class,
        $evento = 'onChange',
        $script = ''
    ) {
        $id = $id ? $id : $nome;

        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
            $disabled = '';
        }

        if (!$descricao) {
            $descricao = $descricao2;
        }

        return "<input style='text-align:right'  onKeyup=\"formataMonetario(this, event);\" $evento = \"{$script}\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}> {$descricao}";
    }

    public function getCampoHora(
        $nome,
        $id,
        $valor,
        $class,
        $tamanhovisivel,
        $tamanhomaximo,
        $acao = '',
        $descricao = ''
    ) {
        $id = $id ? $id : $nome;

        $valor = strlen($valor) < 6 ? $valor : substr($valor, 0, 5);

        return "<input onKeyPress=\"formataHora(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" $acao>{$descricao}";
    }

    public function getCampoRotulo($valor)
    {
        return "<span class=\"form\"> $valor</span>";
    }

    public function getCampoCheck($nome, $id, $valor, $desc = '', $script = false, $disabled = false)
    {
        $id = $id ?: $nome;

        $onClick = '';

        if ($script) {
            $onClick = "onclick=\"{$script}\"";
        }

        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
            $disabled = '';
        }

        $retorno = "<input type='checkbox' name=\"{$nome}\" id=\"{$id}\" {$onClick}";

        if ($valor) {
            $retorno .= ' checked';
        }

        $retorno .= " {$disabled}> {$desc}";

        return $retorno;
    }

    public function getCampoCNPJ($nome, $id, $valor, $class, $tamanhovisivel, $tamanhomaximo)
    {
        $id = $id ?: $nome;

        return "<input onKeyPress=\"formataCNPJ(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\">";
    }

    public function getCampoCPF($nome, $id, $valor, $class, $tamanhovisivel, $tamanhomaximo, $disabled = false, $onChange = '')
    {
        $id = $id ?: $nome;

        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
            $disabled = '';
        }

        return "<input onChange=\"{$onChange}\"onKeyPress=\"formataCPF(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" $disabled>";
    }

    public function getCampoIdFederal(
        $nome,
        $id,
        $valor,
        $class,
        $tamanhovisivel,
        $tamanhomaximo,
        $disabled = false
    ) {
        $id = $id ?: $nome;

        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
            $disabled = '';
        }

        return "<input onkeyPress=\"formataIdFederal(this,event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}>";
    }

    public function getCampoOculto($nome, $valor, $id = '')
    {
        $id = $id ? $id : $nome;

        if ($valor) {
            $valor = urlencode($valor);
        }

        return "<input name='$nome' id='$id' type='hidden' value='{$valor}'>\n";
    }

    public function getCampoData($nome, $id, $valor, $class, $tamanhovisivel, $tamanhomaximo, $disabled = false)
    {
        $campoDisabled = '';
        if ($disabled !== false) {
            $campoDisabled = 'disabled=\'disabled\'';
        }

        $id = $id ?: $nome;

        return "<input onKeyPress=\"formataData(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$campoDisabled}> \n";
    }

    public function getCampoCep(
        $nome,
        $id,
        $valor,
        $class,
        $tamanhovisivel,
        $tamanhomaximo,
        $disabled = false,
        $descricao = ''
    ) {
        if ($disabled) {
            $disabled = 'disabled=\'disabled\'';
        } else {
            $disabled = '';
        }

        return "<input onKeyPress=\"formataCEP(this, event);\" class='{$class}' type='text' name=\"{$nome}\" id=\"{$nome}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$disabled}>$descricao\n";
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
    public function getCampoTextoPesquisa(
        $nome,
        $id,
        $valor,
        $class,
        $tamanhovisivel,
        $tamanhomaximo,
        $disabled,
        $caminho,
        $campos_serializados = null,
        $descricao = null,
        $script = null,
        $evento = null
    ) {
        if ($disabled) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }

        $id = $id ?: $nome;

        $retorno = "<input class='{$class}' type='text' name=\"{$nome}\" id=\"{$id}\" value=\"{$valor}\" size=\"{$tamanhovisivel}\" maxlength=\"{$tamanhomaximo}\" {$evento}='{$script}' {$disabled}> ";

        if ($campos_serializados) {
            // Tem serialized campos
            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" name='{$nome}_lupa' id='{$id}_lupa' onclick=\"pesquisa_valores_popless('{$caminho}?campos={$campos_serializados}', '{$nome}')\">$descricao";
        } else {
            $retorno .= "<img src=\"imagens/lupa.png\" alt=\"Pesquisa\" border=\"0\" onclick=\"pesquisa_valores_f('{$caminho}', '{$nome}')\"> $descricao";
        }

        return $retorno;
    }
}
