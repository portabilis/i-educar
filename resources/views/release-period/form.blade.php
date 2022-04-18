@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    @if($canModify)
        <form id="formcadastro"
              action="@if($releasePeriod->exists) {{ route('release-period.update', ['releasePeriod' => $releasePeriod->id]) }} @else {{ route('release-period.create') }}@endif"
              method="post">
            <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
                <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24"><b>Período de lançamento de notas e faltas por etapa</b>
                    </td>
                </tr>
                <tr id="tr_nm_ano">
                    <td class="formmdtd" valign="top">
                        <span class="form">Ano</span>
                        <span class="campo_obrigatorio">*</span>
                        <br>
                        <sub style="vertical-align:top;">somente números</sub>
                    </td>
                    <td class="formmdtd" valign="top">
                        @include('form.select-year')
                    </td>
                </tr>
                <tr id="tr_nm_instituicao">
                    <td class="formlttd" valign="top">
                        <span class="form">Instituição</span>
                        <span class="campo_obrigatorio">*</span>
                    </td>
                    <td class="formlttd" valign="top">
                        @include('form.select-institution')
                    </td>
                </tr>
                <tr>
                    <td class="formmdtd" valign="top">
                        <span class="form">Escola</span>
                        <span class="campo_obrigatorio">*</span>
                    </td>
                    <td class="formmdtd" valign="top">
                        @include('form.select-school-multiple')
                        <a href="javascript:void(0)" id="link-select-all">Selecionar todas</a>
                    </td>
                </tr>
                <tr>
                    <td class="formlttd" valign="top">
                        <span class="form">Tipo de etapa</span>
                        <span class="campo_obrigatorio">*</span>
                    </td>
                    <td class="formlttd" valign="top">
                        @include('form.stage-type')
                    </td>
                </tr>
                <tr>
                    <td class="formmdtd" valign="top">
                        <span class="form">Etapa</span>
                        <span class="campo_obrigatorio">*</span>
                    </td>
                    <td class="formmdtd" valign="top">
                        <span class="form">
                            <select class="geral" name="stage" id="stage" style="width: 308px;">
                                <option value="">Selecione uma etapa</option>
                            </select>
                        </span>
                    </td>
                </tr>

                <tr>
                    <td id="tr_nm_data" valign="top" align="center" colspan="2">
                        <table cellspacing="0" id="dates-table" class="tabela-adicao" cellpadding="2"
                               style="margin:10px 0px 10px 0px;">
                            <tbody>
                            <tr align="center" id="tr_gestores_tit" style="font-weight:bold" class="formdktd">
                                <td colspan="6">Datas</td>
                            </tr>
                            <tr align="center" style="font-weight:bold" id="tr_gestores_cab">
                                <td class="formmdtd" align="center"><span class="form">Data inicial</span>
                                </td>
                                <td class="formmdtd" align="center"><span class="form">Data final</span>
                                </td>
                                <td class="formmdtd" align="center"><span class="form">Ação</span></td>
                            </tr>
                            @if($releasePeriod->exists)
                                @foreach($releasePeriod->periodDates as $date)
                                    <tr class="formmdtd dd tr-dates">
                                        <td class="formmdtd dd" valign="top">
                                            <input class="geral" onkeypress="formataData(this, event);" type="text"
                                                   name="start_date[]"
                                                   value="{{ $date->start_date->format('d/m/Y') }}"
                                                   size="9" maxlength="10" placeholder="dd/mm/aaaa">
                                        <td class="formmdtd dd" valign="top">
                                            <input class="geral" onkeypress="formataData(this, event);" type="text"
                                                   name="end_date[]"
                                                   value="{{ $date->end_date->format('d/m/Y') }}"
                                                   size="9" maxlength="10" placeholder="dd/mm/aaaa">
                                        </td>
                                        <td align="center">
                                            <a href="javascript:void(0)" style="outline: none;">
                                                <img src="/intranet/imagens/banco_imagens/excluirrr.png" border="0"
                                                     alt="Excluir"
                                                     class="btn-remove"></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="formmdtd dd tr-dates">
                                    <td class="formmdtd dd" valign="top">
                                        <input class="geral" onkeypress="formataData(this, event);" type="text"
                                               name="start_date[]"
                                               size="9" maxlength="10" placeholder="dd/mm/aaaa">
                                    <td class="formmdtd dd" valign="top">
                                        <input class="geral" onkeypress="formataData(this, event);" type="text"
                                               name="end_date[]"
                                               size="9" maxlength="10" placeholder="dd/mm/aaaa">
                                    </td>
                                    <td align="center">
                                        <a href="javascript:void(0)" style="outline: none;">
                                            <img src="/intranet/imagens/banco_imagens/excluirrr.png" border="0"
                                                 alt="Excluir"
                                                 class="btn-remove"></a>
                                    </td>
                                </tr>
                            @endif
                            <tr></tr>
                            <tr id="adicionar_linha" style="background-color:#f5f9fd;">
                                <td colspan="6" align="left" style="padding-top: 17px !important;">
                                    <a style="color: #47728f; text-decoration:none;" href="javascript:void(0)"
                                       id="btn-add-row"><img
                                            src="/intranet/imagens/nvp_bot_novo.png" border="0" alt="incluir"
                                            style="float:left; margin:5px;">
                                        <label style="padding:9px; margin:0;">ADICIONAR NOVO</label>
                                    </a>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="separator"></div>

            <div style="text-align: center">
                <button class="btn" type="button" onclick="resetForm()">Limpar campos</button>

                @if($releasePeriod->exists)
                    @if($canModify)
                        <button class="btn-green" type="submit">Editar</button>
                    @endif
                    <a href="{{route('release-period.index')}}">
                        <button class="btn" type="button">Cancelar</button>
                    </a>
                    @if($canRemove)
                        <a href="javascript:confirmDelete()">
                            <button class="btn" type="button">Excluir</button>
                        </a>
                    @endif
                @else
                    @if($canModify)
                        <button class="btn-green" type="submit">Criar novo</button>
                    @endif
                @endif

            </div>
        </form>
    @endif

    @include('release-period.index')

