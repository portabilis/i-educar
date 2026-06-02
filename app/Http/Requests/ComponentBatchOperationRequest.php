<?php

namespace App\Http\Requests;

use App\Models\LegacyGrade;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class ComponentBatchOperationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'year' => $this->input('ano'),
            'institution_id' => $this->input('ref_cod_instituicao'),
            'course_ids' => $this->inputToIntArray('curso'),
            'grade_ids' => $this->inputToIntArray('ref_cod_serie'),
            'school_ids' => $this->inputToIntArray('escola'),
            'discipline_ids' => $this->inputToIntArray('discipline_ids'),
            'remove_records' => $this->has('remove_records'),
            'unlink_class_components' => $this->has('unlink_class_components'),
            'unlink_teacher_disciplines' => $this->has('unlink_teacher_disciplines'),
            'unlink_school_grade_disciplines' => $this->has('unlink_school_grade_disciplines'),
            'unlink_grade_components' => $this->has('unlink_grade_components'),
            'remove_exemptions' => $this->has('remove_exemptions'),
        ]);

        $this->enforceCheckboxHierarchy();
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000'],
            'institution_id' => ['required', 'integer'],
            'course_ids' => ['required', 'array'],
            'course_ids.*' => ['integer'],
            'grade_ids' => ['required', 'array'],
            'grade_ids.*' => ['integer'],
            'school_ids' => ['required', 'array'],
            'school_ids.*' => ['integer'],
            'discipline_ids' => ['required', 'array'],
            'discipline_ids.*' => ['integer'],
            'remove_records' => ['nullable', 'boolean'],
            'unlink_class_components' => ['nullable', 'boolean'],
            'unlink_teacher_disciplines' => ['nullable', 'boolean'],
            'unlink_school_grade_disciplines' => ['nullable', 'boolean'],
            'unlink_grade_components' => ['nullable', 'boolean'],
            'remove_exemptions' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasOperation = $this->input('remove_records')
                || $this->input('unlink_class_components')
                || $this->input('unlink_teacher_disciplines')
                || $this->input('unlink_school_grade_disciplines')
                || $this->input('unlink_grade_components')
                || $this->input('remove_exemptions');

            if (!$hasOperation) {
                $validator->errors()->add('operations', 'Selecione ao menos uma operação.');
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateSelectHierarchy($validator);
        });
    }

    private function validateSelectHierarchy(Validator $validator): void
    {
        $schoolIds = $this->input('school_ids', []);
        $courseIds = $this->input('course_ids', []);
        $gradeIds = $this->input('grade_ids', []);
        $disciplineIds = $this->input('discipline_ids', []);

        $validCourseIds = LegacySchoolCourse::query()
            ->where('ativo', 1)
            ->whereIn('ref_cod_escola', $schoolIds)
            ->whereIn('ref_cod_curso', $courseIds)
            ->distinct()
            ->pluck('ref_cod_curso')
            ->toArray();

        if (array_diff($courseIds, $validCourseIds)) {
            $validator->errors()->add('course_ids', 'Cursos selecionados não pertencem às escolas informadas.');

            return;
        }

        $validGradeIds = LegacyGrade::query()
            ->active()
            ->whereIn('serie.ref_cod_curso', $courseIds)
            ->whereIn('serie.cod_serie', $gradeIds)
            ->join('pmieducar.escola_serie as es', 'es.ref_cod_serie', 'serie.cod_serie')
            ->where('es.ativo', 1)
            ->whereIn('es.ref_cod_escola', $schoolIds)
            ->distinct()
            ->pluck('serie.cod_serie')
            ->toArray();

        if (array_diff($gradeIds, $validGradeIds)) {
            $validator->errors()->add('grade_ids', 'Séries selecionadas não pertencem aos cursos/escolas informados.');

            return;
        }

        $validDisciplineIds = LegacySchoolGradeDiscipline::query()
            ->where('ativo', 1)
            ->whereIn('ref_ref_cod_serie', $gradeIds)
            ->whereIn('ref_ref_cod_escola', $schoolIds)
            ->whereIn('ref_cod_disciplina', $disciplineIds)
            ->distinct()
            ->pluck('ref_cod_disciplina')
            ->toArray();

        if (array_diff($disciplineIds, $validDisciplineIds)) {
            $validator->errors()->add('discipline_ids', 'Componentes selecionados não pertencem às séries/escolas informadas.');
        }
    }

    private function enforceCheckboxHierarchy(): void
    {
        if ($this->input('unlink_grade_components')) {
            $this->merge(['unlink_school_grade_disciplines' => true]);
        }
        if ($this->input('unlink_school_grade_disciplines')) {
            $this->merge(['unlink_class_components' => true, 'remove_exemptions' => true]);
        }
        if ($this->input('unlink_class_components')) {
            $this->merge(['unlink_teacher_disciplines' => true, 'remove_records' => true]);
        }
        if ($this->input('unlink_teacher_disciplines')) {
            $this->merge(['remove_records' => true]);
        }

        if (!$this->input('remove_records')) {
            $this->merge([
                'unlink_class_components' => false,
                'unlink_teacher_disciplines' => false,
            ]);
        }
        if (!$this->input('unlink_class_components') || !$this->input('remove_exemptions')) {
            $this->merge(['unlink_school_grade_disciplines' => false]);
        }
        if (!$this->input('unlink_school_grade_disciplines')) {
            $this->merge(['unlink_grade_components' => false]);
        }
    }

    private function inputToIntArray(string $field): array
    {
        return array_map('intval', array_filter(Arr::flatten((array) $this->input($field, []))));
    }

    public function attributes(): array
    {
        return [
            'year' => 'ano',
            'institution_id' => 'instituição',
            'school_ids' => 'escolas',
            'course_ids' => 'cursos',
            'grade_ids' => 'séries',
            'discipline_ids' => 'componentes curriculares',
        ];
    }
}
