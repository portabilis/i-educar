@php
    $ultima = $historico->first();
    $descricao = static function (array $payload) {
        $depois = $payload['after'] ?? [];

        if (!empty($depois['CPF'])) {
            return 'Informou o CPF ' . $depois['CPF'];
        }

        if (!empty($depois['Motivo da ausência'])) {
            return 'Marcou "Não possui" com o motivo ' . $depois['Motivo da ausência'];
        }

        return 'Removeu o CPF';
    };
    $classeLinha = 'formmdtd';
@endphp
<span>
    {{ $descricao($ultima->payload) }}, por
    <strong>{{ $ultima->payload['user']['name'] ?? 'Usuário não identificado' }}</strong>
    em <strong>{{ $ultima->created_at->format('d/m/Y \à\s H:i') }}</strong>.
</span>
<a class="decorated" id="link_cpf_timeline" style="cursor: pointer; margin-left: 10px;">Ver histórico</a>

<div id="cpf_timeline_historico" style="display: none;"><table class="tablelistagem" cellspacing="1" cellpadding="4" border="0" style="width: 100%; padding: 0">
        <tbody>
            <tr>
                <td class="titulo-tabela-listagem" colspan="3">Alterações do CPF</td>
            </tr>
            <tr>
                <th scope="col" class="formdktd" style="font-weight:bold;">Data e hora</th>
                <th scope="col" class="formdktd" style="font-weight:bold;">Usuário</th>
                <th scope="col" class="formdktd" style="font-weight:bold;">Alteração</th>
            </tr>
            @foreach ($historico as $entrada)
                <tr>
                    <td class="{{ $classeLinha }}">{{ $entrada->created_at->format('d/m/Y H:i') }}</td>
                    <td class="{{ $classeLinha }}">{{ $entrada->payload['user']['name'] ?? 'Não identificado' }}</td>
                    <td class="{{ $classeLinha }}">
                        {{ $descricao($entrada->payload) }}
                        @if (!empty($entrada->payload['after']['Observações']))
                            <br><em>{{ $entrada->payload['after']['Observações'] }}</em>
                        @endif
                    </td>
                </tr>
                @php $classeLinha = $classeLinha === 'formmdtd' ? 'formlttd' : 'formmdtd'; @endphp
            @endforeach
        </tbody>
    </table></div>

<script>
    $j('#cpf_timeline_historico').dialog({
        autoOpen: false,
        closeOnEscape: true,
        draggable: false,
        width: 720,
        maxHeight: 500,
        modal: true,
        resizable: false,
        buttons: {
            'Fechar': function () {
                $j(this).dialog('close');
            }
        }
    });

    $j('#link_cpf_timeline').on('click', function () {
        $j('#cpf_timeline_historico').dialog('open');
    });
</script>
