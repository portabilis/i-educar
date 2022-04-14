@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')

    <table class="tableDetalhe" width="100%" border="0" cellpadding="2" cellspacing="0">
        <tbody>
        <tr>
            <td class="formdktd" colspan="2" height="24"><b>Período de lançamento de notas e faltas por etapa -
                    Detalhe</b></td>
        </tr>
        <tr>
            <td class="formlttd" valign="top"><span class="form">Ano:</span></td>
            <td class="formlttd" valign="top"><span class="form">{{ $releasePeriod->year }}</span></td>
        </tr>
        <tr>
            <td class="formmdtd" valign="top"><span class="form">Escolas:</span></td>
            <td class="formmdtd" valign="top"><span
                    class="form">{{ implode(', ', $releasePeriod->schools->pluck('name')->toArray())  }}</span></td>
        </tr>
        <tr>
            <td class="formlttd" valign="top"><span class="form">Etapa:</span></td>
            <td class="formlttd" valign="top"><span class="form">{{ $releasePeriod->stage }}</span></td>
        </tr>
        <tr>
            <td class="formmdtd" valign="top"><span class="form">Datas:</span></td>
            <td class="formmdtd" valign="top"><span
                    class="form">{!! implode('<br>', $releasePeriod->getDatesArray()) !!}</span></td>
        </tr>
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        @if($canModify)
            <a href="{{url()->route('release-period.form')}}">
                <button class="btn-green" type="button">Novo</button>
            </a>
            <a href="{{ route('release-period.form', ['releasePeriod' => $releasePeriod->getKey()]) }}">
                <button class="btn" type="button">Editar</button>
            </a>
        @endif
        <a href="{{ route('release-period.index') }}">
            <button class="btn" type="button">Voltar</button>
        </a>
    </div>

    <div class="separator"></div>
@endsection
