<?php

class LocalizacaoSistema {

    private $localizacao    = array();
    private $url            = '';
    private $protocolo      = '';
    private $separador      = '';
    private $html           = '';

    public function  __construct() {
        $this->protocolo = $this->getProtocoloHttp();
        $this->url = $this->getUrl();
        $this->localizacao = $this->url( $this->url );
        $this->montarLocalizacao();
    }

    public function entradaCaminhos( array $localizacao ) {
        $this->localizacao = $localizacao;
        $this->montarLocalizacao();
    }

    public function get() {
        return $this->localizacao;
    }

    public function getProtocoloHttp() {
        if( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] === 'on' ) {
            $protocolo = 'https://';
        } else {
            $protocolo = 'http://';
        }
        return $protocolo;
    }

    public function getUrl() {
        return $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }

    public function url( $url ) {
        $fragments  = array();
        $localizacao     = array();
        $_url = preg_replace( array( "/[http]s?:\/\//", "/\/$/" ), array( "", "" ), $url );
        $fragments = explode( "/", $_url );

        foreach( $fragments as $fragment ) {
            $localizacao[$fragment] = $fragment;
        }

        return $localizacao;
    }

    private function montarLocalizacao() {
        $href = '';
        $localizacao_count = sizeof( $this->localizacao );
        $i = 1;
        $linkVazio="#";

        $this->html = '<div class="breadcrumb">';
        foreach( $this->localizacao as $link => $inner ) {
            $href .= ( $i === 1 ) ? $this->protocolo . $link : "/$link";
            if( $i === $localizacao_count )
                $this->html .= "<a href=\"$linkVazio\" class='breadcrumb-current'>$inner</a>";
            elseif($i == 1)
                $this->html .= "<a href=\"$href\" title=\"Ir para o $inner\"><i class='fa fa-home' aria-hidden='true'></i> <span> $inner</span></a> <a class='breadcrumb-separator' href=\"$linkVazio\"> / </a> {$this->separador} ";
            else
                $this->html .= "<a href=\"$href\" title=\"Ir para o $inner\">$inner</a> <a class='breadcrumb-separator' href=\"$linkVazio\"> / </a> {$this->separador} ";

            $i++;
        }
        $this->html .= '</div>';
    }
    

    public function montar() {
        return $this->html;
    }
    
}
?>