@endsection

@prepend('scripts')
    <style>
        .chosen-choices{
            max-height:200px !important;
            overflow: auto !important;;
        }
    </style>

    <script type='text/javascript'>
        (function ($) {
            $('#link-select-all').click(function () {
                $('#escola option').prop('selected', true); // Selects all options
                $('#escola').trigger('chosen:updated');
            });

            let stageTypes = JSON.parse('{!! $stageTypes !!}')

            $('#stage_type').change(function () {
                stageSelect = $('#stage');
                stageSelect.find('option').not(':first').remove();

                stageType = stageTypes[$(this).val()];

                if (typeof stageType === 'undefined') {
                    return;
                }

                for (i = 1; i <= parseInt(stageType.num_etapas); i++) {
                    stageSelect.append($('<option>', {
                        value: i,
                        text: i + 'º ' + stageType.nm_tipo
                    }));
                }

                @if(old('stage', Request::get('stage')))
                $('#stage').val('{{old('stage', Request::get('stage'))}}');
                @endif
            })

            $('#btn-add-row').click(function () {
                var $lastRow = $('.tr-dates:last');
                var $newRow = $lastRow.clone();
                $newRow.find(':text').val('');
                $lastRow.after($newRow);
            });

            $('#dates-table').on('click', '.btn-remove', function () {
                if ($('.tr-dates').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        })(jQuery);

        function resetForm() {
            document.getElementById('formcadastro').reset();
            $j('#escola').trigger('chosen:updated');
        }

        function confirmDelete() {
            if (confirm('Excluir registro?')) {
                window.location.href = '{{ route('release-period.delete', ['periods' => [$releasePeriod->id]]) }}';
            }
        }
    </script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
@endprepend
