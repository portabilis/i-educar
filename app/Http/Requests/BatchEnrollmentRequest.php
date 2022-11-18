<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchEnrollmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date' => [
                'required',
                'date_format:d/m/Y',
            ],
            'registrations' => [
                'required',
            ]
        ];

        if ($this->schoolClass->denyEnrollmentsWhenNoVacancy()) {
            $rules['registrations'][] = 'max:' . $this->schoolClass->vacancies;
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        $count = count($this->registrations ?? []);
        $vacancies = $this->schoolClass->vacancies;

        $registrationMax = [
            '{0} Não há vaga(s) na turma, você tentou enturmar ' . $count . ' aluno(s).',
            '{1} Há somente 1 vaga na turma, você tentou enturmar ' . $count . ' aluno(s).',
            '[2,*] Há :max vagas na turma, você tentou  ' . $count . ' aluno(s).',
        ];

        return [
            'date.required' => 'A data de enturmação é obrigatória.',
            'date.date_format' => 'A data de enturmação deve ser uma data válida.',
            'registrations.required' => 'Ao menos uma matrícula deve ser selecionada.',
            'registrations.max' => trans_choice(implode('|', $registrationMax), $vacancies),
        ];
    }
}
