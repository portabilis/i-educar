<?php

use App\Models\LegacyRoundingTable;

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'TabelaArredondamento_Model_TabelaDataMapper';

    protected $_titulo = 'Listagem de tabelas de arredondamento de nota';

    protected $_processoAp = 949;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Sistema de nota' => 'tipo_nota',
    ];

    public function Gerar()
    {
        parent::Gerar();

        $this->campoTexto(
            nome: 'nome',
            campo: 'Nome',
            valor: request('nome'),
        );

        $tipoNotas = collect(RegraAvaliacao_Model_Nota_TipoValor::getInstance()
            ->getBasicDescriptiveValues())
            ->prepend('Todos os tipos', '');

        $this->campoLista(
            nome: 'tipo_nota',
            campo: 'Sistema de nota',
            valor: $tipoNotas,
            default: request('tipo_nota')
        );
    }

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de tabelas de arredondamento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function getEntries()
    {
        return LegacyRoundingTable::query()
            ->when(request('nome'), fn ($q, $nome) => $q->whereRaw('unaccent(nome) ~* unaccent(?)', $nome))
            ->when(request('tipo_nota'), fn ($q, $tipoNota) => $q->where('tipo_nota', $tipoNota))
            ->orderBy('nome')
            ->get()
            ->map(function ($roudindTable) {
                $tipoNotas = RegraAvaliacao_Model_Nota_TipoValor::getInstance()->getBasicDescriptiveValues();
                $roudindTable->tipo_nota = $tipoNotas[$roudindTable->tipo_nota];

                return $roudindTable;
            });
    }
}
