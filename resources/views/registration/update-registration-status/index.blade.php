@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Atualização da situação de matrículas em lote</b></td>
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
                <td class="formmdtd" valign="top"><span class="form">Escola</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            <tr id="tr_nm_curso">
                <td class="formlttd" valign="top"><span class="form">Curso</span></td>
                <td class="formlttd" valign="top">
                    <span class="form">
                        <select class="geral" name="ref_cod_curso" id="ref_cod_curso" style="width: 308px;">
                            <option value="">Selecione um curso</option>
                            @if (old('ref_cod_escola', Request::get('ref_cod_escola')) || ($user->isAdmin() || $user->isInstitutional()))
                                @foreach(App_Model_IedFinder::getCursos(old('ref_cod_escola', Request::get('ref_cod_escola'))) as $id => $name)
                                    <option value="{{$id}}">{{$name}}</option>
                                @endforeach
                            @endif
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
                             @if (old('ref_cod_curso', Request::get('ref_cod_curso')) || ($user->isAdmin() || $user->isInstitutional()))
                                @foreach(App_Model_IedFinder::getSeries(null, old('ref_cod_escola', Request::get('ref_cod_escola')), old('ref_cod_curso', Request::get('ref_cod_curso'))) as $id => $name)
                                    <option value="{{$id}}">{{$name}}</option>
                                @endforeach
                             @endif
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
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                        <select class="geral" name="situacao" id="situacao" style="width: 308px;">
                            <option value="">Selecione</option>
                                @foreach(App_Model_MatriculaSituacao::getInstance()->getEnums() as $id => $name)
                                    @if(!in_array($id, [4,6,5,11,15]))
                                        <option value="{{$id}}" @if(old('situacao', Request::get('situacao')) == $id) selected @endif>
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
                    <span class="form">Nova situação</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                        <select class="geral" name="nova_situacao" id="nova_situacao" style="width: 308px;">
                            <option value="">Selecione</option>
                                @foreach(App_Model_MatriculaSituacao::getInstance()->getEnums() as $id => $name)
                                    @if($id != 11)
                                        <option value="{{$id}}" @if(old('nova_situacao', Request::get('nova_situacao')) == $id) selected @endif>
                                            {{$name}}
                                        </option>
                                    @endif
                            @endforeach
                        </select>
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_motivo" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Motivo da Transferência</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                        <select class="geral" name="transferencia_tipo" id="transferencia_tipo" style="width: 308px;">
                            <option value="">Selecione</option>
                                @foreach(\App\Models\LegacyTransferType::all()->getKeyValueArray('nm_tipo') as $id => $name)
                                <option value="{{$id}}" @if(old('transferencia_tipo', Request::get('transferencia_tipo')) == $id) selected @endif>
                                    {{$name}}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Data da Transferência</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" class="obrigatorio" type="text" name="transferencia_data" id="transferencia_data" value="{{date('d/m/Y')}}" size="9" maxlength="10" placeholder="dd/mm/yyyy">
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Obsercações da Transferência</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <textarea class="geral" name="transferencia_observacoes" id="transferencia_observacoes" cols="60" rows="5" style="wrap:virtual"></textarea>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="formdktd" colspan="2"></td>
            </tr>
            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>

    </form>
@endsection

@prepend('scripts')
    <script>
        if ($j('#nova_situacao').val() != '4') {
            $j('.field-transfer').hide();
        }

        $j('#nova_situacao').on('change', function(){
            if ($j(this).val() == '4') {
                $j('.field-transfer').show();
            } else {
                $j('.field-transfer').hide();
            }
        })
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
@endprepend
