<?php

class clsCalendario
{
    /**
     * @var int
     */
    public $permite_trocar_ano = 0;

    /**
     * @var int
     */
    public $largura_externa = 400;

    /**
     * @var int
     */
    public $largura_interna = 250;

    /**
     * @var int
     */
    public $padding = 5;

    /**
     * @var array
     */
    public $COR = [
        0 => '#FADEAF',
        'LARANJA_CLARO' => '#FADEAF',
        1 => '#93BDC9',
        'AZUL_ESCURO' => '#93BDC9',
        2 => '#BCD39D',
        'VERDE_ESCURO' => '#BCD39D',
        3 => '#e9f0f8',
        'AZUL_CLARO' => '#e9f0f8',
        4 => '#f8e9ee',
        'ROSA' => '#f8e9ee',
        5 => '#E9D1AF',
        'LARANJA_ESCURO' => '#E9D1AF',
        6 => '#E9E6BB',
        'AMARELO' => '#E9E6BB',
        7 => '#C9D9CF',
        'VERDE_CLARO' => '#C9D9CF',
        8 => '#DDE3D9',
        'CINZA' => '#DDE3D9',
    ];

    /**
     * @var array
     */
    public $array_icone = [
        'A' => [
            'nome' => 'Anotações',
            'link' => ''
        ],
        ''
    ];

    /**
     * @var array
     */
    public $array_icone_dias = [];

    /**
     * Cores da legenda.
     *
     * @var array
     */
    public $array_cor = ['#F7F7F7'];

    /**
     * Legendas.
     *
     * @var array
     */
    public $array_legenda = ['Padrão'];

    /**
     * Cor para os dias da semana.
     *
     * @var array
     */
    public $array_cor_dia_padrao = [];

    /**
     * Dias do mês.
     *
     * @var array
     */
    public $array_dias = [];

    /**
     * Javascript de um "dia".
     *
     * @var array
     */
    public $all_days_onclick;

    /**
     * URL de um "dia".
     *
     * @var array
     */
    public $all_days_url;

    /**
     * @var array
     */
    public $array_onclick_dias = [];

    /**
     * Div flutuante para dias.
     *
     * @var array
     */
    public $array_div_flutuante_dias = [];

    public function resetAll()
    {
        $this->array_div_flutuante_dias = [];
        $this->array_onclick_dias = [];
        $this->array_dias = [];
        $this->array_cor_dia_padrao = [];
        $this->array_legenda = ['Padrão'];
        $this->array_cor = ['#F7F7F7'];
        $this->largura_externa = 400;
        $this->largura_interna = 250;
        $this->padding = 5;
    }

    /**
     * @param int $int_largura
     */
    public function setLargura($int_largura)
    {
        $this->largura_externa = $int_largura;

        if ($int_largura > 250) {
            $this->largura_interna = $this->largura_externa - 121;
        } else {
            $this->largura_interna = '40%';
        }

        $this->padding = floor((($int_largura - 30) / 7) / 10) * 2;
    }

    /**
     * @param array $arr_dias
     * @param array $array_mensagem_dias
     */
    public function diaDescricao($arr_dias, $array_mensagem_dias)
    {
        if (is_array($arr_dias)) {
            foreach ($arr_dias as $key => $dia) {
                $this->array_div_flutuante_dias[$key] = $array_mensagem_dias[$key];
            }
        }
    }

    /**
     * @param array $arr_dias
     * @param array $array_onclick_dias
     */
    public function diaOnClick($arr_dias, $array_onclick_dias)
    {
        if (is_array($arr_dias)) {
            foreach ($arr_dias as $key => $dia) {
                $this->array_onclick_dias[$dia][] = $array_onclick_dias[$key];
            }
        }
    }

