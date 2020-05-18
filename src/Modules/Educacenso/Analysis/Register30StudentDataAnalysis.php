<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use iEducar\Modules\Educacenso\Validator\InepExamValidator;
use Illuminate\Support\Facades\DB;
use Portabilis_Utils_Database;

class Register30StudentDataAnalysis implements AnalysisInterface
{
    /**
     * @var Registro30
     */
    private $data;

    private $year;

    /**
     * @var array
     */
    private $messages = [];

    public function __construct(RegistroEducacenso $data)
    {
        $this->data = $data;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function run()
    {
        $data = $this->data;

        $arrayDeficiencias = array_filter(Portabilis_Utils_Database::pgArrayToArray($data->arrayDeficiencias));
        $arrayRecursos = array_filter(Portabilis_Utils_Database::pgArrayToArray($data->recursosProvaInep));

        if (!$arrayDeficiencias && ($data->dadosAluno->tipoAtendimentoTurma == TipoAtendimentoTurma::AEE || $data->dadosAluno->modalidadeCurso == ModalidadeCurso::EDUCACAO_ESPECIAL)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que o curso ou a turma do(a) aluno(a) {$data->nomePessoa} é de AEE, portanto é necessário informar qual a sua deficiência.",
                'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados pessoais > Campo: Deficiências / habilidades especiais)',
                'linkPath' => "/module/Cadastro/aluno?id={$data->codigoAluno}",
                'fail' => true
            ];
        }

        if (empty($arrayRecursos) && $arrayDeficiencias) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que o(a) aluno(a)  {$data->nomePessoa} possui deficiência, portanto é necessário informar qual o recurso para a realização de provas o(a) mesmo(a) necessita ou já recebe.",
                'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados educacenso > Campo: Recursos necessários para realização de provas)',
                'linkPath' => "/module/Cadastro/aluno?id={$data->codigoAluno}",
                'fail' => true
            ];
        }

        $inepExamValidator = new InepExamValidator($arrayRecursos, $arrayDeficiencias);
        if (!$inepExamValidator->isValid()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que o(s) recurso(s) necessário(s) para realização de provas foi preenchido incorretamente para o(a) aluno(a) {$data->nomePessoa}.",
                'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados pessoais > Campo: Recursos necessários para realização de provas)',
                'linkPath' => "/module/Cadastro/aluno?id={$data->codigoAluno}",
                'fail' => true
            ];
        }

        $birthCertificateValidator = new BirthCertificateValidator($data->certidaoNascimento, $data->dataNascimento);
        if ($data->certidaoNascimento && !$birthCertificateValidator->validateCertificateDigits()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verifique se a certidão de nascimento (nova) do(a) aluno(a) {$data->nomePessoa} foi preenchida corretamente.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Tipo certidão civil (novo formato))',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if ($data->certidaoNascimento && !$birthCertificateValidator->validateCertificateLength()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verifique se a certidão de nascimento (nova) do(a) aluno(a) {$data->nomePessoa} possui 32 dígitos.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Tipo certidão civil (novo formato))',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if ($data->certidaoNascimento && !$birthCertificateValidator->validateCertificateYear()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que o ano de registro da certidão de nascimento (nova) do(a) aluno(a) {$data->nomePessoa}, é anterior ao ano do nascimento ou posterior ao ano corrente (Posições de 11 a 14 do número da certidão).",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Tipo certidão civil (novo formato))',
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