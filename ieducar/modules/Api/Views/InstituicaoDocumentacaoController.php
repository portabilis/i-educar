<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class InstituicaoDocumentacaoController extends ApiCoreController
{
    protected function insertDocuments()
    {
        $var1 = $this->getRequest()->instituicao_id;
        $var2 = $this->getRequest()->titulo_documento;
        $var3 = $this->getRequest()->url_documento;
        $var4 = $this->getRequest()->ref_usuario_cad;
        $var5 = $this->getRequest()->ref_cod_escola;

        $sql = "INSERT INTO pmieducar.instituicao_documentacao (instituicao_id, titulo_documento, url_documento, ref_usuario_cad, ref_cod_escola) VALUES ($var1, '$var2', '$var3', $var4, $var5)";
        $this->fetchPreparedQuery($sql);

        $sql = "SELECT MAX(id) FROM pmieducar.instituicao_documentacao WHERE instituicao_id = $var1";
        $novoId = $this->fetchPreparedQuery($sql);

        return ['id' => $novoId[0][0]];
    }

    protected function getDocuments()
    {
        $var1 = $this->getRequest()->instituicao_id;
        $sql = "SELECT * FROM pmieducar.instituicao_documentacao WHERE instituicao_id = $var1 ORDER BY id DESC";
        $instituicao = $this->fetchPreparedQuery($sql);
        $attrs = ['id', 'titulo_documento', 'url_documento', 'ref_usuario_cad', 'ref_cod_escola'];
        $instituicao = Portabilis_Array_Utils::filterSet($instituicao, $attrs);

        return ['documentos' => $instituicao];
    }

    protected function deleteDocuments()
    {
        $var1 = $this->getRequest()->id;
        $sql = "DELETE FROM pmieducar.instituicao_documentacao WHERE id = $var1";
        $instituicao = $this->fetchPreparedQuery($sql);

        return $instituicao;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'insertDocuments')) {
            $this->appendResponse($this->insertDocuments());
        } elseif ($this->isRequestFor('get', 'getDocuments')) {
            $this->appendResponse($this->getDocuments());
        } elseif ($this->isRequestFor('get', 'deleteDocuments')) {
            $this->appendResponse($this->deleteDocuments());
        }
    }
}
