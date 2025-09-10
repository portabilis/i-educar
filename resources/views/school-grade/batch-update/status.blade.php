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
                    <b>Resultado da Atualização em Lote</b>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="margin: 5px 0;">
                        <strong>Status:</strong> {{ $result['status'] === 'completed' ? 'Concluída' : 'Falhou' }}<br>
                        <strong>Escola/série processadas:</strong> {{ $result['processed'] ?? 0 }} de {{ $result['total'] ?? 0 }}
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
                                        <strong>Escola ID {{ $warning['school_id'] }}, Série ID {{ $warning['grade_id'] }}:</strong> {{ $warning['warning'] }}
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
                                        @if($error['school_id'] > 0 && $error['grade_id'] > 0)
                                            <strong>Escola ID {{ $error['school_id'] }}, Série ID {{ $error['grade_id'] }}:</strong>
                                        @else
                                            <strong>Erro Geral:</strong>
                                        @endif
                                        {{ $error['error'] }}
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
                        <div style="background-color: #d9edf7; border: 1px solid #bce8f1; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 10px 0;">✅ Processamentos Realizados ({{ count($result['details']) }}):</strong>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($result['details'] as $detail)
                                    @php
                                        $message = $detail['message'];
                                        $message = str_replace(' processadas com sucesso.', '', $message);

                                        $schoolId = $detail['school_id'] ?? null;
                                        $gradeId = $detail['grade_id'] ?? null;

                                        if ($schoolId && $gradeId) {
                                            $link = "/intranet/educar_escola_serie_cad.php?ref_cod_escola={$schoolId}&ref_cod_serie={$gradeId}";
                                            $displayText = $message;
                                        } else {
                                            $link = null;
                                            $displayText = $message;
                                        }
                                    @endphp
                                    <div style="margin: 5px 0; padding: 5px; background-color: rgba(255,255,255,0.5); border-radius: 3px;">
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
            </tbody>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="text-align: center">
                        @if($result['status'] === 'completed')
                            <div style="background-color: #dff0d8; border: 1px solid #d6e9c6; padding: 15px; border-radius: 4px;">
                                <strong>✅ Atualização Concluída!</strong>
                                <p style="margin: 0">
                                    Total processado: <strong>{{ $result['processed'] ?? 0 }}</strong> escola/série<br>
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
                                <strong>❌ Atualização Falhou!</strong>
                                <p style="margin: 0">Ocorreu um erro durante o processamento. Verifique os detalhes acima.</p>
                            </div>
                        @endif
                        <div style="margin-top: 20px">
                            @if($result['status'] === 'completed')
                                <a href="{{ route('school-grade.batch-update.index') }}" class="btn-green">
                                    <i class="fa fa-refresh"></i> Nova Atualização
                                </a>
                            @else
                                <a href="{{ route('school-grade.batch-update.index') }}" class="btn-green">
                                    <i class="fa fa-arrow-left"></i> Voltar
                                </a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
