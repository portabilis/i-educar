<?php

require_once 'Core/Controller/Page/ListController.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'Docente_Model_LicenciaturaDataMapper';
    protected $_titulo     = 'Listagem de licenciaturas do servidor';
    protected $_processoAp = 635;
    protected $_tableMap = [
        'Licenciatura'     => 'licenciatura',
        'Curso'            => 'curso',
        'Ano de conclusão' => 'anoConclusao',
        'IES'              => 'ies'
    ];

    public function getEntries()
    {
        return $this->getDataMapper()->findAll(
            [],
            ['servidor' => $this->getRequest()->servidor],
            ['anoConclusao' => 'ASC']
        );
    }

    public function setAcao()
    {
        $this->acao = sprintf(
            'go("edit?servidor=%d&instituicao=%d")',
            $this->getRequest()->servidor,
            $this->getRequest()->instituicao
        );

        $this->nome_acao = 'Novo';
    }

    public function Gerar()
    {
        $headers = $this->getTableMap();

        $this->addCabecalhos(array_keys($headers));

        $entries = $this->getEntries();

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome])
            ? $_GET['pagina_' . $this->nome] * $this->limite - $this->limite
            : 0;

        foreach ($entries as $entry) {
            $item = [];
            $data = $entry->toArray();
            $options = [
                'query' => [
                    'id'          => $entry->id,
                    'servidor'    => $entry->servidor,
                    'instituicao' => $this->getRequest()->instituicao
                ]
            ];

            foreach ($headers as $label => $attr) {
                $item[] = CoreExt_View_Helper_UrlHelper::l(
                    $entry->$attr,
                'view',
                    $options
                );
            }

            $this->addLinhas($item);
        }

        $this->addPaginador2('', count($entries), $_GET, $this->nome, $this->limite);

        $this->setAcao();

        $this->acao_voltar = sprintf(
      'go("/intranet/educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d")',
            $this->getRequest()->servidor,
            $this->getRequest()->instituicao
        );

        $this->largura = '100%';
    }
}
