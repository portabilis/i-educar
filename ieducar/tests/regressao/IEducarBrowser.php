<?php

/* 
 * Simula um browser para realizar teste de regressao do IEducar
 */
class IEducarBrowser
{
    private $curl;
    private $enderecoBase;
    private $paginaLogin = 'intranet/index.php';
    
    function __construct($enderecoBase = "http://localhost/") 
    {
        $this->enderecoBase = $enderecoBase;
        
        // Inicia o curl
        $this->curl = curl_init();
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
        ));
    }
    
    /**
     *  Libera geral!
     */
    function __destruct() 
    {
        curl_close($this->curl);
    }
    
    /**
     * Abre qualquer pagina dentro do IEducar.
     * Caso queira passar parametros GET, passe pela pagina
     * @param type $pagina pagina a ser aberta
     * @return type corpo HTML da resposta da requisicao
     */
    function abrirPagina($pagina, $postArray = null)
    {
        if($postArray != null && is_array($postArray))
        {
            curl_setopt_array($this->curl, array(
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postArray
            ));
        }
        else
        {
            ;//curl_setopt ($this->curl, CURLOPT_POST, 0);
        }
        
        curl_setopt_array($this->curl, array(CURLOPT_URL => $this->enderecoBase . $pagina));
        return curl_exec($this->curl);
    }
    
    /**
     * Loga no IEducar passando login e senhas padrao
     * @param type $login login qualquer, padrao eh admin
     * @param type $senha senha qualquer, padrao eh admin
     * @return type true se logou, false caso contrario
     */
    function logar($login = 'admin', $senha = 'admin')
    {        
        $resposta = $this->abrirPagina('intranet/index.php', array(
                'login' => $login,
                'senha' => $senha
        ));
        
        //echo $resposta;
        
        // Verifica se logou
        return strpos($resposta, 'ltimo Acesso') > 0;
    }
}
