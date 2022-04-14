<?php

class relatoriosPref
{
    public $titulo_relatorio;
    public $pdf;
    public $altura=0;
    public $texto;
    public $lembretes;
    public $num_linhas=0;
    public $espacoEntreLinhas;
    public $numeroPagina=0;
    public $capa;
    public $rodape;
    public $lastMod;
    public $cabecalho;
    public $margem_esquerda = 50;
    public $margem_direita = 50;
    public $margem_topo = 50;
    public $margem_fundo = 50;
    public $txt_padding_left = 5;
    public $largura;
    public $alturaUltimaLinha = 13;
    public $qtd_pagina = 0;

    public function __construct($nome, $espacoEntreLinhas=80, $capa=false, $rodape=false, $tipoFolha='A4', $cabecalho='')
    {
        $this->pdf = new clsPDF($nome, 'Cartas Folhas de Rosto', $tipoFolha, '', false, false);
        $this->titulo_relatorio = $nome;
        $this->rodape = $rodape;
        $this->cabecalho = $cabecalho;
        $this->espacoEntreLinhas = $espacoEntreLinhas;
        $this->largura = $this->pdf->largura;
        if ($capa && ! ($capa[0] == '' && $capa[1] = '')) {
            $this->pdf->OpenPage();
            $linha = 0.0001;
            $cores = [ '#000000', '#111111', '#222222', '#333333', '#444444', '#555555', '#666666', '#777777', '#888888', '#999999', '#AAAAAA', '#BBBBBB', '#CCCCCC', '#DDDDDD', '#FFFFFF' ];
            $x = 100;
            $y = 150;
            $xMod = 7;
            $yMod = 9;

            $total = count($cores);
            for ($i = 0; $i < 7; $i++) {
                $this->pdf->Shape('ret', $x + ($i * $xMod), $y + ($i * $yMod), 400, 500, $linha, $cores[($total - $i - 1)]);
            }
            $this->pdf->Write($capa[0], 190, 290, 300, 100, 'arial', 20, $cores[0], 'center');
            $this->pdf->Write($capa[1], 190, 400, 300, 100, 'arial', 15, $cores[2], 'center');

            $this->pdf->Shape('ret', 50, 0, 25, 850, $linha, $cores[13], $cores[13]);
            $this->pdf->Shape('ret', 0, 750, 600, 25, $linha, $cores[13], $cores[13]);

            $this->pdf->Shape('ret', 52, 0, 25, 850, $linha, $cores[14], $cores[14]);
            $this->pdf->Shape('ret', 0, 752, 600, 25, $linha, $cores[14], $cores[14]);
            $this->pdf->ClosePage();
        }
    }

    public function setMargem($esquerda=50, $direita=50, $topo=50, $fundo=50)
    {
        $this->margem_direita = $direita;
        $this->margem_esquerda = $esquerda;
        $this->margem_topo = $topo;
        $this->margem_fundo = $fundo;
    }

