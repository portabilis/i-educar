@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Bloquear enturmação em lote</b></td>
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
                    <span class="form"><label for="ref_cod_instituicao">Instituição</label></span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_nm_escola">
                <td class="formmdtd" valign="top"><span class="form"><label for="ref_cod_escola">Escola</label></span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            <tr id="tr_nm_curso">
                <td class="formlttd" valign="top"><span class="form"><label for="ref_cod_curso">Curso</label></span></td>
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
            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form"><label for="bloquear_enturmacao_sem_vagas">Bloquear enturmação após atingir limite de vagas</label></span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input type="checkbox" name="bloquear_enturmacao_sem_vagas" @if(old('bloquear_enturmacao_sem_vagas', Request::get('bloquear_enturmacao_sem_vagas'))) checked="checked" @endif id="bloquear_enturmacao_sem_vagas">
                    </span>
                </td>
            </tr>

            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>

    </form>

    @if(Session::has('show-confirmation'))
        <div id="modal-confirmation">
           <p>Serão atualizadas <b>{{ Session::get('show-confirmation')}}</b> séries da escola</p>
            <p>Deseja continuar?</p>
        </div>
    @endif
@endsection

@prepend('scripts')
    <script>
        $j("#modal-confirmation").dialog({
            autoOpen: false,
            closeOnEscape: false,
            draggable: false,
            width: 560,
            modal: true,
            resizable: false,
            title: 'Confirmação',
            buttons: {
                "Salvar": function () {
                    $j('#formcadastro').append(
                        "<input type='text' name='confirmation' value='1'>"
                    ).submit();
                    $j(this).dialog("close");
                },
                "Cancelar": function () {
                    $j(this).dialog("close");
                }
            },
            close: function () {

            },
        });
        $j("#modal-confirmation").dialog("open");
    </script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Turma.js") }}"></script>
    <script type="text/javascript" src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js") }}"></script>
@endprepend
