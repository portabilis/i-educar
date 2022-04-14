<?php

use App\Menu;

class MenuController extends ApiCoreController
{
    protected function search()
    {
        if ($this->canSearch()) {
            $query = $this->getRequest()->query;

            $resources = Menu::findByUser($this->user(), $query)
                ->map(function (Menu $menu) {
                    return [
                        'link' => $menu->link,
                        'label' => $menu->title,
                    ];
                });
        }

        if (empty($resources)) {
            $resources = [
                [
                    'link' => '',
                    'label' => 'Sem resultados.',
                ]
            ];
        }

        return ['menus' => $resources];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'menu-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
