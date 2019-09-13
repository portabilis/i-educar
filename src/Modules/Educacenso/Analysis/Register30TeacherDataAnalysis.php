<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Nacionalidade;

class Register30TeacherDataAnalysis implements AnalysisInterface
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

        if (!$data->cpf && $data->nacionalidade != Nacionalidade::ESTRANGEIRA) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que o(a) {$data->nomePessoa} se trata de um(a) docente com nacionalidade {$data->nomeNacionalidade}, portanto é necessário informar o CPF.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: CPF)',
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