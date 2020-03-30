<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use Illuminate\Support\Facades\DB;
use Portabilis_Utils_Database;

class Register30TeacherAndStudentDataAnalysis implements AnalysisInterface
{
    /**
     * @var Registro30
     */
    private $data;

    /**
     * @var array
     */
    private $messages = [];

    public function __construct(RegistroEducacenso $data)
    {
        $this->data = $data;
    }

    public function run()
    {
        $data = $this->data;

        if ($data->paisResidencia == PaisResidencia::BRASIL && !$data->localizacaoResidencia) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a zona de residência do(a) aluno(a)/docente {$data->nomePessoa} foi informada.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Zona de residência)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $validator = new DifferentiatedLocationValidator($data->localizacaoDiferenciada, $data->localizacaoResidencia);
        if (!$validator->isValid()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que a zona/localização do(a) aluno(a)/docente {$data->nomePessoa} é urbana, portanto a localização diferenciada de residência não pode ser área de assentamento.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Localização diferenciada de residência)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
