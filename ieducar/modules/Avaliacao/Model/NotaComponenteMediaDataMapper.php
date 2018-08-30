<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteMedia.php';

class Avaliacao_Model_NotaComponenteMediaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_NotaComponenteMedia';
    protected $_tableName = 'nota_componente_curricular_media';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'notaAluno' => 'nota_aluno_id',
        'componenteCurricular' => 'componente_curricular_id',
        'mediaArredondada' => 'media_arredondada'
    ];

    protected $_primaryKey = [
        'notaAluno' => 'nota_aluno_id',
        'componenteCurricular' => 'componente_curricular_id'
    ];

    public function updateSituation($notaAlunoId, $situacao)
    {
        $entities = $this->findAll([], ['nota_aluno_id' => $notaAlunoId]);

        if (empty($entities)) {
            return true;
        }

        foreach ($entities as $entity) {
            $entity->situacao = $situacao;
            $this->save($entity);
        }

        return true;
    }
}
