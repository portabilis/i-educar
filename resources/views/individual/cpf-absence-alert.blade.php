<p class="{{ $situacao['type'] === 'justified_absence' ? 'notice' : 'error' }} simple-block cpf-ausencia">
    @if ($situacao['type'] === 'justified_absence')
        {{ $sujeito }} {{ $nome }} não possui CPF registrado. Motivo: {{ $situacao['reason'] }}@if ($situacao['user']), registrado por {{ $situacao['user'] }} em {{ $situacao['date'] }}@endif.
        Se você possui essa informação, aproveite para atualizá-la.
    @else
        {{ $sujeito }} {{ $nome }} não possui CPF registrado e não há justificativa de ausência.
        Se você possui essa informação, {{ $aluno ? 'atualize antes de prosseguir.' : 'aproveite para atualizá-la.' }}
    @endif
    @if ($podeAtualizar)
        <input type="button" class="btn_small atualizar-cpf" value="Atualizar CPF"
            data-pessoa-id="{{ $pessoa->getKey() }}"
            data-nome="{{ $nome }}"
            data-data-nascimento="{{ $dataNascimento }}"
            data-motivo="{{ $pessoa->document_absence_reason_id }}"
            data-observacao="{{ $pessoa->document_absence_observation }}">
    @endif
</p>
