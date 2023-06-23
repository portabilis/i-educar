<tr class="header-sequence-schoolclass">
    <td colspan="2"><b>Ano:</b> {{ $schoolclass->ano }}</td>
</tr>
<tr class="header-sequence-schoolclass">
    <td colspan="2"><b>Escola:</b> {{ $schoolclass->school->name }}</td>
</tr>
<tr class="header-sequence-schoolclass">
    <td colspan="2"><b>Curso:</b> {{ $schoolclass->course->name }}</td>
</tr>
<tr class="header-sequence-schoolclass">
    <td colspan="2"><b>Série:</b> {{ $schoolclass->grade->name }}</td>
</tr>
<tr class="header-sequence-schoolclass">
    <td colspan="2"><b>Turma:</b> {{ $schoolclass->name }}</td>
</tr>

<style>
    tr.header-sequence-schoolclass td {
        padding: 8px;
        font-size: 14px;
    }
</style>
