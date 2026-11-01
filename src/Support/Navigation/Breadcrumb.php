<?php

namespace iEducar\Support\Navigation;

class Breadcrumb
{
    /**
     * @var string
     */
    private $currentPage;

    /**
     * @var array
     */
    private $pages = [];

    /**
     * @var string
     */
    private $legacy;

    /**
     * @var bool
     */
    private $beta = false;

    /**
     * Define a página atual e as páginas até ela.
     *
     * @param string $currentPage
     * @param array  $pages
     * @return $this
     */
    public function current($currentPage, $pages = [])
    {
        $this->currentPage = html_entity_decode($currentPage);

        // A trilha é redefinida junto com a página atual, e não acrescentada: a
        // classe é singleton do container, e num processo reaproveitado — como o
        // servidor in-process dos testes de browser, ou o Octane — o mesmo objeto
        // atende várias requisições. Sem o reset a trilha só cresce, repetindo os
        // mesmos itens ("Início / Escola / Escola / Escola / Configurações").
        $this->pages = [];

        foreach ($pages as $link => $label) {
            $this->push($label, $link);
        }

        return $this;
    }

    /**
     * Adiciona uma página.
     *
     * @param string $label
     * @param string $link
     * @return $this
     */
    public function push($label, $link)
    {
        $std = new \stdClass;

        $std->label = $label;
        $std->link = $link;

        $this->pages[] = $std;

        return $this;
    }

    /**
     * Retorna todas as páginas.
     *
     * @return array
     */
    public function pages()
    {
        return $this->pages;
    }

    /**
     * Retorna o título da página atual.
     *
     * @return string
     */
    public function currentPage()
    {
        return $this->currentPage;
    }

    /**
     * Indica se existem páginas para serem exibidas.
     *
     * @return bool
     */
    public function hasPages()
    {
        return count($this->pages()) || strlen($this->currentPage());
    }

    /**
     * @return string
     */
    public function getLegacy()
    {
        return $this->legacy;
    }

    public function setLegacy(string $legacy)
    {
        $this->legacy = $legacy;
    }

    public function addBetaFlag()
    {
        $this->beta = true;
    }

    public function isBeta(): bool
    {
        return $this->beta;
    }
}
