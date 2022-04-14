<?php

class Core_Controller_Page_ViewController extends clsDetalhe implements Core_View_Tabulable
{
    /**
     * Mapeia um nome descritivo a um atributo de CoreExt_Entity retornado pela
     * instância CoreExt_DataMapper retornada por getDataMapper().
     *
     * Para uma instância de CoreExt_Entity que tenha os seguintes atributos:
     * <code>
     * <?php
     * $_data = array(
     *   'nome' => NULL
     *   'idade' => NULL,
     *   'data_validacao' => NULL
     * );
     * </code>
     *
     * O mapeamento poderia ser feito da seguinte forma:
     * <code>
     * <?php
     * $_tableMap = array(
     *   'Nome' => 'nome',
     *   'Idade (anos)' => 'idade'
     * );
     * </code>
     *
     * Se um atributo não for mapeado, ele não será exibido por padrão durante
     * a geração de HTML na execução do método Gerar().
     *
     * @var array
     */
    protected $_tableMap = [];

    /**
     * Construtor.
     */
    public function __construct()
    {
        $this->titulo = $this->getBaseTitulo();
        $this->largura = '100%';
    }

    /**
     * Getter.
     *
     * @see Core_View_Tabulable#getTableMap()
     */
    public function getTableMap()
    {
        return $this->_tableMap;
    }

    /**
     * Configura a URL padrão para a ação de Edição de um registro.
     *
     * Por padrão, cria uma URL "edit/id", onde id é o valor do atributo "id"
     * de uma instância CoreExt_Entity.
     *
     * @param CoreExt_Entity $entry A instância atual recuperada
     *                              ViewController::Gerar().
     */
    public function setUrlEditar(CoreExt_Entity $entry)
    {
        if ($this->_hasPermissaoCadastra()) {
            $this->url_editar = CoreExt_View_Helper_UrlHelper::url(
                'edit',
                ['query' => ['id' => $entry->id]]
            );
        }
    }

    /**
     * Configura a URL padrão para a ação Cancelar da tela de Edição de um
     * registro.
     *
     * Por padrão, cria uma URL "index".
     *
     * @param CoreExt_Entity $entry A instância atual recuperada
     *                              ViewController::Gerar().
     */
    public function setUrlCancelar(CoreExt_Entity $entry)
    {
        $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url('index');
    }

    /**
     * Getter.
     *
     * @return clsPermissoes
     */
    public function getClsPermissoes()
    {
        return new clsPermissoes();
    }

    /**
     * Verifica se o usuário possui privilégios de cadastro para o processo.
     *
     * @return bool|void Redireciona caso a opção 'nivel_acesso_insuficiente' seja
     *                   diferente de NULL.
     *
     * @throws Core_Controller_Page_Exception
     */
    protected function _hasPermissaoCadastra()
    {
        return $this->getClsPermissoes()->permissao_cadastra(
            $this->getBaseProcessoAp(),
            $this->getPessoaLogada(),
            7
        );
    }

    protected function getPessoaLogada()
    {
        return \Illuminate\Support\Facades\Auth::id();
    }

    public function Gerar()
    {
        $headers = $this->getTableMap();

        $this->titulo = $this->getBaseTitulo();
        $this->largura = '100%';

        try {
            $entry = $this->getEntry();
        } catch (Exception $e) {
            $this->mensagem = $e;

            return false;
        }

        foreach ($headers as $label => $attr) {
            $value = $entry->$attr;
            if (!is_null($value)) {
                $this->addDetalhe([$label, $value]);
            }
        }

        $this->setUrlEditar($entry);
        $this->setUrlCancelar($entry);
    }

    public function getEntry()
    {
        return $this->getDataMapper()->find($this->getRequest()->id);
    }
}
