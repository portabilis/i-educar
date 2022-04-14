@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Dispensa em lote</b></td>
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
            <tr id="tr_nm_escola">
                <td class="formmdtd" valign="top">
                    <span class="form">Escola</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            <tr id="tr_nm_curso">
                <td class="formlttd" valign="top">
                    <span class="form">Curso</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    <span class="form">
                        <select class="geral" name="ref_cod_curso" id="ref_cod_curso" style="width: 308px;">
                            <option value="">Selecione um curso</option>
                        </select>
                    </span>

                    @if(old('ref_cod_curso', Request::get('ref_cod_curso')))
                        @push('scripts')
                            <script>
                                (function ($) {
                                    $(document).ready(function () {
                                        $j('#ref_cod_curso').val({{old('ref_cod_curso', Request::get('ref_cod_curso'))}})
                                    });
                                })(jQuery);
                            </script>
                        @endpush
                    @endif

                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formmdtd" valign="top"><span class="form">Série</span></td>
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <select class="geral" name="ref_cod_serie" id="ref_cod_serie" style="width: 308px;">
                            <option value="">Selecione uma série</option>
                        </select>
                    </span>

                    @if(old('ref_cod_serie', Request::get('ref_cod_serie')))
                        @push('scripts')
                            <script>
                                (function ($) {
                                    $(document).ready(function () {
                                        $j('#ref_cod_serie').val({{old('ref_cod_serie', Request::get('ref_cod_serie'))}})
                                    });
                                })(jQuery);
                            </script>
                        @endpush
                    @endif

                </td>
            </tr>
            <tr id="tr_nm_turma">
                <td class="formlttd" valign="top"><span class="form">Turma</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-school-class')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formmdtd" valign="top">
                    <span class="form">Situação</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                        <select class="geral" name="situacao" id="situacao" style="width: 308px;">
                            <option value="">Todas</option>
                                @foreach(App_Model_MatriculaSituacao::getInstance()->getEnums() as $id => $name)
                                @if(!in_array($id, [4,6,5,11,15]))
                                    <option value="{{$id}}"
                                            @if(old('situacao', Request::get('situacao')) == $id) selected @endif>
                                            {{$name}}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </span>
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formlttd" valign="top">
                    <span class="form">Componentes curriculares</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @inject('service', 'App\Services\SchoolGradeDisciplineService')
                    <span class="form">
                    <select class="geral" name="ref_cod_componente_curricular" id="ref_cod_componente_curricular"
                            style="width: 308px;">
                        @if(Request::get('ref_cod_escola') && Request::get('ref_cod_serie'))
                            @foreach($service->getDisciplines(Request::get('ref_cod_escola'), Request::get('ref_cod_serie')) as $discipline)
                                <option value="{{$discipline->id}}">{{$discipline->nome}}</option>
                            @endforeach
                        @endif
                    </select>
                    </span>
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formmdtd" valign="top">
                    <span class="form">Tipo de dispensa</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                        <select class="geral" name="exemption_type" id="exemption_type" style="width: 308px;">
                            <option value="">Selecione</option>
                                @foreach(\App\Models\LegacyExemptionType::all() as $exemptionType)
                                <option value="{{$exemptionType->getKey()}}"
                                        @if(old('exemption_type', Request::get('exemption_type')) == $exemptionType->getKey()) selected @endif>{{$exemptionType->nm_tipo}}</option>
                                @endforeach
                        </select>
                    </span>
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
                            <select class="geral" name="stage[]" id="stage" style="width: 308px;" multiple="multiple"
                                    data-placeholder="Selecione uma etapa">
                            </select>
                        </span>
                </td>
            </tr>
            <tr id="tr_descricao">
                <td class="formlttd">
                    <span class="form">Observações</span>
                </td>
                <td class="formlttd">
                            <span class="form">
                                <textarea class="geral" name="observacoes" cols="37" rows="5"></textarea>
                            </span>
                </td>
            </tr>
            <tr id="tr_nm_data" class="field-transfer">
                <td class="formmdtd" valign="top">
                    <span class="form">Não remover frequências lançadas?</span>
                </td>
                <td class="formmdtd" valign="top">
                           <span class="form">
                               <input type="checkbox" value="1" name="manter_frequencias" @if(old('manter_frequencias', Request::get('manter_frequencias'))) checked="checked" @endif id="manter_frequencias">
                            </span>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>

    </form>

    @if(Session::has('registrations'))
        <h3>Matrículas alteradas</h3>
        <br>
        <table class="table-default">
            <thead>
            <tr>
                <th width="100">Matrícula</th>
                <th>Nome</th>
                <th>Data de matrícula</th>
                <th>Data de enturmação</th>
            </tr>
            </thead>
            <tbody>
            @foreach(Session::get('registrations') as $registration)
                <tr class="form-success">
                    <td>{{ $registration->cod_matricula }}</td>
                    <td>{{ $registration->student->person->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

@prepend('styles')
    <link type='text/css' rel='stylesheet'
          href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
@endprepend

@prepend('scripts')
    @if(Request::get('ref_cod_componente_curricular'))
        @push('scripts')
            <script>
                (function ($) {
                    $(document).ready(function () {
                        $j('#ref_cod_componente_curricular').val({{Request::get('ref_cod_componente_curricular')}})
                        $j('#ref_cod_componente_curricular').trigger('chosen:updated');
                    });
                })(jQuery);
            </script>
        @endpush
    @endif

    <script>
        (function ($) {
            let stageTypes = JSON.parse('{!! $stageTypes !!}')

            $('#stage_type').change(function () {
                stageSelect = $('#stage');
                stageSelect.find('option').remove();

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

                @php
                  $stage = old('stage', Request::get('stage'));
                @endphp
                @if($stage)
                    $('#stage').val('@if(is_array($stage)) {{implode(',', $stage)}} @else {{$stage}} @endif');
                @endif

                $j('#stage').trigger('chosen:updated');
            })

            $(document).ready(function () {
                if (typeof multipleSearchComponentecurricularOptions == 'undefined') {
                    multipleSearchComponentecurricularOptions = {}
                }
                searchForArea = true;
                multipleSearchComponentecurricularOptions.placeholder = safeUtf8Decode('Selecione os componentes');
                multipleSearchComponentecurricularOptions = typeof multipleSearchComponentecurricularOptions == 'undefined' ? {} : multipleSearchComponentecurricularOptions;
                multipleSearchHelper.setup('ref_cod_componente_curricular', '', 'multiple', 'multiple', multipleSearchComponentecurricularOptions);

                $('#stage').chosen();
            });
        })(jQuery);

        $j('#ref_cod_serie, #ref_cod_turma').on('change', function () {
            setTimeout(function () {
                $j('#ref_cod_componente_curricular').trigger('chosen:updated');
            }, 1000);
        });

        $j('#ref_cod_turma').on('change', function () {
            if (!$j(this).val()) {
                return;
            }

            $j.ajax({
                url: '/api/school-class/stages/' + $j(this).val(),
                dataType: 'json',
                success: function (response) {
                    var stageType = response[0].ref_cod_modulo;
                    $j('#stage_type').val(stageType).trigger('change');
                }
            });
        });
    </script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Turma.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/ComponenteCurricularEscolaSerie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/ComponenteCurricular.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchComponenteCurricular.js") }}"></script>
    <script type='text/javascript'
            src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
@endprepend
