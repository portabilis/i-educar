@if(isset($idiarioData['error']))
    <div style="background-color: #fcf8e3; border: 1px solid #faebcc; color: #8a6d3b; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
        {{ $idiarioErrorMessage ?? 'Não foi possível consultar o i-Diário.' }}
    </div>
@else
    <table class="tablelistagem" style="border: 0; padding: 0; border-collapse: collapse; width: 100%">
        <tr>
            <td class="titulo-tabela-listagem" colspan="2">Registros no i-Diário</td>
        </tr>
        <tr>
            <td class="formdktd" style="vertical-align: top; text-align: left; width: 60%">Tipo</td>
            <td class="formdktd" style="vertical-align: top; text-align: left; width: 40%">Quantidade</td>
        </tr>
        @php $idiarioRowClass = 'formlttd'; @endphp
        @foreach($idiarioData as $item)
            <tr>
                <td class="{{ $idiarioRowClass }}">{{ $item['label'] }}</td>
                <td class="{{ $idiarioRowClass }}">{{ number_format($item['count'], 0, ',', '.') }}</td>
            </tr>
            @php $idiarioRowClass = $idiarioRowClass === 'formlttd' ? 'formmdtd' : 'formlttd'; @endphp
        @endforeach
    </table>
@endif
