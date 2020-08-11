@inject('view', 'App\Support\View\SettingView')
@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')

    <form id="formcadastro" action="" method="post">
        <table class="table-default" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="titulo-tabela-listagem" colspan="2" height="24"><b>Configurações iniciais</b></td>
            </tr>
            @foreach($fields as $field)
                {!! $view->makeInput($field->id, $field->description, $field->type, $field->key, $field->value) !!}
            @endforeach
            </tbody>
        </table>
        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
        </div>

    </form>

@endsection
