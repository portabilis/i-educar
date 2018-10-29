<?php

namespace iEducar\Modules\Navigation;

class Breadcrumb
{
    private $htmlBreadcrumb;

    public function makeBreadcrumb($currentPage, $breadcrumbs)
    {
        return $this->makeHtmlBreadcrumb($currentPage, $breadcrumbs);
    }

    private function makeHtmlBreadcrumb($currentPage, $breadcrumbs = [])
    {
        $html = [];
        $html[] = '<div id="localizacao">';
        $html[] = '<a href="/" title="Ir para o Início">';
        $html[] = '<i class="fa fa-home" aria-hidden="true"></i>';
        $html[] = '<span>Início</span>';
        $html[] = '</a>';
        $html[] = '<a class="flechinha"> / </a>';
        foreach ($breadcrumbs as $url => $label) {
            $html[] = '<a href="' . $url . '" title="' . $label . '">' . $label . '</a>';
            $html[] = '<a class="flechinha"> / </a>';
        }
        $html[] = '<span class="pagina_atual">' . $currentPage . '</span>';
        $html[] = '</div>';

        $this->htmlBreadcrumb = implode("\n", $html);

        return $this->htmlBreadcrumb;
    }

    /**
     * @return array
     */
    public function getHtmlBreadcrumb()
    {
        return $this->htmlBreadcrumb;
    }
}