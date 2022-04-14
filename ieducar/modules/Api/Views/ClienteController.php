<?php

class ClienteController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

        return ['sqlParams' => [$escolaId]];
    }

    protected function formatResourceValue($resource)
    {
        $nome = $this->toUtf8($resource['nome'], ['transform' => true]);

        return $nome;
    }

    protected function sqlsForNumericSearch()
    {
        return 'SELECT cliente.cod_cliente as id, initcap(pessoa.nome) as nome
                 FROM pmieducar.cliente
                INNER JOIN pmieducar.cliente_tipo_cliente ON (cliente_tipo_cliente.ref_cod_cliente = cliente.cod_cliente)
                INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = cliente_tipo_cliente.ref_cod_biblioteca)
                INNER JOIN cadastro.pessoa ON (pessoa.idpes = cliente.ref_idpes)
                WHERE (case when $2 = 0 then true else biblioteca.ref_cod_escola = $2 end)
                  AND cliente.cod_cliente ILIKE \'%\'||$1||\'%\'';
    }

    protected function sqlsForStringSearch()
    {
        return 'SELECT cliente.cod_cliente as id, initcap(pessoa.nome) as nome
                 FROM pmieducar.cliente
                INNER JOIN pmieducar.cliente_tipo_cliente ON (cliente_tipo_cliente.ref_cod_cliente = cliente.cod_cliente)
                INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = cliente_tipo_cliente.ref_cod_biblioteca)
                INNER JOIN cadastro.pessoa ON (pessoa.idpes = cliente.ref_idpes)
                WHERE (case when $2 = 0 then true else biblioteca.ref_cod_escola = $2 end)
                  AND pessoa.nome ILIKE \'%\'||$1||\'%\'';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'cliente-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