    /**
     * @param array  $arr_dias
     * @param string $id_icone
     */
    public function adicionarIconeDias($arr_dias, $id_icone)
    {
        if (is_array($arr_dias)) {
            foreach ($arr_dias as $key => $dia) {
                if (key_exists($id_icone, $this->array_icone)) {
                    $this->array_icone_dias[$dia] = $id_icone;
                    $this->array_icone[$id_icone]['utilizado'] = true;
                }
            }
        }
    }

    /**
     * @param string $str_legenda
     * @param string $str_cor
     */
    public function adicionarLegenda($str_legenda, $str_cor)
    {
        $key = array_search($str_legenda, $this->array_legenda);

        if (!empty($key)) {
            if ($this->array_legenda[$key] == $str_legenda) {
                return;
            }
        }

        $this->array_legenda[] = $str_legenda;
        $str_cor = mb_strtoupper($str_cor);
        $this->array_cor[] = $this->COR[$str_cor];
    }

    /**
     * @param string $str_legenda
     * @param string $str_cor
     */
    public function setLegendaPadrao($str_legenda, $str_cor = '#F7F7F7')
    {
        $this->array_legenda[0] = $str_legenda;
        $this->array_cor[0] = $this->COR[$str_cor];
    }

    /**
     * @param array  $arr_dia_semana
     * @param string $str_cor
     */
    public function setCorDiaSemana($arr_dia_semana, $str_cor)
    {
        $str_cor = mb_strtoupper($str_cor);

        if (is_array($arr_dia_semana)) {
            foreach ($arr_dia_semana as $dia) {
                $this->array_cor_dia_padrao[$dia] = $this->COR[$str_cor];
            }
        } else {
            $this->array_cor_dia_padrao["{$arr_dia_semana}"] = $str_cor;
        }
    }

    /**
     * Adiciona os dias do mês com a sua legenda.
     *
     * @param string $str_cod_legenda
     * @param int    $dias
     */
    public function adicionarArrayDias($str_cod_legenda, $dias)
    {
        $key = array_shift(array_keys($this->array_legenda, $str_cod_legenda));

        foreach ($dias as $dia) {
            $dia = (int) $dia;
            $this->array_dias[$dia] = $key;
        }

        ksort($this->array_dias);
    }

