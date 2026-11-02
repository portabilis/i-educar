@inject('trilha', App\Services\IndividualCpfTimelineService::class)
@php
    $ultima = $historico->first();
    $classeLinha = 'formmdtd';
@endphp
<span>
    {{ $trilha->summary($ultima->payload) }}, por
    <strong>{{ $trilha->userName($ultima->payload) }}</strong>
    em <strong>{{ $ultima->created_at->format('d/m/Y \à\s H:i') }}</strong>.
</span>
<a class="decorated" id="link_cpf_timeline" style="cursor: pointer; margin-left: 10px;">Ver histórico</a>

<div id="cpf_timeline_historico" style="display: none;">
    <table class="tablelistagem" border="0" style="width: 100%; padding: 0; border-spacing: 1px">
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
                    <td class="{{ $classeLinha }}">{{ $trilha->userName($entrada->payload) }}</td>
                    <td class="{{ $classeLinha }}">
                        {{ $trilha->summary($entrada->payload) }}
                        @if (!empty($entrada->payload['after']['Observações']))
                            <br><em>{{ $entrada->payload['after']['Observações'] }}</em>
                        @endif
                    </td>
                </tr>
                @php $classeLinha = $classeLinha === 'formmdtd' ? 'formlttd' : 'formmdtd'; @endphp
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $j('#cpf_timeline_historico').dialog({
        autoOpen: false,
        closeOnEscape: true,
        draggable: false,
        width: 720,
        maxHeight: 500,
        modal: true,
        resizable: false,
        open: function () {
            var $modal = $j(this).closest('.ui-dialog');

            $modal.find('.ui-dialog-titlebar').hide();
            $modal.add($modal.find('.ui-widget-content')).css('padding', 0);
        },
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
