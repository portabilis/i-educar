<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Nacionalidade;

class Register30ManagerDataAnalysis implements AnalysisInterface
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
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que o(a) {$data->nomePessoa} se trata de um(a) gestor com nacionalidade {$data->nomeNacionalidade}, portanto é necessário informar o CPF.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: CPF)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (empty($data->email)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se e-mail do(a) gestor(a) escolar {$data->nomePessoa} foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: E-mail)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$data->codigoEscola}",
                'fail' => true
            ];
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
