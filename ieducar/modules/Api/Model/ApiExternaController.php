<?php

class ApiExternaController
{
    private $url;
    private $curl;
    private $params;
    private $tipoRequisicao;
    private $recurso;

    const REQUISICAO_GET = 1;
    const REQUISICAO_POST = 2;

    public function __construct($options)
    {
        $this->url = $options['url'];
        $this->recurso = $options['recurso'];
        $this->tipoRequisicao = $options['tipoRequisicao'];
        $this->params = $options['params'];

        $this->setTokenHeader($options['token_header']);
        $this->setTokenKey($options['token_key']);

        $this->curl = curl_init();
    }

    // TODO ajustar para funcionar com magic getters e setters
    public function setParams($params)
    {
        $this->params = $params;
    }

    private function getParams()
    {
        return $this->params;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    private function getUrl()
    {
        return $this->url . $this->recurso;
    }

    public function setRequisicaoTipo($tipoRequisicao)
    {
        $this->tipoRequisicao = $tipoRequisicao;
    }

    private function getResquisicaoTipo()
    {
        return $this->tipoRequisicao;
    }

    private function setTokenHeader($tokenHeader)
    {
        $this->tokenHeader = $tokenHeader;
    }

    private function setTokenKey($tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    private function getTokenHeader()
    {
        return $this->tokenHeader;
    }

    private function getTokenKey()
    {
        return $this->tokenKey;
    }

    public function executaRequisicao()
    {
        $options = [
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_POST => ($this->tipoRequisicao == self::REQUISICAO_POST),
            CURLOPT_POSTFIELDS => $this->params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->getTokenKey(),
        ];

        curl_setopt_array($this->curl, $options);

        return curl_exec($this->curl);
    }
}
