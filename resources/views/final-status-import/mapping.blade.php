@extends('layout.default')

@push('styles')
    <link rel="stylesheet" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="{{ route('final-status-import.import') }}" method="post">
        @csrf
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
                <tr>
                    <td class="formdktd" colspan="2" height="24">
                        <b>Mapeamento de Colunas</b>
                    </td>
                </tr>
                <tr>
                    <td class="formmdtd" colspan="2">
                        <p style="margin: 0;">
                            Selecione qual coluna do seu arquivo corresponde a cada campo necessário:
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="formmdtd" colspan="2">
                        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px;">
                            <strong style="margin: 0 0 5px 0; font-weight: bold;">✅ Mapeamento Automático Aplicado</strong>
                            <p style="margin: 0;">O sistema detectou automaticamente as colunas do seu arquivo. Verifique se o mapeamento está correto antes de prosseguir.</p>
                        </div>
                    </td>
                </tr>

                <!-- Cabeçalho da tabela de mapeamento -->
                <tr>
                    <td class="formlttd" valign="top">
                        <span style="color: #337ab7; font-weight: bold;">Campos do Sistema</span>
                    </td>
                    <td class="formlttd" valign="top">
                        <span style="color: #337ab7; font-weight: bold;">Campos do CSV</span>
                    </td>
                </tr>

                @foreach($expectedColumns as $field => $label)
                    <tr>
                        <td class="formlttd" valign="top">
                            <span class="form">{{ $label }}:</span>
                            @if(in_array($field, ['registration_id', 'final_status', 'exit_date']))
                                <span style="color: red; font-weight: bold;">*</span>
                            @endif
                            @if($field === 'exit_date')
                                <br><sub style="color: #666;">Obrigatória para: Deixou de frequentar, Transferido, Falecido e Reclassificado</sub>
                            @endif
                        </td>
                        <td class="formlttd" valign="top">
                            <select required name="column_mapping[{{ $field }}]" class="geral" style="width: 300px;">
                                <option value="">-- Selecione --</option>
                                @foreach($headers as $index => $header)
                                    @php
                                        $selected = isset($autoMapping[$field]) && $autoMapping[$field] === $index;
                                    @endphp
                                    <option value="{{ $header }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $header }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has("column_mapping.{$field}"))
                                <br><span style="color: red">{{ $errors->first("column_mapping.{$field}") }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                @if($errors->has('column_mapping'))
                    <tr>
                        <td colspan="2" style="padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: red;">
                            <strong>Erro de Mapeamento:</strong> {{ $errors->first('column_mapping') }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td class="formlttd" valign="top">
                        <span class="form">Ignorar matrículas aprovadas do arquivo</span>
                        <br>
                        <sub style="vertical-align:top;">Marque para pular matrículas com situação "Aprovado" e importar mais rapidamente</sub>
                    </td>
                    <td class="formlttd" valign="top">
                        <span class="form">
                            <input type="hidden" name="ignore_approved" value="0">
                            <input type="checkbox" name="ignore_approved" id="ignore_approved" value="1" onclick="fixupCheckboxValue(this)">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button id="btn_iniciar" type="submit" class="btn-green">
                <i class="fa fa-upload"></i> Iniciar Importação
            </button>
            <a href="{{ route('final-status-import.analysis') }}" class="btn" style="margin-left: 10px; background-color: #6c757d; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
window.fixupCheckboxValue = function(input) {
    var $checkbox = $j(input);
    var $hidden = $j('input[type="hidden"][name="' + input.name + '"]');
    if ($checkbox.is(':checked')) {
        $hidden.val('1');
        $checkbox.val('1');
    } else {
        $hidden.val('0');
        $checkbox.val('0');
    }
};

$j(document).ready(function() {
    fixupCheckboxValue(document.getElementById('ignore_approved'));
    $j('#formcadastro').on('submit', function(e) {
        $j('#btn_iniciar').prop('disabled', true)
                          .html('<i class="fa fa-spinner fa-spin"></i> Processando importação...');
        return true;
    });
});
</script>
@endpush