    /**
     * Retorna o código HTML do calendário.
     *
     * Parte do código foi baseada em um tutorial antigo disponível
     * no site da Zend. A URL original não existe mais mas, parte do
     * código foi preservado por um blogueiro no endereço <http://miud.in/7NM>.
     * Não existe informação de licenciamento relevante.
     *
     * @link   http://miud.in/7NM Código fonte da geração de calendário
     *
     * @param int    $mes
     * @param int    $ano
     * @param string $nome
     * @param mixed  mixVariaveisMantidas
     *
     * @return string
     *
     * @todo   Substituir código de geração de calendário por uma biblioteca
     *         com licença compatível com GPL2
     */
    public function getCalendario(
        $mes,
        $ano,
        $nome,
        $mixVariaveisMantidas,
        array $formValues = []
    ) {
        $array_color = $this->array_cor;
        $array_legenda = $this->array_legenda;

        if (
            isset($mixVariaveisMantidas["{$nome}_mes"]) &&
            is_numeric($mixVariaveisMantidas["{$nome}_mes"])
        ) {
            $mes = $mixVariaveisMantidas["{$nome}_mes"];
        }

        if (
            isset($mixVariaveisMantidas["{$nome}_ano"]) &&
            is_numeric($mixVariaveisMantidas["{$nome}_ano"]) &&
            $this->permite_trocar_ano == true
        ) {
            $ano = $mixVariaveisMantidas["{$nome}_ano"];
        }

        // Array com todos os dias da semana
        $diasDaSemana = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB'];

        $mesesDoAno = [
            '1' => 'JANEIRO',
            '2' => 'FEVEREIRO',
            '3' => 'MARÇO',
            '4' => 'ABRIL',
            '5' => 'MAIO',
            '6' => 'JUNHO',
            '7' => 'JULHO',
            '8' => 'AGOSTO',
            '9' => 'SETEMBRO',
            '10' => 'OUTUBRO',
            '11' => 'NOVEMBRO',
            '12' => 'DEZEMBRO'
        ];

        // Qual o primeiro dia do mes
        $primeiroDiaDoMes = mktime(0, 0, 0, $mes, 1, $ano);

        // Quantos dias tem o mes
        $NumeroDiasMes = date('t', $primeiroDiaDoMes);

        // Retrieve some information about the first day of the
        // month in question.
        $dateComponents = getdate($primeiroDiaDoMes);

        // What is the name of the month in question?
        $NomeMes = $mesesDoAno[$dateComponents['mon']];

        // What is the index value (0-6) of the first day of the
        // month in question.
        $DiaSemana = $dateComponents['wday'];

        // Create the table tag opener and day headers
        // GET
        $linkFixo = '?';
        if (is_array($mixVariaveisMantidas)) {
            foreach ($mixVariaveisMantidas as $key => $value) {
                if ($key != "{$nome}_mes" && $key != "{$nome}_ano") {
                    $linkFixo .= $key = $value . '&';
                }
            }
        } else {
            if (is_string($mixVariaveisMantidas)) {
                $linkFixo .= "$mixVariaveisMantidas&";
            }
        }

        $linkFixo = $linkFixo == '?' ? '' : $linkFixo;

        if ($mes == 12) {
            if ($this->permite_trocar_ano) {
                $mes_posterior_mes = 1;
                $mes_anterior_mes = 11;
                $ano_posterior_mes = $ano + 1;
                $ano_anterior_mes = $ano;

                $mes_ano = $mes;
                $ano_posterior_ano = $ano + 1;
                $ano_anterior_ano = $ano - 1;
            } else {
                $mes_posterior_mes = 1;
                $mes_anterior_mes = 11;
                $ano_posterior_mes = $ano;
                $ano_anterior_mes = $ano;
            }
        } elseif ($mes == 1) {
            if ($this->permite_trocar_ano) {
                $mes_posterior_mes = 2;
                $mes_anterior_mes = 12;
                $ano_posterior_mes = $ano;
                $ano_anterior_mes = $ano - 1;

                $mes_ano = $mes;
                $ano_posterior_ano = $ano + 1;
                $ano_anterior_ano = $ano - 1;
            } else {
                $mes_posterior_mes = 2;
                $mes_anterior_mes = 12;
                $ano_posterior_mes = $ano;
                $ano_anterior_mes = $ano;
            }
        } else {
            if ($this->permite_trocar_ano) {
                $mes_posterior_mes = $mes + 1;
                $mes_anterior_mes = $mes - 1;
                $ano_posterior_mes = $ano;
                $ano_anterior_mes = $ano;

                $mes_ano = $mes;
                $ano_posterior_ano = $ano + 1;
                $ano_anterior_ano = $ano - 1;
            } else {
                $mes_posterior_mes = $mes + 1;
                $mes_anterior_mes = $mes - 1;
                $ano_posterior_mes = $ano;
                $ano_anterior_mes = $ano;
            }
        }

        $form = sprintf(
            '
      <form id="form_calendario" name="form_calendario" method="post" action="%s">
        <input type="hidden" id="cal_nome" name="nome" value="">
        <input type="hidden" id="cal_dia" name="dia" value="">
        <input type="hidden" id="cal_mes" name="mes" value="">
        <input type="hidden" id="cal_ano" name="ano" value="">
        %s
      </form>',
            $linkFixo,
            $this->_generateFormValues($formValues, ['nome', 'ano', 'mes', 'dia'])
        );

        if ($this->permite_trocar_ano == true) {
            $select = sprintf(
                '<select name="mes" id="smes" onchange="acaoCalendario(\'%s\', \'\', this.value, \'%s\');">',
                $nome,
                $ano
            );

            foreach ($mesesDoAno as $key => $mes_) {
                $selected = ($dateComponents['mon'] == $key) ? 'selected="selected"' : '';
                $select .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    $key,
                    $selected,
                    $mes_
                );
            }

            $select .= '</select>';

            $cab = [];
            $cab[] = sprintf(
                '
        <a href="#" onclick="acaoCalendario(\'%s\', \'\', \'%s\', \'%s\')">
          <img src="/intranet/imagens/i-educar/seta_esq.gif" border="0" style="margin-right: 5px;" alt="Mês Anterior">
        </a>
        %s',
                $nome,
                $mes_anterior_mes,
                $ano_anterior_mes,
                $select
            );

            $cab[] = sprintf(
                '
        <a href="#" onclick="acaoCalendario(\'%s\', \'\', \'%s\', \'%s\')">
          <img src="/intranet/imagens/i-educar/seta_dir.gif" border="0" style="margin-left: 5px;" alt="Mês Posterior">
        </a>',
                $nome,
                $mes_posterior_mes,
                $ano_posterior_mes
            );

            $cab[] = sprintf(
                '
        <a href="#" onclick="acaoCalendario(\'%s\', \'\', \'%s\', \'%s\')">
          <img src="/intranet/imagens/i-educar/seta_esq.gif" border="0" style="margin-right: 5px;" alt="Mês Anterior">
        </a>
        %s',
                $nome,
                $mes_ano,
                $ano_anterior_ano,
                $ano
            );

            $cab[] = sprintf(
                '
        <a href="#" onclick="acaoCalendario(\'%s\', \'\', \'%s\', \'%s\')">
          <img src="/intranet/imagens/i-educar/seta_dir.gif" border="0" style="margin-left: 5px;" alt="Mês Posterior">
        </a>',
                $nome,
                $mes_ano,
                $ano_posterior_ano
            );

            $cab = implode("\n", $cab);
        } else {
            $cab = [];

            $cab[] = sprintf(
                '
        <a href="javascript:void(1);" onclick="acaoCalendario(\'%s\',\'\',\'%s\',\'%s\')">
          <img src="/intranet/imagens/i-educar/seta_esq.gif" border="0" style="margin-right: 5px;" alt="Mês Anterior">
        </a>
        %s&nbsp;
        %s',
                $nome,
                $mes_anterior_mes,
                $ano_anterior_mes,
                $NomeMes,
                $ano
            );

            $cab[] = sprintf(
                '
        <a href="#" onclick="acaoCalendario(\'%s\', \'\', \'%s\', \'%s\')">
          <img src="/intranet/imagens/i-educar/seta_dir.gif" border="0" style="margin-left: 5px;" alt="Mês Posterior">
        </a>',
                $nome,
                $mes_posterior_mes,
                $ano_anterior_mes
            );

            $cab = implode("\n", $cab);
        }