    public function novaPagina()
    {
        $this->numeroPagina++;
        $this->pdf->OpenPage();
        $this->pdf->Shape('ret', 50, 785, 495, 15, 1, '#d3d3d3', '#d3d3d3');
        $this->pdf->Write($this->cabecalho, 50, 120, 500, 80, 'Arial', 14, '#000000', 'left');

        /*  // desenha as barras cinza do topo
            $this->pdf->Shape('ret', $this->margem_esquerda - 10, 765, 5, 50, 1, "#d3d3d3", "#d3d3d3");
            $this->pdf->Shape('ret', $this->margem_esquerda + 56, 765, $this->largura - $this->margem_esquerda - $this->margem_direita - 106, 50, 1, "#d3d3d3", "#d3d3d3");
            $this->pdf->Shape('ret', $this->largura - $this->margem_direita - 40, 765, ( $this->largura - $this->margem_direita ) - ( $this->largura - $this->margem_direita - 40 ) + 10, 50, 1, "#d3d3d3", "#d3d3d3");
            // desenha as duas caixas do fim (repete o de cima com altura diferente)
            $this->pdf->Shape('ret', $this->margem_esquerda - 10, 40, 5, 50, 1, "#d3d3d3", "#d3d3d3");
            $this->pdf->Shape('ret', $this->margem_esquerda + 56, 40, $this->largura - $this->margem_esquerda - $this->margem_direita - 106, 50, 1, "#d3d3d3", "#d3d3d3");
            $this->pdf->Shape('ret', $this->largura - $this->margem_direita - 40, 40, ( $this->largura - $this->margem_direita ) - ( $this->largura - $this->margem_direita - 40 ) + 10, 50, 1, "#d3d3d3", "#d3d3d3");
            // escreve a numeracao da pagina
            $this->pdf->Write( $this->numeroPagina, $this->pdf->largura - $this->margem_direita - 25, 125, 15, 80, 'Arial', 10, "#000000", "center" );
            // insere o brasao da prefeitura
            $this->pdf->InsertJpng( "gif", "imagens/brasao.gif", $this->margem_esquerda, 85, 0.35  );
            $this->pdf->Write( "Prefeitura de Itajaí\nCentro Tecnologico de Informação e Modernização Administrativa.\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC", 120, 110, 500, 80, 'Arial', 10, "#000000", "left" );
            // desenha a caixa para o titulo do relatorio
            $this->pdf->Shape('ret', $this->margem_esquerda - 1, $this->pdf->altura-129, $this->largura - $this->margem_esquerda - $this->margem_direita + 2, 12, 1, "#000000", "#000000");
            // escreve o titulo do relatorio
            $this->pdf->Write( $this->titulo_relatorio, $this->margem_esquerda + $this->txt_padding_left, 130, 500, 14, $fonte ,'10','#FFFFFF','left');
            // escreve o texto de rodape
            $this->pdf->Write( $this->rodape, $this->margem_esquerda + 70, 848, 500, 80, 'Arial', 15, "#000000", "left" );
            $this->pdf->Write( "produzido por CTIMA", $this->margem_esquerda + 350, 870, 500, 80, 'Arial', 7, "#000000", "left" ); */
        $this->pdf->Shape('ret', 50, 20, 500, 80, 0.01, '#000000');
        $this->pdf->Write('Lembretes:', 52, 822, 500, 80, 'Arial', 10, '#000000', 'left');
        $this->pdf->Line(295, 92, 295, 26, 0.01);

        $this->altura = 60;
    }

    public function fechaPagina()
    {
        $this->pdf->ClosePage();
        $this->altura = 0;
    }

    // funcao para ser chamada a cada nova linha
    public function novalinha($texto, $deslocamento=0, $altura=13, $titulo=false, $fonte='arial', $divisoes=false, $lembretes=false, $extra_hor_spaco_antes = 0, $extra_hor_spaco_depois = 0)
    {
        if (! $divisoes) {
            $divisoes = $this->espacoEntreLinhas;
        }
        $cor = '#000000';
        $fundo = '#FFFFFF';
        if ($this->altura == 0) {
            $this->novaPagina();
        }
        if ($titulo) {
            $fundo = '#e1e1e1';
            $cor = '#000000';
            $this->qtd_pagina++;
        }

        //Verifica se é o fim da página
        if ($this->altura +$altura > ($this->pdf->altura * 0.80) || $this->qtd_pagina >2 && $lembretes == false) {
            $this->fillText();
            //  if($this->altura == 0 || $this->qtd_pagina >2)
            //  {
            $this->novaPagina();
            // altera a altura atual (de acordo com a altura passa)
            $this->altura += $altura + $extra_hor_spaco_antes;
            $this->alturaUltimaLinha = $altura;
            if ($titulo) {
                $this->qtd_pagina = 1;
            } else {
                $this->qtd_pagina = 0;
            }
            //  }
        } elseif ($lembretes == false) {
            // altera a altura atual (de acordo com a altura passa)
            $this->altura += $altura + $extra_hor_spaco_antes;
            $this->alturaUltimaLinha = $altura;
        }
        if ($lembretes) {
            $this->lembretes[] = ['texto'=>$texto, 'altura'=>$this->altura, 'fonte'=>$fonte, 'desloc'=>$deslocamento, 'alturaLinha'=>$altura, 'fundo'=>$fundo, 'cor'=>$cor, 'titulo'=>$titulo, 'divisoes'=>$divisoes, 'alturaultimalinha'=>$this->alturaUltimaLinha ];
        } else {
            $this->texto[] = ['texto'=>$texto, 'altura'=>$this->altura, 'fonte'=>$fonte, 'desloc'=>$deslocamento, 'alturaLinha'=>$altura, 'fundo'=>$fundo, 'cor'=>$cor, 'titulo'=>$titulo, 'divisoes'=>$divisoes, 'alturaultimalinha'=>$this->alturaUltimaLinha ];
        }

        $this->altura += $extra_hor_spaco_depois;
    }

