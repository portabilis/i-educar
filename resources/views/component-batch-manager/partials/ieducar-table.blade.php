<table class="tablelistagem" style="border: 0; padding: 0; border-collapse: collapse; width: 100%">
    <tr>
        <td class="titulo-tabela-listagem" colspan="2">{{ $title ?? 'Registros no i-Educar' }}</td>
    </tr>
    @if($totalIeducar === 0)
        <tr>
            <td class="formlttd" colspan="2" style="padding: 10px;">
                {{ $emptyMessage ?? 'Nenhum registro encontrado para os filtros selecionados.' }}
            </td>
        </tr>
    @else
        <tr>
            <td class="formdktd" style="vertical-align: top; text-align: left; width: 60%">Tipo</td>
            <td class="formdktd" style="vertical-align: top; text-align: left; width: 40%">Quantidade</td>
        </tr>
        @php $rowClass = 'formlttd'; @endphp
        @if($data['remove_records'] ?? false)
            <tr><td class="{{ $rowClass }}">Médias</td><td class="{{ $rowClass }}">{{ number_format($counts['nota_media'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
            <tr><td class="{{ $rowClass }}">Notas</td><td class="{{ $rowClass }}">{{ number_format($counts['nota'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
            <tr><td class="{{ $rowClass }}">Faltas</td><td class="{{ $rowClass }}">{{ number_format($counts['falta'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
            <tr><td class="{{ $rowClass }}">Pareceres</td><td class="{{ $rowClass }}">{{ number_format($counts['parecer'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endif
        @if($data['remove_exemptions'] ?? false)
            <tr><td class="{{ $rowClass }}">Dispensas</td><td class="{{ $rowClass }}">{{ number_format($counts['dispensa'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endif
        @if($data['unlink_class_components'] ?? false)
            <tr><td class="{{ $rowClass }}">Componentes da turma</td><td class="{{ $rowClass }}">{{ number_format($counts['componente_turma'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endif
        @if($data['unlink_teacher_disciplines'] ?? false)
            <tr><td class="{{ $rowClass }}">Disciplinas do vínculo professor/turma</td><td class="{{ $rowClass }}">{{ number_format($counts['professor_disciplina'] ?? 0, 0, ',', '.') }}</td></tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endif
        @if($data['unlink_school_grade_disciplines'] ?? false)
            <tr>
                <td class="{{ $rowClass }}">Componentes da série da escola</td>
                <td class="{{ $rowClass }}">
                    {{ number_format($counts['escola_serie_disciplina'] ?? 0, 0, ',', '.') }}
                    @if(($counts['escola_serie_disciplina_skipped'] ?? 0) > 0)
                        <span style="color: #a94442; font-weight: bold; font-size: 0.9em;">
                            ({{ $counts['escola_serie_disciplina_skipped'] }} protegido(s))
                        </span>
                    @endif
                </td>
            </tr>
            @php $rowClass = $rowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endif
        @if($data['unlink_grade_components'] ?? false)
            <tr>
                <td class="{{ $rowClass }}">Componentes da série</td>
                <td class="{{ $rowClass }}">
                    {{ number_format($counts['componente_ano_escolar'] ?? 0, 0, ',', '.') }}
                    @if(($counts['componente_ano_escolar_skipped'] ?? 0) > 0)
                        <span style="color: #a94442; font-weight: bold; font-size: 0.9em;">
                            ({{ $counts['componente_ano_escolar_skipped'] }} protegido(s))
                        </span>
                    @endif
                </td>
            </tr>
        @endif
    @endif
</table>
