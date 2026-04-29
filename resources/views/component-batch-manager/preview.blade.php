@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro">
    <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
        <tbody>
        <tr>
            <td class="formdktd" colspan="2" height="24"><b>Operações selecionadas</b></td>
        </tr>
        <tr>
            <td class="formmdtd" colspan="2">
                @include('component-batch-manager.partials.operations-list', ['params' => $params])
            </td>
        </tr>

        <tr>
            <td class="formdktd" colspan="2" height="24"><b>Filtros da Operação</b></td>
        </tr>
        <tr>
            <td class="formmdtd" valign="top" style="width: 200px;"><span class="form">Ano</span></td>
            <td class="formmdtd" valign="top">{{ $params['year'] }}</td>
        </tr>
        <tr>
            <td class="formlttd" valign="top"><span class="form">Escolas</span></td>
            <td class="formlttd" valign="top">
                {{ implode(', ', $schoolNames) }}
            </td>
        </tr>
        <tr>
            <td class="formmdtd" valign="top"><span class="form">Cursos</span></td>
            <td class="formmdtd" valign="top">
                {{ implode(', ', $courseNames) }}
            </td>
        </tr>
        <tr>
            <td class="formlttd" valign="top"><span class="form">Séries</span></td>
            <td class="formlttd" valign="top">
                {{ implode(', ', $gradeNames) }}
            </td>
        </tr>
        <tr>
            <td class="formmdtd" valign="top"><span class="form">Componentes</span></td>
            <td class="formmdtd" valign="top">
                {{ implode(', ', $disciplineNames) }}
            </td>
        </tr>
        <tr>
            <td class="formlttd" valign="top"><span class="form">Turmas afetadas</span></td>
            <td class="formlttd" valign="top">{{ $preview['turma_count'] ?? 0 }}</td>
        </tr>

        @php
            $idiarioError = isset($preview['idiario']['error']);
            $hasBlockingError = $blockingError || !empty($protectionDetails) || $idiarioError;
        @endphp

        </tbody>
    </table>
    </form>

    @if($params['remove_records'] ?? false)
        @if($preview['idiario'] === null)
            <div style="background-color: #d9edf7; border: 1px solid #bce8f1; color: #31708f; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                i-Diário não configurado. Apenas registros locais serão afetados.
            </div>
        @else
            @include('component-batch-manager.partials.idiario-table', [
                'idiarioData' => $preview['idiario'],
                'idiarioErrorMessage' => 'Não foi possível consultar o i-Diário. Verifique se o endpoint está disponível.',
            ])
        @endif
    @endif

    @include('component-batch-manager.partials.ieducar-table', [
        'counts' => $preview,
        'data' => $params,
        'totalIeducar' => $totalIeducar,
    ])

    <form id="formcadastro">
    <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
        <tbody>

            @if(!empty($warnings))
                <tr>
                    <td class="formmdtd" colspan="2">
                        @foreach($warnings as $warning)
                            <div style="background-color: #fcf8e3; border: 1px solid #faebcc; color: #8a6d3b; padding: 10px; margin: 5px 0; border-radius: 4px;">
                                {{ $warning }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endif

        @if(!empty($protectionDetails))
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px;">
                        <strong>Não é possível executar a operação.</strong>

                        @if(!empty($protectionDetails['componente_ano_escolar']))
                            <p style="margin: 8px 0 4px;">
                                Os seguintes componentes da série não podem ser removidos porque
                                outras escolas ainda os utilizam para o ano {{ $params['year'] }}:
                            </p>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($protectionDetails['componente_ano_escolar'] as $item)
                                    <li>
                                        <strong>{{ $item['componente'] }}</strong> ({{ $item['serie'] }}):
                                        {{ implode(', ', $item['escolas']) }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($protectionDetails['escola_serie_disciplina']))
                            <p style="margin: 8px 0 4px;">
                                Os seguintes componentes da série da escola não podem ser removidos porque
                                turmas de outros anos ainda os utilizam:
                            </p>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($protectionDetails['escola_serie_disciplina'] as $item)
                                    <li>
                                        <strong>{{ $item['componente'] }}</strong> — {{ $item['escola'] }}
                                        ({{ $item['serie'] }}): ano(s) {{ implode(', ', $item['anos_bloqueando']) }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <p style="margin: 8px 0 0;">
                            <em>Desmarque a opção ou inclua todas as escolas/anos envolvidos.</em>
                        </p>
                    </div>
                </td>
            </tr>
        @elseif($blockingError)
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px;">
                        {{ $blockingError }}
                    </div>
                </td>
            </tr>
        @endif

        @if(!$hasBlockingError)
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Confirmação</b></td>
            </tr>
            <tr>
                <td class="formmdtd" colspan="2">
                    <div style="padding: 10px;">
                        <p>Para confirmar a operação, digite <strong>delete</strong> no campo abaixo:</p>
                        <input type="text" id="confirm_input" class="geral" style="width: 200px;" autocomplete="off" placeholder="Digite delete" onpaste="return false;" ondrop="return false;">
                    </div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
    </form>

    <div style="text-align: center; margin-top: 10px; margin-bottom: 20px;">
        <a href="{{ route('component-batch-manager.create', ['restore' => 1]) }}" class="btn" style="margin-right: 10px; text-decoration: none;">Voltar</a>

        @if(!$hasBlockingError)
            <form id="execute-form" action="{{ route('component-batch-manager.execute') }}" method="post" style="display: inline;">
                @csrf
                <button type="submit" id="btn-execute" class="btn-green" style="background-color: #d9534f; border-color: #d43f3a;">
                    Confirmar e Executar
                </button>
            </form>
        @endif
    </div>

    <div id="modal-confirmation" style="display: none;">
        <p><strong>Atenção:</strong> todos os lançamentos, vínculos e registros listados acima serão excluídos permanentemente do banco de dados.</p>
        <p>Tem certeza que deseja prosseguir?</p>
    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            var $input = $('#confirm_input');
            var $btn = $('#btn-execute');

            if ($input.length && $btn.length) {
                $('#modal-confirmation').dialog({
                    autoOpen: false,
                    closeOnEscape: false,
                    draggable: false,
                    width: 560,
                    modal: true,
                    resizable: false,
                    title: 'Confirmação',
                    buttons: {
                        "Executar": function () {
                            $btn.prop('disabled', true).text('Processando...');
                            $(this).dialog("close");
                            $('#execute-form')[0].submit();
                        },
                        "Cancelar": function () {
                            $(this).dialog("close");
                        }
                    }
                });

                $('#execute-form').on('submit', function(e) {
                    if ($input.val().trim().toLowerCase() !== 'delete') {
                        e.preventDefault();
                        messageUtils.error('Digite DELETE no campo de confirmação.');
                        return false;
                    }
                    e.preventDefault();
                    $('#modal-confirmation').dialog('open');
                });
            }
        })(jQuery);
    </script>
@endpush
