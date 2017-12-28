<?php

class ApiExternaController{
    private $url;
    private $curl;
    private $params;
    private $tipoRequisicao;
    private $recurso;

    const REQUISICAO_GET  = 1;
    const REQUISICAO_POST = 2;

    public function __construct($options){

        $this->url            = $options['url'];
        $this->recurso        = $options['recurso'];
        $this->tipoRequisicao = $options['tipoRequisicao'];
        $this->params         = $options['params'];

        $this->curl = curl_init();
    }

    #TODO ajustar para funcionar com magic getters e setters
    public function setParams($params){
        $this->params = $params;
    }
    private function getParams(){
        return $this->params;
    }

    public function setUrl($url){
        $this->url = $url;
    }
    private function getUrl(){
        return $this->url . $this->recurso;
    }

    public function setRequisicaoTipo($tipoRequisicao){
        $this->tipoRequisicao = $tipoRequisicao;
    }
    private function getResquisicaoTipo(){
        return $this->tipoRequisicao;
    }

    public function executaRequisicao(){
        $options = array( CURLOPT_URL            => $this->getUrl(),
                          CURLOPT_POST           => ($this->tipoRequisicao == self::REQUISICAO_POST),
                          CURLOPT_POSTFIELDS     => $this->params,
                          CURLOPT_RETURNTRANSFER => true);
        curl_setopt_array($this->curl, $options);

        return curl_exec($this->curl);
    }

}