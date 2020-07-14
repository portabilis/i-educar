<form id="formcadastro" method="get">
    <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
        <tbody>
        <tr>
            <td class="formdktd" colspan="2" height="24"><b>Filtros de busca</b></td>
        </tr>
        <td id="tr_nm_data" valign="top" align="center" colspan="2">
            <table cellspacing="0" id="dates-table" class="tabela-adicao" cellpadding="2"
                   style="margin:10px 0px 10px 0px;">
                <tbody>
                <tr align="center" style="font-weight:bold" id="tr_gestores_cab">
                    <td class="formmdtd" align="center"><span class="form">Ano</span>
                    </td>
                    <td class="formmdtd" align="center"><span class="form">Instituição</span>
                    </td>
                    <td class="formmdtd" align="center"><span class="form">Escola</span></td>
                </tr>
                <tr class="formmdtd dd">
                    <td class="formmdtd dd" valign="top">
                        <input type="text" class="geral" name="ano" id="ano_busca" maxlength="4" value="{{old('ano', Request::get('ano', date('Y')))}}" size="4">
                    <td class="formmdtd dd" valign="top">
                        <select class="geral" name="ref_cod_instituicao" id="instituicao_busca" style="width: 308px;">
                            <option value="">Selecione uma instituição</option>
                            @foreach(\App\Models\LegacyInstitution::active()->get() as $institution)
                                <option value="{{$institution->cod_instituicao}}" @if(old('ref_cod_instituicao', Request::get('ref_cod_instituicao')) == $institution->cod_instituicao) selected @endif>
                                    {{$institution->name}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="formmdtd dd" valign="top">
                        <select class="geral" name="ref_cod_escola" id="escola_busca" style="width: 308px;">
                            <option value="">Selecione uma escola</option>
                            @foreach(App_Model_IedFinder::getEscolasByUser(app(\App\Models\LegacyInstitution::class)->getKey()) as $id => $name)
                                <option value="{{$id}}" @if(old('ref_cod_escola', Request::get('ref_cod_escola')) == $id) selected @endif>{{$name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        <button class="btn-green" type="submit">Buscar</button>
    </div>

</form>

<table class="table-default">
    <thead>
    <tr>
        <th>Escolas</th>
        <th>Etapa</th>
        <th>Datas</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $releasePeriod)
        <tr>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">{{ implode(', ', $releasePeriod->schools->pluck('name')->toArray())  }}</a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">{{ $releasePeriod->stage }}</a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">{!! implode('<br>', $releasePeriod->getDatesArray()) !!}</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3">Não foi encontrado nenhum período de lançamento</td>
        </tr>
    @endforelse

    </tbody>
</table>

<div class="separator"></div>

<div style="text-align: center">
    {{ $data->appends(request()->except('page'))->links() }}
</div>

@prepend('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endprepend
