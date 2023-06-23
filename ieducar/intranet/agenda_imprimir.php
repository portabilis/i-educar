<?php

return new class extends clsCadastro
{
    public $cod_agenda;

    public $link;

    public function __construct()
    {
        foreach ($_GET as $nm => $var) {
            $this->$nm = $var;
        }
        foreach ($_POST as $nm => $var) {
            $this->$nm = $var;
        }
    }

    public function semDesc($data_atual)
    {
        $diasSemana = ['Domingo', 'Segunda Feira', 'Terça Feira', 'Quarta Feira', 'Quinta Feira', 'Sexta Feira', 'Sabado'];

        return $diasSemana[date(format: 'w', timestamp: strtotime(datetime: $data_atual))];
    }

    public function quebraLinha($texto, $tamaho_max_caracteres)
    {
        if (strlen(string: $texto) > $tamaho_max_caracteres) {
            $texto_array = explode(separator: ' ', string: $texto);
            $texto = '';
            $tamanho_linha = 0;
            foreach ($texto_array as $palavra) {
                $tamanho_palavra = strlen(string: $palavra);
                // se uma unica palavra for maior do que a linha quebra essa palavra no meio
                if ($tamanho_palavra > $tamaho_max_caracteres) {
                    $texto .= substr(string: $palavra, offset: 0, length: $tamaho_max_caracteres) . "\n" . substr(string: $palavra, offset: $tamaho_max_caracteres) . ' ';
                } else {
                    // com essa palavra a linha vai passar do limite de caracteres?
                    if ($tamanho_linha + $tamanho_palavra >= $tamaho_max_caracteres) {
                        $texto .= "\n{$palavra} ";
                        $tamanho_linha = $tamanho_palavra;
                    } else {
                        // apenas adiciona a palavra na linha e continua
                        $texto .= "{$palavra} ";
                        $tamanho_linha += $tamanho_palavra;
                    }
                }
            }
        }

        return $texto;
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        if (isset($_GET['cod_agenda'])) {
            $this->cod_agenda = $_GET['cod_agenda'];
        }

        $this->breadcrumb(currentPage: 'Imprimir agenda');

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoData(nome: 'data_inicio', campo: 'Data Inicial', valor: $this->data_inicio);

        $this->campoData(nome: 'data_fim', campo: 'Data Final', valor: $this->data_fim);

        $this->campoRadio(nome: 'impressora', campo: 'Tipo de Impressora', valor: ['Laser', 'Jato de Tinta'], default: $this->impressora);

        if ($this->link) {
            $this->campoRotulo(nome: 'arquivo', campo: 'Arquivo', valor: "<a href='$this->link'>Clique aqui para visualizar</a>");
        }

        $this->url_cancelar = "agenda.php?cod_agenda={$this->cod_agenda}";

        $this->nome_url_cancelar = 'Cancelar';
    }

    public function Novo()
    {
        $where = '';
        // define as datas de limite dos compromissos
        if (!empty($this->data_inicio)) {
            $data_inicio = urldecode(string: $this->data_inicio);
            $data_inicio = explode(separator: '/', string: $data_inicio);
            $data_inicio = "{$data_inicio[2]}-{$data_inicio[1]}-{$data_inicio[0]}";
            $where .= "'{$data_inicio} 00:00:00' <= data_inicio AND ";
        }
        if (!empty($this->data_fim)) {
            $data_fim = urldecode(string: $this->data_fim);
            $data_fim = explode(separator: '/', string: $data_fim);
            $data_fim = "{$data_fim[2]}-{$data_fim[1]}-{$data_fim[0]}";
            $where .= "'{$data_fim} 23:59:59' >= data_fim AND ";
        }

        $compromissos = [];

        //busca nome da agenda para titulo do relatorio
        $dba = new clsBanco();
        $nm_agenda = $dba->CampoUnico(consulta: " SELECT nm_agenda FROM agenda WHERE cod_agenda = {$this->cod_agenda} ");

        //verifica tipo de impressao
        if ($this->impressora == 1) {
            //impressao laser
            $relatorio = new relatoriosPref(nome: false, espacoEntreLinhas: 80, capa: false, rodape: false, tipoFolha: 'A4', cabecalho: 'Agenda: '.$nm_agenda);
        } else {
            //impressao jato de tinta
            $relatorio = new relatorios(nome: 'Agenda: '.$nm_agenda, espacoEntreLinhas: 10);
        }

        if (($data_inicio > $data_fim) & (isset($data_fim))) {
            $this->mensagem = 'A data inicial não pode ser maior que a data final.';
        }

        $db = new clsBanco();
        $db->Consulta(consulta: "SELECT cod_agenda_compromisso, versao FROM agenda_compromisso WHERE ativo = 1 AND ref_cod_agenda = {$this->cod_agenda} AND $where data_fim IS NOT NULL ORDER BY data_inicio ASC ");

        while ($db->ProximoRegistro()) {
            [$cod_comp, $versao] = $db->Tupla();
            $compromissos[] = ['cod' => $cod_comp, 'versao' => $versao];
        }

        if (count(value: $compromissos)) {
            $data_ant = '';
            foreach ($compromissos as $compromisso) {
                $db->Consulta(consulta: "SELECT data_inicio, data_fim, titulo, descricao FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$compromisso['cod']}' AND ref_cod_agenda = {$this->cod_agenda} AND versao = '{$compromisso['versao']}' ");

                if ($db->ProximoRegistro()) {
                    // inicializacao de variaveis
                    $qtd_tit_copia_desc = 5;

                    [$data_inicio, $data_fim, $titulo, $descricao] = $db->Tupla();

                    // TITULO
                    if ($titulo) {
                        $disp_titulo = $titulo;
                    } else {
                        // se nao tiver titulo pega as X primeiras palavras da descricao ( X = $qtd_tit_copia_desc )
                        $disp_titulo = implode(separator: ' ', array: array_slice(array: explode(separator: ' ', string: $descricao), offset: 0, length: $qtd_tit_copia_desc));
                    }

                    $hora_comp = substr(string: $data_inicio, offset: 11, length: 5);
                    $hora_fim = substr(string: $data_fim, offset: 11, length: 5);

                    //verifica tipo da impressora 1 laser 0 jato de tinta
                    if ($this->impressora == 0) {
                        if ($data_ant != substr(string: $data_inicio, offset: 0, length: 10)) {
                            $relatorio->novalinha(texto: [$this->semDesc(data_atual: $data_inicio).': '.date(format: 'd/m/Y', timestamp: strtotime(datetime: $data_inicio))], deslocamento: 0, altura: 12, titulo: true);
                            $relatorio->novalinha(texto: ["{$hora_comp} as {$hora_fim} {$disp_titulo}"], deslocamento: 0, altura: 13 + 10 * (strlen(string: $disp_titulo) / 60));

                            $linhas = count(value: explode(separator: "\n", string: $descricao));
                            $relatorio->novalinha(texto: [false, $descricao], deslocamento: 62, altura: 13 + 10 * $linhas);

                            $data_ant = substr(string: $data_inicio, offset: 0, length: 10);
                        } else {
                            if ($hora_comp == '00:00') {
                                $relatorio->novalinha([date(format: 'd/m/Y', timestamp: strtotime(datetime: $data_inicio))." - $descricao"], 0, 13 * (count(value: explode(separator: "\n", string: $descricao)) + 1), false, 'arial', false, true);
                            } else {
                                $relatorio->novalinha(texto: ["{$hora_comp} as {$hora_fim} {$disp_titulo}"], deslocamento: 0, altura: 13 * (count(value: explode(separator: "\n", string: $disp_titulo))));
                                $linhas = count(value: explode(separator: "\n", string: $descricao));
                                $relatorio->novalinha(texto: [false, $descricao], deslocamento: 62, altura: 13 + 10 * $linhas);
                            }
                        }
                    } else {
                        // laser
                        if ($data_ant != substr(string: $data_inicio, offset: 0, length: 10)) {
                            $relatorio->novalinha([$this->semDesc(data_atual: $data_inicio).': '.date(format: 'd/m/Y', timestamp: strtotime(datetime: $data_inicio))], 0, 13, true, 'arial', false, false, 10, 3);
                            $data_ant = substr(string: $data_inicio, offset: 0, length: 10);
                        }
                        if ($hora_comp == '00:00') {
                            $relatorio->novalinha([date(format: 'd/m/Y', timestamp: strtotime(datetime: $data_inicio))." - $descricao"], 0, 13 + 10 * (strlen(string: $descricao) / 60), false, 'arial', false, true);
                        } else {
                            if ($titulo || $descricao) {
                                $textoLinha = '';
                                if ($titulo) {
                                    $textoLinha = $titulo;
                                }
                                if ($descricao) {
                                    if ($textoLinha) {
                                        $textoLinha .= "\n\n";
                                    }
                                    $textoLinha .= $descricao;
                                }

                                if ($textoLinha) {
                                    $linhas = ceil(num: strlen(string: $textoLinha) / 90);
                                    $linhas += count(value: explode(separator: "\n", string: $textoLinha));
                                    $relatorio->novalinha(texto: ["{$hora_comp} as {$hora_fim}", $textoLinha], deslocamento: 0, altura: 13 + 10 * $linhas);
                                }
                            }
                        }
                    }
                }
            }
            $this->link = $relatorio->fechaPdf();
        }

        return true;
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '345';
    }
};
