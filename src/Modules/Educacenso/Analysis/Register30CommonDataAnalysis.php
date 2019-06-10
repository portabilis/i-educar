<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyInstitution;
use DateTime;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
use iEducar\Modules\Educacenso\Validator\NameValidator;
use Illuminate\Support\Str;
use Portabilis_Utils_Database;

class Register30CommonDataAnalysis implements AnalysisInterface
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

        if (Str::length($data->nomePessoa) > 100) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Insira no máximo 100 letras no nome da pessoa {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Nome)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $nameValidator = new NameValidator((string)$data->nomePessoa);
        if ($data->nomePessoa && !$nameValidator->isValid()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Não é permitido a repetição de 4 caracteres seguidos no nome da pessoa {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Nome)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (!$data->dataNascimento) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a data de nascimento da pessoa {$data->nomePessoa} foi informada.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Data de nascimento)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $dataReferenciaDatetime = new DateTime(LegacyInstitution::active()->first()->data_educacenso);
        $dataNascimentoDatetime = new DateTime($data->dataNascimento);
        if ($dataNascimentoDatetime > $dataReferenciaDatetime) {
            $this->messages[] = [
                'text' => " Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. A data de nascimento da pessoa {$data->nomePessoa} não pode ser maior que a data de referência do Educacenso ({$dataReferenciaDatetime->format('d/m/Y')}).",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Data de nascimento)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if ($data->filiacao1 && Str::length($data->filiacao1) > 100) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Insira no máximo 100 letras no nome da mãe de {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Pessoa mãe)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $nameValidator = new NameValidator((string)$data->filiacao1);
        if ($data->filiacao1 && !$nameValidator->isValid()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Não é permitido a repetição de 4 caracteres seguidos no nome da mãe de {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Pessoa mãe)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if ($data->filiacao2 && Str::length($data->filiacao2) > 100) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Insira no máximo 100 letras no nome do pai de {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Pessoa pai)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $nameValidator = new NameValidator((string)$data->filiacao2);
        if ($data->filiacao2 && !$nameValidator->isValid()) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Não é permitido a repetição de 4 caracteres seguidos no nome do pai de {$data->nomePessoa}.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Pessoa pai)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (!$data->sexo) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se o sexo da pessoa {$data->nomePessoa} foi informado.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Sexo)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (is_null($data->raca)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a raça da pessoa {$data->nomePessoa} foi informado.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Raça)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (!$data->nacionalidade) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se o nacionalidade da pessoa {$data->nomePessoa} foi informado.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Nacionalidade)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (!$data->paisNacionalidade && $data->nacionalidade == Nacionalidade::ESTRANGEIRA) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que a nacionalidade da pessoa {$data->nomePessoa} é estrangeira, portanto o país de origem deve ser informado.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Nacionalidade)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if ($data->nacionalidade == Nacionalidade::ESTRANGEIRA && $data->paisNacionalidade == 76) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que a nacionalidade da pessoa {$data->nomePessoa} é estrangeira, portanto o país de origem não pode ser Brasil.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Nacionalidade)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        if (!$data->municipioNascimento && $data->nacionalidade == Nacionalidade::BRASILEIRA) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a naturalidade da pessoa {$data->nomePessoa} foi informada.",
                'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: Naturalidade)',
                'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$data->codigoPessoa}",
                'fail' => true
            ];
        }

        $deficiencyValidator = new DeficiencyValidator(Portabilis_Utils_Database::pgArrayToArray($data->arrayDeficiencias));
        if (!$deficiencyValidator->isValid()){
            $path = '(Servidores > Cadastros > Servidores > Editar > Aba: Dados gerais > Campo: Deficiências)';
            $linkPath = "/intranet/educar_servidor_cad.php?cod_servidor={$data->codigoServidor}&ref_cod_instituicao=" . LegacyInstitution::active()->first()->cod_instituicao;
            if ($data->isStudent()) {
                $path = '(Escola > Cadastros > Alunos > Editar > Aba: Dados pessoais > Campo: Deficiências / habilidades especiais)';
                $linkPath = "/module/Cadastro/aluno?id={$data->codigoAluno}";
            }

            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que a pessoa {$data->nomePessoa} possui o campo de deficiências preenchido incorretamente: " . $deficiencyValidator->getMessage(),
                'path' => $path,
                'linkPath' => $linkPath,
                'fail' => true
            ];
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}