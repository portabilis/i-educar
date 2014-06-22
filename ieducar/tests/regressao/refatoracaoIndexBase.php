<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
/**
 * Esse script em PHP fara a refatoracao das classes com IndexBase
 */

// Listar as classes que usem o indexBase
// Continuar setando o titulo usando o metodo 'SetTitulo' 
// Pegar o que eh passado para 'processoAP' e criar um metodo 'SetProcessoAP'
// O restante deve ser utilizado substituindo-se $this-> por $base->


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
            $arquivosParaRefatorar[$nomeArquivo]['codigo'] = $codigo;
            $arquivosParaRefatorar[$nomeArquivo]['classe'] = $classe[0][0];
        }
    }
}

// Agora vamos iterar osbre os arquivos para refatorar 
foreach($arquivosParaRefatorar as $nomeArquivo => $info)
{
    // Primeiro, deleta a classe IndexBase
    $info['codigo'] = str_replace($info['classe'], 'class', $info['codigo']);
    
    // Agora pega a linha de instanciacao da Index(Base) e troca por clsBase
    $info['codigo'] = preg_replace('%pagina = new clsIndex(Base)?%', 'pagina = new clsBase', $info['codigo']);
    
    // Antes do gran finale, substitui o 'this' do formular para 'pagina'
    $info['classe'] = str_replace('$this->', '$pagina->', $info['classe']);
    $info['classe'] = preg_replace('%.*Formular\s*\(\s*\)\s*\{(.*?)\}\s*\}\s*class%s', '\1', $info['classe']);
    $info['classe'] = preg_replace('%\t{2}%s', '', $info['classe']); // retira os tabs
    
    // Para finalizar, traz o conteudo do metodo Formular pra dentro baixo
    // da instanciacao da clsBase
    $posicaoDaInstanciacao = strpos($info['codigo'], 'pagina = new clsBase();') + strlen('pagina = new clsBase();') + 1;
    $antes = substr($info['codigo'], 0, $posicaoDaInstanciacao);
    $depois = substr($info['codigo'], $posicaoDaInstanciacao);
    $info['codigo'] = $antes . $info['classe'] . $depois;
    
    var_dump($info);
    //break;
}