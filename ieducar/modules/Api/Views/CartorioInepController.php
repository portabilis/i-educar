<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

class CartorioInepController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        $siglaUfCartorio = $this->getRequest()->sigla_uf_cartorio;

        return [
            'sqlParams' => [$siglaUfCartorio],
            'selectFields' => ['id_cartorio']
        ];
    }

    protected function formatResourceValue($resource)
    {
        return $resource['id_cartorio'] . ' - ' . $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function sqlsForNumericSearch()
    {
        return 'SELECT id,
                       id_cartorio,
                       descricao AS name
                  FROM cadastro.codigo_cartorio_inep
                 WHERE id_cartorio::varchar LIKE $1||\'%\'
                   AND ref_sigla_uf = $2
                 ORDER BY id_cartorio
                 LIMIT 15';
    }

    protected function sqlsForStringSearch()
    {
        return 'SELECT id,
                       id_cartorio,
                       descricao AS name
                  FROM cadastro.codigo_cartorio_inep
                 WHERE unaccent(descricao) ILIKE \'%\'|| unaccent($1) ||\'%\'
                   AND ref_sigla_uf = $2
                 ORDER BY name
                 LIMIT 15';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'cartorioinep-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
