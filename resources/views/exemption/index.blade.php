@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('modules/Portabilis/Assets/Plugins/Chosen/chosen.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="get">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Consulta de dispensas</b></td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <span class="form">Ano</span><br>
                    <sub style="vertical-align:top;">somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top"><span class="form">Instituição</span></td>
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
                    @include('form.select-course')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formmdtd" valign="top"><span class="form">Serie</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-grade')
                </td>
            </tr>
            <tr id="tr_nm_serie">
                <td class="formlttd" valign="top"><span class="form">Componente curricular</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-discipline-school-grade')
                </td>
            </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Buscar</button>
        </div>

    </form>

    <table class="table-default">
        <tr class="titulo-tabela-listagem">
            <th colspan="7">Dispensas - Listagem</th>
        </tr>
        <tr>
            <td style="font-weight:bold;">Ano</td>
            <td style="font-weight:bold;">Aluno</td>
            <td style="font-weight:bold;">Componente curricular</td>
            <td style="font-weight:bold;">Tipo de dispensa</td>
            <td style="font-weight:bold;">Data da dispensa</td>
            <td style="font-weight:bold;">Feito por</td>
            <td style="font-weight:bold;">Feito em lote</td>
        </tr>
        @forelse($exemptions as $exemption)
            @php $exemptionUrl = url('intranet/educar_dispensa_disciplina_det.php?ref_cod_matricula=' . $exemption->ref_cod_matricula . '&ref_cod_serie=' . $exemption->ref_cod_serie . '&ref_cod_escola=' . $exemption->ref_cod_escola . '&ref_cod_disciplina=' . $exemption->ref_cod_disciplina) @endphp
            <tr>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->registration->ano }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->registration->student->person->nome }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->discipline->nome }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->type }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->data_cadastro->format('d/m/Y') }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">{{ $exemption->createdBy->name }}</a>
                </td>
                <td>
                    <a href="{{ $exemptionUrl }}" target="_blank" rel="noopener">@if($exemption->batch) Sim @else Não @endif</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" align=center>Não há informação para ser apresentada</td>
            </tr>
        @endforelse
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $exemptions->links() }}
    </div>

    </form>
@endsection

@prepend('scripts')
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
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/ComponenteCurricularEscolaSerie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js") }}"></script>
@endprepend
