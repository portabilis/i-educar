@extends('layout.default')

@section('content')
    <form id="formcadastro" action="" method="post">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0" role="presentation">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24">
                    <b>Publicação de avisos</b>
                </td>
            </tr>
            <tr id="tr_description">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Nome</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <input name="name" size="60" id="name" value="{{ old('name', $announcement->name) }}">
                   </span>
                </td>
            </tr>
            <tr id="tr_description">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Conteúdo do aviso</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <textarea rows="10" cols="80" class="summernote" name="description" id="description">{{ old('description', $announcement->description) }}</textarea>
                   </span>
                </td>
            </tr>
            <tr id="tr_repeat_on_login">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Repetir aviso a cada login do usuário?</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <input value="off" type="checkbox" name="repeat_on_login" id="repeat_on_login" onclick="fixupCheckboxValue(this)" autocomplete="off" @checked($announcement->repeat_on_login)>
                   </span>
                </td>
            </tr>
            <tr id="tr_show_confirmation">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Exigir que o usuário marque "Estou ciente" para poder fechar o aviso?</span>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <input value="off" type="checkbox" name="show_confirmation" id="show_confirmation" onclick="fixupCheckboxValue(this)" autocomplete="off" @checked($announcement->show_confirmation)>
                   </span>
                </td>
            </tr>
            <tr id="tr_show_vacancy">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Incluir aviso de vagas disponíveis na unidades escolares vinculadas ao usuário?</span>
                    <i data-toogle="tooltip"
                       title="Ao marcar esta opção o sistema irá informar ao fim do aviso a quantidade de vagas livres nas turmas das unidades escolares vinculadas. Se o usuário for de nível gestão (institucional), apresentará as vagas de todas a rede municipal."
                       class="ml-5 fa fa-info-circle">
                    </i>
                </td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <input value="off" type="checkbox" name="show_vacancy" id="show_vacancy" onclick="fixupCheckboxValue(this)" autocomplete="off"  @checked($announcement->show_vacancy)>
                   </span>
                </td>
            </tr>
            <tr id="tr_active">
                <td class="formmdtd" valign="top" nowrap>
                    <span class="form">Ativar aviso</span></td>
                <td class="formmdtd" valign="top">
                   <span class="form">
                       <input value="off" type="checkbox" name="active" id="active" onclick="fixupCheckboxValue(this)" autocomplete="off" @checked($announcement->deleted_at === null)>
                   </span>
                </td>
            </tr>
            <tr id="tr_tipo_usuario">
                <td scope="row" class="formmdtd" valign="top"><span class="form">Tipos de usuários que serão notificados</span></td>
                <td class="formmdtd" valign="top">
                    @include('form.select-user-type-multiple')
                </td>
            </tr>
            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Salvar</button>
            <input type="button" class="btn-default" onclick="javascript: go('/avisos/publicacao')" value="Cancelar">
        </div>
    </form>
@endsection

@prepend('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
    <link type='text/css' rel='stylesheet'
          href='{{ Asset::get("/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
@endprepend

@prepend('scripts')
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Turma.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/DynamicInput/Assets/Javascripts/Matricula.js") }}"></script>
    <script type='text/javascript'
            src='{{ Asset::get('/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script>
        $j(document).ready(function () {
            $j('.summernote').summernote({
                height: 200
            });
        });
    </script>
@endprepend
