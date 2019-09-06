<?php

use App\Menu;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

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
