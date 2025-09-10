@extends('layout.default')

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('final-status-import.upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24">
                        <b>Importação de Situação Final das Matrículas</b>
                    </td>
                </tr>
                <tr>
                    <td class="formmdtd" colspan="2">
                        <p style="margin: 10px 0;">
                            Esta funcionalidade permite importar um arquivo CSV com as situações finais das matrículas.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #d9edf7; border: 1px solid #bce8f1; color: #31708f; padding: 10px; margin: 10px 0; border-radius: 4px;">
                            <p style="margin: 0 0 10px 0; font-weight: bold;">⚠️ Informações Importantes:</p>
                            <ul style="margin: 0; padding-left: 20px;">
                                <li><strong>O csv deve conter pelo menos as colunas: </strong> {{ implode(', ', $expectedColumns) }}
                                <li><strong>Data de Saída é obrigatória</strong> para as situações: Deixou de frequentar, Falecido, Transferido e Reclassificado</li>
                                <li><strong>Formato da Data de Saída:</strong> DD/MM/AAAA (ex: 15/12/2023)</li>
                                <li><strong>Enturmações:</strong> Situações como Transferido, Deixou de frequentar, Falecido e Reclassificado exigem uma única enturmação ativa. Múltiplas enturmações impedirão o processamento.</li>
                                <li><strong>Mapeamento Automático:</strong> O sistema tentará mapear automaticamente as colunas do seu arquivo</li>
                                <li><strong>Situações:</strong>
                                    @foreach($situations as $situation)
                                        <small style="background-color: #337ab7; color: white; padding: 3px 6px; border-radius: 3px; margin-right: 2px; line-height: 2.2; white-space: nowrap">
                                            {{ $situation }}
                                        </small>
                                    @endforeach
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="formmdtd" colspan="2">
                        @php
                            $remover = [
                                App_Model_MatriculaSituacao::EM_EXAME,
                                App_Model_MatriculaSituacao::PRE_MATRICULA,
                                App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
                            ];
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td class="formlttd" valign="top">
                        <span class="form">Arquivo CSV:</span><br>
                        <sub style="vertical-align:top;">Máximo 20MB</sub>
                    </td>
                    <td class="formlttd" valign="top">
                        <input type="file" class="geral" name="file" id="file" accept=".csv" required>
                        @if($errors->has('file'))
                            <br><span style="color: red;">{{ $errors->first('file') }}</span>
                        @endif
                        <br><small style="color: #666;">Formatos aceitos: CSV</small>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button id="btn_enviar" class="btn-green" type="submit">
                <i class="fa fa-upload"></i> Enviar Arquivo
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Validação e feedback visual no envio
        document.getElementById('formcadastro').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            const submitButton = document.getElementById('btn_enviar');

            if (!fileInput.files.length) {
                e.preventDefault();
                return false;
            }

            // Verificar tamanho do arquivo (20MB = 20 * 1024 * 1024 bytes)
            const maxSize = 20 * 1024 * 1024;
            if (fileInput.files[0].size > maxSize) {
                e.preventDefault();
                alert('Arquivo muito grande. Tamanho máximo permitido: 20MB');
                return false;
            }

            // Bloquear botão e mostrar feedback
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processando arquivo...';
            submitButton.style.backgroundColor = '#6c757d';
        });
    </script>
@endpush
