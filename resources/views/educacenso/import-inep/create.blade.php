@extends('layout.default')

@section('content')
    <form id="formcadastro" action="{{ route('educacenso.import.inep.store') }}" method="post"
          enctype="multipart/form-data">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24">
                    <b>Nova importação</b>
                </td>
            </tr>
            <tr id="tr_nm_ano">
                <td class="formmdtd" valign="top">
                    <span class="form">Ano</span>
                    <span class="campo_obrigatorio">*</span>
                    <br>
                    <sub style="vertical-align:top;">somente números</sub>
                </td>
                <td class="formmdtd" valign="top">
                    @include('form.select-year')
                </td>
            </tr>
            <tr id="tr_nm_arquivo">
                <td class="formmdtd" valign="top" style="padding-bottom: 30px">
                    <span class="form">Arquivos</span>
                    <span class="campo_obrigatorio">*</span>
                </td>
                <td class="formmdtd" valign="top" style="padding-top: 20px;padding-bottom: 20px">
                   <span class="form">
                       <input data-multiple-caption="{count} arquivos" class="inputfile inputfile-buttom" name="arquivos[]" id="arquivos" type="file" accept=".txt" multiple required>
                       <label for="arquivos"><span></span> <strong>Escolha um arquivo</strong></label>&nbsp;<br>
                       <span style="font-style: italic; font-size: 10px;">* Somente arquivos com formato txt serão aceitos</span>
                   </span>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Importar Ineps</button>
        </div>
    </form>
@endsection

@prepend('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endprepend
