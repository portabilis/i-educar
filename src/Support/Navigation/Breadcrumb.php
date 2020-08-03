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
     *
     * @return $this
     */
    public function current($currentPage, $pages = [])
    {
        $this->currentPage = $currentPage;

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
     *
     * @return $this
     */
    public function push($label, $link)
    {
        $std = new \stdClass();

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

    /**
     * @param string $legacy
     */
    public function setLegacy(string $legacy)
    {
        $this->legacy = $legacy;
    }

    public function addBetaFlag()
    {
        $this->beta = true;
    }

    /**
     * @return bool
     */
    public function isBeta(): bool
    {
        return $this->beta;
    }
}