    public function fillText()
    {
        //  $this->pdf->Shape('ret', $this->margem_esquerda - 1, $this->pdf->altura -1 - $this->altura, $this->largura - $this->margem_direita - $this->margem_esquerda + 2, $this->altura-48, 1);
        // passa todas as linhas
        if ($this->texto) {
            foreach ($this->texto as $linha) {
                if (!$linha['titulo']) {
                    $this->num_linhas++;
                }
                $mod = ($linha['alturaLinha'] - $linha['alturaultimalinha'] > 0) ? ($linha['alturaLinha'] - $linha['alturaultimalinha']): 0;
                $mod += ($linha['alturaLinha'] > $this->lastMod) ? $this->lastMod : 0;
                // se for titulo ou linha impar desenha uma caixa no fundo
                if ($this->num_linhas % 2 || $linha['titulo']) {
                    if ($linha['titulo']) {
                        $this->pdf->Shape('ret', $this->margem_esquerda, $this->pdf->altura - $linha['altura'] - $mod, $this->largura - $this->margem_direita - $this->margem_esquerda, $linha['alturaLinha'], 1, $linha['fundo'], $linha['fundo']);
                    } else {
                        $this->pdf->Shape('ret', $this->margem_esquerda, $this->pdf->altura - $linha['altura'] - $mod, $this->largura - $this->margem_direita - $this->margem_esquerda, $linha['alturaLinha'], 1, $linha['fundo'], $linha['fundo']);
                    }
                }
                $i = 0;
                $col = 0;
                // passa as colunas escrevendo elas
                foreach ($linha['texto'] as $texto) {
                    $posx = $this->margem_esquerda + $this->txt_padding_left + $i + $linha['desloc'];
                    $this->pdf->Write($texto, $posx, $linha['altura']+$mod, $this->largura - $this->margem_direita - $posx, $linha['alturaLinha'], $linha['fonte'], '10', $linha['cor'], 'left');
                    $colSum = (is_array($linha['divisoes']))? $linha['divisoes'][$col]: $linha['divisoes'];
                    $i += $colSum;
                    $col++;
                }
                $this->lastMod = $mod ;
            }
        }

        for ($i = 0; $i<2;$i++) {
            $lembrete = $this->lembretes[$i];
            if (is_array($lembrete)) {
                $lembrete = $lembrete['texto'][0];
            }
            if ($i==1) {
                $this->pdf->Write("$lembrete", 52, 842, 250, 80, 'Arial', 8, '#000000', 'left');
            } else {
                $this->pdf->Write("$lembrete", 300, 842, 250, 80, 'Arial', 8, '#000000', 'left');
            }

            //print_r($lembrete);
        }
        $this->texto ='';
        $this->lembretes ='';
        $this->altura = 0;
        $this->fechaPagina();
    }

    public function fechaPdf()
    {
        if ($this->texto || $this->lembretes) {
            $this->fillText();
        }
        $this->pdf->ClosePage();
        $link = $this->pdf->GetLink();
        $this->pdf->CloseFile();

        return $link;
    }
}
