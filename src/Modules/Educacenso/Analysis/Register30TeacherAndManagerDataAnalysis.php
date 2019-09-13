<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use Illuminate\Support\Facades\DB;
use Portabilis_Utils_Database;

class Register30TeacherAndManagerDataAnalysis implements AnalysisInterface
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

        if (empty($data->escolaridade)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a escolaridade da pessoa {$data->nomePessoa} foi informada.",
                'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Campo: Escolaridade)',
                'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                'fail' => true
            ];
        }

        if ($data->escolaridade == Escolaridade::ENSINO_MEDIO && empty($data->tipoEnsinoMedioCursado)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que a escolaridade da pessoa {$data->nomePessoa} é ensino médio, portanto é necessário informar o tipo de ensino médio cursado.",
                'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Campo: Tipo de ensino médio cursado)',
                'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                'fail' => true
            ];
        }

        if ($data->escolaridade == Escolaridade::EDUCACAO_SUPERIOR && empty($data->countPosGraduacao)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que a escolaridade da pessoa {$data->nomePessoa} é ensino superior, portanto é necessário informar se o(a) mesmo(a) possui alguma pós-graduação concluída.",
                'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Seção: Curso(s) Superior(es) Concluído(s) > Campo: Pós-Graduações concluídas)',
                'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                'fail' => true
            ];
        } else {
            if ($data->escolaridade == Escolaridade::EDUCACAO_SUPERIOR && $data->posGraduacaoNaoPossui && $data->countPosGraduacao > 1) {
                $this->messages[] = [
                    'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que a pós-graduação da pessoa {$data->nomePessoa} foi preenchida incorretamente.",
                    'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Seção: Curso(s) Superior(es) Concluído(s) > Campo: Pós-Graduações concluídas)',
                    'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                    'fail' => true
                ];
            }
        }

        if (empty($data->countFormacaoContinuada)) {
            $this->messages[] = [
                'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verifique se a formação continuada da pessoa {$data->nomePessoa} foi informada.",
                'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Campo: Outros cursos de formação continuada (Mínimo de 80 horas))',
                'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                'fail' => true
            ];
        } else {
            if ($data->formacaoContinuadaEducacaoNenhum && $data->countFormacaoContinuada > 1) {
                $this->messages[] = [
                    'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. Verificamos que a formação continuada da pessoa {$data->nomePessoa} foi preenchida incorretamente.",
                    'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Campo: Outros cursos de formação continuada (Mínimo de 80 horas))',
                    'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                    'fail' => true
                ];
            }
        }

        if ($data->escolaridade == Escolaridade::EDUCACAO_SUPERIOR) {
            if (empty($data->formacaoCurso) || empty($data->formacaoAnoConclusao) || empty($data->formacaoInstituicao)) {
                $this->messages[] = [
                    'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} não encontrados. Verificamos que a escolaridade da pessoa {$data->nomePessoa} é ensino superior, portanto é necessário informar o nome do curso superior, o ano de conclusão e a instituição de ensino.",
                    'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Seção: Curso(s) Superior(es) Concluído(s))',
                    'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                    'fail' => true
                ];
            }

            $anoAtual = date('Y');

            foreach ($data->formacaoAnoConclusao as $anoConclusao) {
                if ($anoConclusao < 1940) {
                    $this->messages[] = [
                        'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. O ano de conclusão do(s) curso(s) superior(es) concluído(s) pela pessoa {$data->nomePessoa} deve ser posterior ao ano de 1940.",
                        'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Seção: Curso(s) Superior(es) Concluído(s))',
                        'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                        'fail' => true
                    ];
                } elseif ($anoConclusao > $anoAtual) {
                    $this->messages[] = [
                        'text' => "Dados para formular o registro 30 da escola {$data->nomeEscola} possui valor inválido. O ano de conclusão do(s) curso(s) superior(es) concluído(s) pela pessoa {$data->nomePessoa} não deve ser posterior ao ano atual.",
                        'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados adicionais > Seção: Curso(s) Superior(es) Concluído(s))',
                        'linkPath' => "educar_servidor_cad.php?cod_servidor={$data->codigoPessoa}&ref_cod_instituicao={$data->codigoInstituicao}",
                        'fail' => true
                    ];
                }
            }
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