        $calendario = sprintf(
            '
      <div id="d_calendario">
        <table class="calendar" cellspacing="0" cellpadding="0" border="0">',
            $this->largura_externa
        );

        $calendario .= sprintf(
            '
      <tr>
        <td class="cal_esq"><i class="fa fa-calendar" aria-hidden="true"></i></td>
        <td width="100%%" class="mes">%s</td>
        <td align="right" class="cal_dir">&nbsp;</td>
      </tr>',
            $cab
        );

        $calendario .= sprintf('<tr><td colspan="3">%s', $form);
        $calendario .= '<table cellspacing="2" cellpadding="0" width="100%" class="header"><tr>';

        // Create the calendar headers
        foreach ($diasDaSemana as $day) {
            if (end($diasDaSemana) == $day) {
                $calendario .= sprintf('<td style="width: 45px;">%s</td>', $day);
            } else {
                $calendario .= sprintf(
                    '<td>%s</td>',
                    $day
                );
            }
        }

        $calendario .= '</tr>';
        $calendario .= '</table>';
        $calendario .= '</td></tr>';

        $calendario .= '<tr><td colspan="3" valign="top">';
        $calendario .= '<table cellspacing="2" cellpadding="0" width="100%">';

        // Create the rest of the calendar
        // Initiate the day counter, starting with the 1st.
        $diaCorrente = 1;
        $calendario .= '<tr>';

