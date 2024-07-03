@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <style type="text/css">
        .select-default {
            padding: 10px;
        }
    </style>
@endpush

@section('content')
<form >
    <div>
        <table class="table-default">
            <thead>
                <tr>
                    <td colspan="2"><strong>Novo</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Aluno:</th>
                    <td>{{ $enrollment->studentName ?? null }}</td>
                </tr>
                <tr>
                    <th scope="row">Turma:</th>
                    <td>{{ $enrollment->schoolClass->name ?? null }}</td>
                </tr>
                <tr id="tr_itinerary_type">
                    <th scope="row">
                        Trilha de aprofundamento do itinerário formativo:
                    </th>
                    <td>
                        @php
                            $types = $enrollment->tipo_itinerario;
                        @endphp
                        <select name="itinerary_type" id="itinerary_type" multiple="multiple" class="select-default">
                            @foreach($itineraryType as $key => $type)
                                <option {{ in_array($key, $types) ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr id="tr_itinerary_composition">
                    <th scope="row">
                        Composição do itinerário formativo integrado (entre as áreas de conhecimento ou entre as áreas de conhecimento e a formação técnica profissional):
                    </th>
                    <td>
                        @php
                            $compositions = $enrollment->composicao_itinerario;
                        @endphp
                        <select name="itinerary_composition" id="itinerary_composition" multiple="multiple" class="select-default">
                            @foreach($itineraryComposition as $key => $composition)
                                <option {{ in_array($key, $compositions) ? 'selected' : '' }} value="{{ $key }}">{{ $composition }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr id="tr_itinerary_course">
                    <th scope="row">Tipo do curso do itinerário de formação técnica e profissional:</th>
                    <td>
                        <select name="itinerary_course" id="itinerary_course" class="select-default">
                            <option value="">Selecione</option>
                            @foreach($itineraryCourse as $key => $course)
                                <option {{ $enrollment->curso_itinerario === $key ? 'selected' : '' }} value="{{ $key }}">{{ $course }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr id="tr_technical_course">
                    <th scope="row">
                        Código do curso técnico:
                    </th>
                    <td>
                        <select name="technical_course" id="technical_course" class="select-default">
                            <option value="">Selecione</option>
                            @foreach($technicalCourses as $key => $course)
                                <option {{ $enrollment->cod_curso_profissional === $key ? 'selected' : '' }} value="{{ $key }}">{{ $course }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr id="tr_concomitant_itinerary">
                    <th scope="row">Itinerário concomitante intercomplementar à matrícula de formação geral básica:</th>
                    <td>
                        <select name="concomitant_itinerary" id="concomitant_itinerary" class="select-default">
                            <option value="">Selecione</option>
                            <option {{ $enrollment->itinerario_concomitante === true ? 'selected' : '' }} value="1">Sim</option>
                            <option {{ $enrollment->itinerario_concomitante === false ? 'selected' : '' }} value="0">Não</option>
                        </select>
                    </td>
                </tr>
                <input type="hidden" name="show_concomitant_itinerary" id="show_concomitant_itinerary" value="{{ $showConcomitantItinerary ? 1 : 0 }}">
                <tr>
                    <th scope="row" colspan="2">
                    <input type="hidden" id="enrollment_id" name="enrollment_id" value="{{ $enrollment->id }}">
                    </th>
                </tr>
            </tbody>
            <tr>
                <td class="formdktd" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="13" align="center">
                    <input type="button" class="btn-green btn-submit botaolistagem" value=" Salvar ">
                    <a href="{{ route('registration.formative-itinerary.index', [$enrollment->ref_cod_matricula]) }}" >
                        <input type="button" class="botaolistagem" value=" Cancelar ">
                    </a>
                </td>
            </tr>
        </table>
    </div>
@endsection

@push('scripts')
    <script type='text/javascript' src='{{ Asset::get('/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript" src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type="text/javascript" src="{{ Asset::get("/js/enrollment-formative-itinerary.js") }}"></script>
    <script type='text/javascript'>

    (function ($) {
        $(document).ready(function () {
            multipleSearchHelper.setup('itinerary_type', '', 'multiple', 'multiple');
            $j('#itinerary_type').trigger('chosen:updated');
            multipleSearchHelper.setup('itinerary_composition', '', 'multiple', 'multiple');
            $j('#itinerary_composition').trigger('chosen:updated');

            $(".btn-submit").click(function(e){

                e.preventDefault();

                const itinerary_type =  $j('#itinerary_type').val();
                const itinerary_composition =  $j('#itinerary_composition').val();
                const concomitant_itinerary =  $j('#concomitant_itinerary').val();
                const itinerary_course =  $j('#itinerary_course').val();
                const technical_course =  $j('#technical_course').val();
                const enrollment_id =  $j('#enrollment_id').val();

                const dataToSend = {
                    itinerary_type:itinerary_type,
                    itinerary_composition:itinerary_composition,
                    concomitant_itinerary:concomitant_itinerary,
                    itinerary_course:itinerary_course,
                    technical_course:technical_course,
                    enrollment_id:enrollment_id
                };

                $.ajax({
                    type:'PUT',
                    url:"{{ route('registration.formative-itinerary.update', [$enrollment->ref_cod_matricula, $enrollment]) }}",
                    data: dataToSend,
                    success:function(data) {
                        windowUtils.redirect("{{ route('registration.formative-itinerary.index', [$enrollment->ref_cod_matricula]) }}")
                    },
                    error:function(data) {
                        messageUtils.error(decodeURIComponent(JSON.parse(data.responseText).message));
                    }
                });

            });
        });
    })(jQuery);
    </script>
@endpush
