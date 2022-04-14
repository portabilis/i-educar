<form id="formcadastro" method="get">
    <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
        <tbody>
        <tr>
            <td class="formdktd" colspan="2" height="24"><b>Filtros de busca</b></td>
        </tr>
        <td id="tr_nm_data" valign="top" align="center" colspan="2">
            <table cellspacing="0" id="dates-table" class="tabela-adicao">
                <tbody>
                <tr align="center" style="font-weight:bold" id="tr_gestores_cab">
                    <td class="formmdtd" align="center"><span class="form">Instituição</span></td>
                    <td class="formmdtd" align="center"><span class="form">Escola</span></td>
                    <td class="formmdtd" align="center"><span class="form">Ano</span></td>
                    <td class="formmdtd" align="center"><span class="form">Etapa</span></td>
                    <td class="formmdtd">&nbsp;</td>
                </tr>
                <tr class="formmdtd dd">
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
                    <td class="formmdtd dd" valign="top">
                        <input type="text" class="geral" name="ano" id="ano_busca" maxlength="4" value="{{old('ano', Request::get('ano', date('Y')))}}" size="4">
                    </td>
                    <td class="formmdtd dd" valign="top">
                        <input type="text" class="geral" name="stage" id="stage_busca"  value="{{old('stage', Request::get('stage'))}}" size="2">
                    </td>
                    <td class="formmdtd dd" valign="top">
                        <button class="btn-green" type="submit">Filtrar</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </tbody>
    </table>
</form>
<div>
    <table width="100%">
        <tr>
            <td>
                @if($canRemove)<span id="text-count-delete">Nenhum período de liberação selecionado</span>@endif
            </td>
            @if($canRemove)
                <td style="text-align: right">
                    <button class="btn" type="submit" onclick="confirmDeleteMultiple()">Excluir selecionados</button>
                </td>
            @endif
        </tr>
    </table>
</div>
<table class="table-default">

    <tr class="table-header">
        @if($canRemove)<th><input type="checkbox" id="check-delete-all"></th>@endif
        <th>Escolas</th>
        <th>Ano</th>
        <th>Etapa</th>
        <th>Data de início</th>
        <th>Data fim</th>
    </tr>
    <tbody>
    @forelse($data as $releasePeriod)
        <tr>
            @if($canRemove)<td><input type="checkbox" name="check-delete" data-id="{{ $releasePeriod->id }}"></td>@endif
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">
                    {{ $releasePeriod->schools()->limit(3)->get()->pluck('name')->implode(', ')  }}@if($releasePeriod->schools->count() > 3)...@endif
                </a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">{{ $releasePeriod->year }}</a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">{{ $releasePeriod->stage }}º {{ $releasePeriod->stageType->nm_tipo }}</a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">
                    @foreach($releasePeriod->periodDates()->pluck('start_date') as $date)
                        {{$date->format('d/m/Y')}} <br>
                    @endforeach
                </a>
            </td>
            <td>
                <a href="{{ route('release-period.show', ['releasePeriod' => $releasePeriod->id]) }}">
                    @foreach($releasePeriod->periodDates()->pluck('end_date') as $date)
                        {{$date->format('d/m/Y')}} <br>
                    @endforeach
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Não foi encontrado nenhum período de lançamento</td>
        </tr>
    @endforelse

    </tbody>
</table>

<div class="separator"></div>

<div style="text-align: center">
    {{ $data->appends(request()->except('page'))->links() }}
</div>

@prepend('scripts')
    <script>
        (function ($) {
            $("#check-delete-all").click(function(){
                $('input:checkbox[name="check-delete"]').not(this).prop('checked', this.checked);
                updateCount();
            });


            $('input:checkbox[name="check-delete"]').click(function () {
                updateCount()
            });
        })(jQuery);

        function updateCount() {
            var count = $j('input:checkbox[name="check-delete"]:checked').length

            if (!count) {
                $j('#text-count-delete').html('Nenhum período de liberação selecionado')
                return
            }

            $j('#text-count-delete').html(count + ' período(s) de liberação selecionado(s)')
        }

        function confirmDeleteMultiple() {
            if (confirm('Excluir os registros selecionados?')) {
                var ids = []
                $j('input:checkbox[name="check-delete"]:checked').each(function(key, field) {
                    ids.push($j(field).data('id'));
                });

                if (ids.length) {
                    window.location.href = '{{ route('release-period.delete') }}?' + $j.param({periods:ids});
                }
            }
        }
    </script>
    <style>
        .table-header th {
            font-weight: bold !important;
            background-color: #f5f9fd;
        }
    </style>
@endprepend
