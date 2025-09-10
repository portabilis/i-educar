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
                    <b>Resultado do Ano Letivo em Lote</b>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="margin: 5px 0;">
                        <strong>Mensagem:</strong> {{ $result['message'] ?? 'Nenhuma mensagem disponível' }}<br>
                        <strong>Total de escolas:</strong> {{ $result['total'] ?? 0 }}<br>
                        <strong>Escolas processadas:</strong> {{ $result['processed'] ?? 0 }}
                    </div>
                </td>
            </tr>

            @if(isset($result['errors']) && count($result['errors']) > 0)
                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 10px 0;">📋 Detalhes dos Erros ({{ count($result['errors']) }}):</strong>
                            <div style="max-height: 400px; overflow-y: auto;">
                                @foreach($result['errors'] as $error)
                                    @php
                                        $bgColor = match($error['type'] ?? 'error') {
                                            'success' => '#d4edda',
                                            'skipped' => '#fff3cd',
                                            'error' => '#f8d7da',
                                            default => '#e2e3e5'
                                        };
                                        $borderColor = match($error['type'] ?? 'error') {
                                            'success' => '#c3e6cb',
                                            'skipped' => '#ffeaa7',
                                            'error' => '#f5c6cb',
                                            default => '#d6d8db'
                                        };
                                        $icon = match($error['type'] ?? 'error') {
                                            'success' => '✅',
                                            'skipped' => '⚠️',
                                            'error' => '❌',
                                            default => 'ℹ️'
                                        };
                                    @endphp
                                    <div style="margin: 5px 0; padding: 8px; background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: 3px;">
                                        <strong>{{ $icon }} {{ $error['school_name'] ?? $error['school_id'] ?? 'Escola' }}:</strong> {{ $error['error'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            @endif

            @if(isset($result['details']) && count($result['details']) > 0)
                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 10px 0;">📋 Detalhes do Processamento ({{ count($result['details']) }}):</strong>
                            <div style="max-height: 400px; overflow-y: auto;">
                                @foreach($result['details'] as $detail)
                                    @php
                                        $bgColor = match($detail['type']) {
                                            'success' => '#d4edda',
                                            'skipped' => '#fff3cd',
                                            'error' => '#f8d7da',
                                            default => '#e2e3e5'
                                        };
                                        $borderColor = match($detail['type']) {
                                            'success' => '#c3e6cb',
                                            'skipped' => '#ffeaa7',
                                            'error' => '#f5c6cb',
                                            default => '#d6d8db'
                                        };
                                        $icon = match($detail['type']) {
                                            'success' => '✅',
                                            'skipped' => '⚠️',
                                            'error' => '❌',
                                            default => 'ℹ️'
                                        };

                                        $link = null;
                                        $displayText = $detail['message'];

                                        if (isset($detail['school_id'])) {
                                            $schoolId = $detail['school_id'];
                                            $year = $result['year'] ?? date('Y');
                                            $link = "/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola={$schoolId}&ano={$year}&referrer=educar_escola_det.php";
                                        }
                                    @endphp
                                    <div style="margin: 5px 0; padding: 8px; background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: 3px;">
                                        @if($link)
                                            <a href="{{ $link }}" target="_blank" style="color: #0066cc; text-decoration: none;">
                                                {{ $displayText }} <span style="font-size: 0.8em;">🔗</span>
                                            </a>
                                        @else
                                            {{ $displayText }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="text-align: center">
                        @if(($result['status'] ?? '') === 'completed')
                            <div style="background-color: #dff0d8; border: 1px solid #d6e9c6; padding: 15px; border-radius: 4px;">
                                <strong>✅ Processamento Concluído!</strong>
                                <p style="margin: 0">
                                    Total: <strong>{{ $result['total'] ?? 0 }}</strong> escola(s)<br>
                                    Processadas: <strong>{{ $result['processed'] ?? 0 }}</strong> escola(s)
                                </p>
                            </div>
                        @else
                            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px;">
                                <strong>❌ Processamento Falhou!</strong>
                                <p style="margin: 0">
                                    {{ $result['message'] ?? 'Ocorreu um erro durante o processamento. Verifique os detalhes acima.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px" >
            <a href="{{ route('academic-year.edit') }}" class="btn-green" style="text-decoration: none">
                Novo Ano Letivo em Lote
            </a>
        </div>
    </div>
@endsection
