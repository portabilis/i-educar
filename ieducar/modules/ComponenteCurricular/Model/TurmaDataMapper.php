<?php

use App\Exceptions\SchoolClass\HasDataInDiario;
use App\Models\LegacyDiscipline;
use App\Services\iDiarioService;

class ComponenteCurricular_Model_TurmaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'ComponenteCurricular_Model_Turma';

    protected $_tableName = 'componente_curricular_turma';

    protected $_tableSchema = 'modules';

    /**
     * Os atributos anoEscolar e escola estão presentes apenas para
     * fins de desnormalização.
     *
     * @var array
     */
    protected $_attributeMap = [
        'componenteCurricular' => 'componente_curricular_id',
        'anoEscolar' => 'ano_escolar_id',
        'escola' => 'escola_id',
        'turma' => 'turma_id',
        'cargaHoraria' => 'carga_horaria',
        'docenteVinculado' => 'docente_vinculado',
        'etapasEspecificas' => 'etapas_especificas',
        'etapasUtilizadas' => 'etapas_utilizadas'
    ];

    protected $_primaryKey = [
        'componenteCurricular' => 'componente_curricular_id',
        'turma' => 'turma_id',
    ];

    /**
     * Realiza uma operação de atualização em todas as instâncias persistidas de
     * ComponenteCurricular_Model_Turma. A atualização envolve criar, atualizar
     * e/ou apagar instâncias persistidas.
     *
     * No exemplo de código a seguir, se uma instância de
     * ComponenteCurricular_Model_Turma com uma referência a componenteCurricular
     * "1" existisse, esta teria seus atributos atualizados e persistidos
     * novamente. Se a referência não existisse, uma nova instância de
     * ComponenteCurricular_Model_Turma seria criada e persistida. Caso uma
     * referência a "2" existisse, esta seria apagada por não estar referenciada
     * no array $componentes.
     *
     * <code>
     * <?php
     * $componentes = array(
     *   array('id' => 1, 'cargaHoraria' => 100)
     * );
     * $mapper->bulkUpdate(1, 1, 1, $componentes);
     * </code>
     *
     *
     *
     * @param int                 $anoEscolar     O código do ano escolar/série.
     * @param int                 $escola         O código da escola.
     * @param int                 $turma          O código da turma.
     * @param array               $componentes    (id => integer, cargaHoraria => float|null)
     * @param iDiarioService|null $iDiarioService
     *
     * @throws CoreExt_DataMapper_Exception
     */
    public function bulkUpdate($anoEscolar, $escola, $turma, array $componentes, $iDiarioService = null)
    {
        $update = $insert = $delete = [];

        $componentesTurma = $this->findAll([], ['turma'  => $turma]);

        $objects = [];

        foreach ($componentesTurma as $componenteTurma) {
            $objects[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
        }

        foreach ($componentes as $componente) {
            $id = $componente['id'];

            if (isset($objects[$id])) {
                $insert[$id] = $objects[$id];
                $insert[$id]->cargaHoraria = $componente['cargaHoraria'];
                $insert[$id]->docenteVinculado = $componente['docenteVinculado'];
                $insert[$id]->etapasEspecificas = $componente['etapasEspecificas'];
                $insert[$id]->etapasUtilizadas = $componente['etapasUtilizadas'];
                continue;
            }

            $insert[$id] = new ComponenteCurricular_Model_Turma([
                'componenteCurricular' => $id,
                'anoEscolar' => $anoEscolar,
                'escola' => $escola,
                'turma' => $turma,
                'cargaHoraria' => $componente['cargaHoraria'],
                'docenteVinculado' => $componente['docenteVinculado'],
                'etapasEspecificas' => $componente['etapasEspecificas'],
                'etapasUtilizadas' => $componente['etapasUtilizadas']
            ]);
        }

        $delete = array_diff(array_keys($objects), array_keys($insert));

        $erros = [];
        foreach ($delete as $id) {
            if ($iDiarioService && $iDiarioService->getClassroomsActivityByDiscipline([$turma], $id)) {
                $discipline = LegacyDiscipline::find($id);
                $erros[] = sprintf('Não é possível desvincular "%s" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta turma no iDiário.', $discipline->nome);
                continue;
            }

            $this->delete($objects[$id]);
        }

        if ($erros) {
            throw new HasDataInDiario($erros);
        }

        foreach ($insert as $entry) {
            $this->save($entry);
        }
    }
}
