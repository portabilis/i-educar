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
                    <b>Resultado da Importação</b>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="margin: 5px 0;">
                        <strong>Status:</strong> {{ $result['status'] === 'completed' ? 'Concluída' : 'Falhou' }}<br>
                        <strong>Linhas processados:</strong> {{ $result['processed'] ?? 0 }} de {{ $result['total'] ?? 0 }}
                    </div>
                </td>
            </tr>

            @if(isset($result['warnings']) && count($result['warnings']) > 0)
                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #fcf8e3; border: 1px solid #faebcc; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 10px 0;">⚠️ Avisos ({{ count($result['warnings']) }}):</strong>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($result['warnings'] as $warning)
                                    <div style="margin: 5px 0; padding: 5px; background-color: rgba(255,255,255,0.5); border-radius: 3px;">
                                        <strong>Linha {{ $warning['row'] }}:</strong> {{ $warning['warning'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            @endif

            @if(isset($result['errors']) && count($result['errors']) > 0)
                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 10px 0;">❌ Erros ({{ count($result['errors']) }}):</strong>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($result['errors'] as $error)
                                    <div style="margin: 5px 0; padding: 5px; background-color: rgba(255,255,255,0.5); border-radius: 3px;">
                                        <strong>Linha {{ $error['row'] }}:</strong> {{ $error['error'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="text-align: center">
                        @if($result['status'] === 'completed')
                            <div style="background-color: #dff0d8; border: 1px solid #d6e9c6; padding: 15px; border-radius: 4px;">
                                <strong>✅ Importação Concluída!</strong>
                                <p style="margin: 0">
                                    Total processado: <strong>{{ $result['processed'] ?? 0 }}</strong> linhas<br>
                                    @if(isset($result['ignored']) && $result['ignored'] > 0)
                                        Ignorados: <strong>{{ $result['ignored'] }}</strong> (situação "Aprovado" no arquivo)<br>
                                    @endif
                                    @if(isset($result['warnings']) && count($result['warnings']) > 0)
                                        Avisos: <strong>{{ count($result['warnings']) }}</strong><br>
                                    @endif
                                    @if(isset($result['errors']) && count($result['errors']) > 0)
                                        Erros: <strong>{{ count($result['errors']) }}</strong>
                                    @endif
                                </p>
                            </div>
                        @else
                            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px;">
                                <strong>❌ Importação Falhou!</strong>
                                <p style="margin: 0">Ocorreu um erro durante o processamento. Verifique os detalhes acima.</p>
                            </div>
                        @endif
                        <div style="margin-top: 20px">
                            <a href="{{ route('final-status-import.index') }}" class="btn-green">
                                <i class="fa fa-refresh"></i> Nova Importação
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
