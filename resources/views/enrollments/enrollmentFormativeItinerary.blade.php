@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
@endpush

@section('content')
<form id="enrollments-formative-itinerary" class="form-new-register" action="{{ Asset::get('/enrollment-formative-itinerary/' . $enrollment->id)}}" method="post">
    <div>
        <table class="table-default">
            <thead>
                <tr>
                    <td colspan="2"><strong>Novo</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aluno:</td>
                    <td>{{ $enrollment->studentName ?? null }}</td>
                </tr>
                <tr>
                    <td>Turma:</td>
                    <td>{{ $enrollment->schoolClass->name ?? null }}</td>
                </tr>
                <tr>
                    <td>Tipo do itinerário formativo:</td>
                    <td>
                        @php
                            $types = transformStringFromDBInArray($enrollment->tipo_itinerario) ?? [];
                        @endphp
                        <select name="itinerary_type" id="itinerary_type" multiple="multiple" class="select-default">
                            @foreach($itineraryType as $key => $type)
                                <option {{ in_array($key, $types) ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Composição do itinerário formativo integrado:</td>
                    <td>
                        @php
                            $compositions = transformStringFromDBInArray($enrollment->composicao_itinerario) ?? [];
                        @endphp
                        <select name="itinerary_composition" id="itinerary_composition" multiple="multiple" class="select-default">
                            @foreach($itineraryComposition as $key => $composition)
                                <option {{ in_array($key, $compositions) ? 'selected' : '' }} value="{{ $key }}">{{ $composition }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Tipo do curso do itinerário de formação técnica e profissional:</td>
                    <td>
                        <select name="itinerary_course" id="itinerary_course" class="select-default">
                            <option value="">Selecione</option>
                            @foreach($itineraryCourse as $key => $course)
                                <option {{ $enrollment->curso_itinerario === $key ? 'selected' : '' }} value="{{ $key }}">{{ $course }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Itinerário concomitante intercomplementar à matrícula de formação geral básica:</td>
                    <td>
                        <select name="concomitant_itinerary" id="concomitant_itinerary" class="select-default">
                            <option value="">Selecione</option>
                            <option {{ $enrollment->itinerario_concomitante === true ? 'selected' : '' }} value="1">Sim</option>
                            <option {{ $enrollment->itinerario_concomitante === false ? 'selected' : '' }} value="0">Não</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                    </td>
                </tr>
            </tbody>
            <tr>
                <td class="formdktd" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="13" align="center">
                    <input type="submit" class="btn-green botaolistagem" onclick="javascript: go('/intranet/educar_matricula_det.php?cod_matricula={{ $enrollment->registration->id }}')" value=" Salvar ">
                    <input type="button" class="botaolistagem" onclick="javascript: go('/intranet/educar_matricula_det.php?cod_matricula={{ $enrollment->registration->id }}')" value=" Cancelar ">
                </td>
            </tr>
        </table>
    </div>
@endsection

@push('scripts')
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript" src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type='text/javascript'>
    (function ($) {
        $(document).ready(function () {
            multipleSearchHelper.setup('itinerary_type', '', 'multiple', 'multiple');
            $j('#itinerary_type').trigger('chosen:updated');
            multipleSearchHelper.setup('itinerary_composition', '', 'multiple', 'multiple');
            $j('#itinerary_composition').trigger('chosen:updated');
        });
    })(jQuery);
    </script>
@endpush
