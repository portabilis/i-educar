<?php

use App\Models\LegacyKnowledgeArea;

class IndexController extends Core_Controller_Page_ListController
{
    public $limite;

    protected $_titulo = 'Listagem de áreas de conhecimento';

    protected $_processoAp = 945;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Seção' => 'secao',
        'Agrupa descritores' => 'agrupar_descritores',
    ];

    public function Gerar()
    {
        $this->addCabecalhos(array_keys($this->getTableMap()));

        $this->limite = 20;

        $paginador = LegacyKnowledgeArea::query()
            ->orderBy('nome')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $paginador->getCollection();
        $total = $paginador->total();

        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $options = ['query' => ['id' => $registro->id]];

                $this->addLinhas([
                    CoreExt_View_Helper_UrlHelper::l($registro->nome, 'view', $options),
                    CoreExt_View_Helper_UrlHelper::l($registro->secao, 'view', $options),
                    CoreExt_View_Helper_UrlHelper::l($registro->agrupar_descritores ? 'Sim' : 'Não', 'view', $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: '',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $this->setAcao();

        $this->largura = '100%';
    }

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de áreas de conhecimento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}
