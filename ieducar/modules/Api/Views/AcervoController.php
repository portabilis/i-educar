<?php

class AcervoController extends ApiCoreController
{
    protected function searchOptions()
    {
        $biblioteca_id = $this->getRequest()->biblioteca_id
            ? $this->getRequest()->biblioteca_id
            : 0;

        return ['sqlParams' => [$biblioteca_id]];
    }

    protected function formatResourceValue($resource)
    {
        $nome = $resource['id'] . ' - ' . $this->toUtf8(
            $resource['nome'],
            ['transform' => true]
        );

        return $nome;
    }

    protected function sqlsForNumericSearch()
    {
        return 'SELECT acervo.cod_acervo as id, initcap(acervo.titulo) as nome
                  FROM pmieducar.acervo
             LEFT JOIN pmieducar.acervo_acervo_autor ON (acervo.cod_acervo = acervo_acervo_autor.ref_cod_acervo)
            INNER JOIN pmieducar.exemplar ON (exemplar.ref_cod_acervo = acervo.cod_acervo)
            INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = acervo.ref_cod_biblioteca)
                 WHERE (case when $2 = 0 then true else biblioteca.cod_biblioteca = $2 end)
                   AND (acervo.cod_acervo::varchar ILIKE \'%\'||$1||\'%\' OR acervo.titulo ILIKE \'%\'||$1||\'%\')';
    }

    protected function sqlsForStringSearch()
    {
        return 'SELECT acervo.cod_acervo as id, initcap(acervo.titulo) as nome
                  FROM pmieducar.acervo
             LEFT JOIN pmieducar.acervo_acervo_autor ON (acervo.cod_acervo = acervo_acervo_autor.ref_cod_acervo)
            INNER JOIN pmieducar.exemplar ON (exemplar.ref_cod_acervo = acervo.cod_acervo)
            INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = acervo.ref_cod_biblioteca)
                 WHERE (case when $2 = 0 then true else biblioteca.cod_biblioteca = $2 end)
                   AND acervo.titulo ILIKE \'%\'||$1||\'%\'';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'acervo-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
