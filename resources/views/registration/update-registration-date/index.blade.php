@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Atualização da data de entrada e enturmação em lote</b></td>
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
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                        <select class="geral" name="situacao" id="situacao" style="width: 308px;">
                            <option value="">Todas</option>
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

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Data de entrada antiga</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" type="text" name="data_entrada_antiga" value="{{ old('data_entrada_antiga', Request::get('data_entrada_antiga')) }}" id="data_entrada_antiga" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>
            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Data de enturmação antiga</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" type="text" name="data_enturmacao_antiga" value="{{ old('data_enturmacao_antiga', Request::get('data_enturmacao_antiga')) }}" id="data_enturmacao_antiga" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Nova data de entrada</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" class="obrigatorio" type="text" name="nova_data_entrada" value="{{ old('nova_data_entrada', Request::get('nova_data_entrada')) }}" id="nova_data_entrada" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Nova data de enturmação</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input onkeypress="formataData(this, event);" type="text" name="nova_data_enturmacao" value="{{ old('nova_data_enturmacao', Request::get('nova_data_enturmacao')) }}" id="nova_data_enturmacao" size="9" maxlength="10" placeholder="dd/mm/aaaa">
                    </span>
                </td>
            </tr>

            <tr id="tr_nm_data" class="field-transfer">
                <td class="formlttd" valign="top">
                    <span class="form">Aplicar também em enturmações remanejadas</span>
                </td>
                <td class="formlttd" valign="top">
                   <span class="form">
                       <input type="checkbox" name="remanejadas" @if(old('remanejadas', Request::get('remanejadas'))) checked="checked" @endif id="remanejadas">
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
                    <td>{{ $registration->data_matricula->format('d/m/Y') }}</td>
                    <td>{{ $registration->lastEnrollment->data_enturmacao->format('d/m/Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if(Session::has('show-confirmation'))
        <div id="modal-confirmation">
           <p>Serão atualizadas <b>{{Session::get('show-confirmation')['count']}}</b> matrículas</p>
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
