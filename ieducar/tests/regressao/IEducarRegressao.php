<?php

// TODO: melhorar a comparacao gerando-se MD5, ao invez de comparacao via texto

// Inicia o simulador de browser
require_once('IEducarBrowser.php');

/**
 * Controla items da regressao como geracao do HTML e remocao de elementos
 * nao estaticos
 */
class IEducarRegressao
{
    /**
     *  Aqui serao guardadas as paginas a serem testadas
     */
    var $paginasParaTestar = array();
    
    /**
     * Paginas com erro
     */
    var $paginasComErro = array();
    
    /**
     *  Onde estao as regressoes geradas
     */
    var $diretorioRegressoes = 'regressoes/';
    
    /**
     *  Usa esse browser para requisitar as paginas
     */
    var $browser;
    
    /**
     *  Algumas paginas nao devem ser testadas
     */
    var $blackListDePaginas = array(
        'acesso_indevido_det.php',
        'logof.php',
        'S3.php',
    );
    
    function __construct() 
    {
        $this->browser = new IEducarBrowser();
        if(!$this->browser->logar())
        {
            echo "*** ERRO ***\n";
            echo " --- NAO FOI POSSIVEL LOGAR NO IEDUCAR ---\n";
            echo " --- ABORTANDO A REGRESSAO ---\n";
            echo "************\n";
            exit(-1);
        }
    }
    
    /**
     * Determina quais paginas testar
     */
    function listarPaginasParaTestar()
    {
        if(isset($_GET['pagina']))
        {
            $this->paginasParaTestar[] = $_GET['pagina'];
            return;
        }
        
        // Lista as paginas disponiveis em intranet/
        $arquivosEPastas = scandir('../../intranet');
        
        // Deixa somente as que possuem extensao php
        foreach($arquivosEPastas as $nomeArquivo)
        {
            // Verifica as 3 ultimas letras do arquivo
            if(substr($nomeArquivo, -4) == ".php" && !in_array($nomeArquivo, $this->blackListDePaginas))
                $this->paginasParaTestar[] = $nomeArquivo;
        }
    }
    
    /**
     * Remove os elementos das paginas que se mudam a cada acesso
     * @param type $html
     */
    function removerElementosMutaveis(&$html)
    {
        // Remove principalmente datas
        $html = preg_replace('%.ltimo Acesso: .*?td>%', '', $html);
        
        // Timestamps
        $html = preg_replace('%1\d{9}%', '', $html);
        
        // Datas
        $html = preg_replace('%\d{1,2} de [JFMASOND] de \d{4}%', '', $html);
        
        // Outro formato de data
        $html = preg_replace('%20\d{2}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}.\d{6}%', '', $html);
    }
    
    /**
     * Verifica se a pagina foi gerada corretamente
     * Retorna true se estiver, false caso contrario
     */
    function paginaEstaCorreta(&$html)
    {
        if(strlen($html) < 100)
            return false;
        
        if(!(strpos($html, 'ltimo Acesso: ') > 0))
            return false;
        
        return true;
    }
    
    /**
     * Acessa TODAS as paginas em $paginasParaTestar e grava a resposta
     * HTML dentro da pasta regressoes
     * Retorna o nome da pasta com as regressoes geradas
     */
    function gerar()
    {   
        // Cria a nova pasta
        $nomeNovaPasta = $this->diretorioRegressoes . time();
        mkdir($nomeNovaPasta);
        
        // Percorre as paginas gerando seus respectivos html
        foreach($this->paginasParaTestar as $pagina)
        {
            echo "Gerando: $pagina...";
            $html = $this->browser->abrirPagina("intranet/$pagina"); 
            
            echo "salvando resposta em $nomeNovaPasta/$pagina: ";
            if($this->paginaEstaCorreta($html))
            {
                $this->removerElementosMutaveis($html);
                echo "OK\n";
            }
            else
            {
                echo "ERRO\n";
                $html = "Houve algum erro ao acessar essa pagina!";
                $this->paginasComErro[] = $pagina;
            }

            file_put_contents("$nomeNovaPasta/$pagina", $html);
        }
        return $nomeNovaPasta;
    }
    
    /**
     * Compara arquivo-por-arquivo duas regressoes, gerando um diff de cada arquivo
     * @param type $regressao1 diretorio da regressao1
     * @param type $regressao2 diretorio da regressao2
     */
    function comparar($regressao1, $regressao2)
    {
        // Extrai os arquivos das regressoes
        $arquivosR1 = scandir($this->diretorioRegressoes . $regressao1);
        $arquivosR2 = scandir($this->diretorioRegressoes . $regressao2);
        
        // Antes de mais nada, lista quais arquivos NAO sao comuns
        // as duas regressoes
        $arquivosSomenteEmR1 = array_diff($arquivosR1, $arquivosR2);
        $arquivosSomenteEmR2 = array_diff($arquivosR2, $arquivosR1);
        
        if(sizeof($arquivosSomenteEmR1) > 0)
            echo "Esses arquivos existem somente em $regressao1: " . implode(', ', $arquivosSomenteEmR1) . "\n";
        if(sizeof($arquivosSomenteEmR2) > 0)
            echo "Esses arquivos existem somente em $regressao2: " . implode(', ', $arquivosSomenteEmR2) . "\n";
        
        // Itera sobre R1 
        $i = 0;
        foreach($arquivosR1 as $arquivo)
        {
            if($i++ < 2)
                continue;

            // Verifica se existe o mesmo arquivo em R2
           // if(!in_array($arquivo, $arquivosSomenteEmR2))
             //   continue;
            
            echo "Arquivos $regressao1/$arquivo e $regressao2/$arquivo sao ";
            
            $diff = shell_exec("diff {$this->diretorioRegressoes}$regressao1/$arquivo {$this->diretorioRegressoes}$regressao2/$arquivo");
            if(strlen($diff) == 0)
                echo "Iguais\n";
            else
                echo "Diferentes: $diff\n\n";            
        }
    }
}

