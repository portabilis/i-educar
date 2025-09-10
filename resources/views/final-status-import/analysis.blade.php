@extends('layout.default')

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <div id="formcadastro">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24">
                    <b>Análise do Arquivo</b>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="background-color: #dff0d8; border: 1px solid #d6e9c6; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <strong>Arquivo processado com sucesso!</strong><br>
                        Total de linhas: <strong>{{ $analysis['total_rows'] }}</strong><br>
                        Colunas encontradas: <strong>{{ count($analysis['headers']) }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <strong>Colunas do arquivo:</strong>
                    <div style="background-color: #f5f5f5; border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        @foreach($analysis['headers'] as $header)
                            <span style="display: inline-block; background-color: #337ab7; color: white; padding: 4px 8px; margin: 2px; border-radius: 3px; font-size: 11px;">{{ $header }}</span>
                        @endforeach
                    </div>
                </td>
            </tr>
            @if(count($analysis['sample_data']) > 0)
                <tr>
                    <td class="formmdtd" colspan="2">
                        <strong>Amostra dos dados (primeiras 5 linhas):</strong>
                        <div style="overflow-x: auto; margin: 10px 0;">
                            <table class="table-default">
                                <thead>
                                <tr>
                                    @foreach($analysis['headers'] as $header)
                                        <th style="padding: 5px">{{ $header }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($analysis['sample_data'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td style="padding: 5px">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 20px">
            <a href="{{ route('final-status-import.mapping') }}" class="btn-green">
                <i class="fa fa-arrow-right"></i> Continuar para Mapeamento
            </a>
            <a href="{{ route('final-status-import.index') }}" class="btn"
               style="margin-left: 10px; background-color: #6c757d; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px">
                <i class="fa fa-refresh"></i> Enviar Outro Arquivo
            </a>
        </div>
    </div>
@endsection