        // The variable $DiaSemana is used to
        // ensure that the calendar
        // display consists of exactly 7 columns.
        if ($DiaSemana > 0) {
            $completar_dias = $DiaSemana;
            $ts = mktime(0, 0, 0, $dateComponents['mon'], -$completar_dias + 1, $dateComponents['year']);
            $day = date('d', $ts);

            for ($a = 0; $a < $completar_dias; $a++) {
                $calendario .= sprintf(
                    '<td class="dayLastMonth" style="padding-left:%spx;">%s</td>',
                    $this->padding,
                    $day
                );

                $day++;
            }
        }

        while ($diaCorrente <= $NumeroDiasMes) {
            // Seventh column (Saturday) reached. Start a new row.
            if ($DiaSemana == 7) {
                $DiaSemana = 0;
                $calendario .= '</tr><tr>';
            }

            $style_dia = sprintf('background-color: %s;', $this->array_cor[0]);

            if (isset($this->array_cor_dia_padrao[$DiaSemana])) {
                $style_dia = sprintf('background-color: %s;', $this->array_cor_dia_padrao[$DiaSemana]);
            }

            if (key_exists($diaCorrente, $this->array_dias)) {
                $key = $this->array_dias[$diaCorrente];
                $cor = $this->array_cor[$key];
                $style_dia = sprintf('background-color: %s;', $cor);
            }

            $onclick = '';

            if ($this->all_days_onclick) {
                $onclick = sprintf('onclick="%s"', $this->all_days_onclick);
            } elseif ($this->all_days_url) {
                $onclick = sprintf(
                    'onclick="document.location=\'%s&dia=%s&mes=%s&ano=%s\';"',
                    $this->all_days_url,
                    $diaCorrente,
                    $mes,
                    $ano
                );
            }

            if (key_exists($diaCorrente, $this->array_onclick_dias)) {
                $onclick = sprintf('onclick="%s;"', $this->array_onclick_dias[$diaCorrente]);
            }

            $icone = '';

            if (key_exists($diaCorrente, $this->array_icone_dias)) {
                $icone = sprintf(
                    '<i class="fa fa-pencil-square-o anotacao" aria-hidden="true"></i>',
                    $this->array_icone[$this->array_icone_dias[$diaCorrente]]['link'],
                    $this->array_icone[$this->array_icone_dias[$diaCorrente]]['nome']
                );
            }

            $message = '';
            $diaCorrente_ = strlen($diaCorrente) == 1 ? '0' . $diaCorrente : $diaCorrente;
            $NomeMes = strtolower($NomeMes);

            if (key_exists($diaCorrente, $this->array_div_flutuante_dias)) {
                $message = "onmouseover=\"ShowContent('{$diaCorrente}','{$mes}','{$ano}','{$nome}'); return true;\"";
                $mouseout = "onmouseout=\"HideContent(event,'{$diaCorrente}','{$mes}','{$ano}','{$nome}')\" ";
                $mensagens .= "
          <div $mouseout class='div_info' style='display:none; z-index: 10;' id=\"{$nome}_div_dia_{$diaCorrente}{$mes}{$ano}\">
            <div style='margin:0px 15px 0px 0px;font-size: 14px; z-index: 0; border-bottom: 1px solid #000000;'>{$diaCorrente_} de {$NomeMes} de $ano
            </div>
            <div style='align:left;padding-top:5px;z-index: 0;' class='dia'>
              {$this->array_div_flutuante_dias[$diaCorrente]}
            </div>
          </div>";
            }

            $calendario .= sprintf(
                '
        <td style=\'%s padding-left: %spx;\' id=\'%s_td_dia_%s%s%s\' class=\'day\' %s %s>
          %s %s
        </td>',
                $style_dia,
                $this->padding,
                $nome,
                $diaCorrente,
                $mes,
                $ano,
                $onclick,
                $message,
                $icone,
                $diaCorrente_
            );

            // Increment counters
            $diaCorrente++;
            $DiaSemana++;
        }

