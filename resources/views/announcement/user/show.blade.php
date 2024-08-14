@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
    <style>
        #table-announcement {
            border-spacing: 0 !important;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }

        #table-announcement thead th {
            padding: 8px;
            border: 1px solid #CCC;
        }

        #table-announcement tbody tr td {
            border: 1px solid #CCC;
            padding: 6px 8px;
        }

        #table-announcement tbody tr td:first-child {
            border-left: 1px solid #CCC;
        }

        #table-announcement tbody tr:last-child td {
            border-bottom: 1px solid #CCC;
        }

        #table-announcement tbody tr td:last-child {
            border-right: 1px solid #CCC;
        }
    </style>
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('announcement.user.confirm') }}" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Avisos</b></td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top">
                    <span class="form">
                        {!! $announcement->description !!}
                    </span>
                </td>
            </tr>
            @if($announcement->show_vacancy)
                <tr>
                    <td class="formdktd" colspan="2" height="24"><b>Vagas disponíveis nas unidades escolares</b></td>
                </tr>
                <tr>
                    <td class="formmdtd" valign="top">
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
        @if($announcement->show_vacancy)
            <table id="table-announcement">
                <thead>
                <tr>
                    <th>ESCOLA</th>
                    <th>CURSO</th>
                    <th>SÉRIE</th>
                    <th>TURMA</th>
                    <th>VAGAS</th>
                </tr>
                </thead>
                <tbody>
                @forelse($schools as $schoolName => $courses)
                    @php
                        $row1 = 0;
                        foreach ($courses as $grades) {
                           foreach ($grades as $schoolClasses) {
                               foreach ($schoolClasses as $schoolClass) {
                                   $row1++;
                               }
                           }
                        }
                        $isFirstSchool = true;
                    @endphp

                    @foreach($courses as $courseName => $grades)
                        @php
                            $row2 = 0;
                            foreach ($grades as $schoolClasses) {
                                foreach ($schoolClasses as $schoolClass) {
                                    $row2++;
                                }
                            }
                            $isFirstCourse = true;
                        @endphp

                        @foreach($grades as $gradeName => $schoolClasses)
                            @php
                                $row3 = 0;
                                foreach ($schoolClasses as $schoolClass) {
                                    $row3++;
                                }
                            @endphp

                            @foreach($schoolClasses as $schoolClass)
                                <tr>
                                    @if($isFirstSchool)
                                        <td rowspan="{{ $row1 }}">{{ $schoolName }}</td>
                                        @php
                                            $isFirstSchool = false;
                                        @endphp
                                    @endif
                                    @if($isFirstCourse)
                                        <td rowspan="{{ $row2 }}">{{ $courseName. '|'. $row2 }}</td>
                                        @php
                                            $isFirstCourse = false;
                                        @endphp
                                    @endif
                                    @if($loop->first)
                                        <td rowspan="{{ $row3 }}">{{ $gradeName }}</td>
                                    @endif
                                    <td>{{ $schoolClass->nm_turma }}</td>
                                    <td>{{ $schoolClass->vagas }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5">Nenhuma escola com vaga encontrada</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @endif
        @if($announcement->show_confirmation)
            <div class="separator"></div>
            <div style="text-align: center; margin-bottom: 10px">
                <button id="export-button" class="btn-green" type="submit"> Estou ciente</button>
            </div>
        @endif
    </form>
@endsection
