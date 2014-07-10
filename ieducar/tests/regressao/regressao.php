<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/** 
 *  Use este arquivo pelo terminal para verificar a saida:
 * Terminal: php regressao.php [gerar|comparar] [intranet/pagina.php]? 
 * gerar -> gera o html da pagina passada ou de todas disponiveis em intranet
 * comparar -> gera o html da pagina passada e compara com htmls gerados anteriormente
 * 
 * *Obs: a pagina eh opcional, se nao for passada, o teste busca todas as paginas
 * que achar pela frente dentro do dominio do ieducar
 */

// Inicia o gerenciador da regressao
require_once('IEducarRegressao.php');
$regressao = new IEducarRegressao();

<<<<<<< HEAD
$regressao->listarPaginasParaTestar();
$regressao->gerar();

//$regressao->comparar('1404404723', '1404404948');
=======
//$regressao->listarPaginasParaTestar();
//$regressao->gerar();

$regressao->comparar('1403402434', '1403404522');
>>>>>>> 2d39b33ffbca7db873d8353afbeb5b433dc4fa65
