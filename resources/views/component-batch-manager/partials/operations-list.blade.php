<ul style="margin: 5px 0; padding-left: 20px;">
    @if($params['remove_records'] ?? false)
        <li>Remover lançamentos</li>
    @endif
    @if($params['remove_exemptions'] ?? false)
        <li>Remover dispensas</li>
    @endif
    @if($params['unlink_class_components'] ?? false)
        <li>Remover componentes da turma</li>
    @endif
    @if($params['unlink_teacher_disciplines'] ?? false)
        <li>Remover vínculos professor/turma e professor/disciplina</li>
    @endif
    @if($params['unlink_school_grade_disciplines'] ?? false)
        <li>Remover componentes da série da escola</li>
    @endif
    @if($params['unlink_grade_components'] ?? false)
        <li>Remover componentes da série</li>
    @endif
</ul>
