<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
/**
 * Esse script em PHP fara a refatoracao das classes com IndexBase
 */

// Listar as classes que usem o indexBase SOMENTE para setar o titulo e o processoAP
// Continuar setando o titulo usando o metodo 'SetTitulo' 
// Pegar o que eh passado para 'processoAP' e criar um metodo 'SetProcessoAP'


// Lista as paginas disponiveis em intranet/
$arquivosEPastas = scandir('../../intranet');

// Deixa somente as que possuem extensao php
$arquivosParaRefatorar = array();
foreach($arquivosEPastas as $nomeArquivo)
{
    // Verifica as 3 ultimas letras do arquivo
    if(substr($nomeArquivo, -4) == ".php")
    {
        $codigo = file_get_contents('../../intranet/' . $nomeArquivo);
        $classe = array();
        if(preg_match_all('%class\s*clsIndex(Base)?\s*extends\s*clsBase\s*\{.*?\}\s*class%ms', $codigo, $classe))
        {
            $arquivosParaRefatorar[$nomeArquivo] = array();
            //$arquivosParaRefatorar[$nomeArquivo][] = $codigo;
            $arquivosParaRefatorar[$nomeArquivo][] = $classe[0][0];
        }
    }
}

var_dump($arquivosParaRefatorar);