@extends('layout.default')

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link rel="stylesheet" href="{{ Asset::get('vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>
@endpush

@push('scripts')
    <script>
        (function($){
            $(document).ready(function() {
                function toggleFields() {
                    const valor = $('#tipo').val();
                    if(valor == 'schoolclass') {
                        $('#tr_cursos').show();
                    } else {
                        $('#tr_cursos').hide();
                    }
                }

                toggleFields();

                $('#tipo').change(function() {
                    toggleFields();
                });
            });
        })(jQuery);
    </script>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Atualizar etapas da turma em lote</b></td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <label for="ano" class="form">Ano <span class="campo_obrigatorio">*</span></label>
                    <sub>Somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top">
                    <label for="ref_cod_instituicao" class="form">Instituição <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_tipo">
                <td class="formlttd" valign="top">
                    <label for="tipo" class="form">Tipo <span class="campo_obrigatorio">*</span></label>
                </td>
                <td class="formlttd" valign="top">
                    <span class="form">
                        <select class="geral" name="tipo" id="tipo" style="width: 308px;">
                            <option value="">Selecione o tipo</option>
                            @foreach(['schoolclass' => 'Etapas da Turma', 'school' =>'Etapas da Escola'] as $valor => $tipo)
                                <option value="{{ $valor }}" @if(old('tipo', Request::get('tipo')) == $valor) selected @endif>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top">
                    <label for="escola" class="form">Escola</label>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school-multiple')
                </td>
            </tr>
            <tr id="tr_cursos">
                <td class="formmdtd" valign="top">
                    <label for="cursos" class="form">Curso</label>
                </td>
                <td class="formmdtd" valign="top">
                    <select name="curso[]" id="cursos" multiple style="width: 308px;">
                        @foreach(App_Model_IedFinder::getCursos() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top">
                    <label for="ref_cod_modulo" class="form">Tipo de Etapa</label>
                </td>
                <td class="formmdtd" valign="top">
                    <select class="geral" name="ref_cod_modulo" id="ref_cod_modulo" style="width: 308px;">
                        <option value="">Selecione as opções</option>
                        @foreach(App_Model_IedFinder::getStageTypes() as $id => $name)
                            <option value="{{$id}}" @if(old('ref_cod_modulo', Request::get('ref_cod_modulo')) == $id) selected @endif>{{ Str::upper($name) }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr id="tr_turma_modulo">
                <td colspan="2" class="formmdtd" style="text-align: center; vertical-align: top;">
                    <table id="turma_modulo" class="tabela-adicao" style="margin: 10px 0;">
                        <thead>
                        <tr class="formdktd" style="font-weight: bold; text-align: center;">
                            <th id="th-etapas" colspan="3" style="text-align: left">Etapas (Somente atualização)</th>
                        </tr>
                        <tr class="formmdtd" style="font-weight: bold; text-align: center;">
                            <th id="th-data-inicial" style="text-align: left">Data inicial</th>
                            <th id="th-data-final" style="text-align: left">Data final</th>
                            <th id="th-dias-letivos" style="text-align: left">Dias Letivos</th>
                        </tr>
                        </thead>
                        <tbody>
                        @for($i = 1; $i <= 4; $i++)
                            <tr class="formmdtd dd">
                                <td>
                                    <input type="text" name="etapas[{{ $i }}][data_inicio]" aria-label="Data inicial da etapa {{ $i }}" placeholder="Manter valor atual" maxlength="10" class="geral" size="9" onkeypress="formataData(this, event);">
                                </td>
                                <td>
                                    <input type="text" name="etapas[{{ $i }}][data_fim]" aria-label="Data final da etapa {{ $i }}" placeholder="Manter valor atual" maxlength="10" class="geral" size="9" onkeypress="formataData(this, event);">
                                </td>
                                <td>
                                    <input type="text" name="etapas[{{ $i }}][dias_letivos]" aria-label="Dias letivos da etapa {{ $i }}" placeholder="Manter valor atual" size="9" class="geral" maxlength="3" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '')">
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>
    </form>
@endsection

@if(old('curso', Request::get('curso')))
    @php
        $courses = collect(old('curso', Request::get('curso')))
            ->flatten()
            ->map(fn($value) => $value);
    @endphp

    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    setTimeout(() => {
                        $('#cursos').val([{{ $courses->implode(',') }}]).trigger('chosen:updated');
                    }, 1000);
                });
            })(jQuery);
        </script>
    @endpush
@endif

@push('scripts')
    <script src="{{ Asset::get('/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js') }}"></script>
    <script src="{{ Asset::get('/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js') }}"></script>
    <script>
        (function($) {
            $(document).ready(function() {
                const options = {
                    placeholder: 'Selecione os cursos'
                };
                multipleSearchHelper.setup('cursos', '', 'multiple', 'multiple', options);
                $('#cursos').trigger('chosen:updated');
            });
        })(jQuery);
    </script>
@endpush