        // Complete the row of the last week in month, if necessary
        if ($DiaSemana != 7) {
            $remainingDays = 7 - $DiaSemana;

            for ($a = 1; $a <= $remainingDays; $a++) {
                //dayLastMonth
                $calendario .= sprintf(
                    '
          <td class="dayLastMonth" style="padding-left:%spx;">%s</td>',
                    $this->padding,
                    $a
                );
            }
        }

        if ($this->array_legenda) {
            $calendario .= '<tr><td colspan="7">';
            $calendario .= '
        <table cellspacing="2" cellpadding="0" class="legenda" width="100%">
          <tr>';

            $cont = 0;

            foreach ($this->array_legenda as $key => $legenda) {
                $style = sprintf(
                    'style="background-color: %s;"',
                    $this->array_cor[$key]
                );

                $calendario .= sprintf(
                    '<td %s class="cor">&nbsp;</td><td>%s</td>',
                    $style,
                    $legenda
                );

                $cont++;

                if ($cont == 3) {
                    $calendario .= '</tr><tr>';
                    $cont = 0;
                }
            }

            $calendario .= '</tr></table>';
            $calendario .= '</td></tr>';
        }

        if ($this->array_icone_dias) {
            $calendario .= '<tr><td colspan="7">';
            $calendario .= '<table cellspacing="2" cellpadding="0" class="legenda" width="100%">
        <tr align="left">';

            $cont = 0;

            foreach ($this->array_icone as $key => $legenda) {
                if ($legenda['utilizado']) {
                    $style = sprintf('style="background-color: %s;"', $this->array_cor[$key]);
                    $icone = '';

                    $icone = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';

                    $calendario .= sprintf(
                        '<td %s align="left"></td><td width="100%%">%s %s</td>',
                        $style,
                        $icone,
                        $legenda['nome']
                    );

                    $cont++;

                    if ($cont == 3) {
                        $calendario .= '</tr><tr>';
                        $cont = 0;
                    }
                }
            }

            $calendario .= '</tr></table>';
            $calendario .= '</td></tr>';
        }

        $calendario .= '</table>';
        $calendario .= '</td></tr>';
        $calendario .= '</table></div>';

        if (isset($mensagens)) {
            $calendario .= $mensagens;
        }

        return $calendario;
    }

    /**
     * Gera campos hidden para o formulário do calendário.
     *
     * Exemplo de uso:
     *
     * <code>
     * <?php
     * $formValues = array(
     *   'formFieldKey' => 'formFieldValue'
     * );
     * print $this->_generateFormValues($formValues);
     * // <input id="cal_formFieldKey" name="formFieldKey" type="hidden" value="formFieldValue" />
     * </code>
     *
     * @access protected
     *
     * @param array $formValues   Array associativo onde a chave torna-se o
     *                            o valor dos atributos id e name do campo hidden.
     * @param array $invalidNames Array com nomes inválidos para campos. Útil
     *                            para evitar que sejam criados campos duplicados.
     *
     * @return string String com o HTML dos campos hidden gerados.
     *
     * @since  Método disponível desde a versão 1.2.0
     *
     * @todo   Refatorar código de geração de html para uma classe externa.
     */
    public function _generateFormValues($formValues = [], $invalidNames = [])
    {
        $ret = '';

        if (is_array($formValues) && 0 < count($formValues)) {
            foreach ($formValues as $name => $value) {
                if (in_array($name, $invalidNames)) {
                    continue;
                }

                $ret .= sprintf(
                    '<input id="cal_%s" name="%s" type="hidden" value="%s" />',
                    $name,
                    $name,
                    $value
                );
            }
        }

        return $ret;
    }
}
