@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="get">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Enturmar em lote</b></td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formmdtd" valign="top"><span class="form">Instituição:</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-institution')
                </td>
            </tr>
            <tr id="tr_nm_escola">
                <td class="formlttd" valign="top"><span class="form">Escola:</span></td>
                <td class="formlttd" valign="top">
                    @include('form.select-school')
                </td>
            </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Buscar</button>
        </div>

    </form>

    <table class="table-default">
        <thead>
        <tr>
            <th>Aluno principal</th>
            <th>Aluno(s) unificado(s)</th>
            <th>Data da unificação</th>
            <th>Feita por</th>
        </tr>
        </thead>
        <tbody>
        @foreach($unifications as $unification)
            <tr>
                <td>
                    <a href="{{ route('student_log_unification.show', ['unification' => $unification->id]) }}">{{ $unification->getMainName()  }}</a>
                </td>
                <td>
                    <a href="{{ route('student_log_unification.show', ['unification' => $unification->id]) }}">{{ implode(', ', $unification->getDuplicatesName()) }}</a>
                </td>
                <td>
                    <a href="{{ route('student_log_unification.show', ['unification' => $unification->id]) }}">{{ $unification->created_at->format('d/m/Y')  }}</a>
                </td>
                <td>
                    <a href="{{ route('student_log_unification.show', ['unification' => $unification->id]) }}">{{ $unification->createdBy->real_name  }}</a>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        <table class="paginacao" border="0" cellpadding="0" cellspacing="0" align="center">
            <tbody>
            <tr>
                <td width="23" align="center"><a href="educar_aluno_lst.php?pagina_formulario=1" class="nvp_paginador"
                                                 title="Ir para a primeira pagina"> « </a></td>
                <td width="23" align="center"><a href="educar_aluno_lst.php?pagina_formulario=1" class="nvp_paginador"
                                                 title="Ir para a pagina anterior"> ‹ </a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=1&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 1">01</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=2&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 2">02</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=3&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 3">03</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=4&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 4">04</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=5&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 5">05</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=6&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 6">06</a></td>
                <td align="center" style="padding-left:5px;padding-right:5px;"><a
                            href="educar_aluno_lst.php?pagina_formulario=7&amp;ordenacao=" class="nvp_paginador"
                            title="Ir para a página 7">07</a></td>
                <td width="23" align="center"><a href="educar_aluno_lst.php?pagina_formulario=2" class="nvp_paginador"
                                                 title="Ir para a proxima pagina"> › </a></td>
                <td width="23" align="center"><a href="educar_aluno_lst.php?pagina_formulario=2294"
                                                 class="nvp_paginador"
                                                 title="Ir para a ultima pagina"> » </a></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="text-align: center">
        <a href="/intranet/educar_unifica_aluno.php">
            <button class="btn-green" type="button">Novo</button>
        </a>
    </div>

    </form>
@endsection

@push('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endpush
